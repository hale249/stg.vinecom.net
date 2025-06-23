<?php

namespace App\Http\Controllers;

use App\Models\ProjectDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProjectDocumentController extends Controller
{
    public function view($projectId, $documentId)
    {
        $document = ProjectDocument::where('project_id', $projectId)
            ->where('id', $documentId)
            ->where('is_public', true)
            ->firstOrFail();

        if (!Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'File not found');
        }

        $filePath = Storage::disk('public')->path($document->file_path);
        $fileName = $document->file_name;

        // Set proper headers for PDF viewing
        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $fileName . '"',
            'Cache-Control' => 'public, max-age=3600',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
} 