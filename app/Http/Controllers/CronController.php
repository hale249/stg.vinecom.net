<?php

namespace App\Http\Controllers;

use App\Constants\Status;
use App\Lib\CurlRequest;
use App\Models\CronJob;
use App\Models\CronJobLog;
use App\Models\Invest;
use App\Models\Transaction;
use Carbon\Carbon;

class CronController extends Controller
{
    public function cron()
    {
        $general            = gs();
        $general->last_cron = now();
        $general->save();

        $crons = CronJob::with('schedule');

        if (request()->alias) {
            $crons->where('alias', request()->alias);
        } else {
            $crons->where('next_run', '<', now())->where('is_running', Status::YES);
        }
        $crons = $crons->get();
        foreach ($crons as $cron) {
            $cronLog              = new CronJobLog();
            $cronLog->cron_job_id = $cron->id;
            $cronLog->start_at    = now();
            if ($cron->is_default) {
                $controller = new $cron->action[0];
                try {
                    $method = $cron->action[1];
                    $controller->$method();
                } catch (\Exception $e) {
                    $cronLog->error = $e->getMessage();
                }
            } else {
                try {
                    CurlRequest::curlContent($cron->url);
                } catch (\Exception $e) {
                    $cronLog->error = $e->getMessage();
                }
            }
            $cron->last_run = now();
            $cron->next_run = now()->addSeconds($cron->schedule->interval);
            $cron->save();

            $cronLog->end_at = $cron->last_run;

            $startTime         = Carbon::parse($cronLog->start_at);
            $endTime           = Carbon::parse($cronLog->end_at);
            $diffInSeconds     = $startTime->diffInSeconds($endTime);
            $cronLog->duration = $diffInSeconds;
            $cronLog->save();
        }
        if (request()->target == 'all') {
            $notify[] = ['success', 'Cron executed successfully'];
            return back()->withNotify($notify);
        }
        if (request()->alias) {
            $notify[] = ['success', keyToTitle(request()->alias) . ' executed successfully'];
            return back()->withNotify($notify);
        }
    }

    public function interest()
    {
        try {
            $now = Carbon::now();
            $invests = Invest::with(['user', 'project.time'])
                ->whereHas('project')
                ->running()
                ->where('next_time', '<=', $now)
                ->orderBy('last_time')
                ->take(100)
                ->get();

            foreach ($invests as $invest) {

                $this->processInvestment($invest, $now);
            }
        } catch (\Throwable $th) {
            throw new \Exception($th->getMessage());
        }
    }

    private function processInvestment($invest, $now)
    {
        $user    = $invest->user;
        $hours   = (int)($invest->project?->time?->hours ?? 24);
        
        // Get the last payment date for reference
        $lastPaymentDate = $invest->last_time ? \Carbon\Carbon::parse($invest->last_time) : now();
        
        // Calculate the next payment date based on the interval
        $nextPaymentDate = clone $lastPaymentDate;
        
        if ($hours == 24 * 30) { // Monthly (approximately)
            // Set to same day next month from the last payment date
            $nextPaymentDate = $lastPaymentDate->copy()->addMonth();
        } elseif ($hours == 24 * 7) { // Weekly
            $nextPaymentDate = $lastPaymentDate->copy()->addWeek();
        } elseif ($hours == 24) { // Daily
            $nextPaymentDate = $lastPaymentDate->copy()->addDay();
        } else {
            // Custom interval in hours
            $nextPaymentDate = $lastPaymentDate->copy()->addHours($hours);
        }
        
        $next = $nextPaymentDate->toDateTimeString();

        // Tính toán lãi hàng tháng dựa trên tổng lãi và thời hạn
        $totalInvestment = $invest->total_price;
        $roiPercentage = $invest->roi_percentage;
        $projectDuration = $invest->project_duration > 0 ? $invest->project_duration : 12; // Mặc định 12 tháng nếu không có
        
        // Tính tổng lãi (annual ROI)
        $totalROI = ($totalInvestment * $roiPercentage / 100);
        
        // Tính lãi hàng tháng (chia đều cho số tháng)
        $monthlyROI = $totalROI / $projectDuration;

        // Process investment
        $invest->period += 1;
        $invest->paid += $monthlyROI;
        if ($invest->return_type == Status::LIFETIME) {
            $invest->total_earning += $monthlyROI;
        }
        $invest->next_time = $next;
        $invest->last_time = $now;

        // Update user's balance
        $user->balance += $monthlyROI;
        $user->save();

        // Log the transaction
        $trx = getTrx();
        $transaction = new Transaction();
        $transaction->user_id = $user->id;
        $transaction->invest_id = $invest->id;
        $transaction->amount = $monthlyROI;
        $transaction->charge = 0;
        $transaction->post_balance = $user->balance;
        $transaction->trx_type = '+';
        $transaction->trx = $trx;
        $transaction->remark = 'profit';
        $transaction->details = 'ROI payment for investment #' . $invest->invest_no;
        $transaction->save();

        // Check if the investment should be closed
        $this->checkInvestmentClosure($invest, $now);

        // Save the updated investment
        $invest->save();

        // Notify the user about the profit
        notify($user, 'INTEREST', [
            'trx' => $trx,
            'amount' => showAmount($monthlyROI),
            'project_name' => @$invest->project->title,
            'post_balance' => showAmount($user->balance),
        ]);
    }

    private function checkInvestmentClosure($invest, $now)
    {
        if ($invest->repeat_times == $invest->period) {
            $invest->status = Status::INVEST_COMPLETED;
            $this->capitalReturn($invest);
        }
    }

    private function capitalReturn($invest)
    {
        if ($invest->project->capital_back == Status::CAPITAL_BACK && $invest->capital_status == Status::NO) {
            $user           = $invest->user;
            $user->balance += $invest->total_price;
            $user->save();

            $invest->capital_status = Status::YES;
            $invest->save();

            $transaction               = new Transaction();
            $transaction->user_id      = $user->id;
            $transaction->invest_id    = $invest->id;
            $transaction->amount       = $invest->total_price;
            $transaction->charge       = 0;
            $transaction->post_balance = $user->balance;
            $transaction->trx_type     = '+';
            $transaction->trx          = getTrx();
            $transaction->remark       = 'capital_return';
            $transaction->details      = showAmount($invest->total_price) . ' capital back from ' . @$invest->project->title;
            $transaction->save();

            // Notify the user about the capital return
            notify($user, 'INVEST_COMPLETED', [
                'invest_id'    => $invest->invest_no,
                'trx'          => $transaction->trx,
                'amount'       => showAmount($invest->total_price),
                'project_name' => @$invest->project->title,
                'post_balance' => showAmount($user->balance),
            ]);
        }
    }
}
