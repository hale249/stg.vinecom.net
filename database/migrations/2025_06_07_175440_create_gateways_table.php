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
        Schema::create('gateways', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('form_id')->default(0);
            $table->integer('code')->unique()->nullable();
            $table->string('name', 40)->nullable();
            $table->string('alias', 40)->default('NULL');
            $table->string('image', 255)->nullable();
            $table->tinyInteger('status')->default(1);
            $table->text('gateway_parameters')->nullable();
            $table->text('supported_currencies')->nullable();
            $table->tinyInteger('crypto')->default(0);
            $table->text('extra')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gateways');
    }
};
