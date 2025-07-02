<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('projects')) {
            return;
        }
        
        Schema::table('projects', function (Blueprint $table) {
            $table->text('contract_content')->nullable();
        });
    }

    public function down()
    {
        if (!Schema::hasTable('projects')) {
            return;
        }
        
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('contract_content');
        });
    }
}; 