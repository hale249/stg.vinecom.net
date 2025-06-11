@extends($activeTemplate.'layouts.master')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card custom--card">
                    <div class="card-header">
                        <h5 class="card-title">@lang('Investment Contract')</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="las la-info-circle"></i>
                            @lang('Please review your investment contract before proceeding.')
                        </div>

                        <div class="contract-content mb-4">
                            <div class="container">
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
                                <div class="section-label">BÊN B: Ông/Bà: {{ $invest->user->fullname }}</div>
                                <ul>
                                    <li>Địa chỉ: {{ $invest->user->address }};</li>
                                    <li>Ngày sinh: {{ $invest->user->date_of_birth ? \Carbon\Carbon::parse($invest->user->date_of_birth)->format('d/m/Y') : 'N/A' }};</li>
                                    <li>CC/CCCD số: {{ $invest->user->id_number }} – Cấp ngày: {{ $invest->user->id_issue_date ? \Carbon\Carbon::parse($invest->user->id_issue_date)->format('d/m/Y') : 'N/A' }} – Nơi cấp: {{ $invest->user->id_issue_place }};</li>
                                    <li>Điện thoại: {{ $invest->user->mobile }} – Email: {{ $invest->user->email }};</li>
                                    <li>Số tài khoản: {{ $invest->user->bank_account_number }} – Ngân hàng: {{ $invest->user->bank_name }} – Chi nhánh: {{ $invest->user->bank_branch }};</li>
                                    <li>Tên chủ tài khoản: {{ $invest->user->bank_account_holder }};</li>
                                    <li>Mã số khách hàng: {{ $invest->user->username }};</li>
                                    <li>Mã số thuế TNCN: {{ auth()->user()->tax_number }};</li>
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
                            </div>
                        </div>

                        <div class="text-center">
                            <form action="{{ route('user.invest.confirm', $invest->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn--base">@lang('Confirm Investment')</button>
                            </form>
                            <a href="{{ route('user.invest.contract.download', $invest->id) }}" class="btn btn--info">
                                <i class="las la-download"></i> @lang('Download PDF')
                            </a>
                            <a href="{{ route('user.home') }}" class="btn btn--danger">@lang('Cancel')</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @page {
            size: A4 portrait;
            margin: 20mm;
        }
        .contract-content {
            font-family: "Times New Roman", Times, serif;
            font-size: 13pt;
            line-height: 1.6;
            color: #000000;
        }
        .contract-header {
            text-align: center;
            font-weight: bold;
            margin-bottom: 20px;
            color: #000000;
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
            color: #000000;
        }
        .doc-number {
            text-align: center;
            font-weight: normal;
            margin-bottom: 10px;
            color: #000000;
        }
        .section-label {
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 15px;
            margin-bottom: 6px;
            color: #000000;
        }
        .contract-content p, .contract-content ul, .contract-content li {
            font-size: 13pt;
            text-align: justify;
            text-justify: inter-word;
            color: #000000;
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
        .italic { 
            font-style: italic;
            color: #000000;
        }
        .bold { 
            font-weight: bold;
            color: #000000;
        }
        .section-title {
            font-weight: bold;
            margin-top: 15px;
            text-transform: uppercase;
            text-align: left;
            color: #000000;
        }
    </style>
@endsection 