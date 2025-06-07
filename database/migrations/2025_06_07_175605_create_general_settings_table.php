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
        Schema::create('general_settings', function (Blueprint $table) {
            $table->id();
            $table->string('site_name', 40)->nullable();
            $table->string('cur_text', 40)->nullable();
            $table->string('cur_sym', 40)->nullable();
            $table->string('email_from', 40)->nullable();
            $table->string('email_from_name', 255)->nullable();
            $table->text('email_template')->nullable();
            $table->string('sms_template', 255)->nullable();
            $table->string('sms_from', 255)->nullable();
            $table->string('push_title', 255)->nullable();
            $table->string('push_template', 255)->nullable();
            $table->string('base_color', 40)->nullable();
            $table->text('mail_config')->nullable();
            $table->text('sms_config')->nullable();
            $table->text('firebase_config')->nullable();
            $table->text('global_shortcodes')->nullable();
            $table->tinyInteger('kv')->default(0);
            $table->tinyInteger('ev')->default(0);
            $table->tinyInteger('en')->default(0);
            $table->tinyInteger('sv')->default(0);
            $table->tinyInteger('sn')->default(0);
            $table->tinyInteger('pn')->default(1);
            $table->tinyInteger('force_ssl')->default(0);
            $table->tinyInteger('maintenance_mode')->default(0);
            $table->tinyInteger('secure_password')->default(0);
            $table->tinyInteger('agree')->default(0);
            $table->tinyInteger('multi_language')->default(1);
            $table->tinyInteger('registration')->default(0);
            $table->string('active_template', 40)->nullable();
            $table->text('socialite_credentials')->nullable();
            $table->dateTime('last_cron')->nullable();
            $table->string('available_version', 40)->nullable();
            $table->tinyInteger('system_customized')->default(0);
            $table->integer('paginate_number')->default(0);
            $table->tinyInteger('currency_format')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('general_settings');
    }
};
