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
            $table->timestamp('next_time')->nullable()->after('status');
            $table->tinyInteger('should_pay')->default(0)->after('next_time');
            $table->decimal('capital_back', 28, 8)->default(0)->after('should_pay');
            $table->decimal('interest', 28, 8)->default(0)->after('capital_back');
            $table->integer('period')->default(0)->after('interest');
            $table->decimal('profit', 28, 8)->default(0)->after('period');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invests', function (Blueprint $table) {
            $table->dropColumn('next_time');
            $table->dropColumn('should_pay');
            $table->dropColumn('capital_back');
            $table->dropColumn('interest');
            $table->dropColumn('period');
            $table->dropColumn('profit');
        });
    }
};
