<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Invest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\HonorService;

class SalesStaffController extends Controller
{
    /**
     * Display the staff dashboard
     */
    public function dashboard()
    {
        $pageTitle = 'Sales Staff Dashboard';
        $user = Auth::user();
        $general = gs();
        
        // Check for active honor
        $honorService = new HonorService();
        $honor = $honorService->getActiveHonor();
        
        // Get count of contracts created by staff
        $createdContractsCount = Invest::where('staff_id', $user->id)->count();
        
        // Get count of contracts referred by staff
        $referredContractsCount = Invest::where('referral_code', $user->referral_code)->count();
        
        // Get active contracts (both created and referred)
        $activeCreatedContracts = Invest::where('staff_id', $user->id)
            ->where('status', Status::INVEST_RUNNING)
            ->count();
            
        $activeReferredContracts = Invest::where('referral_code', $user->referral_code)
            ->where('status', Status::INVEST_RUNNING)
            ->count();
        
        // Get pending contracts (both created and referred)
        $pendingCreatedContracts = Invest::where('staff_id', $user->id)
            ->whereIn('status', [Status::INVEST_PENDING, Status::INVEST_PENDING_ADMIN_REVIEW])
            ->count();
            
        $pendingReferredContracts = Invest::where('referral_code', $user->referral_code)
            ->whereIn('status', [Status::INVEST_PENDING, Status::INVEST_PENDING_ADMIN_REVIEW])
            ->count();
        
        // Dashboard statistics
        $stats = [
            'total_contracts' => $createdContractsCount + $referredContractsCount,
            'active_contracts' => $activeCreatedContracts + $activeReferredContracts,
            'pending_contracts' => $pendingCreatedContracts + $pendingReferredContracts,
            'customers' => User::whereIn('id', function($query) use ($user) {
                $query->select('user_id')
                    ->from('invests')
                    ->where(function($q) use ($user) {
                        $q->where('staff_id', $user->id)
                          ->orWhere('referral_code', $user->referral_code);
                    })
                    ->distinct();
            })->count()
        ];
        
        // Get upcoming payments
        $today = Carbon::now();
        $alertPeriod = 30; // 30 days for alerts
        $alertDate = $today->copy()->addDays($alertPeriod);
        
        // Get interest payment alerts (both created and referred)
        $interestAlerts = Invest::where(function($query) use ($user) {
                $query->where('staff_id', $user->id)
                      ->orWhere('referral_code', $user->referral_code);
            })
            ->where('status', Status::INVEST_RUNNING)
            ->whereNotNull('next_time')
            ->where('next_time', '<=', $alertDate)
            ->with(['project', 'user'])
            ->orderBy('next_time')
            ->limit(5)
            ->get();
            
        // Calculate monthly interest amount for each alert
        foreach ($interestAlerts as $alert) {
            // Calculate monthly ROI (annual ROI divided by 12)
            $totalInvestment = $alert->total_price;
            $roiPercentage = $alert->roi_percentage;
            $annualROI = ($totalInvestment * $roiPercentage / 100);
            $monthlyROI = $annualROI / 12;
            $alert->monthly_roi_amount = $monthlyROI;
        }
        
        // Get maturity alerts (both created and referred)
        $maturityAlerts = Invest::where(function($query) use ($user) {
                $query->where('staff_id', $user->id)
                      ->orWhere('referral_code', $user->referral_code);
            })
            ->where('status', Status::INVEST_RUNNING)
            ->whereNotNull('project_closed')
            ->where('project_closed', '<=', $alertDate)
            ->with(['project', 'user'])
            ->orderBy('project_closed')
            ->limit(5)
            ->get();
            
        // Get all contracts (both created and referred)
        $createdContracts = Invest::where('staff_id', $user->id)
            ->with(['project', 'user'])
            ->latest();
            
        $referredContracts = Invest::where('referral_code', $user->referral_code)
            ->with(['project', 'user'])
            ->latest();
            
        $recentContracts = $createdContracts->union($referredContracts)
            ->latest()
            ->limit(10)
            ->get();
        
        // Get notifications for topnav
        $pending_notifications = $user->notifications()->where('user_read', 0)->count();
        $notifications = $user->notifications()->latest()->limit(5)->get();
        
        $emptyMessage = 'Không có dữ liệu';
        
        return view('user.staff.staff.dashboard', compact('pageTitle', 'user', 'stats', 'interestAlerts', 'maturityAlerts', 'recentContracts', 'pending_notifications', 'notifications', 'general', 'emptyMessage', 'honor'));
    }
    
    /**
     * Display list of staff contracts
     */
    public function contracts()
    {
        $pageTitle = 'My Contracts';
        $user = Auth::user();
        $general = gs();
        
        // Get contracts created by staff
        $createdContracts = Invest::where('staff_id', $user->id)
            ->with(['project', 'user'])
            ->latest();
            
        // Get contracts referred by staff
        $referredContracts = Invest::where('referral_code', $user->referral_code)
            ->with(['project', 'user'])
            ->latest();
            
        // Combine both queries
        $contracts = $createdContracts->union($referredContracts)
            ->latest()
            ->paginate(getPaginate());
            
        // Get notifications for topnav
        $pending_notifications = $user->notifications()->where('user_read', 0)->count();
        $notifications = $user->notifications()->latest()->limit(5)->get();
        
        $emptyMessage = 'Không tìm thấy hợp đồng nào';
            
        return view('user.staff.staff.contracts', compact('pageTitle', 'contracts', 'pending_notifications', 'notifications', 'emptyMessage', 'general'));
    }
    
    /**
     * Display contract details
     */
    public function contractDetails($id)
    {
        $user = Auth::user();
        $general = gs();
        
        // Look for contracts that the staff created or referred
        $invest = Invest::where('id', $id)
            ->where(function($query) use ($user) {
                $query->where('staff_id', $user->id)
                      ->orWhere('referral_code', $user->referral_code);
            })
            ->with(['project', 'user', 'interests'])
            ->firstOrFail();
            
        $pageTitle = 'Contract Details: ' . $invest->invest_no;
        
        // Get notifications for topnav
        $pending_notifications = $user->notifications()->where('user_read', 0)->count();
        $notifications = $user->notifications()->latest()->limit(5)->get();
        
        return view('user.staff.staff.contract_details', compact('pageTitle', 'invest', 'pending_notifications', 'notifications', 'general'));
    }
    
    /**
     * Display create contract form
     */
    public function createContract()
    {
        $pageTitle = 'Create New Contract';
        $user = Auth::user();
        $general = gs();
        
        // Get customers for dropdown
        $customers = User::where('status', 1)->get();
        
        // Get projects for dropdown
        $projects = \App\Models\Project::where('status', 1)->get();
        
        // Get notifications for topnav
        $pending_notifications = $user->notifications()->where('user_read', 0)->count();
        $notifications = $user->notifications()->latest()->limit(5)->get();
        
        return view('user.staff.staff.create_contract', compact('pageTitle', 'pending_notifications', 'notifications', 'general', 'customers', 'projects'));
    }
    
    /**
     * Store a new contract
     */
    public function storeContract(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|integer|exists:users,id',
            'project_id' => 'required|integer|exists:projects,id',
            'amount' => 'required|numeric|min:1',
            'duration' => 'required|integer|min:1',
        ]);
        
        // Get the project details
        $project = \App\Models\Project::findOrFail($request->project_id);
        $customer = User::findOrFail($request->customer_id);
        
        // Generate a unique contract number
        $investNo = generateContractNumber();
        
        // Create a new contract
        $invest = new Invest();
        $invest->user_id = $customer->id; // Customer ID
        $invest->staff_id = Auth::id(); // Set to staff member ID
        $invest->project_id = $request->project_id;
        $invest->invest_no = $investNo;
        $invest->total_price = $request->amount;
        $invest->period = $request->duration;
        $invest->payment_type = \App\Constants\Status::PAYMENT_WALLET; // Mặc định là thanh toán ví
        $invest->status = Status::INVEST_PENDING_ADMIN_REVIEW; // Set as pending for admin review
        $invest->contract_content = generateContractContent($project, $customer, $investNo, $invest->status);
        $invest->created_at = now();
        $invest->updated_at = now();
        $invest->save();
        
        $notify[] = ['success', 'Contract created and submitted for approval'];
        return redirect()->route('user.staff.staff.contracts')->withNotify($notify);
    }
    
    /**
     * Cancel a contract
     */
    public function cancelContract(Request $request, $id)
    {
        $user = Auth::user();
        
        $invest = Invest::where('id', $id)
            ->where('staff_id', $user->id)
            ->where('status', Status::INVEST_PENDING) // Can only cancel pending contracts
            ->firstOrFail();
            
        $invest->status = Status::INVEST_CANCELED;
        $invest->save();
        
        $notify[] = ['success', 'Contract canceled successfully'];
        return back()->withNotify($notify);
    }
    
    /**
     * View own alerts
     */
    public function alerts()
    {
        $pageTitle = 'My Alerts';
        $user = Auth::user();
        $general = gs();
        
        $today = Carbon::now();
        $sixMonthsLater = $today->copy()->addMonths(6);
        
        // Get interest payment alerts for next 6 months
        $interestAlerts = Invest::where(function($query) use ($user) {
                $query->where('staff_id', $user->id)
                      ->orWhere('referral_code', $user->referral_code);
            })
            ->whereIn('status', [Status::INVEST_RUNNING, Status::INVEST_ACCEPT, Status::INVEST_PENDING, Status::INVEST_PENDING_ADMIN_REVIEW])
            ->whereNotNull('next_time')
            ->where('next_time', '>=', $today)
            ->where('next_time', '<=', $sixMonthsLater)
            ->orderBy('next_time')
            ->get();
            
        // Calculate monthly interest amount for each alert
        foreach ($interestAlerts as $alert) {
            // Calculate monthly ROI (annual ROI divided by 12)
            $totalInvestment = $alert->total_price;
            $roiPercentage = $alert->roi_percentage;
            $annualROI = ($totalInvestment * $roiPercentage / 100);
            $monthlyROI = $annualROI / 12;
            $alert->monthly_roi_amount = $monthlyROI;
        }
        
        // Get maturity alerts for next 6 months
        $maturityAlerts = Invest::where(function($query) use ($user) {
                $query->where('staff_id', $user->id)
                      ->orWhere('referral_code', $user->referral_code);
            })
            ->whereIn('status', [Status::INVEST_RUNNING, Status::INVEST_ACCEPT, Status::INVEST_PENDING, Status::INVEST_PENDING_ADMIN_REVIEW])
            ->whereNotNull('project_closed')
            ->where('project_closed', '>=', $today)
            ->where('project_closed', '<=', $sixMonthsLater)
            ->orderBy('project_closed')
            ->get();
            
        // Load all related projects directly
        $projectIds = array_merge(
            $interestAlerts->pluck('project_id')->toArray(),
            $maturityAlerts->pluck('project_id')->toArray()
        );
        $projectIds = array_unique(array_filter($projectIds));
        
        if (!empty($projectIds)) {
            $projects = \App\Models\Project::whereIn('id', $projectIds)->get()->keyBy('id');
            
            // Set project information for each alert
            foreach ($interestAlerts as $alert) {
                if ($alert->project_id && isset($projects[$alert->project_id])) {
                    $alert->setRelation('project', $projects[$alert->project_id]);
                }
            }
            
            foreach ($maturityAlerts as $alert) {
                if ($alert->project_id && isset($projects[$alert->project_id])) {
                    $alert->setRelation('project', $projects[$alert->project_id]);
                }
            }
        }
        
        // Organize alerts by month
        $monthlyAlerts = [];
        for ($date = $today->copy()->startOfMonth(); $date->lte($sixMonthsLater); $date->addMonth()) {
            $monthKey = $date->format('Y-m');
            $monthlyAlerts[$monthKey] = [
                'month' => $date->format('F Y'),
                'interest_alerts' => [],
                'maturity_alerts' => []
            ];
        }
        
        // After setting project relations, now organize alerts by month
        foreach ($interestAlerts as $alert) {
            $monthKey = Carbon::parse($alert->next_time)->format('Y-m');
            if (isset($monthlyAlerts[$monthKey])) {
                $monthlyAlerts[$monthKey]['interest_alerts'][] = $alert;
            }
        }
        
        foreach ($maturityAlerts as $alert) {
            $monthKey = Carbon::parse($alert->project_closed)->format('Y-m');
            if (isset($monthlyAlerts[$monthKey])) {
                $monthlyAlerts[$monthKey]['maturity_alerts'][] = $alert;
            }
        }
        
        // Get notifications for topnav
        $pending_notifications = $user->notifications()->where('user_read', 0)->count();
        $notifications = $user->notifications()->latest()->limit(5)->get();
        
        return view('user.staff.staff.alerts', compact('pageTitle', 'interestAlerts', 'maturityAlerts', 'monthlyAlerts', 'pending_notifications', 'notifications', 'general'));
    }
    
    /**
     * View customers assigned to this staff
     */
    public function customers()
    {
        $pageTitle = 'My Customers';
        $user = Auth::user();
        $general = gs();
        
        // Get all customers that have contracts with this staff (both created and referred)
        $customers = User::whereHas('invests', function($query) use ($user) {
                $query->where('staff_id', $user->id)
                      ->orWhere('referral_code', $user->referral_code);
            })
            ->withCount('invests')
            ->withSum('invests', 'total_price')
            ->latest()
            ->paginate(getPaginate());
        
        // Get notifications for topnav
        $pending_notifications = $user->notifications()->where('user_read', 0)->count();
        $notifications = $user->notifications()->latest()->limit(5)->get();
        
        $emptyMessage = 'No customers found';
        
        return view('user.staff.staff.customers', compact('pageTitle', 'customers', 'pending_notifications', 'notifications', 'emptyMessage', 'general'));
    }

    /**
     * Xem bảng lương cá nhân
     */
    public function salary(Request $request)
    {
        $pageTitle = 'Lương & Thu nhập';
        $user = Auth::user();
        $month = $request->get('month', now()->format('Y-m'));
        $salaries = \App\Models\StaffSalary::where('staff_id', $user->id)
            ->where('month_year', $month)
            ->latest()->paginate(getPaginate());
        $summary = [
            'total_base_salary' => $salaries->sum('base_salary'),
            'total_commission' => $salaries->sum('commission_amount'),
            'total_bonus' => $salaries->sum('bonus_amount'),
            'total_deduction' => $salaries->sum('deduction_amount'),
            'total_salary' => $salaries->sum('total_salary'),
            'avg_kpi_percentage' => $salaries->avg('kpi_percentage'),
        ];
        return view('user.staff.staff.salary', compact('pageTitle', 'salaries', 'summary', 'month'));
    }

    /**
     * Xem KPI cá nhân
     */
    public function kpi(Request $request)
    {
        $pageTitle = 'KPI & Chỉ số';
        $user = Auth::user();
        $month = $request->get('month', now()->format('Y-m'));
        $kpis = \App\Models\StaffKPI::where('staff_id', $user->id)
            ->where('month_year', $month)
            ->latest()->paginate(getPaginate());
        $summary = [
            'total_target_contracts' => $kpis->sum('target_contracts'),
            'total_actual_contracts' => $kpis->sum('actual_contracts'),
            'total_target_sales' => $kpis->sum('target_sales'),
            'total_actual_sales' => $kpis->sum('actual_sales'),
            'avg_overall_kpi' => $kpis->avg('overall_kpi_percentage'),
        ];
        
        // Get KPI policy documents
        $kpiCategory = \App\Models\DocumentCategory::where('name', 'Tài liệu chính sách KPI')->first();
        $kpiDocuments = [];
        if ($kpiCategory) {
            $kpiDocuments = \App\Models\ReferenceDocument::where('category_id', $kpiCategory->id)
                ->where('status', 1)
                ->where('for_staff', 1)
                ->ordered()
                ->get();
        }
        
        return view('user.staff.staff.kpi', compact('pageTitle', 'kpis', 'summary', 'month', 'kpiDocuments'));
    }


} 