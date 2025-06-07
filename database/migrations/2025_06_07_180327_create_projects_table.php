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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title', 40)->nullable();
            $table->string('slug', 255)->nullable();
            $table->decimal('goal', 28, 8)->default(0);
            $table->decimal('share_count', 28, 8)->default(0);
            $table->integer('available_share')->default(0);
            $table->decimal('share_amount', 28, 8)->default(0);
            $table->decimal('roi_percentage', 28, 8)->default(0);
            $table->decimal('roi_amount', 28, 8)->default(0);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('maturity_time')->default(0);
            $table->date('maturity_date')->nullable();
            $table->integer('time_id')->default(0);
            $table->integer('category_id')->default(0);
            $table->tinyInteger('return_type')->default(2);
            $table->integer('project_duration')->default(0);
            $table->integer('repeat_times')->default(0);
            $table->tinyInteger('capital_back')->default(0);
            $table->tinyInteger('status')->default(1);
            $table->string('image', 255)->nullable();
            $table->text('map_url')->nullable();
            $table->text('description')->nullable();
            $table->text('gallery')->nullable();
            $table->tinyInteger('featured')->default(0);
            $table->text('seo_content')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
