<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('honors')) {
            return;
        }

        try {
            // Try to drop the foreign key constraint if it exists
            Schema::table('honors', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
            });
        } catch (\Exception $e) {
            // Foreign key constraint doesn't exist, continue
        }

        Schema::table('honors', function (Blueprint $table) {
            if (Schema::hasColumn('honors', 'user_id')) {
                $table->dropColumn('user_id');
            }
            
            if (Schema::hasColumn('honors', 'role')) {
                $table->dropColumn('role');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('honors')) {
            return;
        }
        
        Schema::table('honors', function (Blueprint $table) {
            if (!Schema::hasColumn('honors', 'user_id')) {
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            }
            
            if (!Schema::hasColumn('honors', 'role')) {
                $table->string('role')->nullable()->comment('User role: sales_manager, sales_staff, or null for all users');
            }
        });
    }
};
