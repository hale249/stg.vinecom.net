<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Invest;
use App\Models\Transaction;
use Illuminate\Http\Request;

class InvestReportController extends Controller
{
    public function index()
    {
        $pageTitle = 'Investment Statistics';

        $widget['total_invest'] = Transaction::where('remark', 'payment', 'wallet_payment')->sum('amount');
        $widget['profit_to_give'] = Invest::where('status', Status::INVEST_RUNNING)->where('period', '>', 0)->sum('recurring_pay');
        $widget['profit_paid'] = Invest::where('status', Status::INVEST_RUNNING)->where('period', '>', 0)->sum('total_earning');

        $interestByProjects = Invest::where('period', '>', 0)
            ->selectRaw("SUM(total_earning) as total_price, project_id, MAX(total_earning) as max_paid")
            ->with('project')
            ->groupBy('project_id')
            ->orderBy('max_paid', 'desc')
            ->get();
        $totalInterest = $interestByProjects->sum('total_price');

        $interestByProjects = $interestByProjects->mapWithKeys(function ($invest) {
            return [
                $invest->project->title => (float)$invest->total_price,
            ];
        });

        $recentInvests = Invest::with('project')->orderBy('id', 'desc')->limit(3)->get();
        $firstInvestYear = Invest::selectRaw("DATE_FORMAT(created_at, '%Y') as date")->first();

        return view('admin.reports.invest_statistics', compact('pageTitle', 'widget', 'interestByProjects', 'recentInvests', 'totalInterest', 'firstInvestYear'));
    }

    public function investStatistics(Request $request)
    {
        $months = [];
        $investCounts = [];
        $investAmounts = [];

        // Check if start_date and end_date are provided
        if ($request->has('start_date') && $request->has('end_date')) {
            $startDate = \Carbon\Carbon::parse($request->start_date);
            $endDate = \Carbon\Carbon::parse($request->end_date);

            // Check if it's the same date (single day selection)
            $isSameDate = $startDate->isSameDay($endDate);

            if ($isSameDate) {
                // Single day selection - show data for that specific day
                $dayStart = $startDate->copy()->startOfDay();
                $dayEnd = $startDate->copy()->endOfDay();

                // Format day for display
                $months[] = $startDate->format('d/m/Y');

                // Count investments on this specific day
                $count = Invest::whereBetween('created_at', [$dayStart, $dayEnd])->count();
                $investCounts[] = $count;

                // Sum investment amounts on this specific day
                $amount = Invest::where('status', Status::INVEST_RUNNING)
                            ->whereBetween('created_at', [$dayStart, $dayEnd])
                            ->sum('total_price');
                $investAmounts[] = (float) $amount;

            } else {
                // Date range selection
                $daysDiff = $startDate->diffInDays($endDate);

                if ($daysDiff <= 31) {
                    // If range is 31 days or less, show daily data
                    $currentDate = $startDate->copy();
                    while ($currentDate <= $endDate) {
                        $dayStart = $currentDate->copy()->startOfDay();
                        $dayEnd = $currentDate->copy()->endOfDay();

                        // Format day for display
                        $months[] = $currentDate->format('d/m');

                        // Count investments on this day
                        $count = Invest::whereBetween('created_at', [$dayStart, $dayEnd])->count();
                        $investCounts[] = $count;

                        // Sum investment amounts on this day
                        $amount = Invest::where('status', Status::INVEST_RUNNING)
                                    ->whereBetween('created_at', [$dayStart, $dayEnd])
                                    ->sum('total_price');
                        $investAmounts[] = (float) $amount;

                        // Move to next day
                        $currentDate->addDay();
                    }
                } else {
                    // If range is more than 31 days, show monthly data
                    $monthDiff = $startDate->diffInMonths($endDate);
                    if ($monthDiff > 12) {
                        // If more than 12 months, limit to last 12 months from end date
                        $startDate = $endDate->copy()->subMonths(11)->startOfMonth();
                    }

                    // Generate data for each month in the selected range
                    $currentDate = $startDate->copy()->startOfMonth();
                    while ($currentDate <= $endDate) {
                        $monthStart = $currentDate->copy()->startOfMonth();
                        $monthEnd = $currentDate->copy()->endOfMonth();

                        // Don't go beyond the selected end date
                        if ($monthEnd > $endDate) {
                            $monthEnd = $endDate->copy()->endOfDay();
                        }

                        // Format month for display
                        $months[] = $currentDate->format('M Y');

                        // Count investments in this month
                        $count = Invest::whereBetween('created_at', [$monthStart, $monthEnd])->count();
                        $investCounts[] = $count;

                        // Sum investment amounts in this month
                        $amount = Invest::where('status', Status::INVEST_RUNNING)
                                    ->whereBetween('created_at', [$monthStart, $monthEnd])
                                    ->sum('total_price');
                        $investAmounts[] = (float) $amount;

                        // Move to next month
                        $currentDate->addMonth();
                    }
                }
            }
        } else {
            // Default behavior - last 12 months
            for ($i = 11; $i >= 0; $i--) {
                $startDate = now()->subMonths($i)->startOfMonth();
                $endDate = now()->subMonths($i)->endOfMonth();

                // Format month for display
                $months[] = now()->subMonths($i)->format('M Y');

                // Count investments in this month
                $count = Invest::whereBetween('created_at', [$startDate, $endDate])->count();
                $investCounts[] = $count;

                // Sum investment amounts in this month
                $amount = Invest::where('status', Status::INVEST_RUNNING)
                            ->whereBetween('created_at', [$startDate, $endDate])
                            ->sum('total_price');
                $investAmounts[] = (float) $amount;
            }
        }

        return [
            'months' => $months,
            'invest_counts' => $investCounts,
            'invest_amounts' => $investAmounts,
            // Keep original data for backwards compatibility
            'invests' => [],
            'total_invest' => array_sum($investAmounts),
            'invest_diff' => 0,
            'up_down' => 'up',
        ];
    }

    public function investStatisticsByProject(Request $request)
    {
        if ($request->time == 'year') {
            $time = now()->startOfYear();
        } elseif ($request->time == 'month') {
            $time = now()->startOfMonth();
        } elseif ($request->time == 'week') {
            $time = now()->startOfWeek();
        } else {
            $time = date('0000-00-00 00:00:00');
        }

        $investChart = Invest::with('project')->where('created_at', '>=', $time)->groupBy('project_id')->selectRaw("SUM(total_price) as investAmount, project_id")->orderBy('investAmount', 'desc');
        if ($request->invest_type == 'active') {
            $investChart = $investChart->where('status', Status::INVEST_RUNNING);
        } elseif ($request->invest_type == 'closed') {
            $investChart = $investChart->where('status', Status::INVEST_CLOSED);
        }

        $investChart = $investChart->get();

        return [
            'invest_data' => $investChart,
            'total_invest' => $investChart->sum('investAmount'),
        ];
    }

    public function investInterestStatistics(Request $request)
    {
        if ($request->time == 'year') {
            $time = now()->startOfYear();
        } elseif ($request->time == 'month') {
            $time = now()->startOfMonth();
        } elseif ($request->time == 'week') {
            $time = now()->startOfWeek();
        } else {
            $time = date('0000-00-00 00:00:00');
        }

        $runningInvests = Invest::where('status', Status::INVEST_RUNNING)->where('created_at', '>=', $time)->sum('total_price');
        $expiredInvests = Invest::where('status', Status::INVEST_COMPLETED)->where('created_at', '>=', $time)->sum('total_price');
        $interests = Transaction::where('remark', 'profit')->where('created_at', '>=', $time)->sum('amount');

        return [
            'running_invests' => showAmount($runningInvests),
            'completed_invests' => showAmount($expiredInvests),
            'interests' => showAmount($interests),
        ];
    }

    public function investInterestChart(Request $request)
    {
        $invests = Invest::whereYear('created_at', $request->year)->whereMonth('created_at', $request->month)->selectRaw("SUM(total_price) as total_price, DATE_FORMAT(created_at, '%d') as date")->groupBy('date')->get();

        $investsDate = $invests->map(function ($invest) {
            return $invest->date;
        })->toArray();

        $interests = Transaction::whereYear('created_at', $request->year)->whereMonth('created_at', $request->month)->where('remark', 'profit')->selectRaw("SUM(amount) as amount, DATE_FORMAT(created_at, '%d') as date")->groupBy('date')->get();

        $interestsDate = $interests->map(function ($interest) {
            return $interest->date;
        })->toArray();

        $dataDates = array_unique(array_merge($investsDate, $interestsDate));
        $investsData = [];
        $interestsData = [];
        foreach ($dataDates as $date) {
            $investsData[] = @$invests->where('date', $date)->first()->total_price ?? 0;
            $interestsData[] = @$interests->where('date', $date)->first()->total_price ?? 0;
        }

        return [
            'keys' => array_values($dataDates),
            'invests' => $investsData,
            'interests' => $interestsData,
        ];
    }
}
