<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\FormProcessor;
use App\Lib\GoogleAuthenticator;
use App\Models\DeviceToken;
use App\Models\Form;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use App\Models\Invest;

class UserController extends Controller
{
    public function projects()
    {
        $pageTitle = 'My Projects';
        $invests = auth()->user()->invests()
            ->with(['project', 'project.time'])
            ->searchable(['project:title'])
            ->latest()
            ->paginate(getPaginate());


        return view('Template::user.projects', compact('pageTitle', 'invests'));
    }

    public function projectsTransactions($id)
    {
        $pageTitle = 'Project Transactions';
        $transactions = Transaction::where('invest_id', $id)->where('user_id', auth()->user()->id)->with(['invest'])->orderBy('id', 'desc')->paginate(getPaginate());
        return view('Template::user.projects_transaction', compact('pageTitle', 'transactions'));
    }

    public function investmentContract()
    {
        $pageTitle = 'Investment Contract';
        $user = auth()->user();
        $invests = $user->invests()
            ->with(['project'])
            ->latest()
            ->paginate(getPaginate());
        $general = gs();
        return view('Template::user.investment.contract', compact('pageTitle', 'invests', 'general'));
    }

    public function home()
    {
        $pageTitle = 'User Dashboard';
        $user = auth()->user();
        $transactions = Transaction::where('user_id', $user->id)->orderBy('id', 'desc')->take(5)->get();

        $allInvests = Invest::where('user_id', $user->id);
        $runningInvests = (clone $allInvests)->where('status', Status::INVEST_RUNNING);
        $completedInvests = (clone $allInvests)->where('status', Status::INVEST_COMPLETED);

        $general = gs();

        session()->put('user', [
            'user_id' => $user->id,
            'all_invests_count' => $allInvests->count(),
            'running_invests_count' => $runningInvests->count(),
            'completed_invests_count' => $completedInvests->count(),
            'unique_projects' => $allInvests->pluck('project_id')->unique()->count()
        ]);

        // Calculate projected future profits (keep this for "Tổng lợi tức theo giá trị HĐ")
        $projectedEarnings = $this->calculateTotalProjectedEarnings($user->id);
        
        // Calculate actual received profits (for balance calculation)
        $actualProfits = Transaction::where('user_id', $user->id)
            ->where('trx_type', '+')
            ->where('remark', 'profit')
            ->sum('amount');
        
        // Calculate total investment only for accepted and running investments
        $totalInvestment = $user->invests()
            ->where('status', Status::INVEST_RUNNING)
            ->sum('total_price');
        
        $investData = [
            'completed' => $user->invests()->completed()->count(),
            'total_invest' => $totalInvestment, // Only count running investments
            'total_earning' => $projectedEarnings, // Keep this as projected earnings for "Tổng lợi tức theo giá trị HĐ"
            'actual_profits' => $actualProfits, // Add this for reference
            'invest_count' => $user->invests()->where('status', Status::INVEST_RUNNING)->select('project_id')->distinct()->count(),
            'total_deposit' => $user->deposits()->directDeposit()->sum('amount'),
            'total_withdraw' => $user->withdrawals()->approved()->sum('amount')
        ];
        
        // Debug output for investData
        \Log::info('User Dashboard investData:', $investData);

        return view('Template::user.dashboard', [
            'pageTitle' => $pageTitle,
            'user' => $user,
            'invests' => $user->invests()->latest()->paginate(getPaginate(5)),
            'investData' => $investData,
            'transactions' => $transactions
        ]);
    }

    public function transactions()
    {
        $pageTitle = 'Transactions';
        $remarks = Transaction::distinct('remark')->orderBy('remark')->get('remark');
        $transactions = Transaction::where('user_id', auth()->id())
            ->searchable(['trx'])
            ->filter(['trx_type', 'remark'])
            ->orderBy('id', 'desc')
            ->paginate(getPaginate());

        return view('Template::user.transactions', compact('pageTitle', 'transactions', 'remarks'));
    }

    public function depositHistory(Request $request)
    {
        $pageTitle = 'Deposit History';
        $deposits = auth()->user()->deposits()
            ->searchable(['trx'])
            ->with(['gateway'])
            ->orderBy('id', 'desc')
            ->paginate(getPaginate());
        return view('Template::user.deposit_history', compact('pageTitle', 'deposits'));
    }

    public function show2faForm()
    {
        $ga = new GoogleAuthenticator();
        $user = auth()->user();
        $secret = $ga->createSecret();
        $qrCodeUrl = $ga->getQRCodeGoogleUrl($user->username . '@' . gs('site_name'), $secret);
        $pageTitle = '2FA Security';
        return view('Template::user.twofactor', compact('pageTitle', 'secret', 'qrCodeUrl'));
    }

    public function create2fa(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'key' => 'required',
            'code' => 'required',
        ]);

        $response = verifyG2fa($user, $request->code, $request->key);

        if ($response) {
            $user->tsc = $request->key;
            $user->ts = Status::ENABLE;
            $user->save();

            $notify[] = ['success', 'Two factor authenticator activated successfully'];
            return back()->withNotify($notify);
        } else {
            $notify[] = ['error', 'Wrong verification code'];
            return back()->withNotify($notify);
        }
    }

    public function disable2fa(Request $request)
    {
        $request->validate([
            'code' => 'required',
        ]);

        $user = auth()->user();
        $response = verifyG2fa($user, $request->code);
        if ($response) {
            $user->tsc = null;
            $user->ts = Status::DISABLE;
            $user->save();
            $notify[] = ['success', 'Two factor authenticator deactivated successfully'];
        } else {
            $notify[] = ['error', 'Wrong verification code'];
        }
        return back()->withNotify($notify);
    }

    public function kycForm()
    {
        if (auth()->user()->kv == Status::KYC_PENDING) {
            $notify[] = ['error', 'Your KYC is under review'];
            return to_route('user.home')->withNotify($notify);
        }
        if (auth()->user()->kv == Status::KYC_VERIFIED) {
            $notify[] = ['error', 'You are already KYC verified'];
            return to_route('user.home')->withNotify($notify);
        }
        $pageTitle = 'KYC Form';
        $form = Form::where('act', 'kyc')->first();
        return view('Template::user.kyc.form', compact('pageTitle', 'form'));
    }

    public function kycData()
    {
        $user = auth()->user();
        $pageTitle = 'KYC Data';
        abort_if($user->kv == Status::VERIFIED, 403);
        return view('Template::user.kyc.info', compact('pageTitle', 'user'));
    }

    public function kycSubmit(Request $request)
    {
        $form = Form::where('act', 'kyc')->firstOrFail();
        $formData = $form->form_data;
        $formProcessor = new FormProcessor();
        $validationRule = $formProcessor->valueValidation($formData);
        $request->validate($validationRule);
        $user = auth()->user();
        foreach (@$user->kyc_data ?? [] as $kycData) {
            if ($kycData->type == 'file') {
                fileManager()->removeFile(getFilePath('verify') . '/' . $kycData->value);
            }
        }
        $userData = $formProcessor->processFormData($request, $formData);
        $user->kyc_data = $userData;
        $user->kyc_rejection_reason = null;
        $user->kv = Status::KYC_PENDING;
        $user->save();

        $notify[] = ['success', 'KYC data submitted successfully'];
        return to_route('user.home')->withNotify($notify);
    }

    public function userData()
    {
        $user = auth()->user();

        if ($user->profile_complete == Status::YES) {
            return to_route('user.home');
        }

        $pageTitle = 'User Data';
        $info = json_decode(json_encode(getIpInfo()), true);
        $mobileCode = @implode(',', $info['code']);
        $countries = json_decode(file_get_contents(resource_path('views/partials/country.json')));

        return view('Template::user.user_data', compact('pageTitle', 'user', 'countries', 'mobileCode'));
    }

    public function userDataSubmit(Request $request)
    {
        $user = auth()->user();

        if ($user->profile_complete == Status::YES) {
            return to_route('user.home');
        }

        $countryData = (array)json_decode(file_get_contents(resource_path('views/partials/country.json')));
        $countryCodes = implode(',', array_keys($countryData));
        $mobileCodes = implode(',', array_column($countryData, 'dial_code'));
        $countries = implode(',', array_column($countryData, 'country'));

        $request->validate([
            'country_code' => 'required|in:' . $countryCodes,
            'country' => 'required|in:' . $countries,
            'mobile_code' => 'required|in:' . $mobileCodes,
            'username' => 'required|unique:users|min:6',
            'mobile' => ['required', 'regex:/^([0-9]*)$/', Rule::unique('users')->where('dial_code', $request->mobile_code)],
            'referral_code' => [
                'required',
                Rule::exists('users', 'referral_code')->where('is_staff', true)
            ],
        ], [
            'referral_code.required' => 'Mã giới thiệu là bắt buộc',
            'referral_code.exists' => 'Mã giới thiệu không hợp lệ hoặc không phải của nhân viên/quản lý'
        ]);

        if (preg_match("/[^a-z0-9_]/", trim($request->username))) {
            $notify[] = ['info', 'Username can contain only small letters, numbers and underscore.'];
            $notify[] = ['error', 'No special character, space or capital letters in username.'];
            return back()->withNotify($notify)->withInput($request->all());
        }

        $user->country_code = $request->country_code;
        $user->mobile = $request->mobile;
        $user->username = $request->username;
        $user->address = $request->address;
        $user->city = $request->city;
        $user->state = $request->state;
        $user->zip = $request->zip;
        $user->country_name = @$request->country;
        $user->dial_code = $request->mobile_code;
        $user->referred_by = $request->referral_code;

        $user->profile_complete = Status::YES;
        $user->save();

        return to_route('user.home');
    }

    public function checkReferralCode(Request $request)
    {
        if (!$request->has('referral_code') || empty($request->referral_code)) {
            return response()->json([
                'success' => false,
                'message' => 'Referral code is required'
            ]);
        }

        $referralCode = $request->input('referral_code');

        $referrer = User::where('referral_code', $referralCode)
                       ->where('is_staff', true)
                       ->first();

        if ($referrer) {
            return response()->json([
                'success' => true,
                'referrer_name' => $referrer->fullname,
                'referrer_username' => $referrer->username
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Mã giới thiệu không hợp lệ hoặc không phải của nhân viên/quản lý'
            ]);
        }
    }

    public function addDeviceToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
        ]);

        if ($validator->fails()) {
            return ['success' => false, 'errors' => $validator->errors()->all()];
        }

        $deviceToken = DeviceToken::where('token', $request->token)->first();

        if ($deviceToken) {
            return ['success' => true, 'message' => 'Already exists'];
        }

        $deviceToken = new DeviceToken();
        $deviceToken->user_id = auth()->user()->id;
        $deviceToken->token = $request->token;
        $deviceToken->is_app = Status::NO;
        $deviceToken->save();

        return ['success' => true, 'message' => 'Token saved successfully'];
    }

    public function downloadAttachment($fileHash)
    {
        $filePath = decrypt($fileHash);
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $title = slug(gs('site_name')) . '- attachments.' . $extension;
        try {
            $mimetype = mime_content_type($filePath);
        } catch (\Exception $e) {
            $notify[] = ['error', 'File does not exists'];
            return back()->withNotify($notify);
        }
        header('Content-Disposition: attachment; filename="' . $title);
        header("Content-Type: " . $mimetype);
        return readfile($filePath);
    }

    /**
     * Calculate total projected earnings at maturity for all user's investments
     * 
     * @param int $userId
     * @return float
     */
    private function calculateTotalProjectedEarnings($userId)
    {
        try {
            $totalProjectedEarnings = 0;
            
            // Get all running investments with their projects
            $investments = Invest::where('user_id', $userId)
                ->where('status', Status::INVEST_RUNNING)
                ->with('project') // Eager load the project
                ->get();
            
            if ($investments->isEmpty()) {
                return 0;
            }
            
            foreach ($investments as $invest) {
                $project = $invest->project;
                
                if (!$project) {
                    continue;
                }
                
                // Get investment amount
                $amount = $invest->total_price;
                
                // Get project ROI percentage
                $roiPercentage = $project->roi_percentage;
                
                // Get term in months (maturity_time)
                $termMonths = $project->maturity_time ?: 5; // Default to 5 months if not set
                
                // Calculate annual ROI
                $annualROI = ($amount * $roiPercentage / 100);
                
                // Calculate monthly ROI (annual ROI divided by 12)
                $monthlyROI = $annualROI / 12;
                
                // Calculate total projected earnings (monthly ROI * term months)
                $projectedEarning = $monthlyROI * $termMonths;
                
                $totalProjectedEarnings += $projectedEarning;
            }
            
            return $totalProjectedEarnings;
        } catch (\Exception $e) {
            // Log the error
            \Log::error("Error calculating projected earnings: " . $e->getMessage());
            
            // Fallback: return 0
            return 0;
        }
    }
}
