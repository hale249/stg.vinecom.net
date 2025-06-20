<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Invest;
use App\Models\User;
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
    public function salaryDashboard()
    {
        $pageTitle = 'Lương & Thu nhập';
        return view('user.staff.manager.salary_commission', compact('pageTitle'));
    }

    /**
     * HR Management: KPI Dashboard
     */
    public function kpiDashboard()
    {
        $pageTitle = 'KPI & Chỉ số';
        return view('user.staff.manager.kpi_dashboard', compact('pageTitle'));
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