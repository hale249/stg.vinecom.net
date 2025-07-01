<?php

namespace App\Http\Controllers\User\Staff;

use App\Http\Controllers\Controller;
use App\Models\Invest;
use App\Models\ContractDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ContractDocumentController extends Controller
{
    /**
     * Display contract documents
     */
    public function index($investId)
    {
        $pageTitle = 'Contract Documents';
        $user = Auth::user();
        $general = gs();
        
        $invest = Invest::where('id', $investId)
            ->where(function($query) use ($user) {
                $query->where('staff_id', $user->id)
                      ->orWhere('referral_code', $user->referral_code);
            })
            ->with(['project', 'user', 'documents'])
            ->firstOrFail();
        
        // Group documents by type - with null safety
        $documents = $invest->documents ?? collect();
        $signedContracts = $documents->where('type', 'signed_contract')->values();
        $transferBills = $documents->where('type', 'transfer_bill')->values();
        $otherDocuments = $documents->where('type', 'other')->values();
        
        // Get notifications for topnav
        $pending_notifications = $user->notifications()->where('user_read', 0)->count();
        $notifications = $user->notifications()->latest()->limit(5)->get();
        
        return view('user.staff.staff.documents', compact(
            'pageTitle', 
            'invest', 
            'signedContracts',
            'transferBills',
            'otherDocuments',
            'pending_notifications',
            'notifications',
            'general'
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
        
        $user = Auth::user();
        $invest = Invest::where('id', $investId)
            ->where(function($query) use ($user) {
                $query->where('staff_id', $user->id)
                      ->orWhere('referral_code', $user->referral_code);
            })
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
        return redirect()->route('user.staff.staff.contract.documents', $investId)->withNotify($notify);
    }
    
    /**
     * Download a document
     */
    public function download($investId, $documentId)
    {
        $user = Auth::user();
        $invest = Invest::where('id', $investId)
            ->where(function($query) use ($user) {
                $query->where('staff_id', $user->id)
                      ->orWhere('referral_code', $user->referral_code);
            })
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
        $user = Auth::user();
        $invest = Invest::where('id', $investId)
            ->where(function($query) use ($user) {
                $query->where('staff_id', $user->id)
                      ->orWhere('referral_code', $user->referral_code);
            })
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