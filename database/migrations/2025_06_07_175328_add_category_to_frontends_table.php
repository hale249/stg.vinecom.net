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
        Schema::table('frontends', function (Blueprint $table) {
            $table->string('category', 50)->nullable()->after('data_keys')->comment('For blog categorization: company_news, market_news, etc.');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('frontends', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }
}; 