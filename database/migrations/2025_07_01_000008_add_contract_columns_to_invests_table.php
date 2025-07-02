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
            if (!Schema::hasColumn('invests', 'contract_content')) {
                $table->text('contract_content')->nullable()->after('status');
            }
            
            if (!Schema::hasColumn('invests', 'contract_confirmed')) {
                $table->boolean('contract_confirmed')->default(false)->after('contract_content');
            }
            
            if (!Schema::hasColumn('invests', 'referral_code')) {
                $table->string('referral_code')->nullable()->after('contract_confirmed');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invests', function (Blueprint $table) {
            $columns = ['contract_content', 'contract_confirmed', 'referral_code'];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('invests', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}; 