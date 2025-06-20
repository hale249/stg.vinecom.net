<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Invest;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AlertDashboardController extends Controller
{
    /**
     * Display the alert dashboard
     */
    public function index(Request $request)
    {
        $pageTitle = 'Interest & Maturity Alerts Dashboard';
        
        // Get filter parameters
        $projectId = $request->project_id;
        $dateRange = $request->date_range ?? 'all';
        $contractStatus = $request->contract_status ?? 'active';
        $alertPeriod = gs()->alert_period ?? 60; // Lấy từ database, mặc định 60 nếu chưa có
        
        // Build query for contracts
        $investsQuery = Invest::with(['project', 'user'])
            ->where('status', Status::INVEST_RUNNING);
            
        // Apply project filter if provided
        if ($projectId) {
            $investsQuery->where('project_id', $projectId);
        }
        
        // Apply date range filter
        $currentDate = Carbon::now();
        $endDate = null;
        
        if ($dateRange == '30days') {
            $endDate = $currentDate->copy()->addDays(30);
        } elseif ($dateRange == '60days') {
            $endDate = $currentDate->copy()->addDays(60);
        } elseif ($dateRange == '90days') {
            $endDate = $currentDate->copy()->addDays(90);
        } elseif ($dateRange == 'custom' && $request->start_date && $request->end_date) {
            $endDate = Carbon::parse($request->end_date);
        }
        
        // Get all invests for chart data
        $invests = $investsQuery->get();
        
        // Calculate interest payments and maturity alerts
        $monthlyData = $this->calculateMonthlyAlerts($invests, $alertPeriod);
        
        // Get all active projects for filter dropdown
        $projects = Project::where('status', Status::PROJECT_CONFIRMED)->get();
        
        // Get summary stats
        $stats = $this->getSummaryStats($invests);
        
        return view('admin.dashboard.alerts', compact('pageTitle', 'projects', 'monthlyData', 'stats', 'alertPeriod'));
    }
    
    /**
     * Calculate monthly interest and maturity alerts
     */
    private function calculateMonthlyAlerts($invests, $alertPeriod)
    {
        $currentDate = Carbon::now();
        $sixMonthsLater = $currentDate->copy()->addMonths(6);
        $monthlyData = [];
        
        // Initialize the months
        for ($date = $currentDate->copy()->startOfMonth(); $date->lte($sixMonthsLater); $date->addMonth()) {
            $monthKey = $date->format('Y-m');
            $monthlyData[$monthKey] = [
                'month' => $date->format('M Y'),
                'interest_alerts' => 0,
                'maturity_alerts' => 0,
                'interest_contracts' => [],
                'maturity_contracts' => [],
                'is_urgent' => false
            ];
        }
        
        // Process each investment
        foreach ($invests as $invest) {
            // Check if the project has maturity date
            if ($invest->project_closed) {
                $maturityDate = Carbon::parse($invest->project_closed);
                
                // If maturity date is within the next 6 months
                if ($maturityDate->between($currentDate, $sixMonthsLater)) {
                    $monthKey = $maturityDate->format('Y-m');
                    
                    if (isset($monthlyData[$monthKey])) {
                        $monthlyData[$monthKey]['maturity_alerts']++;
                        $monthlyData[$monthKey]['maturity_contracts'][] = [
                            'id' => $invest->id,
                            'invest_no' => $invest->invest_no,
                            'project_name' => $invest->project->title,
                            'maturity_date' => $maturityDate->format('Y-m-d'),
                            'days_remaining' => (int) round($currentDate->diffInDays($maturityDate)),
                            'amount' => $invest->total_price
                        ];
                        
                        // Check if it's urgent (less than alertPeriod days)
                        if ($currentDate->diffInDays($maturityDate) <= $alertPeriod) {
                            $monthlyData[$monthKey]['is_urgent'] = true;
                        }
                    }
                }
            }
            
            // Calculate next interest payment date
            // Assuming monthly interest payments based on contract date
            if ($invest->next_time) {
                $nextPaymentDate = Carbon::parse($invest->next_time);
                
                // If next payment is within the next 6 months
                if ($nextPaymentDate->between($currentDate, $sixMonthsLater)) {
                    $monthKey = $nextPaymentDate->format('Y-m');
                    
                    if (isset($monthlyData[$monthKey])) {
                        $monthlyData[$monthKey]['interest_alerts']++;
                        $monthlyData[$monthKey]['interest_contracts'][] = [
                            'id' => $invest->id,
                            'invest_no' => $invest->invest_no,
                            'project_name' => $invest->project->title,
                            'payment_date' => $nextPaymentDate->format('Y-m-d'),
                            'days_remaining' => (int) round($currentDate->diffInDays($nextPaymentDate)),
                            'amount' => $invest->recurring_pay
                        ];
                        
                        // Check if it's urgent (less than alertPeriod days)
                        if ($currentDate->diffInDays($nextPaymentDate) <= $alertPeriod) {
                            $monthlyData[$monthKey]['is_urgent'] = true;
                        }
                    }
                }
            }
        }
        
        return $monthlyData;
    }
    
    /**
     * Get summary statistics
     */
    private function getSummaryStats($invests)
    {
        $currentDate = Carbon::now();
        $currentMonth = $currentDate->format('Y-m');
        $nextMonth = $currentDate->copy()->addMonth()->format('Y-m');
        
        $stats = [
            'total_active_contracts' => $invests->count(),
            'current_month_interest' => 0,
            'current_month_maturity' => 0,
            'next_month_interest' => 0,
            'next_month_maturity' => 0,
        ];
        
        foreach ($invests as $invest) {
            // Check maturity payments
            if ($invest->project_closed) {
                $maturityDate = Carbon::parse($invest->project_closed);
                $maturityMonth = $maturityDate->format('Y-m');
                
                if ($maturityMonth == $currentMonth) {
                    $stats['current_month_maturity']++;
                } elseif ($maturityMonth == $nextMonth) {
                    $stats['next_month_maturity']++;
                }
            }
            
            // Check interest payments
            if ($invest->next_time) {
                $nextPaymentDate = Carbon::parse($invest->next_time);
                $paymentMonth = $nextPaymentDate->format('Y-m');
                
                if ($paymentMonth == $currentMonth) {
                    $stats['current_month_interest']++;
                } elseif ($paymentMonth == $nextMonth) {
                    $stats['next_month_interest']++;
                }
            }
        }
        
        return $stats;
    }
    
    /**
     * Save alert settings
     */
    public function saveSettings(Request $request)
    {
        $request->validate([
            'alert_period' => 'required|integer|min:1|max:180',
        ]);
        
        // Save alert settings to general settings
        $general = gs();
        $general->alert_period = $request->alert_period;
        $general->save();
        
        $notify[] = ['success', 'Alert settings updated successfully'];
        return back()->withNotify($notify);
    }
} 