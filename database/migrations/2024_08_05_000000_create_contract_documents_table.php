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