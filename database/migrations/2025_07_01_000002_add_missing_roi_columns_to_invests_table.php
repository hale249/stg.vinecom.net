<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('invests', function (Blueprint $table) {
            if (!Schema::hasColumn('invests', 'roi_amount')) {
                $table->decimal('roi_amount', 28, 8)->default(0)->after('total_price');
            }
            
            if (!Schema::hasColumn('invests', 'roi_percentage')) {
                $table->decimal('roi_percentage', 28, 8)->default(0)->after('roi_amount');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invests', function (Blueprint $table) {
            if (Schema::hasColumn('invests', 'roi_amount')) {
                $table->dropColumn('roi_amount');
            }
            
            if (Schema::hasColumn('invests', 'roi_percentage')) {
                $table->dropColumn('roi_percentage');
            }
        });
    }
};