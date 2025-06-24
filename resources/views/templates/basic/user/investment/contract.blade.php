@extends($activeTemplate . 'layouts.master')
@php
    use App\Constants\Status;
@endphp
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="card custom--card">
                    <div class="card-header">
                        <h5 class="card-title">@lang('Hợp Đồng Đầu Tư')</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive--md">
                            <table class="table custom--table">
                                <thead>
                                    <tr>
                                        <th>@lang('Mã Đầu Tư')</th>
                                        <th>@lang('Dự Án')</th>
                                        <th>@lang('Số Tiền')</th>
                                        <th>@lang('Số Lượng')</th>
                                        <th>@lang('Trạng Thái')</th>
                                        <th>@lang('Ngày')</th>
                                        <th>@lang('Thao Tác')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($invests as $invest)
                                        <tr>
                                            <td>{{ $invest->invest_no }}</td>
                                            <td>{{ __($invest->project->title) }}</td>
                                            <td>{{ showAmount($invest->total_price) }} {{ $general->cur_text }}</td>
                                            <td>{{ $invest->quantity }}</td>
                                            <td>
                                                @php
                                                    $statusClass = '';
                                                    switch ($invest->status) {
                                                        case Status::INVEST_RUNNING:
                                                            $statusClass = 'success';
                                                            $status = 'Đang Chạy';
                                                            break;
                                                        case Status::INVEST_CANCELED:
                                                            $statusClass = 'danger';
                                                            $status = 'Đã Hủy';
                                                            break;
                                                        case Status::INVEST_PENDING:
                                                            $statusClass = 'warning';
                                                            $status = 'Đang Chờ';
                                                            break;
                                                        case Status::INVEST_COMPLETED:
                                                            $statusClass = 'info';
                                                            $status = 'Hoàn Thành';
                                                            break;
                                                        case Status::INVEST_PENDING_ADMIN_REVIEW:
                                                            $statusClass = 'warning';
                                                            $status = 'Chờ Duyệt';
                                                            break;
                                                        case Status::INVEST_ACCEPT:
                                                            $statusClass = 'success';
                                                            $status = 'Đã Chấp Nhận';
                                                            break;
                                                        case Status::INVEST_CLOSED:
                                                            $statusClass = 'dark';
                                                            $status = 'Đã Đóng';
                                                            break;
                                                        default:
                                                            $statusClass = 'warning';
                                                            $status = 'Đang Chờ';
                                                    }
                                                @endphp
                                                <span class="badge badge--{{ $statusClass }}">{{ __($status) }}</span>
                                            </td>
                                            <td>{{ showDateTime($invest->created_at) }}</td>
                                            <td>
                                                <div class="action-buttons">
                                                    @if($invest->status == Status::INVEST_PENDING)
                                                    <form action="{{ route('user.invest.confirm', $invest->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn--xsm btn--outline action-btn" data-toggle="tooltip" data-placement="top" title="@lang('Xác Nhận Đầu Tư')">
                                                            <i class="las la-check"></i>
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('user.invest.cancel', $invest->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn--xsm btn--outline action-btn" data-toggle="tooltip" data-placement="top" title="@lang('Hủy Đầu Tư')">
                                                            <i class="las la-times"></i>
                                                        </button>
                                                    </form>
                                                    @endif
                                                    <button type="button" 
                                                        class="btn btn--xsm btn--outline action-btn view-contract" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#contractModal"
                                                        data-invest-no="{{ $invest->invest_no }}"
                                                        data-project="{{ __($invest->project->title) }}"
                                                        data-user-name="{{ $invest->user->fullname }}"
                                                        data-user-address="{{ $invest->user->address }}"
                                                        data-user-dob="{{ $invest->user->date_of_birth ? \Carbon\Carbon::parse($invest->user->date_of_birth)->format('d/m/Y') : 'N/A' }}"
                                                        data-user-id="{{ $invest->user->id_number }}"
                                                        data-user-id-date="{{ $invest->user->id_issue_date ? \Carbon\Carbon::parse($invest->user->id_issue_date)->format('d/m/Y') : 'N/A' }}"
                                                        data-user-id-place="{{ $invest->user->id_issue_place }}"
                                                        data-user-mobile="{{ $invest->user->mobile }}"
                                                        data-user-email="{{ $invest->user->email }}"
                                                        data-user-bank="{{ $invest->user->bank_account_number }}"
                                                        data-user-bank-name="{{ $invest->user->bank_name }}"
                                                        data-user-bank-branch="{{ $invest->user->bank_branch }}"
                                                        data-user-bank-holder="{{ $invest->user->bank_account_holder }}"
                                                        data-user-username="{{ $invest->user->username }}"
                                                        data-user-tax="{{ $invest->user->tax_number }}"
                                                        data-consultant-name="{{ $invest->project->consultant_name }}"
                                                        data-consultant-code="{{ $invest->project->consultant_code }}">
                                                        <i class="las la-eye"></i> @lang('Xem')
                                                    </button>
                                                    <a href="{{ route('user.invest.contract.watermark', $invest->id) }}" 
                                                        class="btn btn--xsm btn--outline action-btn">
                                                        <i class="las la-stamp"></i> @lang('Trạng Thái')
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="100%" class="text-center">
                                                <div class="text-center text--base">@lang('Không tìm thấy dữ liệu!')</div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if ($invests->hasPages())
                            <div class="mt-4">
                                {{ paginateLinks($invests) }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contract Modal -->
    <div class="modal fade" id="contractModal" tabindex="-1" aria-labelledby="contractModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="contractModalLabel">@lang('Hợp Đồng Đầu Tư')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="contract-info mb-4">
                            <div class="row">
                                <div class="col-md-4">
                                    <p><strong>@lang('Mã Đầu Tư'):</strong> <span id="modalInvestNo"></span></p>
                                </div>
                                <div class="col-md-8">
                                    <p><strong>@lang('Dự Án'):</strong> <span id="modalProject"></span></p>
                                </div>
                            </div>
                        </div>
                    <div class="contract-content">
                        <div class="contract-header">
                            <div>CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM</div>
                            <div>Độc lập - Tự do - Hạnh phúc</div>
                            <div>-------o0o-------</div>
                            <div class="date">Hà Nội, ngày {{ date('d') }} tháng {{ date('m') }} năm {{ date('Y') }}</div>
                        </div>
                        <div class="main-title">HỢP ĐỒNG HỢP TÁC KINH DOANH</div>
                        <div class="doc-number">Số: <span id="modalContractNo"></span></div>

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

                        <div class="section-label">BÊN B: Ông/Bà: <span id="modalUserName"></span></div>
                        <ul>
                            <li>Địa chỉ: <span id="modalUserAddress"></span>;</li>
                            <li>Ngày sinh: <span id="modalUserDob"></span>;</li>
                            <li>CC/CCCD số: <span id="modalUserId"></span> – Cấp ngày: <span id="modalUserIdDate"></span> – Nơi cấp: <span id="modalUserIdPlace"></span>;</li>
                            <li>Điện thoại: <span id="modalUserMobile"></span> – Email: <span id="modalUserEmail"></span>;</li>
                            <li>Số tài khoản: <span id="modalUserBank"></span> – Ngân hàng: <span id="modalUserBankName"></span> – Chi nhánh: <span id="modalUserBankBranch"></span>;</li>
                            <li>Tên chủ tài khoản: <span id="modalUserBankHolder"></span>;</li>
                            <li>Mã số khách hàng: <span id="modalUserUsername"></span>;</li>
                            <li>Mã số thuế TNCN: <span id="modalUserTax"></span>;</li>
                            <li>Họ tên chuyên viên tư vấn: <span id="modalConsultantName"></span> – Mã số CVTV: <span id="modalConsultantCode"></span>;</li>
                        </ul>

                        <div class="section-label">XÉT RẰNG:</div>
                        <ul>
                            <li>Bên A là pháp nhân, được thành lập và hoạt động hợp pháp tại Việt Nam, có chức năng hoạt động đầu tư kinh doanh trong các lĩnh vực: bất động sản, cho thuê máy móc, thiết bị, xây dựng …;</li>
                            <li>Bên A đang đầu tư hạ tầng hệ thống tủ Smartbox cho Tổng Công ty cổ phần Bưu chính Viettel thuê;</li>
                            <li>Bên B là cá nhân, có điều kiện về tài chính, có đầy đủ năng lực hành vi dân sự, có nhu cầu hợp tác với Bên A để cùng kinh doanh;</li>
                        </ul>

                        <p><strong><span class="italic">Bởi vậy, sau khi thống nhất, bàn bạc trên tinh thần hoàn toàn tự nguyện, hai bên đồng ý ký hợp đồng với các điều kiện và điều khoản sau đây:</span></strong></p>

                        <div class="section-title">ĐIỀU 1: NỘI DUNG HỢP TÁC KINH DOANH</div>
                        <p><strong>1.1 Mục đích hợp tác kinh doanh:</strong> Bên A đồng ý nhận và Bên B tự nguyện hợp tác đầu tư theo hình thức góp vốn bằng tiền mặt/tài sản vào BHG để thực hiện dự án <span id="modalProjectTitle"></span>, phân chia lợi tức theo kết quả kinh doanh.</p>
                        <p><strong>1.2 Chi tiết dự án:</strong> Dự án đầu tư kinh doanh hạ tầng hệ thống tủ lưu giữ và giao nhận tự động Smartbox thông minh theo hình thức cho thuê.</p>
                                <ul>
                                    <li>Bên A thực hiện đầu tư các hệ thống tủ Smartbox tại các địa điểm do Bên A và Viettel Post thống nhất;</li>
                                    <li>Tủ Smartbox đảm bảo tiêu chuẩn kỹ thuật và hoạt động liên tục, ổn định;</li>
                                    <li>Bên A chịu trách nhiệm lắp đặt, chi trả chi phí thuê mặt bằng và các chi phí vận hành liên quan;</li>
                                </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Đóng')</button>
                    <button type="button" class="btn btn--primary" id="printContract">@lang('In')</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
<style>
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
    .modal-xl {
        max-width: 90%;
    }
    .modal-body {
        padding: 0;
        background: #f5f5f5;
    }
    .contract-info {
        background: white;
        padding: 15px;
        margin-bottom: 0 !important;
        border-bottom: 1px solid #dee2e6;
    }
    @media print {
        .contract-content {
            box-shadow: none;
            margin: 0;
            padding: 20mm;
        }
        .modal-header, .modal-footer, .contract-info {
            display: none;
        }
        .modal-body {
            padding: 0;
            background: white;
        }
    }
</style>
@endpush

@push('script')
<script>
    (function($) {
        "use strict";
        $('.view-contract').on('click', function() {
            var investNo = $(this).data('invest-no');
            var project = $(this).data('project');
            var userName = $(this).data('user-name');
            var userAddress = $(this).data('user-address');
            var userDob = $(this).data('user-dob');
            var userId = $(this).data('user-id');
            var userIdDate = $(this).data('user-id-date');
            var userIdPlace = $(this).data('user-id-place');
            var userMobile = $(this).data('user-mobile');
            var userEmail = $(this).data('user-email');
            var userBank = $(this).data('user-bank');
            var userBankName = $(this).data('user-bank-name');
            var userBankBranch = $(this).data('user-bank-branch');
            var userBankHolder = $(this).data('user-bank-holder');
            var userUsername = $(this).data('user-username');
            var userTax = $(this).data('user-tax');
            var consultantName = $(this).data('consultant-name');
            var consultantCode = $(this).data('consultant-code');
            
            $('#modalInvestNo').text(investNo);
            $('#modalContractNo').text(investNo);
            $('#modalProject').text(project);
            $('#modalProjectTitle').text(project);
            $('#modalUserName').text(userName);
            $('#modalUserAddress').text(userAddress);
            $('#modalUserDob').text(userDob);
            $('#modalUserId').text(userId);
            $('#modalUserIdDate').text(userIdDate);
            $('#modalUserIdPlace').text(userIdPlace);
            $('#modalUserMobile').text(userMobile);
            $('#modalUserEmail').text(userEmail);
            $('#modalUserBank').text(userBank);
            $('#modalUserBankName').text(userBankName);
            $('#modalUserBankBranch').text(userBankBranch);
            $('#modalUserBankHolder').text(userBankHolder);
            $('#modalUserUsername').text(userUsername);
            $('#modalUserTax').text(userTax || 'N/A');
            $('#modalConsultantName').text(consultantName);
            $('#modalConsultantCode').text(consultantCode);
        });
    })(jQuery);
</script>
@endpush