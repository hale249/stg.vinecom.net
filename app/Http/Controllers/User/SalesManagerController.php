<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Invest;
use App\Models\User;
use App\Models\StaffSalary;
use App\Models\StaffKPI;
use App\Models\StaffAttendance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use League\Csv\Reader;
use League\Csv\Writer;
use App\Services\HonorService;

class SalesManagerController extends Controller
{
    /**
     * Display the manager dashboard
     */
    public function dashboard()
    {
        $pageTitle = 'Sales Manager Dashboard';
        $user = Auth::user();
        
        // Check for active honor
        $honorService = new HonorService();
        $honor = $honorService->getActiveHonor();
        
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
        
        return view('user.staff.manager.dashboard', compact('pageTitle', 'user', 'staffMembers', 'stats', 'interestAlerts', 'maturityAlerts', 'honor'));
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
        
        $invest->status = Status::INVEST_CANCELED;
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
     * HR Management: Attendance Dashboard
     */
    public function attendanceDashboard(Request $request)
    {
        $pageTitle = 'Quản lý Chấm công';
        $user = Auth::user();
        
        // Get filter parameters
        $month = $request->get('month', now()->format('Y-m'));
        $staffId = $request->get('user_id');
        $year = substr($month, 0, 4);
        $monthNum = substr($month, 5, 2);
        
        // Get staff members
        $staffMembers = $user->staffMembers;
        
        // Build query for attendance
        $query = StaffAttendance::with(['staff'])
            ->whereHas('staff', function($q) use ($user) {
                $q->where('manager_id', $user->id);
            })
            ->whereYear('date', $year)
            ->whereMonth('date', $monthNum);
            
        if ($staffId) {
            $query->where('staff_id', $staffId);
        }
        
        $attendances = $query->orderBy('date', 'desc')->paginate(getPaginate());
        
        // Calculate summary statistics by employee
        $summaryByEmployee = StaffAttendance::with(['staff'])
            ->whereHas('staff', function($q) use ($user) {
                $q->where('manager_id', $user->id);
            })
            ->whereYear('date', $year)
            ->whereMonth('date', $monthNum)
            ->select('staff_id', 'employee_code', DB::raw('SUM(working_day) as total_working_days'), DB::raw('COUNT(*) as total_days'))
            ->groupBy('staff_id', 'employee_code')
            ->get();
        
        // Return JSON if requested
        if ($request->get('format') === 'json') {
            return response()->json([
                'attendances' => $attendances->items(),
                'summary' => $summaryByEmployee
            ]);
        }
        
        return view('user.staff.manager.attendance', compact('pageTitle', 'attendances', 'staffMembers', 'summaryByEmployee', 'month', 'staffId'));
    }

    /**
     * Store new attendance record
     */
    public function storeAttendance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'staff_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'working_day' => 'required|numeric|min:0|max:1',
            'note' => 'nullable|string|max:255',
        ]);
        
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        
        $user = Auth::user();
        $staff = User::findOrFail($request->staff_id);
        
        // Check if manager has permission to add attendance for this staff
        if ($staff->manager_id != $user->id && !auth()->user()->is_admin) {
            $notify[] = ['error', 'Bạn không có quyền thêm chấm công cho nhân viên này'];
            return back()->withNotify($notify);
        }
        
        // Check if attendance already exists for this date and employee
        $existingAttendance = StaffAttendance::where('staff_id', $request->staff_id)
            ->where('date', $request->date)
            ->first();
            
        if ($existingAttendance) {
            // Update existing record
            $existingAttendance->working_day = $request->working_day;
            $existingAttendance->note = $request->note;
            $existingAttendance->updated_by = $user->id;
            $existingAttendance->save();
            
            $notify[] = ['success', 'Cập nhật chấm công thành công'];
        } else {
            // Create new record
            $attendance = new StaffAttendance();
            $attendance->staff_id = $request->staff_id;
            $attendance->employee_code = $staff->username; // Using username as employee code
            $attendance->date = $request->date;
            $attendance->working_day = $request->working_day;
            $attendance->note = $request->note;
            $attendance->created_by = $user->id;
            $attendance->save();
            
            $notify[] = ['success', 'Thêm chấm công thành công'];
        }
        
        return back()->withNotify($notify);
    }

    /**
     * Delete attendance record
     */
    public function deleteAttendance(Request $request, $id)
    {
        $user = Auth::user();
        $attendance = StaffAttendance::findOrFail($id);
        
        // Check if manager has permission to delete attendance for this staff
        $staff = User::findOrFail($attendance->staff_id);
        if ($staff->manager_id != $user->id && !auth()->user()->is_admin) {
            $notify[] = ['error', 'Bạn không có quyền xóa chấm công của nhân viên này'];
            return back()->withNotify($notify);
        }
        
        $attendance->delete();
        
        $notify[] = ['success', 'Xóa chấm công thành công'];
        return back()->withNotify($notify);
    }

    /**
     * Export attendance data to CSV
     */
    public function exportAttendance(Request $request)
    {
        $user = Auth::user();
        $month = $request->get('month', now()->format('Y-m'));
        $staffId = $request->get('user_id');
        $year = substr($month, 0, 4);
        $monthNum = substr($month, 5, 2);
        
        // Build query for attendance
        $query = StaffAttendance::with(['staff'])
            ->whereHas('staff', function($q) use ($user) {
                $q->where('manager_id', $user->id);
            })
            ->whereYear('date', $year)
            ->whereMonth('date', $monthNum);
            
        if ($staffId) {
            $query->where('staff_id', $staffId);
        }
        
        $attendances = $query->orderBy('date')->get();
        
        // Create CSV
        $csv = Writer::createFromString('');
        $csv->insertOne(['employee_code', 'employee_name', 'date', 'working_day', 'note']);
        
        foreach ($attendances as $attendance) {
            $csv->insertOne([
                $attendance->employee_code,
                $attendance->staff->fullname ?? 'N/A',
                $attendance->date->format('Y-m-d'),
                $attendance->working_day,
                $attendance->note ?? '',
            ]);
        }
        
        $filename = 'attendance_' . $month . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        return response($csv->getContent(), 200, $headers);
    }

    /**
     * Import attendance data from CSV
     */
    public function importAttendance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'csv_file' => 'required|file|mimes:csv,txt',
            'overwrite' => 'nullable|boolean',
        ]);
        
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        
        $user = Auth::user();
        $overwrite = $request->has('overwrite') ? true : false;
        
        try {
            $csv = Reader::createFromPath($request->file('csv_file')->getPathname(), 'r');
            $csv->setHeaderOffset(0);
            
            $records = $csv->getRecords();
            $importCount = 0;
            $updateCount = 0;
            $errorCount = 0;
            $errors = [];
            
            DB::beginTransaction();
            
            foreach ($records as $offset => $record) {
                // Validate required fields
                if (empty($record['employee_code']) || empty($record['date']) || !isset($record['working_day'])) {
                    $errors[] = "Hàng " . ($offset + 2) . ": Thiếu thông tin bắt buộc";
                    $errorCount++;
                    continue;
                }
                
                // Find staff by employee code
                $staff = User::where('username', $record['employee_code'])
                    ->orWhere('email', $record['employee_code'])
                    ->first();
                    
                if (!$staff) {
                    $errors[] = "Hàng " . ($offset + 2) . ": Không tìm thấy nhân viên với mã " . $record['employee_code'];
                    $errorCount++;
                    continue;
                }
                
                // Check if manager has permission to add attendance for this staff
                if ($staff->manager_id != $user->id && !auth()->user()->is_admin) {
                    $errors[] = "Hàng " . ($offset + 2) . ": Bạn không có quyền thêm chấm công cho nhân viên " . $staff->fullname;
                    $errorCount++;
                    continue;
                }
                
                // Validate date format
                try {
                    $date = Carbon::createFromFormat('Y-m-d', $record['date'])->toDateString();
                } catch (\Exception $e) {
                    $errors[] = "Hàng " . ($offset + 2) . ": Định dạng ngày không hợp lệ, phải là YYYY-MM-DD";
                    $errorCount++;
                    continue;
                }
                
                // Validate working_day
                $workingDay = (float) $record['working_day'];
                if ($workingDay < 0 || $workingDay > 1) {
                    $errors[] = "Hàng " . ($offset + 2) . ": Số công không hợp lệ, phải từ 0 đến 1";
                    $errorCount++;
                    continue;
                }
                
                // Check if attendance already exists
                $existingAttendance = StaffAttendance::where('staff_id', $staff->id)
                    ->where('date', $date)
                    ->first();
                    
                if ($existingAttendance) {
                    if ($overwrite) {
                        // Update existing record
                        $existingAttendance->working_day = $workingDay;
                        $existingAttendance->note = $record['note'] ?? '';
                        $existingAttendance->updated_by = $user->id;
                        $existingAttendance->save();
                        $updateCount++;
                    } else {
                        // Skip if not overwriting
                        $errors[] = "Hàng " . ($offset + 2) . ": Đã tồn tại chấm công cho nhân viên " . $staff->fullname . " vào ngày " . $date;
                        $errorCount++;
                        continue;
                    }
                } else {
                    // Create new record
                    $attendance = new StaffAttendance();
                    $attendance->staff_id = $staff->id;
                    $attendance->employee_code = $record['employee_code'];
                    $attendance->date = $date;
                    $attendance->working_day = $workingDay;
                    $attendance->note = $record['note'] ?? '';
                    $attendance->created_by = $user->id;
                    $attendance->save();
                    $importCount++;
                }
            }
            
            DB::commit();
            
            $message = "Nhập dữ liệu thành công. Thêm mới: $importCount, Cập nhật: $updateCount";
            if ($errorCount > 0) {
                $message .= ", Lỗi: $errorCount";
            }
            
            $notify[] = ['success', $message];
            
            if (!empty($errors)) {
                session()->flash('import_errors', $errors);
            }
            
            return back()->withNotify($notify);
            
        } catch (\Exception $e) {
            DB::rollBack();
            $notify[] = ['error', 'Lỗi khi nhập dữ liệu: ' . $e->getMessage()];
            return back()->withNotify($notify);
        }
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
    public function performanceDashboard(Request $request)
    {
        $pageTitle = 'Hiệu suất làm việc';
        $user = Auth::user();
        $month = $request->get('month', now()->format('Y-m'));
        $staffId = $request->get('user_id');
        $projectId = $request->get('project_id');

        // Get all staff under this manager
        $staffMembers = $user->staffMembers;
        $projects = \App\Models\Project::all();

        // Build query for performance data
        $query = \App\Models\Invest::query()
            ->where('status', \App\Constants\Status::INVEST_COMPLETED)
            ->whereYear('created_at', substr($month, 0, 4))
            ->whereMonth('created_at', substr($month, 5, 2));
        if ($staffId) {
            $query->where('staff_id', $staffId);
        } else {
            $query->whereIn('staff_id', $staffMembers->pluck('id'));
        }
        if ($projectId) {
            $query->where('project_id', $projectId);
        }
        $invests = $query->get();

        // Prepare performance data
        $performanceData = [];
        foreach ($staffMembers as $staff) {
            if ($staffId && $staff->id != $staffId) continue;
            $staffInvests = $invests->where('staff_id', $staff->id);
            if ($projectId) {
                // Only for selected project
                $projectInvests = $staffInvests->where('project_id', $projectId);
                $contracts = $projectInvests->count();
                $sales = $projectInvests->sum('total_price');
            } else {
                $contracts = $staffInvests->count();
                $sales = $staffInvests->sum('total_price');
            }
            // Get KPI for this staff/month
            $kpi = \App\Models\StaffKPI::where('staff_id', $staff->id)
                ->where('month_year', $month)
                ->first();
            $kpiPercent = $kpi ? $kpi->overall_kpi_percentage : 0;
            $kpiStatus = $kpi ? $kpi->kpi_status : 'not_achieved';
            $performanceData[] = [
                'staff' => $staff,
                'contracts' => $contracts,
                'sales' => $sales,
                'kpi_percent' => $kpiPercent,
                'kpi_status' => $kpiStatus,
            ];
        }
        return view('user.staff.manager.performance_dashboard', compact('pageTitle', 'performanceData', 'staffMembers', 'projects', 'month', 'staffId', 'projectId'));
    }

    /**
     * Store new KPI data
     */
    public function storeKPI(Request $request)
    {
        $request->validate([
            'staff_id' => 'required|exists:users,id',
            'month_year' => 'required|date_format:Y-m',
            'target_contracts' => 'nullable|integer|min:0',
            'actual_contracts' => 'nullable|integer|min:0',
            'target_sales' => 'nullable|numeric|min:0',
            'actual_sales' => 'nullable|numeric|min:0',
            'target_customers' => 'nullable|integer|min:0',
            'actual_customers' => 'nullable|integer|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();
        
        // Check if KPI already exists for this staff and month
        $existingKPI = StaffKPI::where('staff_id', $request->staff_id)
            ->where('month_year', $request->month_year)
            ->where('manager_id', $user->id)
            ->first();

        if ($existingKPI) {
            $notify[] = ['error', 'KPI cho nhân viên này trong tháng này đã tồn tại.'];
            return back()->withNotify($notify);
        }

        // Calculate completion rates
        $contractCompletionRate = $request->target_contracts > 0 ? 
            ($request->actual_contracts / $request->target_contracts) * 100 : 0;
        $salesCompletionRate = $request->target_sales > 0 ? 
            ($request->actual_sales / $request->target_sales) * 100 : 0;
        $customerCompletionRate = $request->target_customers > 0 ? 
            ($request->actual_customers / $request->target_customers) * 100 : 0;
        
        // Calculate overall KPI (average of 3 rates)
        $overallKpiPercentage = ($contractCompletionRate + $salesCompletionRate + $customerCompletionRate) / 3;

        // Determine KPI status
        if ($overallKpiPercentage >= 120) {
            $kpiStatus = 'exceeded';
        } elseif ($overallKpiPercentage >= 100) {
            $kpiStatus = 'achieved';
        } elseif ($overallKpiPercentage >= 80) {
            $kpiStatus = 'near_achieved';
        } else {
            $kpiStatus = 'not_achieved';
        }

        // Create KPI record
        StaffKPI::create([
            'staff_id' => $request->staff_id,
            'manager_id' => $user->id,
            'month_year' => $request->month_year,
            'target_contracts' => $request->target_contracts ?? 0,
            'actual_contracts' => $request->actual_contracts ?? 0,
            'target_sales' => $request->target_sales ?? 0,
            'actual_sales' => $request->actual_sales ?? 0,
            'target_customers' => $request->target_customers ?? 0,
            'actual_customers' => $request->actual_customers ?? 0,
            'contract_completion_rate' => $contractCompletionRate,
            'sales_completion_rate' => $salesCompletionRate,
            'customer_completion_rate' => $customerCompletionRate,
            'overall_kpi_percentage' => $overallKpiPercentage,
            'kpi_status' => $kpiStatus,
            'notes' => $request->notes,
            'status' => 'approved',
            'approved_at' => now(),
        ]);

        $notify[] = ['success', 'KPI đã được tạo thành công.'];
        return back()->withNotify($notify);
    }

    /**
     * Show KPI details
     */
    public function showKPI($id)
    {
        $user = Auth::user();
        $kpi = StaffKPI::where('id', $id)
            ->where('manager_id', $user->id)
            ->with(['staff'])
            ->firstOrFail();

        $html = view('user.staff.manager.partials.kpi_detail', compact('kpi'))->render();
        
        return response()->json([
            'success' => true,
            'html' => $html
        ]);
    }

    /**
     * Edit KPI form data
     */
    public function editKPI($id)
    {
        $user = Auth::user();
        $kpi = StaffKPI::where('id', $id)
            ->where('manager_id', $user->id)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'kpi' => $kpi
        ]);
    }

    /**
     * Update KPI data
     */
    public function updateKPI(Request $request, $id)
    {
        $request->validate([
            'staff_id' => 'required|exists:users,id',
            'month_year' => 'required|date_format:Y-m',
            'target_contracts' => 'nullable|integer|min:0',
            'actual_contracts' => 'nullable|integer|min:0',
            'target_sales' => 'nullable|numeric|min:0',
            'actual_sales' => 'nullable|numeric|min:0',
            'target_customers' => 'nullable|integer|min:0',
            'actual_customers' => 'nullable|integer|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();
        $kpi = StaffKPI::where('id', $id)
            ->where('manager_id', $user->id)
            ->firstOrFail();

        // Check if KPI already exists for this staff and month (excluding current record)
        $existingKPI = StaffKPI::where('staff_id', $request->staff_id)
            ->where('month_year', $request->month_year)
            ->where('manager_id', $user->id)
            ->where('id', '!=', $id)
            ->first();

        if ($existingKPI) {
            return response()->json([
                'success' => false,
                'message' => 'KPI cho nhân viên này trong tháng này đã tồn tại.'
            ]);
        }

        // Calculate completion rates
        $contractCompletionRate = $request->target_contracts > 0 ? 
            ($request->actual_contracts / $request->target_contracts) * 100 : 0;
        $salesCompletionRate = $request->target_sales > 0 ? 
            ($request->actual_sales / $request->target_sales) * 100 : 0;
        $customerCompletionRate = $request->target_customers > 0 ? 
            ($request->actual_customers / $request->target_customers) * 100 : 0;
        
        // Calculate overall KPI (average of 3 rates)
        $overallKpiPercentage = ($contractCompletionRate + $salesCompletionRate + $customerCompletionRate) / 3;

        // Determine KPI status
        if ($overallKpiPercentage >= 120) {
            $kpiStatus = 'exceeded';
        } elseif ($overallKpiPercentage >= 100) {
            $kpiStatus = 'achieved';
        } elseif ($overallKpiPercentage >= 80) {
            $kpiStatus = 'near_achieved';
        } else {
            $kpiStatus = 'not_achieved';
        }

        // Update KPI record
        $kpi->update([
            'staff_id' => $request->staff_id,
            'month_year' => $request->month_year,
            'target_contracts' => $request->target_contracts ?? 0,
            'actual_contracts' => $request->actual_contracts ?? 0,
            'target_sales' => $request->target_sales ?? 0,
            'actual_sales' => $request->actual_sales ?? 0,
            'target_customers' => $request->target_customers ?? 0,
            'actual_customers' => $request->actual_customers ?? 0,
            'contract_completion_rate' => $contractCompletionRate,
            'sales_completion_rate' => $salesCompletionRate,
            'customer_completion_rate' => $customerCompletionRate,
            'overall_kpi_percentage' => $overallKpiPercentage,
            'kpi_status' => $kpiStatus,
            'notes' => $request->notes,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'KPI đã được cập nhật thành công.'
        ]);
    }

    /**
     * Delete KPI
     */
    public function destroyKPI($id)
    {
        $user = Auth::user();
        $kpi = StaffKPI::where('id', $id)
            ->where('manager_id', $user->id)
            ->firstOrFail();

        $kpi->delete();

        return response()->json([
            'success' => true,
            'message' => 'KPI đã được xóa thành công.'
        ]);
    }

    /**
     * Export KPI to Excel
     */
    public function exportKPI(Request $request)
    {
        $user = Auth::user();
        $month = $request->get('month', now()->format('Y-m'));
        $staffId = $request->get('user_id');
        $kpiStatus = $request->get('kpi_status');

        // Build query for KPIs
        $query = StaffKPI::with(['staff'])
            ->where('manager_id', $user->id)
            ->where('month_year', $month);
            
        if ($staffId) {
            $query->where('staff_id', $staffId);
        }
        if ($kpiStatus) {
            $query->where('kpi_status', $kpiStatus);
        }
        
        $kpis = $query->get();

        // Create CSV
        $csv = Writer::createFromString('');
        $csv->insertOne([
            'Nhân viên', 'Tháng', 'Chỉ tiêu HĐ', 'Thực tế HĐ', 'Chỉ tiêu DS', 
            'Thực tế DS', 'KPI (%)', 'Trạng thái', 'Ghi chú'
        ]);

        foreach ($kpis as $kpi) {
            $csv->insertOne([
                $kpi->staff->fullname ?? $kpi->staff->username,
                \Carbon\Carbon::createFromFormat('Y-m', $kpi->month_year)->format('m/Y'),
                $kpi->target_contracts,
                $kpi->actual_contracts,
                number_format($kpi->target_sales, 0, ',', '.'),
                number_format($kpi->actual_sales, 0, ',', '.'),
                number_format($kpi->overall_kpi_percentage, 1) . '%',
                $kpi->kpi_status,
                $kpi->notes ?? '',
            ]);
        }

        $filename = 'kpi_' . $month . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        return response($csv->getContent(), 200, $headers);
    }
} 