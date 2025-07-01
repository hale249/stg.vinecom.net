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
            if (!Schema::hasColumn('invests', 'quantity')) {
                $table->integer('quantity')->default(1)->after('project_id');
            }
            
            if (!Schema::hasColumn('invests', 'unit_price')) {
                $table->decimal('unit_price', 28, 8)->default(0)->after('quantity');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invests', function (Blueprint $table) {
            if (Schema::hasColumn('invests', 'quantity')) {
                $table->dropColumn('quantity');
            }
            
            if (Schema::hasColumn('invests', 'unit_price')) {
                $table->dropColumn('unit_price');
            }
        });
    }
}; 