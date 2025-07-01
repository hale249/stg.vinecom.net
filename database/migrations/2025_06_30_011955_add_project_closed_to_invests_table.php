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
            $table->timestamp('project_closed')->nullable()->after('profit');
            $table->string('contract', 255)->nullable()->after('project_closed');
            $table->tinyInteger('contract_signed')->default(0)->after('contract');
            $table->string('referral_user_id', 255)->nullable()->after('contract_signed');
            $table->tinyInteger('payment_status')->default(0)->after('referral_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invests', function (Blueprint $table) {
            $table->dropColumn('project_closed');
            $table->dropColumn('contract');
            $table->dropColumn('contract_signed');
            $table->dropColumn('referral_user_id');
            $table->dropColumn('payment_status');
        });
    }
};
