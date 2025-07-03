<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update all user referral codes from BHJ prefix to BHG prefix
        DB::table('users')
            ->where('referral_code', 'LIKE', 'BHJ%')
            ->update([
                'referral_code' => DB::raw("REPLACE(referral_code, 'BHJ', 'BHG')")
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back from BHG to BHJ
        DB::table('users')
            ->where('referral_code', 'LIKE', 'BHG%')
            ->update([
                'referral_code' => DB::raw("REPLACE(referral_code, 'BHG', 'BHJ')")
            ]);
    }
};
