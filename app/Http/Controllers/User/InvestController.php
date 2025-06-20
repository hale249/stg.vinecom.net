<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Gateway\PaymentController;
use App\Models\Invest;
use App\Models\Project;
use App\Models\User;
use App\Traits\GlobalStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Transaction;

class InvestController extends Controller {
    use GlobalStatus;

    public function order(Request $request) {
        /*
         * Users can now invest in two different ways:
         * 1. By providing the number of units (previous behaviour – field `quantity`).
         * 2. By providing an arbitrary amount (new behaviour – field `amount`).
         * Either `quantity` **or** `amount` must be present. If both are present, `amount` will be
         * taken as the source of truth.
         */

        $rules = [
            'project_id'    => 'required|exists:projects,id',
            'payment_type'  => 'required|in:1,2',
            'referral_code' => 'nullable|exists:users,referral_code',
        ];

        // Conditional validation depending on which field the client sends
        if ($request->filled('amount')) {
            $rules['amount'] = 'numeric|min:0.01'; // Minimum 0.01 of currency
        } else {
            $rules['quantity'] = 'required|integer|min:1';
        }

        $request->validate($rules);

        $user = auth()->user();

        // Check KYC status
        if ($user->kv != Status::KYC_VERIFIED) {
            $notify[] = ['error', 'Vui lòng hoàn thành xác minh danh tính (KYC) trước khi đầu tư. <a href="' . route('user.kyc.form') . '" class="text-white">Nhấn vào đây để xác minh</a>'];
            return back()->withNotify($notify);
        }

        $project = Project::active()->find($request->project_id);
        if (!$project) {
            $notify[] = ['error', 'Project not found.'];
            return back()->withNotify($notify);
        }

        /*
         * ---------------------------------------------------------------------
         * Determine investment amount & quantity
         * ---------------------------------------------------------------------
         */

        // Calculate unit price based on total package and total units from database
        $totalPackage = $project->share_amount; // Tổng gói từ database
        $totalUnits = $project->share_count; // Tổng số đơn vị tối đa từ database
        $unitPrice = $totalPackage / $totalUnits; // Giá 1 đơn vị = Tổng gói / Số đơn vị

        // Fallback defaults – will be overwritten depending on investment mode
        $quantity        = 1;
        $totalPrice      = $unitPrice;
        $recurringAmount = 0;

        if ($request->filled('amount')) {
            // --- Investment by AMOUNT ---------------------------------------
            if ($request->amount < $unitPrice) {
                $notify[] = ['error', 'Số tiền đầu tư tối thiểu là ' . showAmount($unitPrice) . ' (1 đơn vị)'];
                return back()->withNotify($notify);
            }

            $totalPrice = getAmount($request->amount);
            $quantity = round($totalPrice / $unitPrice); // Calculate number of units

            // Calculate ROI amount based on percentage so profit scales with amount
            $projectRoiPercentage = getAmount($project->roi_percentage);
            $project->roi_amount  = ($totalPrice * $projectRoiPercentage) / 100; // Dynamically override for this investment

            // Recurring amount per payout period
            $recurringAmount = $project->roi_amount;

        } else {
            // --- Investment by QUANTITY ------------------------------------
            if ($request->quantity > $project->available_share) {
                $notify[] = ['error', 'Số lượng đơn vị không được vượt quá ' . $project->available_share . ' đơn vị còn lại.'];
                return back()->withNotify($notify);
            }

            $quantity        = (int) $request->quantity;
            $totalPrice      = $unitPrice * $quantity;
            $recurringAmount = ($quantity * $project->roi_amount);
        }

        $totalEarning = 0;
        $investClosed = null;
        $totalShare = $project->share_count;

        if ($project->return_type == Status::LIFETIME) {
            $totalEarning = 0;
            $investClosed = Carbon::parse($project->maturity_date)->addMonths($project->project_duration);
        } else if ($project->return_type == Status::REPEAT) {
            $totalEarning = $recurringAmount * $project->repeat_times;
        }

        $invest = new Invest();
        $invest->invest_no = getTrx();
        $invest->user_id = $user->id;
        $invest->project_id = $request->project_id;
        $invest->quantity = $quantity;
        $invest->unit_price = $unitPrice;
        $invest->total_price = $totalPrice;
        $invest->roi_percentage = $project->roi_percentage;
        $invest->roi_amount = $project->roi_amount;
        $invest->payment_type = $request->payment_type;
        $invest->total_earning = $totalEarning;
        $invest->total_share = $totalShare;
        $invest->capital_back = $project->capital_back;
        $invest->capital_status = Status::NO;
        $invest->return_type = $project->return_type;
        $invest->project_duration = $project->project_duration;
        $invest->project_closed = $investClosed ?? null;
        $invest->repeat_times = $project->repeat_times ?? 0;
        $invest->time_name = $project->time->name;
        $invest->hours = $project->time->hours;
        $invest->recurring_pay = $recurringAmount;
        $invest->contract_content = generateContractContent($project, $user);
        $invest->contract_confirmed = true;
        $invest->referral_code = $request->referral_code;
        $invest->save();

        // Redirect to contract confirmation page
        return redirect()->route('user.invest.contract', $invest->id);
    }

    public function showContract($id)
    {
        $invest = Invest::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $pageTitle = 'Investment Contract';
        $activeTemplate = activeTemplate();
        return view('templates.basic.user.invest.contract', compact('pageTitle', 'invest', 'activeTemplate'));
    }

    public function confirm($id)
    {
        $invest = Invest::where('id', $id)
            ->where('user_id', auth()->id())
            ->where('status', Status::INVEST_PENDING)
            ->firstOrFail();

        $user = auth()->user();
        
        // Check if user has enough balance
        if ($user->balance < $invest->total_price) {
            $notify[] = ['error', 'Số dư tài khoản không đủ để thực hiện đầu tư.'];
            return back()->withNotify($notify);
        }

        // Deduct balance
        $user->balance -= $invest->total_price;
        $user->save();

        // Create transaction record
        $transaction = new Transaction();
        $transaction->user_id = $user->id;
        $transaction->invest_id = $invest->id;
        $transaction->amount = $invest->total_price;
        $transaction->post_balance = $user->balance;
        $transaction->trx_type = '-';
        $transaction->details = 'Đầu tư vào dự án ' . $invest->project->title;
        $transaction->remark = 'payment';
        $transaction->trx = $invest->invest_no;
        $transaction->save();

        // Update investment status
        $invest->status = Status::INVEST_PENDING_ADMIN_REVIEW;
        $invest->save();

        notify($invest->user, 'INVEST_SUBMITTED_FOR_REVIEW', [
            'invest_id' => $invest->invest_no,
            'project_title' => $invest->project->title,
            'invest_amount' => showAmount($invest->total_price),
            'quantity' => $invest->quantity,
        ]);

        $notify[] = ['success', 'Đầu tư đã được gửi để xem xét.'];
        return redirect()->route('user.investment.contract')->withNotify($notify);
    }

    public function downloadContract($id)
    {
        $invest = Invest::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('templates.basic.user.invest.contract_pdf', [
            'invest' => $invest,
            'user' => auth()->user()
        ]);

        $pdf->setPaper('A4', 'portrait');
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', true);
        $pdf->setOption('defaultFont', 'DejaVu Sans');
        $pdf->setOption('encoding', 'UTF-8');

        return $pdf->download('contract-' . $invest->invest_no . '.pdf');
    }

    public function history()
    {
        $pageTitle = 'Investment History';
        $invests = Invest::where('user_id', auth()->id())
            ->with(['project'])
            ->latest()
            ->paginate(getPaginate());
        $activeTemplate = activeTemplate();
        $general = gs();
        return view('templates.basic.user.invest.history', compact('pageTitle', 'invests', 'activeTemplate', 'general'));
    }

    public function cancel($id)
    {
        $invest = Invest::where('id', $id)
            ->where('user_id', auth()->id())
            ->where('status', Status::INVEST_PENDING)
            ->firstOrFail();

        $invest->status = Status::INVEST_CANCELED;
        $invest->save();

        notify($invest->user, 'INVEST_CANCELED', [
            'invest_id' => $invest->invest_no,
            'project_title' => $invest->project->title,
            'invest_amount' => showAmount($invest->total_price),
            'quantity' => $invest->quantity,
        ]);

        $notify[] = ['success', 'Đầu tư đã được hủy thành công.'];
        return redirect()->route('user.investment.contract')->withNotify($notify);
    }

    public function downloadProfitSchedulePdf(Request $request)
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            abort(401, 'Vui lòng đăng nhập để xem bảng lãi dự kiến.');
        }

        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'amount'     => 'required|numeric|min:0.01',
        ]);

        $project = \App\Models\Project::findOrFail($request->project_id);
        $amount = getAmount($request->amount);
        $user = auth()->user();

        // Calculate unit price based on total package and total units from database
        $totalPackage = $project->share_amount; // Tổng gói từ database
        $totalUnits = $project->share_count; // Tổng số đơn vị tối đa từ database
        $unitPrice = $totalPackage / $totalUnits; // Giá 1 đơn vị = Tổng gói / Số đơn vị
        
        if ($amount < $unitPrice) {
            abort(400, 'Số tiền đầu tư không được nhỏ hơn mức tối thiểu ' . number_format($unitPrice, 0, ',', '.') . ' VNĐ (1 đơn vị).');
        }

        // Kiểm tra dự án có hợp lệ không
        if (!$project->maturity_time || $project->maturity_time <= 0) {
            abort(400, 'Dự án không có thông tin kỳ hạn hợp lệ.');
        }

        $schedule = [];
        $annualRate = $project->roi_percentage;
        $totalPeriods = (int) $project->maturity_time;
        $principal = $amount;
        $cumulativeInterest = 0;

        // Ngày ký hợp đồng là ngày hiện tại
        $contractDate = \Carbon\Carbon::now();
        
        // Ngày đáo hạn
        $maturityDate = $project->maturity_date ? \Carbon\Carbon::parse($project->maturity_date) : $contractDate->copy()->addMonths($totalPeriods);
        
        // Đảm bảo ngày đáo hạn không nhỏ hơn ngày hiện tại
        if ($maturityDate <= $contractDate) {
            $maturityDate = $contractDate->copy()->addMonths((int)$project->maturity_time);
        }

        // Tính toán các kỳ theo tháng
        $currentDate = $contractDate->copy();
        $i = 1;

        while ($currentDate->lte($maturityDate)) {
            // Ngày bắt đầu kỳ
            $periodStart = $currentDate->copy();
            
            // Ngày kết thúc kỳ: ngày bắt đầu + 1 tháng, trừ đi 1 ngày
            $periodEnd = $periodStart->copy()->addMonth()->subDay();
            
            // Nếu ngày kết thúc vượt quá ngày đáo hạn, thì đặt là ngày đáo hạn
            if ($periodEnd->gte($maturityDate)) {
                $periodEnd = $maturityDate->copy();
            }
            
            // Số ngày thực tế của kỳ này
            $daysInPeriod = $periodStart->diffInDaysFiltered(function ($date) {
                return true; // Đếm tất cả các ngày
            }, $periodEnd) + 1;
            
            // Tính lãi kỳ này (làm tròn đến 0 chữ số thập phân)
            $periodInterest = round(($principal * ($annualRate / 100 / 365)) * $daysInPeriod, 0);
            $cumulativeInterest += $periodInterest;

            $schedule[] = [
                'period_no' => 'Kỳ ' . $i,
                'start_date' => $periodStart->copy(),
                'end_date' => $periodEnd->copy(),
                'days' => (int) $daysInPeriod, // Đảm bảo là số nguyên
                'interest_rate' => $annualRate,
                'principal' => $principal,
                'period_interest' => $periodInterest,
                'pay_date' => $periodEnd->copy(),
                'principal_left' => $principal, // Nếu có tất toán gốc thì cập nhật ở đây
                'cumulative_total' => $principal + $cumulativeInterest, // Gốc + lãi cộng dồn
            ];
            
            // Cập nhật ngày bắt đầu cho kỳ tiếp theo (ngày kết thúc + 1 ngày)
            $currentDate = $periodEnd->copy()->addDay();
            $i++;

            // Dừng vòng lặp nếu ngày bắt đầu tiếp theo đã qua ngày đáo hạn
            if ($currentDate->gt($maturityDate) && $periodEnd->eq($maturityDate)) {
                break;
            }
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('templates.basic.user.invest.profit_schedule_pdf', [
            'schedule' => $schedule,
            'project' => $project,
            'user' => $user,
            'investment_amount' => $amount
        ]);

        $pdf->setPaper('A4', 'landscape');
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', true);
        $pdf->setOption('defaultFont', 'Times New Roman');

        return $pdf->stream('bang-lai-du-kien-' . slug($project->title) . '.pdf');
    }

    public function getProfitScheduleHtml(Request $request)
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            abort(401, 'Vui lòng đăng nhập để xem bảng lãi dự kiến.');
        }

        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'amount'     => 'required|numeric|min:0.01',
        ]);

        $project = \App\Models\Project::findOrFail($request->project_id);
        $amount = getAmount($request->amount);
        $user = auth()->user();

        // Calculate unit price based on total package and total units from database
        $totalPackage = $project->share_amount; // Tổng gói từ database
        $totalUnits = $project->share_count; // Tổng số đơn vị tối đa từ database
        $unitPrice = $totalPackage / $totalUnits; // Giá 1 đơn vị = Tổng gói / Số đơn vị
        
        if ($amount < $unitPrice) {
            abort(400, 'Số tiền đầu tư không được nhỏ hơn mức tối thiểu ' . number_format($unitPrice, 0, ',', '.') . ' VNĐ (1 đơn vị).');
        }

        // Kiểm tra dự án có hợp lệ không
        if (!$project->maturity_time || $project->maturity_time <= 0) {
            abort(400, 'Dự án không có thông tin kỳ hạn hợp lệ.');
        }

        $schedule = [];
        $annualRate = $project->roi_percentage;
        $totalPeriods = (int) $project->maturity_time;
        $principal = $amount;
        $cumulativeInterest = 0;

        // Ngày ký hợp đồng là ngày hiện tại
        $contractDate = \Carbon\Carbon::now();
        
        // Ngày đáo hạn
        $maturityDate = $project->maturity_date ? \Carbon\Carbon::parse($project->maturity_date) : $contractDate->copy()->addMonths($totalPeriods);
        
        // Đảm bảo ngày đáo hạn không nhỏ hơn ngày hiện tại
        if ($maturityDate <= $contractDate) {
            $maturityDate = $contractDate->copy()->addMonths((int)$project->maturity_time);
        }

        // Tính toán các kỳ theo tháng
        $currentDate = $contractDate->copy();
        $i = 1;

        while ($currentDate->lte($maturityDate)) {
            // Ngày bắt đầu kỳ
            $periodStart = $currentDate->copy();
            
            // Ngày kết thúc kỳ: ngày bắt đầu + 1 tháng, trừ đi 1 ngày
            $periodEnd = $periodStart->copy()->addMonth()->subDay();
            
            // Nếu ngày kết thúc vượt quá ngày đáo hạn, thì đặt là ngày đáo hạn
            if ($periodEnd->gte($maturityDate)) {
                $periodEnd = $maturityDate->copy();
            }
            
            // Số ngày thực tế của kỳ này
            $daysInPeriod = $periodStart->diffInDaysFiltered(function ($date) {
                return true; // Đếm tất cả các ngày
            }, $periodEnd) + 1;
            
            // Tính lãi kỳ này (làm tròn đến 0 chữ số thập phân)
            $periodInterest = round(($principal * ($annualRate / 100 / 365)) * $daysInPeriod, 0);
            $cumulativeInterest += $periodInterest;

            $schedule[] = [
                'period_no' => 'Kỳ ' . $i,
                'start_date' => $periodStart->copy(),
                'end_date' => $periodEnd->copy(),
                'days' => (int) $daysInPeriod, // Đảm bảo là số nguyên
                'interest_rate' => $annualRate,
                'principal' => $principal,
                'period_interest' => $periodInterest,
                'pay_date' => $periodEnd->copy(),
                'principal_left' => $principal, // Nếu có tất toán gốc thì cập nhật ở đây
                'cumulative_total' => $principal + $cumulativeInterest, // Gốc + lãi cộng dồn
            ];
            
            // Cập nhật ngày bắt đầu cho kỳ tiếp theo (ngày kết thúc + 1 ngày)
            $currentDate = $periodEnd->copy()->addDay();
            $i++;

            // Dừng vòng lặp nếu ngày bắt đầu tiếp theo đã qua ngày đáo hạn
            if ($currentDate->gt($maturityDate) && $periodEnd->eq($maturityDate)) {
                break;
            }
        }

        return view('templates.basic.user.invest.profit_schedule_modal', [
            'schedule' => $schedule,
            'project' => $project,
            'user' => $user,
            'investment_amount' => $amount
        ]);
    }
}
