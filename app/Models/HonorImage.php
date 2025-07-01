<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HonorImage extends Model
{
    protected $fillable = [
        'honor_id',
        'image',
        'caption',
        'sort_order',
        'is_featured',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
    ];

    public function honor()
    {
        return $this->belongsTo(Honor::class);
    }
}
