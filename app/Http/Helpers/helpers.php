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

function showAmount($amount, $decimal = 2, $separate = true, $exceptZeros = false, $currencyFormat = true) {
    $separator = '';
    if ($separate) {
        $separator = ',';
    }
    $printAmount = number_format($amount, $decimal, '.', $separator);
    if ($exceptZeros) {
        $exp = explode('.', $printAmount);
        if ($exp[1] * 1 == 0) {
            $printAmount = $exp[0];
        } else {
            $printAmount = rtrim($printAmount, '0');
        }
    }
    if ($currencyFormat) {
        if (gs('currency_format') == Status::CUR_BOTH) {
            return gs('cur_sym') . $printAmount . ' ' . __(gs('cur_text'));
        } else if (gs('currency_format') == Status::CUR_TEXT) {
            return $printAmount . ' ' . __(gs('cur_text'));
        } else {
            return gs('cur_sym') . $printAmount;
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
    $date = Carbon::parse($project->end_date)->addMonth($project->maturity_time); // investMaturedDate
    if ($endDate) {
        $date = $date->addMonths($project->project_duration); //investmentEndDate
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

function generateContractContent($project, $user = null) {
    $date = now()->format('d/m/Y');
    $contractNumber = 'SMB/' . date('Y') . '/BHG-' . str_pad($project->id, 4, '0', STR_PAD_LEFT);
    
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
        .container {
            width: calc(210mm - 40mm);
            margin: 0 auto;
            font-family: "Times New Roman", Times, serif;
            font-size: 13pt;
            line-height: 1.6;
        }
        .header {
            text-align: center;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .header .date {
            text-align: right;
            font-weight: normal;
            margin-bottom: 10px;
        }
        .main-title {
            margin: 5px 0;
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
        p, ul, li {
            font-size: 13pt;
            text-align: justify;
            text-justify: inter-word;
        }
        ul {
            list-style-type: disc;
            padding-left: 30px;
            margin: 5px 0 15px 0;
        }
        li {
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
    <div class="container">
        <div class="header">
            <div>CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM</div>
            <div>Độc lập - Tự do - Hạnh phúc</div>
            <div>-------o0o-------</div>
            <div class="date">Hà Nội, ngày {$date}</div>
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

        <p><strong><span class="italic">Hôm nay, ngày {$date}, tại trụ sở Công ty cổ phần Tập đoàn đầu tư Bắc Hải, chúng tôi gồm có:</span></strong></p>

        <p><strong>BÊN A: CÔNG TY CỔ PHẦN TẬP ĐOÀN ĐẦU TƯ BẮC HẢI (BHG)</strong></p>
        <ul>
            <li>Trụ sở chính: Tầng 04, Tòa nhà Thương mại và Dịch vụ B-CC, Dự án khu nhà ở Ngân Hà Vạn Phúc, phố Tố Hữu, phường Vạn Phúc, quận Hà Đông, TP. Hà Nội;</li>
            <li>Đại diện (Ông): TRẦN VĂN DUY – Chức vụ: Tổng Giám đốc;</li>
            <li>Mã doanh nghiệp: 0109034215;</li>
            <li>Điện thoại: 092 153 939 – Email: hotro@tapdoanbachai.vn;</li>
            <li>Website: tapdoanbachai.vn;</li>
            <li>Số tài khoản: 0511100235999 – Ngân hàng: MB – CN Vạn Phúc;</li>
        </ul>

        <p><strong>BÊN B: Ông/Bà: ' . ($user ? $user->fullname : '........................') . '</strong></p>
        <ul>
            <li>Địa chỉ: ' . ($user ? $user->address : '........................') . ';</li>
            <li>Ngày sinh: ' . ($user ? $user->birth_date : '........................') . ';</li>
            <li>CC/CCCD số: ' . ($user ? $user->id_number : '........................') . ' – Cấp ngày: ' . ($user ? $user->id_issue_date : '........................') . ' – Nơi cấp: ' . ($user ? $user->id_issue_place : '........................') . ';</li>
            <li>Điện thoại: ' . ($user ? $user->mobile : '........................') . ' – Email: ' . ($user ? $user->email : '........................') . ';</li>
            <li>Số tài khoản: ' . ($user ? $user->account_number : '........................') . ' – Ngân hàng: ' . ($user ? $user->bank_name : '........................') . ' – Chi nhánh: ' . ($user ? $user->bank_branch : '........................') . ';</li>
            <li>Tên chủ tài khoản: ' . ($user ? $user->account_name : '........................') . ';</li>
            <li>Mã số khách hàng: ' . ($user ? $user->customer_code : '........................') . ';</li>
            <li>Mã số thuế TNCN: ' . ($user ? $user->tax_number : '........................') . ';</li>
            <li>Họ tên chuyên viên tư vấn: ' . ($user ? $user->consultant_name : '........................') . ' – Mã số CVTV: ' . ($user ? $user->consultant_code : '........................') . ';</li>
        </ul>

        <div class="section-label">XÉT RẰNG:</div>
        <ul>
            <li>Bên A là pháp nhân, được thành lập và hoạt động hợp pháp tại Việt Nam, có chức năng hoạt động đầu tư kinh doanh trong các lĩnh vực: bất động sản, cho thuê máy móc, thiết bị, xây dựng …;</li>
            <li>Bên A đang đầu tư hạ tầng hệ thống tủ Smartbox cho Tổng Công ty cổ phần Bưu chính Viettel thuê;</li>
            <li>Bên B là cá nhân, có điều kiện về tài chính, có đầy đủ năng lực hành vi dân sự, có nhu cầu hợp tác với Bên A để cùng kinh doanh;</li>
        </ul>

        <p><strong><span class="italic">Bởi vậy, sau khi thống nhất, bàn bạc trên tinh thần hoàn toàn tự nguyện, hai bên đồng ý ký hợp đồng với các điều kiện và điều khoản sau đây:</span></strong></p>

        <div class="section-title">ĐIỀU 1: NỘI DUNG HỢP TÁC KINH DOANH</div>
        <p><strong>1.1 Mục đích hợp tác kinh doanh:</strong> Bên A đồng ý nhận và Bên B tự nguyện hợp tác đầu tư theo hình thức góp vốn bằng tiền mặt/tài sản vào BHG để thực hiện dự án {$project->title}, phân chia lợi tức theo kết quả kinh doanh.</p>
        <p><strong>1.2 Chi tiết dự án:</strong> {$project->description}</p>
        <ul>
            <li>Bên A thực hiện đầu tư các hệ thống tủ Smartbox tại các địa điểm do Bên A và Viettel Post thống nhất;</li>
            <li>Tủ Smartbox đảm bảo tiêu chuẩn kỹ thuật và hoạt động liên tục, ổn định;</li>
            <li>Bên A chịu trách nhiệm lắp đặt, chi trả chi phí thuê mặt bằng và các chi phí vận hành liên quan;</li>
        </ul>
    </div>
</body>
</html>
HTML;

    return $contractContent;
}
