<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Invest;
use App\Models\ContractDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ContractDocumentController extends Controller
{
    /**
     * Display contract documents
     */
    public function index($investId)
    {
        $invest = Invest::where('id', $investId)
            ->where('user_id', auth()->id())
            ->with(['project', 'documents'])
            ->firstOrFail();

        $pageTitle = 'Contract Documents: ' . $invest->invest_no;
        $activeTemplate = activeTemplate();
        
        // Group documents by type
        $signedContracts = $invest->documents()->byType('signed_contract')->get();
        $transferBills = $invest->documents()->byType('transfer_bill')->get();
        $otherDocuments = $invest->documents()->byType('other')->get();
        
        return view($activeTemplate . 'user.invest.documents', compact(
            'pageTitle', 
            'invest', 
            'activeTemplate',
            'signedContracts',
            'transferBills',
            'otherDocuments'
        ));
    }
    
    /**
     * Upload a new document
     */
    public function upload(Request $request, $investId)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:signed_contract,transfer_bill,other',
            'description' => 'nullable|string',
            'document_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240', // Max 10MB
        ]);
        
        $invest = Invest::where('id', $investId)
            ->where('user_id', auth()->id())
            ->firstOrFail();
        
        // Upload file
        $file = $request->file('document_file');
        $fileExtension = $file->getClientOriginalExtension();
        $fileName = time() . '_' . Str::slug($request->title) . '.' . $fileExtension;
        $filePath = 'contract-documents/' . $investId . '/' . $fileName;
        
        Storage::disk('public')->put($filePath, file_get_contents($file));
        
        // Create document record
        ContractDocument::create([
            'invest_id' => $investId,
            'title' => $request->title,
            'file_path' => $filePath,
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'type' => $request->type,
            'description' => $request->description
        ]);
        
        $notify[] = ['success', 'Document uploaded successfully'];
        return redirect()->route('user.invest.documents', $investId)->withNotify($notify);
    }
    
    /**
     * Download a document
     */
    public function download($investId, $documentId)
    {
        $invest = Invest::where('id', $investId)
            ->where('user_id', auth()->id())
            ->firstOrFail();
            
        $document = ContractDocument::where('invest_id', $investId)
            ->findOrFail($documentId);
        
        if (!Storage::disk('public')->exists($document->file_path)) {
            $notify[] = ['error', 'Document file not found'];
            return back()->withNotify($notify);
        }
        
        return Storage::disk('public')->download($document->file_path, $document->file_name);
    }
    
    /**
     * Delete a document
     */
    public function delete($investId, $documentId)
    {
        $invest = Invest::where('id', $investId)
            ->where('user_id', auth()->id())
            ->firstOrFail();
            
        $document = ContractDocument::where('invest_id', $investId)
            ->findOrFail($documentId);
        
        // Delete file
        if (Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }
        
        // Delete record
        $document->delete();
        
        $notify[] = ['success', 'Document deleted successfully'];
        return back()->withNotify($notify);
    }
} 