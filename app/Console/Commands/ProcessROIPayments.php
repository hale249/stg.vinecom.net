<?php

namespace App\Console\Commands;

use App\Constants\Status;
use App\Models\Invest;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcessROIPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'roi:process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process ROI payments for all running investments';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting ROI payment processing...');
        
        // Get all running investments
        $runningInvestments = Invest::where('status', Status::INVEST_RUNNING)->get();
        
        $processedCount = 0;
        
        foreach ($runningInvestments as $invest) {
            try {
                // Get current date
                $currentDate = Carbon::now();
                
                // Get investment start date
                $investmentDate = Carbon::parse($invest->created_at);
                
                // Calculate months since investment
                $monthsSinceInvestment = $investmentDate->diffInMonths($currentDate);
                
                // If at least one month has passed
                if ($monthsSinceInvestment > 0) {
                    // Get investment details
                    $totalInvestment = $invest->total_price;
                    $roiPercentage = $invest->roi_percentage;
                    
                    // Calculate annual ROI
                    $annualROI = ($totalInvestment * $roiPercentage / 100);
                    
                    // Calculate monthly ROI (annual ROI divided by 12)
                    $monthlyROI = $annualROI / 12;
                    
                    // For each payment, we pay one month's worth of ROI
                    $periodROI = $monthlyROI;
                    
                    // Check if we need to create a new transaction for this month
                    $currentMonthStart = Carbon::now()->startOfMonth();
                    $lastMonthPayment = Transaction::where('invest_id', $invest->id)
                        ->where('created_at', '>=', $currentMonthStart)
                        ->where('details', 'like', '%ROI%')
                        ->first();
                    
                    // If no payment has been made this month and today is the ROI payment date
                    $roiDay = $investmentDate->day;
                    if (!$lastMonthPayment && $currentDate->day == $roiDay) {
                        // Get user
                        $user = User::find($invest->user_id);
                        
                        if ($user) {
                            // Create transaction for ROI payment
                            $transaction = new Transaction();
                            $transaction->user_id = $user->id;
                            $transaction->invest_id = $invest->id;
                            $transaction->amount = $periodROI;
                            $transaction->charge = 0;
                            $transaction->post_balance = $user->balance + $periodROI;
                            $transaction->trx_type = '+';
                            $transaction->trx = getTrx();
                            $transaction->remark = 'profit';
                            $transaction->details = 'ROI payment for investment #' . $invest->invest_no;
                            $transaction->save();
                            
                            // Update user balance
                            $user->balance += $periodROI;
                            $user->save();
                            
                            // Update total earnings for the investment
                            $invest->total_earning += $periodROI;
                            $invest->save();
                            
                            $processedCount++;
                            
                            // Send notification to user
                            notify($user, 'ROI_PAYMENT', [
                                'invest_id' => $invest->invest_no,
                                'amount' => showAmount($periodROI),
                                'project_title' => $invest->project->title ?? 'Investment',
                                'post_balance' => showAmount($user->balance),
                            ]);
                            
                            $this->info("Processed ROI payment of {$periodROI} for investment #{$invest->invest_no}");
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::error("Error processing ROI for investment #{$invest->id}: " . $e->getMessage());
                $this->error("Error processing ROI for investment #{$invest->id}: " . $e->getMessage());
            }
        }
        
        $this->info("Completed ROI payment processing. Processed {$processedCount} payments.");
        
        return 0;
    }
} 