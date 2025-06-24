<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffAttendance extends Model
{
    protected $fillable = [
        'staff_id',
        'employee_code',
        'date',
        'working_day',
        'note',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'date' => 'date',
        'working_day' => 'float',
    ];

    // Relationships
    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scopes
    public function scopeByStaff($query, $staffId)
    {
        return $query->where('staff_id', $staffId);
    }

    public function scopeByEmployeeCode($query, $employeeCode)
    {
        return $query->where('employee_code', $employeeCode);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    public function scopeByMonth($query, $year, $month)
    {
        $startDate = "{$year}-{$month}-01";
        $endDate = date('Y-m-t', strtotime($startDate));
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    // Get total working days for a period
    public function scopeTotalWorkingDays($query)
    {
        return $query->sum('working_day');
    }
}
