<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Invest;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;

class ManageInvestController extends Controller
{
    public function index()
    {
        $pageTitle = 'Manage Investments';
        $invests = Invest::latest()->searchable(['invest_no', 'project:title'])->paginate(getPaginate());

        return view('admin.invest.index', compact('pageTitle', 'invests'));
    }

    public function details($id)
    {
        $pageTitle = 'Invest Details';
        $invest = Invest::with('user', 'project')->findOrFail($id);
        
        // Calculate ROI profits based on investment date
        $this->calculateROIProfits($invest);
        
        $transactions = Transaction::where('invest_id', $invest->id)->orderBy('id', 'desc')->paginate(getPaginate());

        return view('admin.invest.details', compact('pageTitle', 'invest', 'transactions'));
    }
    
    /**
     * Calculate and update ROI profits based on investment date
     * This automatically displays profits on the specified date
     */
    private function calculateROIProfits($invest)
    {
        // Get current date
        $currentDate = Carbon::now();
        
        // Get investment start date
        $investmentDate = Carbon::parse($invest->created_at);
        
        // Calculate months since investment
        $monthsSinceInvestment = $investmentDate->diffInMonths($currentDate);
        
        // If project is running and at least one month has passed
        if ($invest->status == Status::INVEST_RUNNING && $monthsSinceInvestment > 0) {
            // Get ROI amount per period
            $roiAmount = $invest->roi_amount;
            $quantity = $invest->quantity > 0 ? $invest->quantity : 1;
            $periodROI = $roiAmount * $quantity;
            
            // Check if we need to create a new transaction for this month
            $currentMonthStart = Carbon::now()->startOfMonth();
            $lastMonthPayment = Transaction::where('invest_id', $invest->id)
                ->where('created_at', '>=', $currentMonthStart)
                ->where('details', 'like', '%ROI%')
                ->first();
            
            // If no payment has been made this month and today is the ROI payment date
            $roiDay = $investmentDate->day;
            if (!$lastMonthPayment && $currentDate->day == $roiDay) {
                // Create transaction for ROI payment
                $user = $invest->user;
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
            }
        }
    }

    public function investStatus($id)
    {
        $invest = Invest::where('status', Status::INVEST_PENDING)
            ->where('payment_status', Status::INVEST_PAYMENT_PENDING)
            ->findOrFail($id);

        $invest->status = Status::INVEST_CANCELED;
        $invest->payment_status = Status::PAYMENT_REJECT;
        $invest->save();

        notify($invest->user, 'INVEST_REJECTED', [
            'invest_id' => $invest->invest_no,
            'project_title' => $invest->project->title,
            'invest_amount' => showAmount($invest->total_price, currencyFormat: false),
            'quantity' => $invest->quantity,
        ]);

        $notify[] = ['success', 'Invest cancelled successfully.'];
        return back()->withNotify($notify);
    }


    public function running()
    {
        $pageTitle = 'Running Investments';
        $invests = $this->investData('running');

        return view('admin.invest.index', compact('pageTitle', 'invests'));
    }

    public function completed()
    {
        $pageTitle = 'Completed Investments';
        $invests = $this->investData('completed');

        return view('admin.invest.index', compact('pageTitle', 'invests'));
    }

    protected function investData($scope = null)
    {
        if ($scope) {
            $users = Invest::$scope();
        } else {
            $users = Invest::query();
        }
        return $users->searchable(['invest_id'])->orderBy('id', 'desc')->paginate(getPaginate());
    }

    public function stopReturns($id)
    {
        $invest = Invest::where('id', $id)
            ->where('status', Status::INVEST_RUNNING)
            ->whereHas('project', function ($query) {
                $query->where('return_type', Status::LIFETIME);
            })
            ->firstOrFail();

        $invest->status = Status::INVEST_CLOSED;
        $invest->save();

        // Refresh contract content
        refreshContractContent($invest);

        $notify[] = ['success', 'Returns have been stopped successfully.'];
        return back()->withNotify($notify);
    }

    public function startReturns($id)
    {
        $invest = Invest::where('id', $id)
            ->where('status', Status::INVEST_CLOSED)
            ->whereHas('project', function ($query) {
                $query->where('return_type', Status::LIFETIME);
            })
            ->firstOrFail();

        $invest->status = Status::INVEST_RUNNING;
        $invest->save();

        // Refresh contract content
        refreshContractContent($invest);

        $notify[] = ['success', 'Returns have been started successfully.'];
        return back()->withNotify($notify);
    }

    public function review()
    {
        $pageTitle = 'Review Investments';
        $invests = Invest::where('status', Status::INVEST_PENDING_ADMIN_REVIEW)
            ->with(['user', 'project'])
            ->latest()
            ->searchable(['invest_no', 'project:title'])
            ->paginate(getPaginate());

        return view('admin.invest.review', compact('pageTitle', 'invests'));
    }

    public function viewContract($id)
    {
        $pageTitle = 'Investment Contract';
        $invest = Invest::where('status', Status::INVEST_PENDING_ADMIN_REVIEW)
            ->with(['user', 'project'])
            ->findOrFail($id);

        return view('admin.invest.contract', compact('pageTitle', 'invest'));
    }

    public function approve($id)
    {
        $invest = Invest::where('status', Status::INVEST_PENDING_ADMIN_REVIEW)
            ->findOrFail($id);

        // Update investment status
        $invest->status = Status::INVEST_RUNNING;
        $invest->payment_status = Status::PAYMENT_SUCCESS;
        
        // Tự động thiết lập ngày thanh toán lãi tiếp theo (next_time)
        $startDate = now(); // Ngày bắt đầu hợp đồng là hiện tại
        
        // Nếu dự án có ngày bắt đầu cụ thể, sử dụng ngày đó
        if ($invest->project->start_date) {
            $startDate = \Carbon\Carbon::parse($invest->project->start_date);
            
            // Nếu ngày bắt đầu trong quá khứ, sử dụng ngày hiện tại
            if ($startDate->isPast()) {
                $startDate = now();
            }
        }
        
        // Tính toán ngày thanh toán tiếp theo dựa trên chu kỳ
        $paymentDate = $startDate->copy()->addMonth(); // Mặc định là hàng tháng
        
        // Nếu dự án có thiết lập thời gian cụ thể
        if ($invest->project->time && $invest->project->time->hours) {
            $interval = $invest->project->time->hours;
            if ($interval == 24 * 30) { // Hàng tháng (xấp xỉ)
                // Đặt vào cùng ngày tháng sau
                $paymentDate = $startDate->copy()->addMonth();
            } elseif ($interval == 24 * 7) { // Hàng tuần
                $paymentDate = $startDate->copy()->addWeek();
            } elseif ($interval == 24) { // Hàng ngày
                $paymentDate = $startDate->copy()->addDay();
            } else {
                // Khoảng thời gian tùy chỉnh theo giờ
                $paymentDate = $startDate->copy()->addHours($interval);
            }
        }
        
        $invest->next_time = $paymentDate;
        $invest->last_time = $startDate;
        $invest->save();

        // Refresh contract content to remove watermark
        refreshContractContent($invest);

        // Use PaymentController to handle the payment confirmation
        \App\Http\Controllers\Gateway\PaymentController::confirmOrder($invest);

        notify($invest->user, 'INVEST_APPROVED', [
            'invest_id' => $invest->invest_no,
            'project_title' => $invest->project->title,
            'invest_amount' => showAmount($invest->total_price),
            'quantity' => $invest->quantity,
        ]);

        $notify[] = ['success', 'Investment approved successfully.'];
        return back()->withNotify($notify);
    }

    public function reject($id)
    {
        $invest = Invest::where('status', Status::INVEST_PENDING_ADMIN_REVIEW)
            ->findOrFail($id);

        $invest->status = Status::INVEST_CANCELED;
        $invest->save();

        // Refresh contract content to keep watermark
        refreshContractContent($invest);

        notify($invest->user, 'INVEST_REJECTED', [
            'invest_id' => $invest->invest_no,
            'project_title' => $invest->project->title,
            'invest_amount' => showAmount($invest->total_price),
            'quantity' => $invest->quantity,
        ]);

        $notify[] = ['success', 'Investment rejected successfully.'];
        return back()->withNotify($notify);
    }
    
    /**
     * Process ROI payments for all running investments
     */
    public function processROI()
    {
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
                        }
                    }
                }
            } catch (\Exception $e) {
                \Log::error("Error processing ROI for investment #{$invest->id}: " . $e->getMessage());
            }
        }
        
        $notify[] = ['success', "Processed ROI payments for {$processedCount} investments."];
        return back()->withNotify($notify);
    }
    
    /**
     * Fix existing ROI transactions by setting the remark field to 'profit'
     */
    public function fixROITransactions()
    {
        // Find all ROI payment transactions
        $transactions = Transaction::where('details', 'like', '%ROI payment%')
            ->where(function($query) {
                $query->whereNull('remark')
                      ->orWhere('remark', '');
            })
            ->get();
            
        $count = 0;
        
        foreach ($transactions as $transaction) {
            $transaction->remark = 'profit';
            $transaction->save();
            $count++;
        }
        
        $notify[] = ['success', "Updated {$count} ROI transactions with proper remark field."];
        return back()->withNotify($notify);
    }

    /**
     * Recalculate ROI transactions to use the new formula
     */
    public function recalculateROITransactions()
    {
        // Find all ROI payment transactions
        $transactions = Transaction::where('details', 'like', '%ROI payment%')
            ->where('remark', 'profit')
            ->get();
            
        $count = 0;
        $totalAdjusted = 0;
        $userBalanceUpdates = [];
        
        foreach ($transactions as $transaction) {
            try {
                // Get the associated investment
                $invest = Invest::find($transaction->invest_id);
                
                if (!$invest || !$invest->project) {
                    continue;
                }
                
                // Get investment details
                $totalInvestment = $invest->total_price;
                $roiPercentage = $invest->roi_percentage;
                
                // Calculate annual ROI
                $annualROI = ($totalInvestment * $roiPercentage / 100);
                
                // Calculate monthly ROI (annual ROI divided by 12)
                $monthlyROI = $annualROI / 12;
                
                // Calculate the difference between old and new amount
                $oldAmount = $transaction->amount;
                $newAmount = $monthlyROI;
                $difference = $oldAmount - $newAmount;
                
                if (abs($difference) > 0.01) {
                    // Get user
                    $user = $invest->user;
                    
                    if ($user) {
                        // Track balance adjustment for this user
                        if (!isset($userBalanceUpdates[$user->id])) {
                            $userBalanceUpdates[$user->id] = 0;
                        }
                        $userBalanceUpdates[$user->id] += $difference;
                        
                        // Update transaction amount
                        $transaction->amount = $newAmount;
                        $transaction->post_balance = $transaction->post_balance - $difference;
                        $transaction->save();
                        
                        // Update total earnings for the investment
                        $invest->total_earning -= $difference;
                        $invest->save();
                        
                        $count++;
                        $totalAdjusted += $difference;
                    }
                }
            } catch (\Exception $e) {
                \Log::error("Error recalculating ROI for transaction #{$transaction->id}: " . $e->getMessage());
            }
        }
        
        // Update user balances after all transactions are recalculated
        foreach ($userBalanceUpdates as $userId => $adjustment) {
            try {
                $user = User::find($userId);
                if ($user) {
                    $user->balance -= $adjustment;
                    $user->save();
                    \Log::info("Updated user #{$userId} balance by -" . $adjustment);
                }
            } catch (\Exception $e) {
                \Log::error("Error updating balance for user #{$userId}: " . $e->getMessage());
            }
        }
        
        $notify[] = ['success', "Recalculated {$count} ROI transactions with the new formula. Total adjusted: " . showAmount($totalAdjusted)];
        return back()->withNotify($notify);
    }

    /**
     * Fix user balances based on their actual transactions
     */
    public function fixUserBalances()
    {
        // Get all users with investments
        $users = User::whereHas('invests')->get();
        $fixedCount = 0;
        
        foreach ($users as $user) {
            try {
                // For the balance, we only want to show profits received
                // Get all profit transactions
                $profits = Transaction::where('user_id', $user->id)
                    ->where('trx_type', '+')
                    ->where('remark', 'profit')
                    ->sum('amount');
                
                // Set the balance to be equal to the profits received
                $correctBalance = $profits;
                
                // If there's a difference, update the user's balance
                if (abs($user->balance - $correctBalance) > 0.01) {
                    $oldBalance = $user->balance;
                    $user->balance = $correctBalance;
                    $user->save();
                    
                    \Log::info("Fixed user #{$user->id} balance from {$oldBalance} to {$correctBalance}");
                    $fixedCount++;
                }
            } catch (\Exception $e) {
                \Log::error("Error fixing balance for user #{$user->id}: " . $e->getMessage());
            }
        }
        
        $notify[] = ['success', "Fixed balances for {$fixedCount} users to show only profits received."];
        return back()->withNotify($notify);
    }
}
