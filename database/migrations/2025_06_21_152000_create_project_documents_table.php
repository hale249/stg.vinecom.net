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
        Schema::create('project_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->string('title'); // Tên tài liệu
            $table->string('file_path'); // Đường dẫn file PDF
            $table->string('file_name'); // Tên file gốc
            $table->string('file_size')->nullable(); // Kích thước file
            $table->enum('type', ['legal', 'financial', 'technical', 'other'])->default('other'); // Loại tài liệu
            $table->text('description')->nullable(); // Mô tả tài liệu
            $table->boolean('is_public')->default(true); // Có hiển thị công khai không
            $table->integer('sort_order')->default(0); // Thứ tự hiển thị
            $table->timestamps();
            
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->index(['project_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_documents');
    }
}; 