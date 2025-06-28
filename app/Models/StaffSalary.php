<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffSalary extends Model
{
    protected $fillable = [
        'staff_id',
        'manager_id',
        'month_year',
        'base_salary',
        'sales_amount',
        'commission_rate',
        'commission_amount',
        'bonus_amount',
        'deduction_amount',
        'total_salary',
        'kpi_percentage',
        'kpi_status',
        'notes',
        'status',
        'paid_at'
    ];

    protected $casts = [
        'base_salary' => 'decimal:2',
        'sales_amount' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'bonus_amount' => 'decimal:2',
        'deduction_amount' => 'decimal:2',
        'total_salary' => 'decimal:2',
        'kpi_percentage' => 'decimal:2',
        'paid_at' => 'datetime'
    ];

    // Relationships
    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    // Scopes
    public function scopeByMonth($query, $monthYear)
    {
        return $query->where('month_year', $monthYear);
    }

    public function scopeByManager($query, $managerId)
    {
        return $query->where('manager_id', $managerId);
    }

    public function scopeByStaff($query, $staffId)
    {
        return $query->where('staff_id', $staffId);
    }

    // Accessors
    public function getKpiStatusTextAttribute()
    {
        return match($this->kpi_status) {
            'exceeded' => 'Vượt KPI',
            'achieved' => 'Đạt KPI',
            'near_achieved' => 'Gần đạt',
            'not_achieved' => 'Không đạt',
            default => 'Không xác định'
        };
    }

    public function getStatusTextAttribute()
    {
        return match($this->status) {
            'pending' => 'Chờ duyệt',
            'approved' => 'Đã duyệt',
            'paid' => 'Đã thanh toán',
            'cancelled' => 'Đã hủy',
            default => 'Không xác định'
        };
    }

    // Methods
    public function calculateTotalSalary()
    {
        $this->total_salary = $this->base_salary + $this->commission_amount + $this->bonus_amount - $this->deduction_amount;
        return $this->total_salary;
    }

    public function calculateCommission()
    {
        // Original formula: ($this->sales_amount * $this->commission_rate) / 100;
        // New formula: (1.5% × Tiền đầu tư của hợp đồng) / 12
        $this->commission_amount = ($this->sales_amount * 1.5 / 100) / 12;
        return $this->commission_amount;
    }
}
