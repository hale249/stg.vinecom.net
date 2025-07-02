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
        Schema::create('staff_salaries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('staff_id');
            $table->unsignedBigInteger('manager_id');
            $table->string('month_year', 7); // Format: YYYY-MM
            $table->decimal('base_salary', 15, 2)->default(0); // Lương cứng
            $table->decimal('sales_amount', 15, 2)->default(0); // Doanh số
            $table->decimal('commission_rate', 5, 2)->default(0); // Tỷ lệ hoa hồng (%)
            $table->decimal('commission_amount', 15, 2)->default(0); // Số tiền hoa hồng
            $table->decimal('bonus_amount', 15, 2)->default(0); // Thưởng
            $table->decimal('deduction_amount', 15, 2)->default(0); // Khấu trừ
            $table->decimal('total_salary', 15, 2)->default(0); // Tổng lương
            $table->decimal('kpi_percentage', 5, 2)->default(0); // Tỷ lệ hoàn thành KPI (%)
            $table->enum('kpi_status', ['exceeded', 'achieved', 'near_achieved', 'not_achieved'])->default('not_achieved');
            $table->text('notes')->nullable(); // Ghi chú
            $table->enum('status', ['pending', 'approved', 'paid', 'cancelled'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            
            $table->foreign('staff_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('manager_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['staff_id', 'month_year']);
            $table->index(['manager_id', 'month_year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_salaries');
    }
};
