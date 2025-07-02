<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Honor extends Model
{
    protected $fillable = [
        'title',
        'description',
        'image',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now());
    }
    
    public function images()
    {
        return $this->hasMany(HonorImage::class)->orderBy('sort_order');
    }
    
    public function featuredImage()
    {
        return $this->hasOne(HonorImage::class)->where('is_featured', true);
    }
}
