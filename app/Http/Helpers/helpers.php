<?php

use App\Constants\Status;
use App\Lib\Captcha;
use App\Lib\ClientInfo;
use App\Lib\CurlRequest;
use App\Lib\FileManager;
use App\Lib\GoogleAuthenticator;
use App\Models\Extension;
use App\Models\Frontend;
use App\Models\GeneralSetting;
use App\Models\Language;
use App\Notify\Notify;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

function systemDetails() {
    $system['name']          = 'Bắc Hải Group';
    $system['version']       = '1.0';
    $system['build_version'] = '5.0.10';
    return $system;
}

function slug($string) {
    return Str::slug($string);
}

function verificationCode($length) {
    if ($length == 0) {
        return 0;
    }

    $min = pow(10, $length - 1);
    $max = (int) ($min - 1) . '9';
    return random_int($min, $max);
}

function getNumber($length = 8) {
    $characters       = '1234567890';
    $charactersLength = strlen($characters);
    $randomString     = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function activeTemplate($asset = false) {
    $template = session('template') ?? gs('active_template');
    if ($asset) {
        return 'assets/templates/' . $template . '/';
    }

    return 'templates.' . $template . '.';
}

function activeTemplateName() {
    $template = session('template') ?? gs('active_template');
    return $template;
}

function siteLogo($type = null) {
    $name = $type ? "/logo_$type.png" : '/logo.png';
    return getImage(getFilePath('logoIcon') . $name);
}

function siteFavicon() {
    return getImage(getFilePath('logoIcon') . '/favicon.png');
}

function loadReCaptcha() {
    return Captcha::reCaptcha();
}

function loadCustomCaptcha($width = '100%', $height = 46, $bgColor = '#003') {
    return Captcha::customCaptcha($width, $height, $bgColor);
}

function verifyCaptcha() {
    return Captcha::verify();
}

function loadExtension($key) {
    $extension = Extension::where('act', $key)->where('status', Status::ENABLE)->first();
    return $extension ? $extension->generateScript() : '';
}

function getTrx($length = 12) {
    $characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789';
    $charactersLength = strlen($characters);
    $randomString     = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function getAmount($amount, $length = 2) {
    $amount = round($amount ?? 0, $length);
    return $amount + 0;
}

function showAmount($amount, $decimal = 0, $separate = true, $exceptZeros = false, $currencyFormat = true) {
    $separator = '';
    if ($separate) {
        $separator = ',';
    }
    
    // Convert to float and round to remove any floating point precision issues
    $amount = (float) $amount;
    $amount = round($amount, $decimal);
    
    // Format the number with the specified decimal places
    $printAmount = number_format($amount, $decimal, '.', $separator);
    
    // Remove trailing zeros after decimal point if exceptZeros is true
    if ($exceptZeros) {
        $printAmount = rtrim(rtrim($printAmount, '0'), '.');
    }
    
    if ($currencyFormat) {
        if (gs('currency_format') == Status::CUR_BOTH) {
            return $printAmount . ' ' . __(gs('cur_text')) . ' ' . gs('cur_sym');
        } else if (gs('currency_format') == Status::CUR_TEXT) {
            return $printAmount . ' ' . __(gs('cur_text'));
        } else {
            return $printAmount . ' ' . gs('cur_sym');
        }
    }
    return $printAmount;
}

function removeElement($array, $value) {
    return array_diff($array, (is_array($value) ? $value : array($value)));
}

function cryptoQR($wallet) {
    return "https://api.qrserver.com/v1/create-qr-code/?data=$wallet&size=300x300&ecc=m";
}

function keyToTitle($text) {
    return ucfirst(preg_replace("/[^A-Za-z0-9 ]/", ' ', $text));
}

function titleToKey($text) {
    return strtolower(str_replace(' ', '_', $text));
}

function strLimit($title = null, $length = 10) {
    return Str::limit($title, $length);
}

function getIpInfo() {
    $ipInfo = ClientInfo::ipInfo();
    return $ipInfo;
}

function osBrowser() {
    $osBrowser = ClientInfo::osBrowser();
    return $osBrowser;
}

function getTemplates() {
    return null;
}

function getPageSections($arr = false) {
    $jsonUrl  = resource_path('views/') . str_replace('.', '/', activeTemplate()) . 'sections.json';
    $sections = json_decode(file_get_contents($jsonUrl));
    if ($arr) {
        $sections = json_decode(file_get_contents($jsonUrl), true);
        ksort($sections);
    }
    return $sections;
}

function getImage($image, $size = null, $avatar = false): string
{
    $clean = '';
    if (file_exists($image) && is_file($image)) {
        return asset($image) . $clean;
    }

    if ($avatar) {
        return asset('assets/images/avatar.png');
    }

    if ($size) {
        return route('placeholder.image', $size);
    }

    return asset('assets/images/default.png');
}

function notify($user, $templateName, $shortCodes = null, $sendVia = null, $createLog = true, $pushImage = null) {
    $globalShortCodes = [
        'site_name'       => gs('site_name'),
        'site_currency'   => gs('cur_text'),
        'currency_symbol' => gs('cur_sym'),
    ];

    if (gettype($user) == 'array') {
        $user = (object) $user;
    }

    $shortCodes = array_merge($shortCodes ?? [], $globalShortCodes);

    $notify               = new Notify($sendVia);
    $notify->templateName = $templateName;
    $notify->shortCodes   = $shortCodes;
    $notify->user         = $user;
    $notify->createLog    = $createLog;
    $notify->pushImage    = $pushImage;
    $notify->userColumn   = isset($user->id) ? $user->getForeignKey() : 'user_id';
    $notify->send();
}

function getPaginate($paginate = null) {
    if (!$paginate) {
        $paginate = gs('paginate_number');
    }
    return $paginate;
}

function paginateLinks($data, $view = null) {
    return $data->appends(request()->all())->links($view);
}

function menuActive($routeName, $type = null, $param = null) {
    if ($type == 3) {
        $class = 'side-menu--open';
    } else if ($type == 2) {
        $class = 'sidebar-submenu__open';
    } else {
        $class = 'active';
    }

    if (is_array($routeName)) {
        foreach ($routeName as $key => $value) {
            if (request()->routeIs($value)) {
                return $class;
            }

        }
    } else if (request()->routeIs($routeName)) {
        if ($param) {
            $routeParam = array_values(@request()->route()->parameters ?? []);
            if (strtolower(@$routeParam[0]) == strtolower($param)) {
                return $class;
            } else {
                return;
            }

        }
        return $class;
    }
}

function fileUploader($file, $location, $size = null, $old = null, $thumb = null, $filename = null) {
    $fileManager           = new FileManager($file);
    $fileManager->path     = $location;
    $fileManager->size     = $size;
    $fileManager->old      = $old;
    $fileManager->thumb    = $thumb;
    $fileManager->filename = $filename;
    $fileManager->upload();
    return $fileManager->filename;
}

function fileManager() {
    return new FileManager();
}

function getFilePath($key) {
    return fileManager()->$key()->path;
}

function getFileSize($key) {
    return fileManager()->$key()->size;
}

function getFileExt($key) {
    return fileManager()->$key()->extensions;
}

function diffForHumans($date) {
    $lang = session()->get('lang');
    if (!$lang) {
        $lang = getDefaultLang();
    }

    Carbon::setlocale($lang);
    return Carbon::parse($date)->diffForHumans();
}

function showDateTime($date, $format = 'Y-m-d h:i A') {
    if (!$date) {
        return '-';
    }
    $lang = session()->get('lang');
    if (!$lang) {
        $lang = getDefaultLang();
    }

    Carbon::setlocale($lang);
    return Carbon::parse($date)->translatedFormat($format);
}

function getDefaultLang() {
    return Language::where('is_default', Status::YES)->first()->code ?? 'en';
}

function getContent($dataKeys, $singleQuery = false, $limit = null, $orderById = false) {

    $templateName = activeTemplateName();
    if ($singleQuery) {
        $content = Frontend::where('tempname', $templateName)->where('data_keys', $dataKeys)->orderBy('id', 'desc')->first();
    } else {
        $article = Frontend::where('tempname', $templateName);
        $article->when($limit != null, function ($q) use ($limit) {
            return $q->limit($limit);
        });
        if ($orderById) {
            $content = $article->where('data_keys', $dataKeys)->orderBy('id')->get();
        } else {
            $content = $article->where('data_keys', $dataKeys)->orderBy('id', 'desc')->get();
        }
    }
    return $content;
}

function verifyG2fa($user, $code, $secret = null) {
    $authenticator = new GoogleAuthenticator();
    if (!$secret) {
        $secret = $user->tsc;
    }
    $oneCode  = $authenticator->getCode($secret);
    $userCode = $code;
    if ($oneCode == $userCode) {
        $user->tv = Status::YES;
        $user->save();
        return true;
    } else {
        return false;
    }
}

function urlPath($routeName, $routeParam = null) {
    if ($routeParam == null) {
        $url = route($routeName);
    } else {
        $url = route($routeName, $routeParam);
    }
    $basePath = route('home');
    $path     = str_replace($basePath, '', $url);
    return $path;
}

function showMobileNumber($number) {
    $length = strlen($number);
    return substr_replace($number, '***', 2, $length - 4);
}

function showEmailAddress($email) {
    $endPosition = strpos($email, '@') - 1;
    return substr_replace($email, '***', 1, $endPosition);
}

function getRealIP() {
    $ip = $_SERVER["REMOTE_ADDR"];
    //Deep detect ip
    if (filter_var(@$_SERVER['HTTP_FORWARDED'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_FORWARDED'];
    }
    if (filter_var(@$_SERVER['HTTP_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_FORWARDED_FOR'];
    }
    if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }
    if (filter_var(@$_SERVER['HTTP_X_REAL_IP'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_X_REAL_IP'];
    }
    if (filter_var(@$_SERVER['HTTP_CF_CONNECTING_IP'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
    }
    if ($ip == '::1') {
        $ip = '127.0.0.1';
    }

    return $ip;
}

function appendQuery($key, $value) {
    return request()->fullUrlWithQuery([$key => $value]);
}

function dateSort($a, $b) {
    return strtotime($a) - strtotime($b);
}

function dateSorting($arr) {
    usort($arr, "dateSort");
    return $arr;
}

function gs($key = null) {
    $general = Cache::get('GeneralSetting');
    if (!$general) {
        $general = GeneralSetting::first();
        Cache::put('GeneralSetting', $general);
    }
    if ($key) {
        return @$general->$key;
    }

    return $general;
}

function isImage($string) {
    $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif');
    $fileExtension     = pathinfo($string, PATHINFO_EXTENSION);
    if (in_array($fileExtension, $allowedExtensions)) {
        return true;
    } else {
        return false;
    }
}

function isHtml($string) {
    if (preg_match('/<.*?>/', $string)) {
        return true;
    } else {
        return false;
    }
}

function convertToReadableSize($size) {
    preg_match('/^(\d+)([KMG])$/', $size, $matches);
    $size = (int) $matches[1];
    $unit = $matches[2];

    if ($unit == 'G') {
        return $size . 'GB';
    }

    if ($unit == 'M') {
        return $size . 'MB';
    }

    if ($unit == 'K') {
        return $size . 'KB';
    }

    return $size . $unit;
}

function frontendImage($sectionName, $image, $size = null, $seo = false) {
    if ($seo) {
        return getImage('assets/images/frontend/' . $sectionName . '/seo/' . $image, $size);
    }
    return getImage('assets/images/frontend/' . $sectionName . '/' . $image, $size);
}

function convertMatureTime($value) {
    if ($value < 12) {
        return $value . ' month' . ($value > 1 ? 's' : '');
    } else {
        $years = intdiv($value, 12);
        return $years . ' year' . ($years > 1 ? 's' : '');
    }
}

function investMaturedDate($project, $endDate = false) {
    $date = Carbon::parse($project->end_date)->addMonths((int)$project->maturity_time); // investMaturedDate
    if ($endDate) {
        $date = $date->addMonths((int)$project->project_duration); //investmentEndDate
    }
    return $date;
}

function getInvestmentRemaining($invest) {
    //how many times remaining to pay the user

    if ($invest->project->return_type == Status::LIFETIME) {
        // Investment start and end dates
        $investmentStartDate = investMaturedDate($invest->project);
        $investmentEndDate   = investMaturedDate($invest->project, true);

        // Total duration in hours
        $totalDurationHours = $investmentStartDate->diffInHours(
            $investmentEndDate,
        );

        // Return interval in hours
        $returnIntervalHours = $invest->project->time->hours;

        // Total number of returns
        $totalReturns = floor($totalDurationHours / $returnIntervalHours);

        // Remaining returns
        $remaining = $totalReturns - $invest->period;
    } else {
        // For repeat investments
        $remaining    = $invest->repeat_times - $invest->period;
        $totalReturns = $invest->repeat_times;
    }

    return $remaining;
}

function getTotalReturns($invest) {
    //how many times user will get return

    if ($invest->project->return_type == Status::LIFETIME) {
        // Investment start and end dates
        $investmentStartDate = investMaturedDate($invest->project);
        $investmentEndDate   = investMaturedDate($invest->project, true);

        // Total duration in hours
        $totalDurationHours = $investmentStartDate->diffInHours(
            $investmentEndDate,
        );

        // Return interval in hours
        $returnIntervalHours = $invest->project->time->hours;

        // Total number of returns
        $totalReturns = floor($totalDurationHours / $returnIntervalHours);
    } else {
        // For repeat investments
        $remaining    = $invest->repeat_times - $invest->period;
        $totalReturns = $invest->repeat_times;
    }

    return $totalReturns;
}

function generateContractContent($project, $user = null, $contractNumber = null, $status = null, $forWeb = false) {
    $date = now();
    if (!$contractNumber) {
        $contractNumber = 'SMB/' . date('Y') . '/BHG-' . str_pad($project->id, 4, '0', STR_PAD_LEFT);
    }
    // Chuẩn bị các biến user
    $benBName = $user ? $user->fullname : '........................';
    $benBAddress = $user ? $user->address : '........................';
    $benBBirth = $user && ($user->date_of_birth ?? $user->birth_date ?? null) ? \Carbon\Carbon::parse($user->date_of_birth ?? $user->birth_date)->format('d/m/Y') : '........................';
    $benBId = $user ? $user->id_number : '........................';
    $benBIdDate = $user && $user->id_issue_date ? \Carbon\Carbon::parse($user->id_issue_date)->format('d/m/Y') : '........................';
    $benBIdPlace = $user ? $user->id_issue_place : '........................';
    $benBMobile = $user ? $user->mobile : '........................';
    $benBEmail = $user ? $user->email : '........................';
    $benBAccount = $user ? ($user->bank_account_number ?? $user->account_number ?? '........................') : '........................';
    $benBBank = $user ? ($user->bank_name ?? '........................') : '........................';
    $benBBankBranch = $user ? ($user->bank_branch ?? '........................') : '........................';
    $benBAccountName = $user ? ($user->bank_account_holder ?? $user->account_name ?? '........................') : '........................';
    $benBCustomerCode = $user ? ($user->customer_code ?? $user->username ?? '........................') : '........................';
    $benBTax = $user ? ($user->tax_number ?? '........................') : '........................';
    $benBConsultantName = $project && property_exists($project, 'consultant_name') ? $project->consultant_name : '........................';
    $benBConsultantCode = $project && property_exists($project, 'consultant_code') ? $project->consultant_code : '........................';
    $projectTitle = $project ? $project->title : '........................';
    $projectDescription = $project ? $project->description : '';
    $today = $date->format('d');
    $month = $date->format('m');
    $year = $date->format('Y');
    $mainContent = <<<HTML
    <div class="contract-content">
        <div class="contract-header">
            <div>CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM</div>
            <div>Độc lập - Tự do - Hạnh phúc</div>
            <div>-------o0o-------</div>
            <div class="date">Hà Nội, ngày {$today} tháng {$month} năm {$year}</div>
        </div>
        <div class="main-title">HỢP ĐỒNG HỢP TÁC KINH DOANH</div>
        <div class="doc-number">Số: {$contractNumber}</div>
        <div class="section-label">CĂN CỨ:</div>
        <ul>
            <li class="italic">Căn cứ Bộ luật dân sự năm 2015;</li>
            <li class="italic">Căn cứ Luật thương mại năm 2005 và các văn bản hướng dẫn thi hành;</li>
            <li class="italic">Căn cứ Luật Đầu tư năm 2020 và các văn bản hướng dẫn thi hành;</li>
            <li class="italic">Căn cứ các văn bản pháp luật Việt Nam liên quan;</li>
            <li class="italic">Căn cứ vào năng lực của BHG – Viettel Post;</li>
            <li class="italic">Căn cứ nhu cầu và khả năng của các Bên;</li>
        </ul>
        <p><strong><span class="italic">Hôm nay, ngày {$today} tháng {$month} năm {$year}, tại trụ sở Công ty cổ phần Tập đoàn đầu tư Bắc Hải, chúng tôi gồm có:</span></strong></p>
        <p><strong>BÊN A: CÔNG TY CỔ PHẦN TẬP ĐOÀN ĐẦU TƯ BẮC HẢI (BHG)</strong></p>
        <ul>
            <li>Trụ sở chính: Tầng 04, Tòa nhà Thương mại và Dịch vụ B-CC, Dự án khu nhà ở Ngân Hà Vạn Phúc, phố Tố Hữu, phường Vạn Phúc, quận Hà Đông, TP. Hà Nội;</li>
            <li>Đại diện (Ông): TRẦN VĂN DUY – Chức vụ: Tổng Giám đốc;</li>
            <li>Mã doanh nghiệp: 0109034215;</li>
            <li>Điện thoại: 092 153 939 – Email: hotro@tapdoanbachai.vn;</li>
            <li>Website: tapdoanbachai.vn;</li>
            <li>Số tài khoản: 0511100235999 – Ngân hàng: MB – CN Vạn Phúc;</li>
        </ul>
        <div class="section-label">BÊN B: Ông/Bà: {$benBName}</div>
        <ul>
            <li>Địa chỉ: {$benBAddress};</li>
            <li>Ngày sinh: {$benBBirth};</li>
            <li>CC/CCCD số: {$benBId} – Cấp ngày: {$benBIdDate} – Nơi cấp: {$benBIdPlace};</li>
            <li>Điện thoại: {$benBMobile} – Email: {$benBEmail};</li>
            <li>Số tài khoản: {$benBAccount} – Ngân hàng: {$benBBank} – Chi nhánh: {$benBBankBranch};</li>
            <li>Tên chủ tài khoản: {$benBAccountName};</li>
            <li>Mã số khách hàng: {$benBCustomerCode};</li>
            <li>Mã số thuế TNCN: {$benBTax};</li>
            <li>Họ tên chuyên viên tư vấn: {$benBConsultantName} – Mã số CVTV: {$benBConsultantCode};</li>
        </ul>
        <div class="section-label">XÉT RẰNG:</div>
        <ul>
            <li>Bên A là pháp nhân, được thành lập và hoạt động hợp pháp tại Việt Nam, có chức năng hoạt động đầu tư kinh doanh trong các lĩnh vực: bất động sản, cho thuê máy móc, thiết bị, xây dựng …;</li>
            <li>Bên A đang đầu tư hạ tầng hệ thống tủ Smartbox cho Tổng Công ty cổ phần Bưu chính Viettel thuê;</li>
            <li>Bên B là cá nhân, có điều kiện về tài chính, có đầy đủ năng lực hành vi dân sự, có nhu cầu hợp tác với Bên A để cùng kinh doanh;</li>
        </ul>
        <p><strong><span class="italic">Bởi vậy, sau khi thống nhất, bàn bạc trên tinh thần hoàn toàn tự nguyện, hai bên đồng ý ký hợp đồng với các điều kiện và điều khoản sau đây:</span></strong></p>
        <div class="section-title">ĐIỀU 1: NỘI DUNG HỢP TÁC KINH DOANH</div>
        <p><strong>1.1 Mục đích hợp tác kinh doanh:</strong> Bên A đồng ý nhận và Bên B tự nguyện hợp tác đầu tư theo hình thức góp vốn bằng tiền mặt/tài sản vào BHG để thực hiện dự án {$projectTitle}, phân chia lợi tức theo kết quả kinh doanh.</p>
        <p><strong>1.2 Chi tiết dự án:</strong> {$projectDescription}</p>
        <ul>
            <li>Bên A thực hiện đầu tư các hệ thống tủ Smartbox tại các địa điểm do Bên A và Viettel Post thống nhất;</li>
            <li>Tủ Smartbox đảm bảo tiêu chuẩn kỹ thuật và hoạt động liên tục, ổn định;</li>
            <li>Bên A chịu trách nhiệm lắp đặt, chi trả chi phí thuê mặt bằng và các chi phí vận hành liên quan;</li>
        </ul>
    </div>
HTML;
    if ($forWeb) {
        $mainContent = addWatermarkToPdf($mainContent, $status);
        return $mainContent;
    }
    $contractContent = <<<HTML
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hợp đồng hợp tác kinh doanh</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 20mm;
        }
        html, body {
            margin: 0;
            padding: 0;
        }
        .contract-content {
            font-family: "Times New Roman", Times, serif;
            font-size: 13pt;
            line-height: 1.6;
            color: #000000;
            width: 210mm;
            min-height: 297mm;
            padding: 20mm;
            margin: 0 auto;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .contract-header {
            text-align: center;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .contract-header div {
            margin-bottom: 5px;
            line-height: 1.8;
        }
        .contract-header .date {
            text-align: right;
            font-weight: normal;
            margin-bottom: 10px;
            margin-top: 15px;
        }
        .main-title {
            margin: 15px 0;
            font-size: 16pt;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: bold;
            text-align: center;
        }
        .doc-number {
            text-align: center;
            font-weight: normal;
            margin-bottom: 10px;
        }
        .section-label {
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 15px;
            margin-bottom: 6px;
        }
        .contract-content p, .contract-content ul, .contract-content li {
            font-size: 13pt;
            text-align: justify;
            text-justify: inter-word;
        }
        .contract-content ul {
            list-style-type: disc;
            padding-left: 30px;
            margin: 5px 0 15px 0;
        }
        .contract-content li {
            margin-bottom: 5px;
            text-align: justify;
        }
        .italic { font-style: italic; }
        .bold { font-weight: bold; }
        .section-title {
            font-weight: bold;
            margin-top: 15px;
            text-transform: uppercase;
            text-align: left;
        }
    </style>
</head>
<body>
    {$mainContent}
</body>
</html>
HTML;
    $contractContent = addWatermarkToPdf($contractContent, $status);
    return $contractContent;
}

function generateContractNumber() {
    // Lấy tháng và năm hiện tại
    $month = date('m'); // 2 chữ số tháng (01-12)
    $year = date('y');  // 2 chữ số năm (25 cho 2025)
    
    // Tìm số thứ tự hợp đồng trong tháng hiện tại
    $currentMonth = date('Y-m'); // 2025-06
    $contractCount = \App\Models\Invest::whereYear('created_at', date('Y'))
                                      ->whereMonth('created_at', date('m'))
                                      ->count();
    
    // Tăng số thứ tự lên 1 cho hợp đồng mới
    $contractCount++;
    
    // Format: 3 số thứ tự + 2 số tháng + 2 số năm
    // Ví dụ: 001 + 06 + 25 = 0010625
    $contractNumber = str_pad($contractCount, 3, '0', STR_PAD_LEFT) . $month . $year;
    
    return $contractNumber;
}

function addWatermarkToPdf($pdfContent, $status = null) {
    if ($status !== \App\Constants\Status::INVEST_RUNNING) {
        $watermarkHtml = <<<HTML
<div class="contract-watermark" style="
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) rotate(-20deg);
    z-index: 2;
    pointer-events: none;
    user-select: none;
    width: 80%;
    text-align: center;
">
    <div style="
        padding: 20px 40px;
        background: rgba(255, 255, 255, 0.11);
        border: 2px solid rgba(255, 0, 0, 0.6);
        border-radius: 0;
        text-align: center;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
        white-space: nowrap;
        display: inline-block;
    ">
        <div style="
            margin-bottom: 8px;
            font-size: 32px;
            font-weight: bold;
            font-family: 'Times New Roman', Times, serif;
            color: rgba(255, 0, 0, 0.5);
        ">
            HỢP ĐỒNG CHƯA CÓ HIỆU LỰC PHÁP LÝ
        </div>
        <div style="
            font-size: 20px;
            font-weight: 600;
            font-family: Arial, sans-serif;
            color: rgba(255, 0, 0, 0.5);
        ">
            KHÔNG CÓ GIÁ TRỊ SỬ DỤNG
        </div>
    </div>
</div>
HTML;
        // Chèn watermark ngay sau <div class="contract-content">
        $pdfContent = preg_replace('/(<div[^>]*class=["\"][^>]*contract-content[^>]*["\"][^>]*>)/i', '$1' . $watermarkHtml, $pdfContent, 1);
    }
    return $pdfContent;
}

function refreshContractContent($invest) {
    // Cập nhật nội dung hợp đồng với status hiện tại
    $contractContent = generateContractContent($invest->project, $invest->user, $invest->invest_no, $invest->status);
    $invest->contract_content = $contractContent;
    $invest->save();
    
    return $invest;
}
