<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectDocument extends Model
{
    protected $fillable = [
        'project_id',
        'title',
        'file_path',
        'file_name',
        'file_size',
        'type',
        'description',
        'is_public',
        'sort_order'
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'sort_order' => 'integer'
    ];

    // Relationships
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    // Scopes
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('created_at', 'desc');
    }

    // Accessors
    public function getTypeTextAttribute()
    {
        return match($this->type) {
            'legal' => 'Tài liệu pháp lý',
            'financial' => 'Tài liệu tài chính',
            'technical' => 'Tài liệu kỹ thuật',
            'other' => 'Tài liệu khác',
            default => 'Không xác định'
        };
    }

    public function getTypeIconAttribute()
    {
        return match($this->type) {
            'legal' => 'las la-gavel',
            'financial' => 'las la-chart-line',
            'technical' => 'las la-cogs',
            'other' => 'las la-file-alt',
            default => 'las la-file'
        };
    }

    public function getFileSizeFormattedAttribute()
    {
        if (!$this->file_size) return null;
        
        $bytes = (int) $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    // Methods
    public function getDownloadUrl()
    {
        return asset('storage/' . $this->file_path);
    }

    public function getPreviewUrl()
    {
        // URL để preview PDF trong browser thông qua route có xử lý headers
        return route('project.document.view', ['projectId' => $this->project_id, 'documentId' => $this->id]);
    }
} 