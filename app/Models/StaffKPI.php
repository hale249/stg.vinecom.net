<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffKPI extends Model
{
    protected $table = 'staff_k_p_i_s';

    protected $fillable = [
        'staff_id',
        'manager_id',
        'month_year',
        'target_contracts',
        'actual_contracts',
        'target_sales',
        'actual_sales',
        'target_customers',
        'actual_customers',
        'contract_completion_rate',
        'sales_completion_rate',
        'customer_completion_rate',
        'overall_kpi_percentage',
        'kpi_status',
        'notes',
        'status',
        'approved_at'
    ];

    protected $casts = [
        'target_sales' => 'decimal:2',
        'actual_sales' => 'decimal:2',
        'target_customers' => 'decimal:0',
        'contract_completion_rate' => 'decimal:2',
        'sales_completion_rate' => 'decimal:2',
        'customer_completion_rate' => 'decimal:2',
        'overall_kpi_percentage' => 'decimal:2',
        'approved_at' => 'datetime'
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
            'draft' => 'Nháp',
            'submitted' => 'Đã gửi',
            'approved' => 'Đã duyệt',
            'rejected' => 'Từ chối',
            default => 'Không xác định'
        };
    }

    // Methods
    public function calculateCompletionRates()
    {
        // Tính tỷ lệ hoàn thành hợp đồng
        if ($this->target_contracts > 0) {
            $this->contract_completion_rate = ($this->actual_contracts / $this->target_contracts) * 100;
        }

        // Tính tỷ lệ hoàn thành doanh số
        if ($this->target_sales > 0) {
            $this->sales_completion_rate = ($this->actual_sales / $this->target_sales) * 100;
        }

        // Tính tỷ lệ hoàn thành khách hàng
        if ($this->target_customers > 0) {
            $this->customer_completion_rate = ($this->actual_customers / $this->target_customers) * 100;
        }

        // Tính KPI tổng thể (trung bình của 3 chỉ số)
        $this->overall_kpi_percentage = ($this->contract_completion_rate + $this->sales_completion_rate + $this->customer_completion_rate) / 3;

        return $this;
    }

    public function determineKpiStatus()
    {
        if ($this->overall_kpi_percentage >= 120) {
            $this->kpi_status = 'exceeded';
        } elseif ($this->overall_kpi_percentage >= 100) {
            $this->kpi_status = 'achieved';
        } elseif ($this->overall_kpi_percentage >= 80) {
            $this->kpi_status = 'near_achieved';
        } else {
            $this->kpi_status = 'not_achieved';
        }

        return $this;
    }
}
