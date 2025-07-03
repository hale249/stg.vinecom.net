<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KpiPolicy extends Model
{
    use HasFactory;

    protected $fillable = [
        'level_name',
        'role_level',
        'kpi_default',
        'kpi_month_1',
        'kpi_month_2',
        'kpi_tuyen_dung',
        'luong_bhxh',
        'luong_co_ban',
        'luong_kinh_doanh',
        'thuong_kinh_doanh',
        'hh_quan_ly',
        'hh_quan_ly_percent',
        'notes',
        'overall_kpi_percentage',
    ];

    protected $casts = [
        'kpi_default' => 'float',
        'kpi_month_1' => 'float',
        'kpi_month_2' => 'float',
        'luong_bhxh' => 'float',
        'luong_co_ban' => 'float',
        'luong_kinh_doanh' => 'float',
        'thuong_kinh_doanh' => 'float',
        'hh_quan_ly' => 'float',
        'hh_quan_ly_percent' => 'float',
        'overall_kpi_percentage' => 'float',
    ];
}
