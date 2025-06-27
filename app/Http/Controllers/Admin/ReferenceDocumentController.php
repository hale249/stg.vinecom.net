<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentCategory;
use App\Models\ReferenceDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ReferenceDocumentController extends Controller
{
    /**
     * Directory path for document storage
     */
    protected $documentPath = 'documents/reference';
    
    /**
     * Display a listing of the reference documents
     */
    public function index(Request $request)
    {
        $pageTitle = 'Tài liệu tham khảo';
        
        $query = ReferenceDocument::with('category')->latest();
        
        // Filter by category
        if ($request->has('category_id') && $request->category_id != '') {
            $query->where('category_id', $request->category_id);
        }
        
        // Filter by title
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('title', 'like', "%$search%");
        }
        
        // Filter by permission
        if ($request->has('permission')) {
            if ($request->permission == 'manager') {
                $query->where('for_manager', true);
            } elseif ($request->permission == 'staff') {
                $query->where('for_staff', true);
            }
        }
        
        $documents = $query->paginate(20);
        $categories = DocumentCategory::active()->get();
        
        return view('admin.reference_documents.index', compact('pageTitle', 'documents', 'categories'));
    }
    
    /**
     * Show the form for creating a new document
     */
    public function create()
    {
        $pageTitle = 'Thêm tài liệu tham khảo';
        $categories = DocumentCategory::active()->get();
        
        return view('admin.reference_documents.create', compact('pageTitle', 'categories'));
    }
    
    /**
     * Store a newly created document
     */
    public function store(Request $request)
    {
        \Log::info('Starting document upload process');
        
        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:document_categories,id',
            'description' => 'nullable|string',
            'document' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar,jpg,jpeg,png|max:10240', // Max 10MB
            'for_manager' => 'required|boolean',
            'for_staff' => 'required|boolean',
            'sort_order' => 'nullable|integer|min:0',
            'status' => 'required|boolean',
        ]);
        
        \Log::info('Validation passed');
        
        $document = new ReferenceDocument();
        $document->title = $request->title;
        $document->category_id = $request->category_id;
        $document->description = $request->description;
        $document->for_manager = $request->for_manager;
        $document->for_staff = $request->for_staff;
        $document->sort_order = $request->sort_order ?? 0;
        $document->status = $request->status;
        
        // Process the uploaded document
        if ($request->hasFile('document')) {
            \Log::info('Document file found in request');
            
            $file = $request->file('document');
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $filename = Str::uuid() . '.' . $extension;
            $path = $this->documentPath;
            
            \Log::info('File details', [
                'original_name' => $originalName,
                'extension' => $extension,
                'new_filename' => $filename,
                'path' => $path
            ]);
            
            // Create directory if it doesn't exist
            $fullPath = storage_path("app/public/$path");
            if (!file_exists($fullPath)) {
                \Log::info("Creating directory: $fullPath");
                mkdir($fullPath, 0755, true);
            }
            
            try {
                // Store the file
                $file->storeAs("public/$path", $filename);
                \Log::info('File stored successfully');
                
                $document->file_path = "$path/$filename";
                $document->file_name = $originalName;
                $document->file_size = $file->getSize();
            } catch (\Exception $e) {
                \Log::error('Error storing file: ' . $e->getMessage());
                throw $e;
            }
        } else {
            \Log::warning('No document file found in request');
        }
        
        try {
            $document->save();
            \Log::info('Document record saved to database');
        } catch (\Exception $e) {
            \Log::error('Error saving document record: ' . $e->getMessage());
            throw $e;
        }
        
        $notify[] = ['success', 'Tài liệu tham khảo đã được thêm thành công'];
        return to_route('admin.documents.index')->withNotify($notify);
    }
    
    /**
     * Show the form for editing the document
     */
    public function edit($id)
    {
        $pageTitle = 'Sửa tài liệu tham khảo';
        $document = ReferenceDocument::findOrFail($id);
        $categories = DocumentCategory::active()->get();
        
        return view('admin.reference_documents.edit', compact('pageTitle', 'document', 'categories'));
    }
    
    /**
     * Update the document in storage
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:document_categories,id',
            'description' => 'nullable|string',
            'document' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar,jpg,jpeg,png|max:10240', // Max 10MB
            'for_manager' => 'required|boolean',
            'for_staff' => 'required|boolean',
            'sort_order' => 'nullable|integer|min:0',
            'status' => 'required|boolean',
        ]);
        
        $document = ReferenceDocument::findOrFail($id);
        $document->title = $request->title;
        $document->category_id = $request->category_id;
        $document->description = $request->description;
        $document->for_manager = $request->for_manager;
        $document->for_staff = $request->for_staff;
        $document->sort_order = $request->sort_order ?? 0;
        $document->status = $request->status;
        
        // Process the uploaded document if a new one is provided
        if ($request->hasFile('document')) {
            // Delete the old file if it exists
            if ($document->file_path) {
                $oldPath = storage_path('app/public/' . $document->file_path);
                if (File::exists($oldPath)) {
                    File::delete($oldPath);
                }
            }
            
            // Save the new file
            $file = $request->file('document');
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $filename = Str::uuid() . '.' . $extension;
            $path = $this->documentPath;
            
            // Create directory if it doesn't exist
            if (!file_exists(storage_path("app/public/$path"))) {
                mkdir(storage_path("app/public/$path"), 0755, true);
            }
            
            // Store the file
            $file->storeAs("public/$path", $filename);
            
            $document->file_path = "$path/$filename";
            $document->file_name = $originalName;
            $document->file_size = $file->getSize();
        }
        
        $document->save();
        
        $notify[] = ['success', 'Tài liệu tham khảo đã được cập nhật thành công'];
        return back()->withNotify($notify);
    }
    
    /**
     * Remove the document from storage
     */
    public function destroy($id)
    {
        $document = ReferenceDocument::findOrFail($id);
        
        // Delete the file if it exists
        if ($document->file_path) {
            $filePath = storage_path('app/public/' . $document->file_path);
            if (File::exists($filePath)) {
                File::delete($filePath);
            }
        }
        
        $document->delete();
        
        $notify[] = ['success', 'Tài liệu tham khảo đã được xóa thành công'];
        return back()->withNotify($notify);
    }
    
    /**
     * Update the status of the document
     */
    public function status($id)
    {
        $document = ReferenceDocument::findOrFail($id);
        $document->status = !$document->status;
        $document->save();
        
        $status = $document->status ? 'kích hoạt' : 'vô hiệu hóa';
        $notify[] = ['success', "Trạng thái tài liệu đã được $status thành công"];
        return back()->withNotify($notify);
    }
    
    /**
     * Download a reference document file
     */
    public function download($id)
    {
        $document = ReferenceDocument::findOrFail($id);
        $filePath = storage_path('app/public/' . $document->file_path);

        if (!file_exists($filePath)) {
            abort(404, 'File not found.');
        }

        return response()->download($filePath, $document->file_name);
    }
} 