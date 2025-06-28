<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentCategory extends Model
{
    use GlobalStatus;

    protected $fillable = [
        'name',
        'description',
        'icon',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean'
    ];

    public function referenceDocuments(): HasMany
    {
        return $this->hasMany(ReferenceDocument::class, 'category_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
} 