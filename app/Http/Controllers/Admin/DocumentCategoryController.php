<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentCategory;
use Illuminate\Http\Request;

class DocumentCategoryController extends Controller
{
    /**
     * Display a listing of categories
     */
    public function index()
    {
        $pageTitle = 'Tài liệu học tập';
        $categories = DocumentCategory::latest()->paginate(20);
        
        return view('admin.document_categories.index', compact('pageTitle', 'categories'));
    }
    
    /**
     * Show the form for creating a new category
     */
    public function create()
    {
        $pageTitle = 'Thêm danh mục tài liệu';
        return view('admin.document_categories.create', compact('pageTitle'));
    }
    
    /**
     * Store a newly created category
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:100',
            'status' => 'required|boolean',
        ]);
        
        $category = new DocumentCategory();
        $category->name = $request->name;
        $category->description = $request->description;
        $category->icon = $request->icon;
        $category->status = $request->status;
        $category->save();
        
        $notify[] = ['success', 'Danh mục tài liệu đã được tạo thành công'];
        return to_route('admin.document.categories.index')->withNotify($notify);
    }
    
    /**
     * Show the form for editing a category
     */
    public function edit($id)
    {
        $pageTitle = 'Sửa danh mục tài liệu';
        $category = DocumentCategory::findOrFail($id);
        
        return view('admin.document_categories.edit', compact('pageTitle', 'category'));
    }
    
    /**
     * Update the specified category
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:100',
            'status' => 'required|boolean',
        ]);
        
        $category = DocumentCategory::findOrFail($id);
        $category->name = $request->name;
        $category->description = $request->description;
        $category->icon = $request->icon;
        $category->status = $request->status;
        $category->save();
        
        $notify[] = ['success', 'Danh mục tài liệu đã được cập nhật thành công'];
        return back()->withNotify($notify);
    }
    
    /**
     * Remove the specified category
     */
    public function destroy($id)
    {
        $category = DocumentCategory::findOrFail($id);
        
        // Check if category has associated documents
        $docCount = $category->referenceDocuments()->count();
        if ($docCount > 0) {
            $notify[] = ['error', 'Không thể xóa danh mục này vì có ' . $docCount . ' tài liệu liên kết'];
            return back()->withNotify($notify);
        }
        
        $category->delete();
        
        $notify[] = ['success', 'Danh mục tài liệu đã được xóa thành công'];
        return back()->withNotify($notify);
    }
    
    /**
     * Update the status of a category
     */
    public function status($id)
    {
        return $this->changeStatus(DocumentCategory::findOrFail($id), 'Trạng thái danh mục tài liệu');
    }
    
    private function changeStatus($model, $message)
    {
        $model->status = !$model->status;
        $model->save();
        
        $status = $model->status ? 'kích hoạt' : 'vô hiệu hóa';
        $notify[] = ['success', "$message đã được $status thành công"];
        return back()->withNotify($notify);
    }
} 