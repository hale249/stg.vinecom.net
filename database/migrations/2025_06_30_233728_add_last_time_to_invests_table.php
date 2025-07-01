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
            // Kiểm tra xem cột last_time đã tồn tại chưa
            if (!Schema::hasColumn('invests', 'last_time')) {
                $table->dateTime('last_time')->nullable()->after('next_time');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invests', function (Blueprint $table) {
            if (Schema::hasColumn('invests', 'last_time')) {
                $table->dropColumn('last_time');
            }
        });
    }
};
