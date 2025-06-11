<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->date('date_of_birth')->nullable();
            $table->string('id_number', 50)->nullable();
            $table->date('id_issue_date')->nullable();
            $table->string('id_issue_place', 255)->nullable();
            $table->string('bank_account_number', 50)->nullable();
            $table->string('bank_name', 255)->nullable();
            $table->string('bank_branch', 255)->nullable();
            $table->string('bank_account_holder', 255)->nullable();
            $table->string('tax_number', 50)->nullable();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'date_of_birth',
                'id_number',
                'id_issue_date',
                'id_issue_place',
                'bank_account_number',
                'bank_name',
                'bank_branch',
                'bank_account_holder',
                'tax_number'
            ]);
        });
    }
}; 