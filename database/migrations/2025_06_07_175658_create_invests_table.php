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
        if (Schema::hasTable('invests')) {
            return; // Table already created by earlier migration
        }
        
        Schema::create('invests', function (Blueprint $table) {
            $table->id();
            $table->string('invest_no', 255)->nullable();
            $table->unsignedInteger('user_id')->default(0);
            $table->unsignedInteger('project_id')->default(0);
            $table->integer('quantity')->default(0);
            $table->decimal('unit_price', 28, 8)->default(0);
            $table->decimal('total_price', 28, 8)->default(0);
            $table->decimal('total_earning', 28, 8)->default(0);
            $table->integer('payment_type')->default(1);
            $table->integer('payment_status')->default(2);
            $table->decimal('roi_amount', 28, 8)->default(0);
            $table->decimal('roi_percentage', 28, 8)->default(0);
            $table->decimal('total_share', 28, 8)->default(0);
            $table->tinyInteger('capital_back')->default(0);
            $table->tinyInteger('capital_status')->default(0);
            $table->integer('return_type')->default(2);
            $table->integer('repeat_times')->default(0);
            $table->integer('return_interval')->default(0);
            $table->integer('project_duration')->default(0);
            $table->string('time_name', 40)->nullable();
            $table->integer('hours')->nullable();
            $table->dateTime('next_time')->nullable();
            $table->dateTime('last_time')->nullable();
            $table->date('project_closed')->nullable();
            $table->decimal('recurring_pay', 28, 8)->default(0);
            $table->integer('period')->default(0);
            $table->decimal('paid', 28, 8)->default(0);
            $table->tinyInteger('return_status')->default(1);
            $table->tinyInteger('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invests');
    }
};
