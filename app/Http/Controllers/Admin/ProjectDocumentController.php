<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProjectDocumentController extends Controller
{
    public function index($projectId)
    {
        $pageTitle = 'Quản lý tài liệu dự án';
        $project = Project::findOrFail($projectId);
        $documents = $project->documents()->paginate(20);
        
        return view('admin.project.documents.index', compact('pageTitle', 'project', 'documents'));
    }

    public function create($projectId)
    {
        $pageTitle = 'Thêm tài liệu mới';
        $project = Project::findOrFail($projectId);
        
        return view('admin.project.documents.create', compact('pageTitle', 'project'));
    }

    public function store(Request $request, $projectId)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:legal,financial,technical,other',
            'description' => 'nullable|string',
            'document_file' => 'required|file|mimes:pdf|max:10240', // Max 10MB
            'is_public' => 'required|in:0,1',
            'sort_order' => 'nullable|integer|min:0'
        ]);

        $project = Project::findOrFail($projectId);
        
        // Upload file
        $file = $request->file('document_file');
        $fileName = time() . '_' . Str::slug($request->title) . '.pdf';
        $filePath = 'project-documents/' . $projectId . '/' . $fileName;
        
        Storage::disk('public')->put($filePath, file_get_contents($file));
        
        // Create document record
        ProjectDocument::create([
            'project_id' => $projectId,
            'title' => $request->title,
            'file_path' => $filePath,
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'type' => $request->type,
            'description' => $request->description,
            'is_public' => (bool) $request->is_public,
            'sort_order' => $request->sort_order ?? 0
        ]);

        $notify[] = ['success', 'Tài liệu đã được thêm thành công'];
        return redirect()->route('admin.project.documents.index', $projectId)->withNotify($notify);
    }

    public function edit($projectId, $documentId)
    {
        $pageTitle = 'Chỉnh sửa tài liệu';
        $project = Project::findOrFail($projectId);
        $document = ProjectDocument::where('project_id', $projectId)->findOrFail($documentId);
        
        return view('admin.project.documents.edit', compact('pageTitle', 'project', 'document'));
    }

    public function update(Request $request, $projectId, $documentId)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:legal,financial,technical,other',
            'description' => 'nullable|string',
            'document_file' => 'nullable|file|mimes:pdf|max:10240',
            'is_public' => 'required|in:0,1',
            'sort_order' => 'nullable|integer|min:0'
        ]);

        $document = ProjectDocument::where('project_id', $projectId)->findOrFail($documentId);
        
        $data = [
            'title' => $request->title,
            'type' => $request->type,
            'description' => $request->description,
            'is_public' => (bool) $request->is_public,
            'sort_order' => $request->sort_order ?? 0
        ];
        
        // Update file if provided
        if ($request->hasFile('document_file')) {
            $file = $request->file('document_file');
            $fileName = time() . '_' . Str::slug($request->title) . '.pdf';
            $filePath = 'project-documents/' . $projectId . '/' . $fileName;
            
            // Delete old file
            if (Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }
            
            // Upload new file
            Storage::disk('public')->put($filePath, file_get_contents($file));
            
            $data['file_path'] = $filePath;
            $data['file_name'] = $file->getClientOriginalName();
            $data['file_size'] = $file->getSize();
        }
        
        $document->update($data);

        $notify[] = ['success', 'Tài liệu đã được cập nhật thành công'];
        return redirect()->route('admin.project.documents.index', $projectId)->withNotify($notify);
    }

    public function destroy($projectId, $documentId)
    {
        $document = ProjectDocument::where('project_id', $projectId)->findOrFail($documentId);
        
        // Delete file
        if (Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }
        
        $document->delete();

        $notify[] = ['success', 'Tài liệu đã được xóa thành công'];
        return redirect()->route('admin.project.documents.index', $projectId)->withNotify($notify);
    }

    public function download($projectId, $documentId)
    {
        $document = ProjectDocument::where('project_id', $projectId)->findOrFail($documentId);
        
        if (!Storage::disk('public')->exists($document->file_path)) {
            abort(404);
        }
        
        return Storage::disk('public')->download($document->file_path, $document->file_name);
    }
} 