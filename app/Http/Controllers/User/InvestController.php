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

class InvestController extends Controller {
    use GlobalStatus;

    public function order(Request $request) {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'quantity' => 'required|integer|min:1',
            'payment_type' => 'required|in:1,2',
            'referral_code' => 'nullable|exists:users,referral_code'
        ]);

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

        if ($request->quantity > $project->available_share) {
            $notify[] = ['error', 'Not enough shares available.'];
            return back()->withNotify($notify);
        }

        $unitPrice = $project->share_amount;
        $totalPrice = $unitPrice * $request->quantity;
        $recurringAmount = ($request->quantity * $project->roi_amount);
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
        $invest->quantity = $request->quantity;
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

        $invest->status = Status::INVEST_PENDING_ADMIN_REVIEW;
        $invest->save();

        notify($invest->user, 'INVEST_SUBMITTED_FOR_REVIEW', [
            'invest_id' => $invest->invest_no,
            'project_title' => $invest->project->title,
            'invest_amount' => showAmount($invest->total_price),
            'quantity' => $invest->quantity,
        ]);

        $notify[] = ['success', 'Investment submitted for admin review successfully.'];
        return redirect()->route('user.invest.history')->withNotify($notify);
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
}
