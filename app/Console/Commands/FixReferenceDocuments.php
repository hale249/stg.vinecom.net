<?php

namespace App\Console\Commands;

use App\Models\ReferenceDocument;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FixReferenceDocuments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reference:fix-files {--id=} {--fix} {--verbose}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Diagnose and fix issues with reference document files';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Checking reference documents...');
        
        $query = ReferenceDocument::query();
        
        // If specific ID provided, only check that document
        if ($id = $this->option('id')) {
            $query->where('id', $id);
        }
        
        $documents = $query->get();
        
        if ($documents->isEmpty()) {
            $this->warn('No documents found.');
            return 0;
        }
        
        $this->info("Found {$documents->count()} documents to check.");
        
        $problemCount = 0;
        $fixedCount = 0;
        
        foreach ($documents as $document) {
            $this->info("Checking document #{$document->id}: {$document->title}");
            
            if (empty($document->file_path)) {
                $this->error("  - No file path stored in database!");
                $problemCount++;
                continue;
            }
            
            $filePath = storage_path('app/public/' . $document->file_path);
            $publicPath = public_path('storage/' . $document->file_path);
            
            $this->line("  - Stored path: {$document->file_path}");
            $this->line("  - Full path: {$filePath}");
            
            if (file_exists($filePath)) {
                $this->info("  - ✅ File exists!");
                
                // Check the symlink
                if (!file_exists($publicPath) && $this->option('fix')) {
                    $this->line("  - Checking storage symlink...");
                    
                    // Ensure storage link
                    if (!file_exists(public_path('storage'))) {
                        $this->warn("  - storage symlink doesn't exist, creating...");
                        $this->call('storage:link');
                    }
                }
            } else {
                $this->error("  - ❌ File doesn't exist!");
                $problemCount++;
                
                if ($this->option('fix')) {
                    $this->info("  - Attempting to fix...");
                    
                    // Ensure directories exist
                    $directory = dirname($filePath);
                    if (!file_exists($directory)) {
                        $this->line("  - Creating directory: {$directory}");
                        File::makeDirectory($directory, 0755, true, true);
                    }
                    
                    // Create an empty placeholder file for testing
                    if (!file_exists($filePath)) {
                        $this->warn("  - Creating placeholder file (you'll need to re-upload the actual document)");
                        File::put($filePath, "This is a placeholder file. Please re-upload the original document.");
                        $fixedCount++;
                    }
                }
            }
            
            if ($this->option('verbose')) {
                $this->line("  - Category: " . ($document->category ? $document->category->name : 'N/A'));
                $this->line("  - File name: {$document->file_name}");
                $this->line("  - File size: {$document->file_size_formatted}");
                $this->line("  - Created: {$document->created_at}");
                $this->line("  - Status: " . ($document->status ? 'Active' : 'Inactive'));
                $this->line("");
            }
        }
        
        if ($problemCount > 0) {
            $this->warn("Found {$problemCount} problems with document files.");
            if ($fixedCount > 0) {
                $this->info("Fixed {$fixedCount} issues (created placeholder files).");
            }
            
            $this->line("");
            $this->line("IMPORTANT: For documents with missing files, you need to:");
            $this->line("1. Edit the document in the admin panel");
            $this->line("2. Re-upload the correct file");
            $this->line("3. Save the changes");
        } else {
            $this->info("All document files appear to be in order!");
        }
        
        return 0;
    }
} 