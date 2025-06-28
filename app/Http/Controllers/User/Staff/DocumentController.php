<?php

namespace App\Http\Controllers\User\Staff;

use App\Http\Controllers\Controller;
use App\Models\DocumentCategory;
use App\Models\ReferenceDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class DocumentController extends Controller
{
    /**
     * Display a listing of reference documents available for the user
     */
    public function index(Request $request)
    {
        $pageTitle = 'Tài liệu tham khảo';
        $user = auth()->user();
        
        // Kiểm tra nếu không phải staff hoặc manager, trả về lỗi
        if (!$user->is_staff) {
            return redirect()->route('user.home')->with('error', 'Bạn không có quyền truy cập trang này');
        }
        
        // Determine which documents to show based on user role
        $query = ReferenceDocument::with('category')
            ->where('status', true)
            ->whereHas('category', function($q) {
                $q->where('status', true);
            });
            
        // Check user permissions and set layout based on route
        $isManager = request()->is('user/manager/*');
        if ($isManager) {
            $query->where('for_manager', true);
            $layout = 'user.staff.layouts.app';
        } else {
            $query->where('for_staff', true);
            $layout = 'user.staff.layouts.staff_app';
        }

        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%");
            });
        }

        if ($request->category) {
            $query->where('category_id', $request->category);
        }

        $documents = $query->orderBy('id', 'desc')->paginate(20);
        $categories = DocumentCategory::where('status', true)->get();
        
        return view('user.staff.documents.index', compact('pageTitle', 'documents', 'categories'))->with([
            'layout' => $layout,
            'isManager' => $isManager
        ]);
    }
    
    /**
     * View a document (if it's a PDF or image)
     */
    public function view($id)
    {
        $user = auth()->user();
        
        // Kiểm tra nếu không phải staff hoặc manager, trả về lỗi
        if (!$user->is_staff) {
            return redirect()->route('user.home')->with('error', 'Bạn không có quyền truy cập trang này');
        }
        
        $document = ReferenceDocument::with('category')->where('status', true)->findOrFail($id);
        
        // Check if user can access the document
        if (!$this->canUserAccessDocument($user, $document)) {
            abort(403, 'Bạn không có quyền xem tài liệu này');
        }
        
        // Check if document category is active
        if (!$document->category || !$document->category->status) {
            abort(404, 'Tài liệu không tồn tại hoặc đã bị xóa');
        }
        
        $pageTitle = 'Xem tài liệu - ' . $document->title;
        
        // Check if the document is viewable in browser (PDF or image)
        $extension = pathinfo($document->file_name, PATHINFO_EXTENSION);
        $viewable = in_array(strtolower($extension), ['pdf', 'jpg', 'jpeg', 'png', 'gif']);
        
        $isManager = request()->is('user/manager*');
        $layout = $isManager ? 'user.staff.layouts.app' : 'user.staff.layouts.staff_app';
        $routePrefix = $isManager ? 'user.staff.manager' : 'user.staff.staff';
        
        if (!$viewable) {
            return redirect()->route("$routePrefix.documents.download", $id);
        }
        
        // Verify file exists
        $filePath = storage_path('app/public/' . $document->file_path);
        if (!file_exists($filePath)) {
            return back()->with('error', 'File không tồn tại. Vui lòng liên hệ quản trị viên.');
        }
        
        return view('user.staff.documents.view', compact(
            'pageTitle', 
            'document',
            'viewable', 
            'isManager', 
            'layout'
        ));
    }
    
    /**
     * Download a document file
     */
    public function download($id)
    {
        $user = auth()->user();
        
        // Kiểm tra nếu không phải staff hoặc manager, trả về lỗi
        if (!$user->is_staff) {
            return redirect()->route('user.home')->with('error', 'Bạn không có quyền truy cập trang này');
        }
        
        $document = ReferenceDocument::where('status', true)->findOrFail($id);
        
        // Check if user can access the document
        if (!$this->canUserAccessDocument($user, $document)) {
            abort(403, 'Bạn không có quyền tải tài liệu này');
        }
        
        // Check if document category is active
        if (!$document->category || !$document->category->status) {
            abort(404, 'Tài liệu không tồn tại hoặc đã bị xóa');
        }
        
        // Check if file exists
        $filePath = storage_path('app/public/' . $document->file_path);
        if (!file_exists($filePath)) {
            abort(404, 'File không tồn tại trong hệ thống');
        }
        
        // Return the file as a download with proper encoding for Vietnamese characters
        $headers = [
            'Content-Type' => mime_content_type($filePath),
            'Content-Disposition' => 'attachment; filename="' . rawurlencode($document->file_name) . '"'
        ];
        
        return Response::download($filePath, $document->file_name, $headers);
    }
    
    /**
     * Stream a document file (for direct viewing in browser)
     */
    public function stream($id)
    {
        $user = auth()->user();
        
        // Kiểm tra nếu không phải staff hoặc manager, trả về lỗi
        if (!$user->is_staff) {
            return redirect()->route('user.home')->with('error', 'Bạn không có quyền truy cập trang này');
        }
        
        $document = ReferenceDocument::with('category')->where('status', true)->findOrFail($id);
        
        // Check if user can access the document
        if (!$this->canUserAccessDocument($user, $document)) {
            abort(403, 'Bạn không có quyền xem tài liệu này');
        }
        
        // Check if document category is active
        if (!$document->category || !$document->category->status) {
            abort(404, 'Tài liệu không tồn tại hoặc đã bị xóa');
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
            $routePrefix = request()->is('user/manager*') ? 'user.staff.manager' : 'user.staff.staff';
            return redirect()->route("$routePrefix.documents.download", $id);
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