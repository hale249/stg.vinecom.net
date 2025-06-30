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

        $rules = [
            'project_id'    => 'required|exists:projects,id',
            'referral_code' => 'nullable|exists:users,referral_code',
            'months'        => 'nullable|integer|min:1',
        ];

        if ($request->filled('amount')) {
            $rules['amount'] = 'numeric|min:0.01'; 
        } else {
            $rules['quantity'] = 'required|integer|min:1';
        }

        $request->validate($rules);

        $user = auth()->user();

        if ($user->kv != Status::KYC_VERIFIED) {
            $notify[] = ['error', 'Vui lòng hoàn thành xác minh danh tính (KYC) trước khi đầu tư. <a href="' . route('user.kyc.form') . '" class="text-white">Nhấn vào đây để xác minh</a>'];
            return back()->withNotify($notify);
        }

        $project = Project::active()->find($request->project_id);
        if (!$project) {
            $notify[] = ['error', 'Project not found.'];
            return back()->withNotify($notify);
        }

        // Determine the minimum investment amount based on the project configuration
        // If min_invest_amount is null or 0, fall back to share_amount (unit price)
        $minInvestAmount = ($project->min_invest_amount > 0) 
            ? $project->min_invest_amount 
            : $project->share_amount;
            
        $quantity        = 1; 
        $totalPrice      = 0;
        $recurringAmount = 0;

        if ($request->filled('amount')) {
            // Validate minimum investment amount
            if ($request->amount < $minInvestAmount) {
                if ($project->min_invest_amount > 0) {
                    $notify[] = ['error', 'Số tiền đầu tư tối thiểu là ' . showAmount($minInvestAmount)];
                } else {
                    $notify[] = ['error', 'Số tiền đầu tư tối thiểu là ' . showAmount($minInvestAmount) . ' (1 đơn vị)'];
                }
                return back()->withNotify($notify);
            }

            $totalPrice = getAmount($request->amount);
            $projectRoiPercentage = getAmount($project->roi_percentage);
            
            // Calculate annual ROI
            $annualROI = ($totalPrice * $projectRoiPercentage / 100);
            
            // Calculate monthly ROI (annual ROI divided by 12)
            $recurringAmount = round($annualROI / 12, 0);

        } else {
            // This is for quantity-based investments (if that feature is still supported in your UI)
            // Calculate based on minimum investment amount or share amount
            $quantity        = (int) $request->quantity;
            $totalPrice      = $minInvestAmount * $quantity;
            $projectRoiPercentage = getAmount($project->roi_percentage);
            
            // Calculate annual ROI
            $annualROI = ($totalPrice * $projectRoiPercentage / 100);
            
            // Calculate monthly ROI (annual ROI divided by 12)
            $recurringAmount = round($annualROI / 12, 0);
            
            if ((int) $request->quantity > $project->available_share) {
                $notify[] = ['error', 'Số lượng đơn vị không được vượt quá ' . $project->available_share . ' đơn vị còn lại.'];
                return back()->withNotify($notify);
            }
        }

        $totalEarning = 0;
        $investClosed = null;
        $totalShare = $project->share_count;

        // Make sure months is properly parsed as an integer
        $selectedDuration = $request->filled('months') ? (int)$request->months : (int)$project->project_duration;
        
        // Ensure the duration is valid (not zero or negative)
        if ($selectedDuration <= 0) {
            $selectedDuration = (int)$project->project_duration;
        }

        if ($project->return_type == Status::LIFETIME) {
            $totalEarning = 0;
            $investClosed = Carbon::parse($project->maturity_date)->addMonths($selectedDuration);
        } else if ($project->return_type == Status::REPEAT) {
            $totalEarning = $recurringAmount * $project->repeat_times;
        }

        $invest = new Invest();
        $invest->invest_no = generateContractNumber();
        $invest->user_id = $user->id;
        $invest->project_id = $request->project_id;
        $invest->quantity = $quantity;
        $invest->unit_price = $minInvestAmount;
        $invest->total_price = $totalPrice;
        $invest->roi_percentage = $projectRoiPercentage;
        $invest->roi_amount = $recurringAmount;
        $invest->payment_status = Status::PAYMENT_PENDING;
        $invest->total_earning = $totalEarning;
        $invest->total_share = $totalShare;
        $invest->capital_back = $project->capital_back;
        $invest->capital_status = Status::NO;
        $invest->return_type = $project->return_type;
        $invest->project_duration = (int)$selectedDuration;
        $invest->project_closed = $investClosed ?? null;
        $invest->repeat_times = $project->repeat_times ?? 0;
        $invest->time_name = $project->time->name ?? 'Tháng';
        $invest->hours = $project->time->hours ?? 24;
        $invest->recurring_pay = $recurringAmount;
        $invest->contract_content = generateContractContent($project, $user, $invest->invest_no, $invest->status);
        $invest->contract_confirmed = true;
        $invest->referral_code = $request->referral_code;
        $invest->save();

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
        
        // Update investment status
        $invest->status = Status::INVEST_PENDING_ADMIN_REVIEW;
        $invest->save();

        // Refresh contract content to add watermark
        refreshContractContent($invest);

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
            'user' => auth()->user(),
            'status' => $invest->status
        ]);

        $pdf->setPaper('A4', 'portrait');
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', true);
        $pdf->setOption('defaultFont', 'Times New Roman');
        $pdf->setOption('encoding', 'UTF-8');

        return $pdf->download('contract-' . $invest->invest_no . '.pdf');
    }

    public function viewContractWithWatermark($id)
    {
        $invest = Invest::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        // Generate contract content with current status (including watermark if needed)
        $contractContent = generateContractContent($invest->project, $invest->user, $invest->invest_no, $invest->status, true);

        $pageTitle = 'Investment Contract';
        $activeTemplate = activeTemplate();
        return view('templates.basic.user.invest.contract_with_watermark', compact('pageTitle', 'invest', 'activeTemplate', 'contractContent'));
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
            'months'     => 'nullable|integer|min:1',
        ]);

        $project = \App\Models\Project::findOrFail($request->project_id);
        $amount = getAmount($request->amount);
        $user = auth()->user();

        $minInvestAmount = $project->min_invest_amount ?? $project->share_amount;
        $minInvestAmount = (float) $minInvestAmount;
        if ($amount < $minInvestAmount) {
            abort(400, 'Số tiền đầu tư không được nhỏ hơn mức tối thiểu ' . showAmount($minInvestAmount) . '.');
        }

        // Kiểm tra dự án có hợp lệ không
        if (!$project->maturity_time || $project->maturity_time <= 0) {
            // Sử dụng giá trị mặc định 12 tháng nếu không có thông tin kỳ hạn
            $project->maturity_time = 12;
        }

        $schedule = [];
        $annualRate = $project->roi_percentage;
        $totalPeriods = $request->months ? (int) $request->months : (int) $project->maturity_time;
        $principal = $amount;
        $cumulativeInterest = 0;

        // Calculate monthly ROI using the correct formula
        $annualROI = ($principal * $annualRate / 100);
        $monthlyROI = round($annualROI / 12, 0);

        // Ngày ký hợp đồng là ngày hiện tại
        $contractDate = \Carbon\Carbon::now();
        
        // Ngày đáo hạn
        $maturityDate = $contractDate->copy()->addMonths($totalPeriods);
        
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
            
            // Sử dụng ROI hàng tháng cố định thay vì tính theo ngày
            $periodInterest = $monthlyROI;
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
        // Bỏ kiểm tra đăng nhập, cho phép cả khách xem
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'amount'     => 'required|numeric|min:0.01',
            'months'     => 'nullable|integer|min:1',
        ]);

        $project = \App\Models\Project::findOrFail($request->project_id);
        $amount = getAmount($request->amount);
        $user = auth()->check() ? auth()->user() : null;

        $minInvestAmount = $project->min_invest_amount ?? $project->share_amount;
        $minInvestAmount = (float) $minInvestAmount;
        if ($amount < $minInvestAmount) {
            abort(400, 'Số tiền đầu tư không được nhỏ hơn mức tối thiểu ' . showAmount($minInvestAmount) . '.');
        }

        // Kiểm tra dự án có hợp lệ không
        if (!$project->maturity_time || $project->maturity_time <= 0) {
            // Sử dụng giá trị mặc định 12 tháng nếu không có thông tin kỳ hạn
            $project->maturity_time = 12;
        }

        $schedule = [];
        $annualRate = $project->roi_percentage;
        $totalPeriods = $request->months ? (int) $request->months : (int) $project->maturity_time;
        $principal = $amount;
        $cumulativeInterest = 0;

        // Calculate monthly ROI using the correct formula
        $annualROI = ($principal * $annualRate / 100);
        $monthlyROI = round($annualROI / 12, 0);

        // Ngày ký hợp đồng là ngày hiện tại
        $contractDate = \Carbon\Carbon::now();
        
        // Ngày đáo hạn
        $maturityDate = $contractDate->copy()->addMonths($totalPeriods);
        
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
            
            // Sử dụng ROI hàng tháng cố định thay vì tính theo ngày
            $periodInterest = $monthlyROI;
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
