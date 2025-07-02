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
        if (!Schema::hasTable('invests')) {
            // If the invests table doesn't exist yet, we'll skip this migration
            // It will be created by the create_invests_table migration
            return;
        }

        Schema::table('invests', function (Blueprint $table) {
            $table->foreignId('staff_id')->nullable()->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('invests')) {
            return;
        }
        
        Schema::table('invests', function (Blueprint $table) {
            $table->dropColumn('staff_id');
        });
    }
};
