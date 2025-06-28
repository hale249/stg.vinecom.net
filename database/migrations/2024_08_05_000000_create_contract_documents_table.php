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
        // First, temporarily create the invests table if it doesn't exist
        if (!Schema::hasTable('invests')) {
            Schema::create('invests', function (Blueprint $table) {
                $table->id();
                $table->string('invest_no', 255)->nullable();
                $table->unsignedInteger('user_id')->default(0);
                $table->unsignedInteger('project_id')->default(0);
                $table->timestamps();
            });
        }

        Schema::create('contract_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invest_id');
            $table->string('title');
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_size')->nullable();
            $table->enum('type', ['signed_contract', 'transfer_bill', 'other'])->default('other');
            $table->text('description')->nullable();
            $table->timestamps();
            
            $table->foreign('invest_id')->references('id')->on('invests')->onDelete('cascade');
            $table->index('invest_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contract_documents');
    }
}; 