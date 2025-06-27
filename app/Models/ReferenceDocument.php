<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReferenceDocument extends Model
{
    use GlobalStatus;

    protected $fillable = [
        'title',
        'category_id',
        'file_path',
        'file_name',
        'file_size',
        'description',
        'for_manager',
        'for_staff',
        'sort_order',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean',
        'for_manager' => 'boolean',
        'for_staff' => 'boolean',
        'sort_order' => 'integer'
    ];

    // Relationships
    public function category(): BelongsTo
    {
        return $this->belongsTo(DocumentCategory::class, 'category_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeForManagers($query)
    {
        return $query->where('for_manager', 1);
    }

    public function scopeForStaff($query)
    {
        return $query->where('for_staff', 1);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('created_at', 'desc');
    }

    // Accessors
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
        return route('reference.document.download', $this->id);
    }

    public function getPreviewUrl()
    {
        return route('reference.document.view', $this->id);
    }

    public function canBeAccessedBy(User $user)
    {
        if ($user->isManager()) {
            return $this->for_manager;
        } elseif ($user->isStaff()) {
            return $this->for_staff;
        }
        
        return false;
    }

    public function isViewable()
    {
        return $this->isPDF() || $this->isImage();
    }

    public function isPDF()
    {
        $extension = strtolower(pathinfo($this->file_name, PATHINFO_EXTENSION));
        return $extension === 'pdf';
    }

    public function isImage()
    {
        $extension = strtolower(pathinfo($this->file_name, PATHINFO_EXTENSION));
        return in_array($extension, ['jpg', 'jpeg', 'png', 'gif']);
    }
} 