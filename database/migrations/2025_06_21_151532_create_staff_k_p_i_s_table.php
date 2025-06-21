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
        Schema::create('staff_k_p_i_s', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('staff_id');
            $table->unsignedBigInteger('manager_id');
            $table->string('month_year', 7); // Format: YYYY-MM
            $table->integer('target_contracts')->default(0); // Chỉ tiêu số hợp đồng
            $table->integer('actual_contracts')->default(0); // Thực tế số hợp đồng
            $table->decimal('target_sales', 15, 2)->default(0); // Chỉ tiêu doanh số
            $table->decimal('actual_sales', 15, 2)->default(0); // Thực tế doanh số
            $table->decimal('target_customers', 10, 0)->default(0); // Chỉ tiêu số khách hàng
            $table->integer('actual_customers')->default(0); // Thực tế số khách hàng
            $table->decimal('contract_completion_rate', 5, 2)->default(0); // Tỷ lệ hoàn thành hợp đồng (%)
            $table->decimal('sales_completion_rate', 5, 2)->default(0); // Tỷ lệ hoàn thành doanh số (%)
            $table->decimal('customer_completion_rate', 5, 2)->default(0); // Tỷ lệ hoàn thành khách hàng (%)
            $table->decimal('overall_kpi_percentage', 5, 2)->default(0); // Tỷ lệ KPI tổng thể (%)
            $table->enum('kpi_status', ['exceeded', 'achieved', 'near_achieved', 'not_achieved'])->default('not_achieved');
            $table->text('notes')->nullable(); // Ghi chú
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected'])->default('draft');
            $table->timestamp('approved_at')->nullable();
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
        Schema::dropIfExists('staff_k_p_i_s');
    }
};
