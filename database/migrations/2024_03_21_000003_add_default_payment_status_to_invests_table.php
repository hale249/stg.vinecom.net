<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\Status;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('invests')) {
            // If the invests table doesn't exist yet, we'll skip this migration
            // It will be created by the create_invests_table migration
            return;
        }
        
        Schema::table('invests', function (Blueprint $table) {
            $table->tinyInteger('payment_status')->default(Status::PAYMENT_PENDING)->change();
        });
    }

    public function down()
    {
        if (!Schema::hasTable('invests')) {
            return;
        }
        
        Schema::table('invests', function (Blueprint $table) {
            $table->tinyInteger('payment_status')->change();
        });
    }
}; 