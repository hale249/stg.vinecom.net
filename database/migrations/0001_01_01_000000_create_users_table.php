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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('firstname', 40)->nullable();
            $table->string('lastname', 40)->nullable();
            $table->string('username', 40)->nullable();
            $table->string('email', 40);
            $table->text('image')->nullable();
            $table->string('dial_code', 40)->nullable();
            $table->string('mobile', 40)->nullable();
            $table->decimal('balance', 28, 8)->default(0);
            $table->string('password', 255);
            $table->string('country_name', 255)->nullable();
            $table->string('country_code', 40)->nullable();
            $table->string('city', 255)->nullable();
            $table->string('state', 255)->nullable();
            $table->string('zip', 255)->nullable();
            $table->text('address')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->text('kyc_data')->nullable();
            $table->string('kyc_rejection_reason', 255)->nullable();
            $table->tinyInteger('kv')->default(0);
            $table->tinyInteger('ev')->default(0);
            $table->tinyInteger('sv')->default(0);
            $table->tinyInteger('profile_complete')->default(0);
            $table->string('ver_code', 40)->nullable();
            $table->dateTime('ver_code_send_at')->nullable();
            $table->tinyInteger('ts')->default(0);
            $table->tinyInteger('tv')->default(1);
            $table->string('tsc', 255)->nullable();
            $table->string('ban_reason', 255)->nullable();
            $table->string('remember_token', 255)->nullable();
            $table->string('provider', 40)->nullable();
            $table->string('provider_id', 255)->nullable();
            $table->timestamps();

            $table->unique(['username', 'email']);
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
