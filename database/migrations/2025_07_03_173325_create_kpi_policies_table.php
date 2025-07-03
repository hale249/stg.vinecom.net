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
        Schema::create('kpi_policies', function (Blueprint $table) {
            $table->id();
            $table->string('level_name');
            $table->string('role_level');
            $table->decimal('kpi_default', 20, 2);
            $table->decimal('kpi_month_1', 20, 2);
            $table->decimal('kpi_month_2', 20, 2);
            $table->string('kpi_tuyen_dung')->nullable();
            $table->decimal('luong_bhxh', 20, 2);
            $table->decimal('luong_co_ban', 20, 2);
            $table->decimal('luong_kinh_doanh', 20, 2);
            $table->decimal('thuong_kinh_doanh', 20, 2)->nullable();
            $table->decimal('hh_quan_ly', 20, 2)->nullable();
            $table->decimal('hh_quan_ly_percent', 8, 2);
            $table->text('notes')->nullable();
            $table->decimal('overall_kpi_percentage', 8, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kpi_policies');
    }
};
