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
            if (!Schema::hasColumn('invests', 'total_earning')) {
                $table->decimal('total_earning', 28, 8)->default(0)->after('payment_status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invests', function (Blueprint $table) {
            if (Schema::hasColumn('invests', 'total_earning')) {
                $table->dropColumn('total_earning');
            }
        });
    }
}; 