<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ContractRevenueExport;
use App\Http\Controllers\Controller;
use App\Models\Invest;
use App\Models\NotificationLog;
use App\Models\Transaction;
use App\Models\UserLogin;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function transaction(Request $request, $userId = null)
    {
        $pageTitle = 'Transaction Logs';

        $remarks = Transaction::distinct('remark')->orderBy('remark')->get('remark');

        $transactions = Transaction::searchable(['trx', 'user:username'])->filter(['trx_type', 'remark'])->dateFilter()->orderBy('id', 'desc')->with('user');
        if ($userId) {
            $transactions = $transactions->where('user_id', $userId);
        }
        $transactions = $transactions->paginate(getPaginate());

        return view('admin.reports.transactions', compact('pageTitle', 'transactions', 'remarks'));
    }

    public function loginHistory(Request $request)
    {
        $pageTitle = 'User Login History';
        $loginLogs = UserLogin::orderBy('id', 'desc')->searchable(['user:username'])->dateFilter()->with('user')->paginate(getPaginate());
        return view('admin.reports.logins', compact('pageTitle', 'loginLogs'));
    }

    public function loginIpHistory($ip)
    {
        $pageTitle = 'Login by - ' . $ip;
        $loginLogs = UserLogin::where('user_ip', $ip)->orderBy('id', 'desc')->with('user')->paginate(getPaginate());
        return view('admin.reports.logins', compact('pageTitle', 'loginLogs', 'ip'));
    }

    public function notificationHistory(Request $request)
    {
        $pageTitle = 'Notification History';
        $logs = NotificationLog::orderBy('id', 'desc')->searchable(['user:username'])->dateFilter()->with('user')->paginate(getPaginate());
        return view('admin.reports.notification_history', compact('pageTitle', 'logs'));
    }

    public function emailDetails($id)
    {
        $pageTitle = 'Email Details';
        $email = NotificationLog::findOrFail($id);
        return view('admin.reports.email_details', compact('pageTitle', 'email'));
    }

    public function investHistory(Request $request)
    {
        $pageTitle = 'Invest History';
        $invests = Invest::with('project', 'user')->searchable(['project:title', 'user:username,firstname,lastname', 'invest_no']);

        if ($request->type == 'lifetime') {
            $invests = $invests->where('period', -1);
        } elseif ($request->type == 'repeat') {
            $invests = $invests->where('period', '>', 0);
        }

        $invests = $invests->filter(['status'])->dateFilter();

        $allInvest = clone $invests;
        $totalInvestCount = $allInvest->count();
        $totalInvestAmount = $allInvest->sum('total_price');
        $totalPaid = $allInvest->sum('total_earning');

        $invests = $invests->orderBy('id', 'desc')->paginate(getPaginate());

        return view('admin.reports.invest_history', compact('pageTitle', 'invests', 'totalInvestCount', 'totalInvestAmount', 'totalPaid'));
    }

    public function contractRevenue(Request $request)
    {
        $pageTitle = 'Doanh số theo hợp đồng';
        
        // Get contracts with their revenue data
        $contracts = Invest::with('project', 'user')
            ->select('id', 'invest_no', 'user_id', 'project_id', 'total_price', 'roi_amount', 'quantity', 'unit_price', 'total_earning', 'status', 'created_at')
            ->searchable(['project:title', 'user:username,firstname,lastname', 'invest_no']);
            
        // Apply filters if provided
        if ($request->has('status') && $request->status !== '') {
            $contracts = $contracts->where('status', $request->status);
        }
        
        if ($request->date) {
            $date = explode('-', $request->date);
            $startDate = trim($date[0]);
            $endDate = trim($date[1]);
            
            $contracts = $contracts->whereDate('created_at', '>=', $startDate)
                ->whereDate('created_at', '<=', $endDate);
        }
        
        // Get totals for summary - only count active contracts (status=2)
        $activeContracts = clone $contracts;
        $activeContracts = $activeContracts->where('status', 2); // Status 2 = INVEST_RUNNING
        
        $totalContractCount = $activeContracts->count();
        $totalContractAmount = $activeContracts->sum('total_price');
        $totalEarnings = $activeContracts->sum('total_earning');
        
        // Check if export to Excel is requested
        if ($request->export == 'excel') {
            try {
                $allContracts = $contracts->get();
                $dateRangeText = $request->date ?? 'Tất cả';
                
                return Excel::download(
                    new ContractRevenueExport(
                        $allContracts, 
                        $totalContractCount, 
                        $totalContractAmount, 
                        $totalEarnings,
                        $dateRangeText,
                        $request->status
                    ), 
                    'doanh-so-hop-dong-' . now()->format('d-m-Y') . '.xlsx'
                );
            } catch (\Exception $e) {
                $notify[] = ['error', 'Có lỗi khi xuất Excel: ' . $e->getMessage()];
                return back()->withNotify($notify);
            }
        }
        
        // Paginate results - show all contracts in the table based on filters
        $contracts = $contracts->orderBy('id', 'desc')->paginate(getPaginate());
        
        // Get general settings
        $general = gs();
        
        return view('admin.reports.contract_revenue', compact('pageTitle', 'contracts', 'totalContractCount', 'totalContractAmount', 'totalEarnings', 'general'));
    }
}
