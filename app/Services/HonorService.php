<?php

namespace App\Services;

use App\Models\Honor;
use Carbon\Carbon;

class HonorService
{
    /**
     * Get active honor
     * 
     * @return Honor|null
     */
    public function getActiveHonor()
    {
        $today = Carbon::today();

        // Get active honor
        $honor = Honor::where('is_active', true)
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->latest()
            ->first();

        return $honor;
    }
} 