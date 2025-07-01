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
            if (!Schema::hasColumn('invests', 'capital_back')) {
                $table->tinyInteger('capital_back')->default(0)->after('total_share');
            }
            
            if (!Schema::hasColumn('invests', 'capital_status')) {
                $table->tinyInteger('capital_status')->default(0)->after('capital_back');
            }
            
            if (!Schema::hasColumn('invests', 'return_type')) {
                $table->integer('return_type')->default(2)->after('capital_status');
            }
            
            if (!Schema::hasColumn('invests', 'project_duration')) {
                $table->integer('project_duration')->default(0)->after('return_type');
            }
            
            if (!Schema::hasColumn('invests', 'project_closed')) {
                $table->dateTime('project_closed')->nullable()->after('project_duration');
            }
            
            if (!Schema::hasColumn('invests', 'repeat_times')) {
                $table->integer('repeat_times')->default(0)->after('project_closed');
            }
            
            if (!Schema::hasColumn('invests', 'time_name')) {
                $table->string('time_name', 40)->nullable()->after('repeat_times');
            }
            
            if (!Schema::hasColumn('invests', 'hours')) {
                $table->integer('hours')->nullable()->after('time_name');
            }
            
            if (!Schema::hasColumn('invests', 'recurring_pay')) {
                $table->decimal('recurring_pay', 28, 8)->default(0)->after('hours');
            }
            
            if (!Schema::hasColumn('invests', 'status')) {
                $table->tinyInteger('status')->default(0)->after('recurring_pay');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invests', function (Blueprint $table) {
            $columns = [
                'capital_back', 'capital_status', 'return_type', 'project_duration',
                'project_closed', 'repeat_times', 'time_name', 'hours', 
                'recurring_pay', 'status'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('invests', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}; 