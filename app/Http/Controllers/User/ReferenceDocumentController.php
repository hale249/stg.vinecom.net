<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\DocumentCategory;
use App\Models\ReferenceDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class ReferenceDocumentController extends Controller
{
    /**
     * Constructor to check staff/manager access
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            
            // Only allow staff and managers to access
            if (!$user->is_staff) {
                abort(403, 'Bạn không có quyền truy cập trang này');
            }
            
            return $next($request);
        });
    }
    
    /**
     * Display a listing of reference documents available for the user
     */
    public function index(Request $request)
    {
        $pageTitle = 'Tài liệu tham khảo';
        $user = auth()->user();
        
        // Determine which documents to show based on user role
        $query = ReferenceDocument::with('category')
            ->where('status', true)
            ->whereHas('category', function($q) {
                $q->where('status', true);
            });
            
        // Check user permissions
        if ($user->isManager()) {
            $query->where('for_manager', true);
        } elseif ($user->isStaff()) {
            $query->where('for_staff', true);
        } else {
            abort(403); // Not authorized if not manager or staff
        }
        
        // Filter by category
        if ($request->has('category_id') && $request->category_id != '') {
            $query->where('category_id', $request->category_id);
        }
        
        // Filter by search term
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                  ->orWhere('description', 'like', "%$search%");
            });
        }
        
        $documents = $query->ordered()->paginate(20);
        $categories = DocumentCategory::active()->get();
        
        return view('user.documents.index', compact('pageTitle', 'documents', 'categories'));
    }
    
    /**
     * View a document (if it's a PDF or image)
     */
    public function view($id)
    {
        $user = auth()->user();
        $document = ReferenceDocument::with('category')->where('status', true)->findOrFail($id);
        
        \Log::info('User attempting to view document', [
            'user_id' => $user->id,
            'document_id' => $document->id,
            'title' => $document->title
        ]);
        
        // Check if user can access the document
        if (!$this->canUserAccessDocument($user, $document)) {
            \Log::warning('User access denied to document view', [
                'user_id' => $user->id,
                'document_id' => $document->id
            ]);
            abort(403, 'Bạn không có quyền truy cập tài liệu này');
        }
        
        // Check if document category is active
        if (!$document->category || !$document->category->status) {
            \Log::warning('Document category inactive or missing during view', [
                'document_id' => $document->id,
                'category_status' => $document->category ? $document->category->status : 'null'
            ]);
            abort(404, 'Danh mục tài liệu không tồn tại hoặc đã bị vô hiệu hóa');
        }
        
        $pageTitle = 'Xem tài liệu - ' . $document->title;
        
        // Check if the document is viewable in browser (PDF or image)
        $extension = pathinfo($document->file_name, PATHINFO_EXTENSION);
        $viewable = in_array(strtolower($extension), ['pdf', 'jpg', 'jpeg', 'png', 'gif']);
        
        if (!$viewable) {
            \Log::info('Document not viewable, redirecting to download', [
                'document_id' => $document->id,
                'extension' => $extension
            ]);
            return redirect()->route('reference.document.download', $id);
        }
        
        // Verify file exists
        $filePath = storage_path('app/public/' . $document->file_path);
        if (!file_exists($filePath)) {
            \Log::error('File not found during view attempt', [
                'document_id' => $document->id,
                'expected_path' => $filePath
            ]);
            return back()->with('error', 'File không tồn tại. Vui lòng liên hệ quản trị viên.');
        }
        
        \Log::info('Serving document for viewing', [
            'document_id' => $document->id,
            'file_exists' => true
        ]);
        
        return view('user.documents.view', compact('pageTitle', 'document', 'viewable'));
    }
    
    /**
     * Download a document file
     */
    public function download($id)
    {
        $user = auth()->user();
        $document = ReferenceDocument::with('category')->where('status', true)->findOrFail($id);
        
        \Log::info('User attempting document download', [
            'user_id' => $user->id,
            'document_id' => $document->id,
            'title' => $document->title,
            'stored_path' => $document->file_path
        ]);
        
        // Check if user can access the document
        if (!$this->canUserAccessDocument($user, $document)) {
            \Log::warning('User access denied to document', [
                'user_id' => $user->id,
                'document_id' => $document->id
            ]);
            abort(403, 'Bạn không có quyền truy cập tài liệu này');
        }
        
        // Check if document category is active
        if (!$document->category || !$document->category->status) {
            \Log::warning('Document category inactive or missing', [
                'document_id' => $document->id,
                'category_status' => $document->category ? $document->category->status : 'null'
            ]);
            abort(404, 'Danh mục tài liệu không tồn tại hoặc đã bị vô hiệu hóa');
        }
        
        // Check if file exists
        $filePath = storage_path('app/public/' . $document->file_path);
        \Log::info('Full file path', ['path' => $filePath]);
        
        if (!file_exists($filePath)) {
            \Log::error('File not found at path', ['path' => $filePath]);
            
            // Try to recreate document directories if they don't exist
            $directory = dirname($filePath);
            if (!file_exists($directory)) {
                \Log::info('Attempting to create missing directory', ['directory' => $directory]);
                mkdir($directory, 0755, true);
            }
            
            return back()->with('error', 'File không tồn tại. Vui lòng liên hệ quản trị viên.');
        }
        
        \Log::info('File found, starting download');
        
        // Return the file as a download
        return Response::download($filePath, $document->file_name);
    }
    
    /**
     * Stream a document file (for direct viewing in browser)
     */
    public function stream($id)
    {
        $user = auth()->user();
        $document = ReferenceDocument::with('category')->where('status', true)->findOrFail($id);
        
        \Log::info('User attempting to stream document', [
            'user_id' => $user->id,
            'document_id' => $document->id
        ]);
        
        // Check if user can access the document
        if (!$this->canUserAccessDocument($user, $document)) {
            abort(403, 'Bạn không có quyền truy cập tài liệu này');
        }
        
        // Check if document category is active
        if (!$document->category || !$document->category->status) {
            abort(404, 'Danh mục tài liệu không tồn tại hoặc đã bị vô hiệu hóa');
        }
        
        // Check if file exists
        $filePath = storage_path('app/public/' . $document->file_path);
        if (!file_exists($filePath)) {
            abort(404, 'File không tồn tại');
        }
        
        // Determine file type
        $extension = pathinfo($document->file_name, PATHINFO_EXTENSION);
        $mimeType = null;
        
        if (strtolower($extension) === 'pdf') {
            $mimeType = 'application/pdf';
        } elseif (in_array(strtolower($extension), ['jpg', 'jpeg'])) {
            $mimeType = 'image/jpeg';
        } elseif (strtolower($extension) === 'png') {
            $mimeType = 'image/png';
        } elseif (strtolower($extension) === 'gif') {
            $mimeType = 'image/gif';
        } else {
            return redirect()->route('reference.document.download', $id);
        }
        
        // Stream the file directly to the browser
        return response()->file($filePath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $document->file_name . '"',
        ]);
    }
    
    /**
     * Check if user can access a document based on their role
     */
    protected function canUserAccessDocument($user, $document)
    {
        if ($user->isManager()) {
            return $document->for_manager;
        } elseif ($user->isStaff()) {
            return $document->for_staff;
        }
        
        return false;
    }
} 