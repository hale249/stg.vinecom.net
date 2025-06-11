<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\Status;

return new class extends Migration
{
    public function up()
    {
        Schema::table('invests', function (Blueprint $table) {
            $table->tinyInteger('payment_status')->default(Status::PAYMENT_PENDING)->change();
        });
    }

    public function down()
    {
        Schema::table('invests', function (Blueprint $table) {
            $table->tinyInteger('payment_status')->change();
        });
    }
}; 