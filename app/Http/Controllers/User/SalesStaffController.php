<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Invest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalesStaffController extends Controller
{
    /**
     * Display the staff dashboard
     */
    public function dashboard()
    {
        $pageTitle = 'Sales Staff Dashboard';
        $user = Auth::user();
        
        // Dashboard statistics
        $stats = [
            'total_contracts' => Invest::where('user_id', $user->id)->count(),
            'active_contracts' => Invest::where('user_id', $user->id)->where('status', Status::INVEST_RUNNING)->count(),
            'pending_contracts' => Invest::where('user_id', $user->id)->where('status', Status::INVEST_PENDING)->count(),
            'customers' => User::whereIn('id', function($query) use ($user) {
                $query->select('user_id')->from('invests')->where('user_id', $user->id)->distinct();
            })->count()
        ];
        
        // Get upcoming payments
        $today = Carbon::now();
        $alertPeriod = 30; // 30 days for alerts
        $alertDate = $today->copy()->addDays($alertPeriod);
        
        // Get interest payment alerts
        $interestAlerts = Invest::where('user_id', $user->id)
            ->where('status', Status::INVEST_RUNNING)
            ->whereNotNull('next_time')
            ->where('next_time', '<=', $alertDate)
            ->with(['project', 'user'])
            ->orderBy('next_time')
            ->limit(5)
            ->get();
            
        // Get maturity alerts    
        $maturityAlerts = Invest::where('user_id', $user->id)
            ->where('status', Status::INVEST_RUNNING)
            ->whereNotNull('project_closed')
            ->where('project_closed', '<=', $alertDate)
            ->with(['project', 'user'])
            ->orderBy('project_closed')
            ->limit(5)
            ->get();
            
        // Get recent contracts
        $recentContracts = Invest::where('user_id', $user->id)
            ->with(['project', 'user'])
            ->latest()
            ->limit(5)
            ->get();
        
        return view('user.staff.staff.dashboard', compact('pageTitle', 'user', 'stats', 'interestAlerts', 'maturityAlerts', 'recentContracts'));
    }
    
    /**
     * Display list of staff contracts
     */
    public function contracts()
    {
        $pageTitle = 'My Contracts';
        $user = Auth::user();
        
        $contracts = Invest::where('user_id', $user->id)
            ->with(['project', 'user'])
            ->latest()
            ->paginate(getPaginate());
            
        return view('user.staff.staff.contracts', compact('pageTitle', 'contracts'));
    }
    
    /**
     * Display contract details
     */
    public function contractDetails($id)
    {
        $user = Auth::user();
        
        $invest = Invest::where('id', $id)
            ->where('user_id', $user->id)
            ->with(['project', 'user'])
            ->firstOrFail();
            
        $pageTitle = 'Contract Details: ' . $invest->invest_no;
        
        return view('user.staff.staff.contract_details', compact('pageTitle', 'invest'));
    }
    
    /**
     * Display create contract form
     */
    public function createContract()
    {
        $pageTitle = 'Create New Contract';
        
        return view('user.staff.staff.create_contract', compact('pageTitle'));
    }
    
    /**
     * Store a new contract
     */
    public function storeContract(Request $request)
    {
        $request->validate([
            // Add validation rules based on your invest model
            'customer_id' => 'required',
            'project_id' => 'required',
            'amount' => 'required|numeric|min:0',
            // Add other necessary fields
        ]);
        
        // Logic to store a new contract
        // This is a simplified version
        $invest = new Invest();
        $invest->user_id = Auth::id(); // Set to staff member ID
        $invest->customer_id = $request->customer_id;
        $invest->project_id = $request->project_id;
        $invest->amount = $request->amount;
        $invest->status = Status::INVEST_PENDING; // Set as pending for manager approval
        // Set other fields as needed
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
            ->where('user_id', $user->id)
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
        
        $today = Carbon::now();
        $sixMonthsLater = $today->copy()->addMonths(6);
        
        // Get interest payment alerts for next 6 months
        $interestAlerts = Invest::where('user_id', $user->id)
            ->where('status', Status::INVEST_RUNNING)
            ->whereNotNull('next_time')
            ->where('next_time', '>=', $today)
            ->where('next_time', '<=', $sixMonthsLater)
            ->with(['project', 'user'])
            ->orderBy('next_time')
            ->get();
            
        // Get maturity alerts for next 6 months
        $maturityAlerts = Invest::where('user_id', $user->id)
            ->where('status', Status::INVEST_RUNNING)
            ->whereNotNull('project_closed')
            ->where('project_closed', '>=', $today)
            ->where('project_closed', '<=', $sixMonthsLater)
            ->with(['project', 'user'])
            ->orderBy('project_closed')
            ->get();
            
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
        
        foreach ($interestAlerts as $alert) {
            $paymentDate = Carbon::parse($alert->next_time);
            $monthKey = $paymentDate->format('Y-m');
            if (isset($monthlyAlerts[$monthKey])) {
                $monthlyAlerts[$monthKey]['interest_alerts'][] = $alert;
            }
        }
        
        foreach ($maturityAlerts as $alert) {
            $maturityDate = Carbon::parse($alert->project_closed);
            $monthKey = $maturityDate->format('Y-m');
            if (isset($monthlyAlerts[$monthKey])) {
                $monthlyAlerts[$monthKey]['maturity_alerts'][] = $alert;
            }
        }
        
        return view('user.staff.staff.alerts', compact('pageTitle', 'monthlyAlerts'));
    }
    
    /**
     * View customers
     */
    public function customers()
    {
        $pageTitle = 'My Customers';
        $user = Auth::user();
        
        $customers = User::whereIn('id', function($query) use ($user) {
            $query->select('user_id')->from('invests')->where('user_id', $user->id)->distinct();
        })->paginate(getPaginate());
        
        return view('user.staff.staff.customers', compact('pageTitle', 'customers'));
    }
} 