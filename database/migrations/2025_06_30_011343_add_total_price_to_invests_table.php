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
        Schema::table('invests', function (Blueprint $table) {
            $table->decimal('total_price', 28, 8)->default(0)->after('project_id');
            $table->tinyInteger('status')->default(0)->after('total_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invests', function (Blueprint $table) {
            $table->dropColumn('total_price');
            $table->dropColumn('status');
        });
    }
};
