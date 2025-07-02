<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Invest;
use Carbon\Carbon;
use App\Constants\Status;

class FixInvestmentDates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invest:fix-dates {--force : Force update all dates regardless of differences}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix investment dates to properly calculate next payment dates';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to fix investment dates...');
        $forceUpdate = $this->option('force');
        
        $investments = Invest::with('project.time')
            ->whereHas('project')
            ->where('status', Status::INVEST_RUNNING)
            ->get();
            
        $this->info("Found {$investments->count()} active investments to process.");
        
        $count = 0;
        foreach ($investments as $invest) {
            $project = $invest->project;
            
            // Get the contract start date - use creation date or project start date, whichever is later
            $startDate = $invest->created_at;
            
            if ($project && $project->start_date) {
                $projectStartDate = Carbon::parse($project->start_date);
                // Use project start date only if it's after the investment was created
                if ($projectStartDate->isAfter($startDate)) {
                    $startDate = $startDate;
                }
            }
            
            // If we have a last_time set and it's after the start date, use it as reference
            if ($invest->last_time && Carbon::parse($invest->last_time)->isAfter($startDate)) {
                $lastPaymentDate = Carbon::parse($invest->last_time);
            } else {
                // No valid last_time, use start date
                $lastPaymentDate = clone $startDate;
            }
            
            // Get time interval from project
            $hours = (int)($project->time->hours ?? 24 * 30);
            
            // Calculate the next payment date from the last payment
            $nextPaymentDate = clone $lastPaymentDate;
            
            if ($hours == 24 * 30) { // Monthly
                $nextPaymentDate = $lastPaymentDate->copy()->addMonth();
            } elseif ($hours == 24 * 7) { // Weekly
                $nextPaymentDate = $lastPaymentDate->copy()->addWeek();
            } elseif ($hours == 24) { // Daily
                $nextPaymentDate = $lastPaymentDate->copy()->addDay();
            } else {
                $nextPaymentDate = $lastPaymentDate->copy()->addHours($hours);
            }
            
            // Check if update is needed
            $needsUpdate = $forceUpdate;
            
            if (!$invest->next_time || 
                !$nextPaymentDate->isSameDay(Carbon::parse($invest->next_time)) ||
                Carbon::parse($invest->next_time)->format('Y-m-d') == '2027-11-27') { // specifically fix maturity date issue
                $needsUpdate = true;
            }
            
            if ($needsUpdate) {
                $this->info("Updating investment #{$invest->id} ({$invest->invest_no}):");
                $this->info("  Old next_time: " . ($invest->next_time ? Carbon::parse($invest->next_time)->format('Y-m-d H:i:s') : 'None'));
                $this->info("  New next_time: " . $nextPaymentDate->format('Y-m-d H:i:s'));
                
                $invest->next_time = $nextPaymentDate;
                
                // Only set last_time if it wasn't already set
                if (!$invest->last_time) {
                    $invest->last_time = $lastPaymentDate;
                }
                
                $invest->save();
                $count++;
            }
        }
        
        $this->info("Fixed dates for {$count} investments.");
        return Command::SUCCESS;
    }
} 