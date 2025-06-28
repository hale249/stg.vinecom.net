<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractDocument extends Model
{
    protected $fillable = [
        'invest_id',
        'title',
        'file_path',
        'file_name',
        'file_size',
        'type',
        'description'
    ];

    // Relationships
    public function invest(): BelongsTo
    {
        return $this->belongsTo(Invest::class);
    }

    // Scopes
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Accessors
    public function getTypeTextAttribute()
    {
        return match($this->type) {
            'signed_contract' => 'Hợp đồng đã ký',
            'transfer_bill' => 'Biên lai chuyển khoản',
            'other' => 'Tài liệu khác',
            default => 'Không xác định'
        };
    }

    public function getTypeIconAttribute()
    {
        return match($this->type) {
            'signed_contract' => 'las la-file-contract',
            'transfer_bill' => 'las la-money-bill',
            'other' => 'las la-file-alt',
            default => 'las la-file'
        };
    }

    public function getFileSizeFormattedAttribute()
    {
        if (!$this->file_size) {
            return '0 KB';
        }

        $size = (int) $this->file_size;

        if ($size < 1024) {
            return "$size bytes";
        } elseif ($size < 1024 * 1024) {
            return round($size / 1024, 2) . " KB";
        } elseif ($size < 1024 * 1024 * 1024) {
            return round($size / (1024 * 1024), 2) . " MB";
        } else {
            return round($size / (1024 * 1024 * 1024), 2) . " GB";
        }
    }
} 