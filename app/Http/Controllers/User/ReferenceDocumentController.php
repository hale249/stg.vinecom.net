<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\DocumentCategory;
use App\Models\ReferenceDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

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
        $document = ReferenceDocument::where('status', true)->findOrFail($id);
        
        // Check if user can access the document
        if (!$this->canUserAccessDocument($user, $document)) {
            abort(403);
        }
        
        // Check if document category is active
        if (!$document->category || !$document->category->status) {
            abort(404);
        }
        
        $pageTitle = 'Xem tài liệu - ' . $document->title;
        
        // Check if the document is viewable in browser (PDF or image)
        $extension = pathinfo($document->file_name, PATHINFO_EXTENSION);
        $viewable = in_array(strtolower($extension), ['pdf', 'jpg', 'jpeg', 'png', 'gif']);
        
        if (!$viewable) {
            return redirect()->route('reference.document.download', $id);
        }
        
        return view('user.documents.view', compact('pageTitle', 'document', 'viewable'));
    }
    
    /**
     * Download a document file
     */
    public function download($id)
    {
        $user = auth()->user();
        $document = ReferenceDocument::where('status', true)->findOrFail($id);
        
        // Check if user can access the document
        if (!$this->canUserAccessDocument($user, $document)) {
            abort(403);
        }
        
        // Check if document category is active
        if (!$document->category || !$document->category->status) {
            abort(404);
        }
        
        // Check if file exists
        $filePath = storage_path('app/public/' . $document->file_path);
        if (!file_exists($filePath)) {
            abort(404);
        }
        
        // Return the file as a download
        return Response::download($filePath, $document->file_name);
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