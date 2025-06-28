<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('invests')) {
            // If the invests table doesn't exist yet, we'll skip this migration
            // It will be created by the create_invests_table migration
            return;
        }

        Schema::table('invests', function (Blueprint $table) {
            $table->text('contract_content')->nullable()->after('invest_no');
            $table->string('referral_code')->nullable()->after('contract_content');
            $table->boolean('contract_confirmed')->default(false)->after('referral_code');
        });
    }

    public function down()
    {
        if (!Schema::hasTable('invests')) {
            return;
        }

        Schema::table('invests', function (Blueprint $table) {
            $table->dropColumn(['contract_content', 'referral_code', 'contract_confirmed']);
        });
    }
}; 