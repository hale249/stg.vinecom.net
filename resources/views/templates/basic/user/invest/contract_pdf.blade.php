<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Hợp đồng hợp tác kinh doanh</title>
    <style>
        @font-face {
            font-family: 'DejaVu Sans';
            src: url('{{ storage_path('fonts/DejaVuSans.ttf') }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }
        @page {
            size: A4 portrait;
            margin: 20mm;
        }
        body {            font-family: 'Times New Roman', Times, serif;
            font-size: 15px;
            line-height: 1.7;
            color: #222;
        }
        .contract-header {
            text-align: center;
            font-weight: bold;
            margin-bottom: 20px;
            color: #000;
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
            font-size: 18px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: bold;
            text-align: center;
            color: #000;
        }
        .doc-number {
            text-align: center;
            font-weight: normal;
            margin-bottom: 10px;
            color: #000;
        }
        .section-label {
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 15px;
            margin-bottom: 6px;
            color: #000;
        }
        p, ul, li {
            font-size: 15px;
            text-align: justify;
            text-justify: inter-word;
            color: #222;
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
        .italic { font-style: italic; color: #222; }
        .bold { font-weight: bold; color: #222; }
        .section-title {
            font-weight: bold;
            margin-top: 15px;
            text-transform: uppercase;
            text-align: left;
            color: #000;
        }
    </style>
</head>
<body>
    @php
        use App\Constants\Status;
    @endphp
    @if(isset($status) && $status == Status::INVEST_RUNNING)
        <div style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-45deg); z-index: 1000; pointer-events: none; opacity: 0.18; font-size: 3.2rem; color: #e74c3c; font-family: 'Times New Roman', Times, serif; font-weight: bold; text-shadow: 2px 2px 8px rgba(0,0,0,0.08);">
            ĐÃ DUYỆT
        </div>
    @else
        <div style="
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-20deg);
            z-index: 9999;
            pointer-events: none;
            user-select: none;
        ">
            <div style="
                padding: 20px 40px;
                background: rgba(255, 255, 255, 0.6);
                border: 2px solid rgba(255, 0, 0, 0.6);
                border-radius: 0;
                text-align: center;
                box-shadow: 0 0 10px rgba(0,0,0,0.1);
                white-space: nowrap;
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
    @endif
    <div class="contract-header">
        <div>CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM</div>
        <div>Độc lập - Tự do - Hạnh phúc</div>
        <div>-------o0o-------</div>
        <div class="date">Hà Nội, ngày {{ date('d') }} tháng {{ date('m') }} năm {{ date('Y') }}</div>
    </div>
    <div class="main-title">HỢP ĐỒNG HỢP TÁC KINH DOANH</div>
    <div class="doc-number">Số: {{ $invest->invest_no }}</div>

    <div class="section-label">CĂN CỨ:</div>
    <ul>
        <li class="italic">Căn cứ Bộ luật dân sự năm 2015;</li>
        <li class="italic">Căn cứ Luật thương mại năm 2005 và các văn bản hướng dẫn thi hành;</li>
        <li class="italic">Căn cứ Luật Đầu tư năm 2020 và các văn bản hướng dẫn thi hành;</li>
        <li class="italic">Căn cứ các văn bản pháp luật Việt Nam liên quan;</li>
        <li class="italic">Căn cứ vào năng lực của BHG – Viettel Post;</li>
        <li class="italic">Căn cứ nhu cầu và khả năng của các Bên;</li>
    </ul>

    <p><strong><span class="italic">Hôm nay, ngày {{ date('d') }} tháng {{ date('m') }} năm {{ date('Y') }}, tại trụ sở Công ty cổ phần Tập đoàn đầu tư Bắc Hải, chúng tôi gồm có:</span></strong></p>

    <p><strong>BÊN A: CÔNG TY CỔ PHẦN TẬP ĐOÀN ĐẦU TƯ BẮC HẢI (BHG)</strong></p>
    <ul>
        <li>Trụ sở chính: Tầng 04, Tòa nhà Thương mại và Dịch vụ B-CC, Dự án khu nhà ở Ngân Hà Vạn Phúc, phố Tố Hữu, phường Vạn Phúc, quận Hà Đông, TP. Hà Nội;</li>
        <li>Đại diện (Ông): TRẦN VĂN DUY – Chức vụ: Tổng Giám đốc;</li>
        <li>Mã doanh nghiệp: 0109034215;</li>
        <li>Điện thoại: 092 153 939 – Email: hotro@tapdoanbachai.vn;</li>
        <li>Website: tapdoanbachai.vn;</li>
        <li>Số tài khoản: 0511100235999 – Ngân hàng: MB – CN Vạn Phúc;</li>
    </ul>

    <p><strong>BÊN B: Ông/Bà: {{ $user->fullname }}</strong></p>
    <ul>
        <li>Địa chỉ: {{ $user->address }};</li>
        <li>Ngày sinh: {{ $user->birth_date }};</li>
        <li>CC/CCCD số: {{ $user->id_number }} – Cấp ngày: {{ $user->id_issue_date }} – Nơi cấp: {{ $user->id_issue_place }};</li>
        <li>Điện thoại: {{ $user->mobile }} – Email: {{ $user->email }};</li>
        <li>Số tài khoản: {{ $user->account_number }} – Ngân hàng: {{ $user->bank_name }} – Chi nhánh: {{ $user->bank_branch }};</li>
        <li>Tên chủ tài khoản: {{ $user->account_name }};</li>
        <li>Mã số khách hàng: {{ $user->username }};</li>
        <li>Mã số thuế TNCN: {{ $user->tax_number }};</li>
        <li>Họ tên chuyên viên tư vấn: {{ $invest->project->consultant_name }} – Mã số CVTV: {{ $invest->project->consultant_code }};</li>
    </ul>

    <div class="section-label">XÉT RẰNG:</div>
    <ul>
        <li>Bên A là pháp nhân, được thành lập và hoạt động hợp pháp tại Việt Nam, có chức năng hoạt động đầu tư kinh doanh trong các lĩnh vực: bất động sản, cho thuê máy móc, thiết bị, xây dựng …;</li>
        <li>Bên A đang đầu tư hạ tầng hệ thống tủ Smartbox cho Tổng Công ty cổ phần Bưu chính Viettel thuê;</li>
        <li>Bên B là cá nhân, có điều kiện về tài chính, có đầy đủ năng lực hành vi dân sự, có nhu cầu hợp tác với Bên A để cùng kinh doanh;</li>
    </ul>

    <p><strong><span class="italic">Bởi vậy, sau khi thống nhất, bàn bạc trên tinh thần hoàn toàn tự nguyện, hai bên đồng ý ký hợp đồng với các điều kiện và điều khoản sau đây:</span></strong></p>

    <div class="section-title">ĐIỀU 1: NỘI DUNG HỢP TÁC KINH DOANH</div>
    <p><strong>1.1 Mục đích hợp tác kinh doanh:</strong> Bên A đồng ý nhận và Bên B tự nguyện hợp tác đầu tư theo hình thức góp vốn bằng tiền mặt/tài sản vào BHG để thực hiện dự án Smartbox, phân chia lợi tức theo kết quả kinh doanh.</p>
    <p><strong>1.2 Chi tiết dự án:</strong> Dự án đầu tư kinh doanh hạ tầng hệ thống tủ lưu giữ và giao nhận tự động Smartbox thông minh theo hình thức cho thuê.</p>
    <ul>
        <li>Bên A thực hiện đầu tư các hệ thống tủ Smartbox tại các địa điểm do Bên A và Viettel Post thống nhất;</li>
        <li>Tủ Smartbox đảm bảo tiêu chuẩn kỹ thuật và hoạt động liên tục, ổn định;</li>
        <li>Bên A chịu trách nhiệm lắp đặt, chi trả chi phí thuê mặt bằng và các chi phí vận hành liên quan;</li>
    </ul>
</body>
</html> 