<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Invest;
use App\Models\User;
use App\Models\StaffSalary;
use App\Models\StaffKPI;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class SalesManagerController extends Controller
{
    /**
     * Display the manager dashboard
     */
    public function dashboard()
    {
        $pageTitle = 'Sales Manager Dashboard';
        $user = Auth::user();
        
        // Get all staff members managed by this manager
        $staffMembers = $user->staffMembers;
        
        // Get all contracts from this manager and all staff
        $staffIds = $staffMembers->pluck('id')->toArray();
        $staffIds[] = $user->id;
        
        // Dashboard statistics
        $stats = [
            'total_contracts' => Invest::whereIn('user_id', $staffIds)->count(),
            'active_contracts' => Invest::whereIn('user_id', $staffIds)->where('status', Status::INVEST_RUNNING)->count(),
            'team_members' => $staffMembers->count(),
            'total_customers' => User::whereIn('id', function($query) use ($staffIds) {
                $query->select('user_id')->from('invests')->whereIn('user_id', $staffIds)->distinct();
            })->count()
        ];
        
        // Get upcoming payments
        $today = Carbon::now();
        $alertPeriod = 30; // 30 days for alerts
        $alertDate = $today->copy()->addDays($alertPeriod);
        
        // Get interest payment alerts
        $interestAlerts = Invest::whereIn('user_id', $staffIds)
            ->where('status', Status::INVEST_RUNNING)
            ->whereNotNull('next_time')
            ->where('next_time', '<=', $alertDate)
            ->with(['project', 'user'])
            ->orderBy('next_time')
            ->limit(10)
            ->get();
            
        // Get maturity alerts    
        $maturityAlerts = Invest::whereIn('user_id', $staffIds)
            ->where('status', Status::INVEST_RUNNING)
            ->whereNotNull('project_closed')
            ->where('project_closed', '<=', $alertDate)
            ->with(['project', 'user'])
            ->orderBy('project_closed')
            ->limit(10)
            ->get();
        
        return view('user.staff.manager.dashboard', compact('pageTitle', 'user', 'staffMembers', 'stats', 'interestAlerts', 'maturityAlerts'));
    }
    
    /**
     * Display team members list
     */
    public function teamMembers()
    {
        $pageTitle = 'Team Members';
        $user = Auth::user();
        $staffMembers = $user->staffMembers()->paginate(getPaginate());
        
        return view('user.staff.manager.team_members', compact('pageTitle', 'staffMembers'));
    }
    
    /**
     * Create a new staff member
     */
    public function createStaffMember(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string|max:40',
            'lastname' => 'required|string|max:40',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);
        
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        
        $manager = Auth::user();
        
        $staff = new User();
        $staff->firstname = $request->firstname;
        $staff->lastname = $request->lastname;
        $staff->email = $request->email;
        $staff->password = Hash::make($request->password);
        $staff->is_staff = true;
        $staff->role = 'sales_staff';
        $staff->manager_id = $manager->id;
        $staff->ev = 1; // Set email as verified for staff
        $staff->sv = 1; // Set SMS as verified for staff
        $staff->save();
        
        $notify[] = ['success', 'Staff member created successfully'];
        return back()->withNotify($notify);
    }
    
    /**
     * View all team contracts
     */
    public function teamContracts()
    {
        $pageTitle = 'Team Contracts';
        $user = Auth::user();
        
        // Get all staff members managed by this manager
        $staffIds = $user->staffMembers()->pluck('id')->toArray();
        $staffIds[] = $user->id;
        
        $contracts = Invest::whereIn('user_id', $staffIds)
            ->with(['project', 'user'])
            ->latest()
            ->paginate(getPaginate());
            
        return view('user.staff.manager.contracts', compact('pageTitle', 'contracts'));
    }
    
    /**
     * View contract approval requests
     */
    public function approvalRequests()
    {
        $pageTitle = 'Contract Approval Requests';
        $user = Auth::user();
        
        // Get all staff members managed by this manager
        $staffIds = $user->staffMembers()->pluck('id')->toArray();
        
        $pendingContracts = Invest::whereIn('user_id', $staffIds)
            ->where('status', Status::INVEST_PENDING)
            ->with(['project', 'user'])
            ->latest()
            ->paginate(getPaginate());
            
        return view('user.staff.manager.approval_requests', compact('pageTitle', 'pendingContracts'));
    }
    
    /**
     * Approve a contract
     */
    public function approveContract(Request $request, $id)
    {
        $invest = Invest::findOrFail($id);
        $user = Auth::user();
        
        // Verify that this is from a staff member of this manager
        $staffIds = $user->staffMembers()->pluck('id')->toArray();
        if (!in_array($invest->user_id, $staffIds)) {
            $notify[] = ['error', 'Unauthorized action'];
            return back()->withNotify($notify);
        }
        
        $invest->status = Status::INVEST_RUNNING;
        $invest->approved_at = now();
        $invest->save();
        
        $notify[] = ['success', 'Contract approved successfully'];
        return back()->withNotify($notify);
    }
    
    /**
     * Reject a contract
     */
    public function rejectContract(Request $request, $id)
    {
        $invest = Invest::findOrFail($id);
        $user = Auth::user();
        
        // Verify that this is from a staff member of this manager
        $staffIds = $user->staffMembers()->pluck('id')->toArray();
        if (!in_array($invest->user_id, $staffIds)) {
            $notify[] = ['error', 'Unauthorized action'];
            return back()->withNotify($notify);
        }
        
        $invest->status = Status::INVEST_REJECTED;
        $invest->rejection_reason = $request->rejection_reason;
        $invest->save();
        
        $notify[] = ['success', 'Contract rejected successfully'];
        return back()->withNotify($notify);
    }
    
    /**
     * View alerts dashboard
     */
    public function alerts()
    {
        $user = auth()->user();
        $staffMembers = $user->staffMembers;
        $staffIds = $staffMembers->pluck('id')->toArray();
        $staffIds[] = $user->id;

        $alertPeriod = request('alert_period', 30);
        $today = \Carbon\Carbon::now();
        $alertDate = $today->copy()->addDays($alertPeriod);

        // Lấy danh sách cảnh báo lãi suất
        $interestAlerts = \App\Models\Invest::whereIn('user_id', $staffIds)
            ->where('status', \App\Constants\Status::INVEST_RUNNING)
            ->whereNotNull('next_time')
            ->where('next_time', '<=', $alertDate)
            ->with(['project', 'user'])
            ->orderBy('next_time')
            ->limit(10)
            ->get();

        // Lấy danh sách cảnh báo đáo hạn
        $maturityAlerts = \App\Models\Invest::whereIn('user_id', $staffIds)
            ->where('status', \App\Constants\Status::INVEST_RUNNING)
            ->whereNotNull('project_closed')
            ->where('project_closed', '<=', $alertDate)
            ->with(['project', 'user'])
            ->orderBy('project_closed')
            ->limit(10)
            ->get();

        return view('user.staff.manager.alerts', compact('alertPeriod', 'interestAlerts', 'maturityAlerts'));
    }
    
    /**
     * View and generate reports
     */
    public function reports()
    {
        $pageTitle = 'Reports';
        $user = Auth::user();
        
        // Get all staff members managed by this manager
        $staffIds = $user->staffMembers()->pluck('id')->toArray();
        $staffIds[] = $user->id;
        
        // Get investment statistics by staff member
        $staffStats = User::whereIn('id', $staffIds)
            ->withCount(['invests', 'invests as active_invests_count' => function($query) {
                $query->where('status', Status::INVEST_RUNNING);
            }])
            ->get();
            
        return view('user.staff.manager.reports', compact('pageTitle', 'staffStats'));
    }

    public function reportTransactions() {
        $pageTitle = 'Báo cáo giao dịch';
        return view('user.staff.manager.report_transactions', compact('pageTitle'));
    }
    public function reportInterests() {
        $pageTitle = 'Báo cáo lãi suất';
        return view('user.staff.manager.report_interests', compact('pageTitle'));
    }
    public function reportCommissions() {
        $pageTitle = 'Báo cáo hoa hồng';
        return view('user.staff.manager.report_commissions', compact('pageTitle'));
    }

    /**
     * HR Management: Salary Dashboard
     */
    public function salaryDashboard(Request $request)
    {
        $pageTitle = 'Lương & Thu nhập';
        $user = Auth::user();
        
        // Get filter parameters
        $month = $request->get('month', now()->format('Y-m'));
        $staffId = $request->get('user_id');
        
        // Get staff members
        $staffMembers = $user->staffMembers;
        
        // Build query for salaries
        $query = StaffSalary::with(['staff'])
            ->where('manager_id', $user->id)
            ->where('month_year', $month);
            
        if ($staffId) {
            $query->where('staff_id', $staffId);
        }
        
        $salaries = $query->latest()->paginate(getPaginate());
        
        // Calculate summary statistics
        $summary = [
            'total_base_salary' => $salaries->sum('base_salary'),
            'total_commission' => $salaries->sum('commission_amount'),
            'total_bonus' => $salaries->sum('bonus_amount'),
            'total_deduction' => $salaries->sum('deduction_amount'),
            'total_salary' => $salaries->sum('total_salary'),
            'avg_kpi_percentage' => $salaries->avg('kpi_percentage'),
            'exceeded_kpi_count' => $salaries->where('kpi_status', 'exceeded')->count(),
            'achieved_kpi_count' => $salaries->where('kpi_status', 'achieved')->count(),
            'near_achieved_count' => $salaries->where('kpi_status', 'near_achieved')->count(),
            'not_achieved_count' => $salaries->where('kpi_status', 'not_achieved')->count(),
        ];
        
        return view('user.staff.manager.salary_commission', compact('pageTitle', 'salaries', 'staffMembers', 'summary', 'month', 'staffId'));
    }

    /**
     * HR Management: KPI Dashboard
     */
    public function kpiDashboard(Request $request)
    {
        $pageTitle = 'KPI & Chỉ số';
        $user = Auth::user();
        
        // Get filter parameters
        $month = $request->get('month', now()->format('Y-m'));
        $staffId = $request->get('user_id');
        
        // Get staff members
        $staffMembers = $user->staffMembers;
        
        // Build query for KPIs
        $query = StaffKPI::with(['staff'])
            ->where('manager_id', $user->id)
            ->where('month_year', $month);
            
        if ($staffId) {
            $query->where('staff_id', $staffId);
        }
        
        $kpis = $query->latest()->paginate(getPaginate());
        
        // Calculate summary statistics
        $summary = [
            'total_target_contracts' => $kpis->sum('target_contracts'),
            'total_actual_contracts' => $kpis->sum('actual_contracts'),
            'total_target_sales' => $kpis->sum('target_sales'),
            'total_actual_sales' => $kpis->sum('actual_sales'),
            'avg_overall_kpi' => $kpis->avg('overall_kpi_percentage'),
            'exceeded_kpi_count' => $kpis->where('kpi_status', 'exceeded')->count(),
            'achieved_kpi_count' => $kpis->where('kpi_status', 'achieved')->count(),
            'near_achieved_count' => $kpis->where('kpi_status', 'near_achieved')->count(),
            'not_achieved_count' => $kpis->where('kpi_status', 'not_achieved')->count(),
        ];
        
        // Get KPI data for charts (last 6 months)
        $chartData = StaffKPI::where('manager_id', $user->id)
            ->where('month_year', '>=', now()->subMonths(5)->format('Y-m'))
            ->orderBy('month_year')
            ->get()
            ->groupBy('month_year')
            ->map(function ($monthKpis) {
                return [
                    'target_sales' => $monthKpis->sum('target_sales'),
                    'actual_sales' => $monthKpis->sum('actual_sales'),
                    'avg_kpi' => $monthKpis->avg('overall_kpi_percentage'),
                ];
            });
        
        return view('user.staff.manager.kpi_dashboard', compact('pageTitle', 'kpis', 'staffMembers', 'summary', 'chartData', 'month', 'staffId'));
    }

    /**
     * HR Management: Performance Dashboard
     */
    public function performanceDashboard()
    {
        $pageTitle = 'Hiệu suất làm việc';
            return view('user.staff.manager.performance_dashboard', compact('pageTitle'));
    }
} 