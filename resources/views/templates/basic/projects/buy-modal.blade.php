<!-- Main Modal -->
<div class="modal fade custom--modal" id="bitModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Đầu tư vào <span class="text-primary" style="color: #FFD700 !important;">{{ __($project->title) }}</span></h5>
                
            </div>
            <div class="modal-body">
                <!-- User Verification Warning -->
                @php
                    $user = auth()->user();
                    $missingFields = [];
                    
                    // Check for required fields
                    if(empty($user->firstname) || empty($user->lastname)) {
                        $missingFields[] = 'Họ tên';
                    }
                    if(empty($user->date_of_birth)) {
                        $missingFields[] = 'Ngày sinh';
                    }
                    if(empty($user->id_number) || empty($user->id_issue_date) || empty($user->id_issue_place)) {
                        $missingFields[] = 'CCCD/CMND';
                    }
                    if(empty($user->bank_account_number) || empty($user->bank_name) || empty($user->bank_branch) || empty($user->bank_account_holder)) {
                        $missingFields[] = 'Thông tin ngân hàng';
                    }
                    
                    $needsVerification = count($missingFields) > 0;
                @endphp
                
                @if($needsVerification)
                <div class="verification-warning mb-4">
                    <div class="alert alert-warning" role="alert">
                        <div class="d-flex align-items-center">
                            <div class="alert-icon me-3">
                                <i class="fas fa-exclamation-triangle fa-2x"></i>
                            </div>
                            <div class="alert-content">
                                <h5 class="mb-1">Yêu cầu xác minh trước đầu tư</h5>
                                <p class="mb-2">Cần cập nhật thông tin cá nhân trước khi xác nhận đầu tư.</p>
                                <p class="mb-2">
                                    <strong>Thông tin còn thiếu:</strong> {{ implode(', ', $missingFields) }}
                                </p>
                                <a href="{{ route('user.profile.setting') }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-user-edit me-1"></i> Cập nhật thông tin ngay
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                <!-- End User Verification Warning -->
                
                <form action="{{ route('invest.order') }}" method="post" class="investment-form">
                    @csrf
                    <input type="hidden" name="project_id" value="{{ $project->id }}">
                    <input type="hidden" name="quantity" id="modal_quantity" value="1">
                    <input type="hidden" name="payment_type" value="2">
                    <input type="hidden" name="total_price" id="modal_total_price" value="">
                    <input type="hidden" name="unit_price" id="modal_unit_price" value="{{ $project->share_amount }}">
                    <input type="hidden" name="total_earning" id="modal_total_earning" value="">

                    <div class="payment-options-wrapper">
                        <!-- Investment Profit Estimation Section -->
                        <div class="profit-estimation-section mb-3">
                            <div class="section-title mb-2">
                                <h6 class="fw-bold mb-0">Ước tính lợi nhuận gói đầu tư</h6>
                            </div>
                            <div class="profit-details">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <div class="profit-item">
                                            <label class="text-muted">Lãi suất</label>
                                            <h6 class="mb-0">{{ number_format($project->roi_percentage, 0) }}%</h6>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="profit-item">
                                            <label class="text-muted">Thành tiền</label>
                                            <h6 class="mb-0" id="total_profit">0 {{ gs('cur_text') }}</h6>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="profit-item">
                                            <label class="text-muted">Ngày dự tính đáo hạn</label>
                                            <h6 class="mb-0" id="maturity_date">--/--/----</h6>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="profit-item">
                                            <label class="text-muted">Ngày trả lãi hàng tháng</label>
                                            <h6 class="mb-0" id="interest_payment_date">--</h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="note mt-2">
                                    <small class="text-muted">*Ngày kích hoạt hợp đồng tính từ ngày ký</small>
                                </div>
                            </div>
                        </div>

                        <!-- Investment Amount Section -->
                        <div class="investment-section mb-3">
                            <div class="section-title mb-2">
                                <h6 class="fw-bold mb-0">Số tiền đầu tư</h6>
                            </div>
                            <div class="form-group mb-3">
                                <div class="input-group input-group-lg">
                                    <input type="number" class="form-control form-control-lg" id="investment_amount" name="amount" value="{{ (int)$project->share_amount }}" min="{{ (int)$project->share_amount }}" step="0.01" readonly>
                                    <span class="input-group-text" style="border: 2px solid #FFD700 !important; background: #FFD700 !important; color: #000 !important;">{{ gs('cur_text') }}</span>
                                </div>
                                <small class="text-muted d-block mt-1">
                                    Giá 1 đơn vị: {{ number_format($project->share_amount, 0, ',', '.') }} {{ gs('cur_text') }} 
                                    (Tổng {{ $project->share_count }} đơn vị)
                                </small>
                            </div>
                        </div>

                        <!-- Referral Code Section -->
                        <div class="referral-section">
                            <div class="section-title mb-2">
                                <h6 class="fw-bold mb-0">Mã giới thiệu</h6>
                                <p class="text-muted small mb-0">Nhập mã giới thiệu nếu bạn có</p>
                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="las la-user-friends"></i>
                                    </span>
                                    <input type="text" class="form-control" name="referral_code" placeholder="Nhập mã giới thiệu" @if($user && $user->is_staff) required @endif>
                                </div>
                            </div>
                            @if($user && $user->is_staff)
                                <small class="text-danger">* Mã giới thiệu là bắt buộc đối với nhân viên chăm sóc</small>
                            @endif
                        </div>
                    </div>

                    <!-- Profit Schedule Table -->
                    <div class="mt-3">
                        <button type="button" id="view-profit-schedule-btn" class="btn btn-success w-100" data-bs-toggle="modal" data-bs-target="#profitScheduleModal">
                            <i class="las la-file-pdf"></i> Xem Bảng Lãi Chi Tiết
                        </button>
                    </div>

                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary btn-lg px-5" @if($needsVerification) disabled @endif>Xác nhận đầu tư</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Profit Schedule Modal -->
<div class="modal fade" id="profitScheduleModal" tabindex="-1" aria-labelledby="profitScheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header profit-schedule-header">
                <div class="profit-schedule-title">
                    <div class="header-text">
                        <div class="country-name">CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM</div>
                        <div class="motto">Độc lập - Tự do - Hạnh phúc</div>
                        <div class="separator">-------o0o-------</div>
                        <div class="main-title">BẢNG LÃI DỰ KIẾN</div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="profitScheduleContent">
                <div style="text-align: center; margin-bottom: 20px;">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p>Đang tải bảng lãi...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Close">Đóng</button>
                <a href="#" id="download-pdf-btn" class="btn btn-primary" target="_blank">
                    <i class="las la-download"></i> Tải PDF
                </a>
            </div>
        </div>
    </div>
</div>

<style>
/* Optimized Modal Size */
.modal-dialog {
    max-width: 500px;
    margin: 1.75rem auto;
    width: 100%;
    box-sizing: border-box;
}

.modal-dialog .modal-content {
    width: 100%;
    max-width: 100%;
    box-sizing: border-box;
}

.modal-content {
    border: none;
    border-radius: 16px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.15);
    background: var(--bs-body-bg);
    transform: translateY(20px);
    opacity: 0;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    max-height: calc(100vh - 3.5rem);
    width: 100%;
    box-sizing: border-box;
    overflow: hidden;
}

.modal.show .modal-content {
    transform: translateY(0);
    opacity: 1;
}

.modal-header {
    padding: 1rem 1.25rem 0.5rem;
    background: var(--bs-body-bg);
    border-radius: 16px 16px 0 0;
    position: relative;
}

.modal-header::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 40px;
    height: 3px;
    background: #FFD700;
    border-radius: 2px;
    opacity: 0.5;
}

.modal-body {
    padding: 1rem 1.25rem;
    width: 100%;
    box-sizing: border-box;
    overflow-x: hidden;
}

.modal-footer {
    padding: 0.75rem 1.25rem;
    background: var(--bs-body-bg);
    border-radius: 0 0 16px 16px;
    width: 100%;
    box-sizing: border-box;
    max-width: 100%;
    overflow: hidden;
}

/* Optimized Section Styles */
.investment-section,
.profit-estimation-section,
.referral-section {
    background: var(--bs-body-bg);
    padding: 1rem;
    border-radius: 12px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.06);
    border: 1px solid rgba(255, 215, 0, 0.1);
    width: 100%;
    box-sizing: border-box;
}

/* Input Group Full Width */
.input-group,
.input-group-lg {
    width: 100%;
}

.input-group .form-control,
.input-group-lg .form-control {
    width: 100%;
    flex: 1;
}

.input-group .input-group-text,
.input-group-lg .input-group-text {
    flex-shrink: 0;
}

/* Form Group Full Width */
.form-group {
    width: 100%;
}

/* Payment Options Wrapper */
.payment-options-wrapper {
    width: 100%;
    box-sizing: border-box;
}

/* Optimized Input Styles */
.input-group-lg {
    box-shadow: 0 4px 16px rgba(0,0,0,0.06);
    border-radius: 10px;
    overflow: hidden;
}

.input-group-lg .form-control {
    border: 2px solid rgba(255, 215, 0, 0.2);
    padding: 0.75rem 1rem;
    font-size: 1rem;
    font-weight: 600;
    text-align: center;
}

.input-group-lg .form-control::-webkit-inner-spin-button,
.input-group-lg .form-control::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

.input-group-lg .form-control[type=number] {
    -moz-appearance: textfield;
}

.input-group-lg .input-group-text {
    border: 2px solid #FFD700;
    background: #FFD700;
    color: #000;
    font-weight: 700;
    padding: 0 1rem;
    font-size: 0.95rem;
}

/* Optimized Profit Item Styles */
.profit-item {
    background: var(--bs-light);
    padding: 0.75rem;
    border-radius: 10px;
    border: 1px solid rgba(255, 215, 0, 0.15);
    display: flex;
    flex-direction: column;
    height: 100%;
}

.profit-item label {
    font-size: 0.8rem;
    margin-bottom: 0.25rem;
    display: block;
    color: var(--bs-gray-600);
    font-weight: 500;
    white-space: nowrap;
}

.profit-item h6 {
    color: var(--bs-body-color);
    font-weight: 700;
    font-size: 0.95rem;
    margin: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Amount Detail Styles */
.amount-detail {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
}

.amount-detail-item {
    flex: 1;
    display: flex;
    flex-direction: column;
    min-width: 0; /* Allows text truncation to work */
}

.amount-detail-item__label {
    font-size: 0.8rem;
    color: var(--bs-gray-600);
    margin-bottom: 0.25rem;
}

.amount-detail-item__value {
    font-size: 0.95rem;
    font-weight: 700;
    color: var(--bs-body-color);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.amount-detail-sperator {
    color: var(--bs-gray-500);
    font-size: 1.2rem;
    flex-shrink: 0;
}

/* Detail List Styles */
.detail-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.detail-list-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
    border-bottom: 1px solid rgba(0,0,0,0.05);
}

.detail-list-item:last-child {
    border-bottom: none;
}

.detail-list-item__label {
    font-size: 0.85rem;
    color: var(--bs-gray-600);
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.detail-list-item__value {
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--bs-body-color);
    white-space: nowrap;
    margin-left: 1rem;
}

/* Optimized Button Styles */
.btn-primary {
    background: linear-gradient(45deg, #FFD700, #FFC800);
    border: none;
    padding: 0.75rem 1.75rem;
    font-weight: 700;
    font-size: 0.95rem;
    border-radius: 10px;
    box-shadow: 0 4px 16px rgba(255, 215, 0, 0.25);
    color: #000;
}

.btn-success {
    background: linear-gradient(45deg, #28a745, #20c997);
    border: none;
    padding: 0.75rem 1.75rem;
    font-weight: 700;
    font-size: 0.95rem;
    border-radius: 10px;
    box-shadow: 0 4px 16px rgba(40, 167, 69, 0.25);
    color: white;
    transition: all 0.3s ease;
}

.btn-success:hover {
    background: linear-gradient(45deg, #218838, #1ea085);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(40, 167, 69, 0.35);
    color: white;
}

.btn-light {
    background: #E9ECEF;
    border: none;
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    font-size: 0.9rem;
    border-radius: 10px;
    color:rgb(51, 56, 59);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

/* Section Title Optimization */
.section-title {
    margin-bottom: 0.5rem;
}

.section-title h6 {
    font-size: 0.95rem;
    margin-bottom: 0.25rem;
}

.section-title p {
    font-size: 0.8rem;
    margin-bottom: 0;
}

/* Note Text Optimization */
.note {
    font-size: 0.75rem;
    color: var(--bs-gray-600);
    margin-top: 0.5rem;
}

/* Responsive Optimization */
@media (max-width: 768px) {
    .modal-dialog {
        margin: 1rem;
    }
    
    .modal-body {
        padding: 0.75rem 1rem;
    }
    
    .investment-section,
    .profit-estimation-section,
    .referral-section {
        padding: 0.75rem;
    }
    
    .btn-primary,
    .btn-light {
        padding: 0.75rem 1.25rem;
        font-size: 0.9rem;
    }
    
    .input-group-lg .form-control {
        font-size: 0.95rem;
        padding: 0.75rem;
    }

    .amount-detail {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }

    .amount-detail-sperator {
        transform: rotate(90deg);
        margin: 0.5rem 0;
    }

    .detail-list-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.25rem;
    }

    .detail-list-item__value {
        margin-left: 0;
    }
}

/* Profit Schedule Modal Specific Styles */
#profitScheduleModal .modal-dialog {
    max-width: none !important;
    width: 100% !important;
    height: 100% !important;
    margin: 0 !important;
}

#profitScheduleModal .modal-content {
    height: 100vh !important;
    border-radius: 0 !important;
    transform: none !important;
    opacity: 1 !important;
    max-height: none !important;
    width: 100% !important;
}

#profitScheduleModal .modal-header {
    padding: 0 !important;
    border-bottom: none !important;
    background: white !important;
    position: relative;
}

#profitScheduleModal .profit-schedule-header {
    padding: 2rem 2rem 1rem 2rem !important;
    text-align: center;
    background: white !important;
    border-bottom: 2px solid #e9ecef !important;
}

#profitScheduleModal .profit-schedule-title {
    flex: 1;
    display: flex;
    justify-content: center;
    align-items: center;
}

#profitScheduleModal .header-text {
    font-family: "Times New Roman", Times, serif !important;
    color: #000000 !important;
}

#profitScheduleModal .country-name {
    font-weight: bold !important;
    font-size: 14pt !important;
    margin-bottom: 5px !important;
    color: #000000 !important;
}

#profitScheduleModal .motto {
    font-weight: bold !important;
    font-size: 14pt !important;
    margin-bottom: 5px !important;
    color: #000000 !important;
}

#profitScheduleModal .separator {
    font-size: 12pt !important;
    margin-bottom: 10px !important;
    color: #000000 !important;
}

#profitScheduleModal .main-title {
    font-size: 18pt !important;
    font-weight: bold !important;
    text-transform: uppercase !important;
    color: #000000 !important;
    letter-spacing: 1px !important;
}

#profitScheduleModal .modal-body {
    padding: 2rem !important;
    overflow-y: auto !important;
    height: calc(100vh - 140px) !important;
    background: white !important;
}

#profitScheduleModal .modal-footer {
    padding: 1rem 2rem !important;
    border-top: 2px solid #e9ecef !important;
    background: white !important;
}

#profitScheduleModal .btn-close {
    position: absolute;
    top: 1rem;
    right: 1rem;
    z-index: 10;
    background: rgba(0,0,0,0.1);
    border-radius: 50%;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
}

#profitScheduleModal .btn-close:hover {
    background: rgba(0,0,0,0.2);
}

/* Landscape orientation for profit schedule */
@media (min-width: 768px) {
    #profitScheduleModal .modal-content {
        max-width: 1200px !important;
        margin: 2rem auto !important;
        height: calc(100vh - 4rem) !important;
        border-radius: 8px !important;
        box-shadow: 0 20px 60px rgba(0,0,0,0.15) !important;
    }
    
    #profitScheduleModal .modal-body {
        height: calc(100vh - 200px) !important;
    }
    
    #profitScheduleModal .profit-schedule-header {
        padding: 2.5rem 2rem 1.5rem 2rem !important;
    }
}

/* Form elements width consistency */
.investment-form {
    width: 100%;
    box-sizing: border-box;
}

.investment-form .form-group {
    width: 100%;
    box-sizing: border-box;
}

.investment-form .input-group,
.investment-form .input-group-lg {
    width: 100%;
    box-sizing: border-box;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const today = new Date();
    const maturityDate = new Date('{{ $project->maturity_date }}');
    
    // Tính toán giá 1 đơn vị dựa trên dữ liệu từ database
    const totalPackage = {{ $project->share_amount * $project->share_count }}; // Tổng gói
    const totalUnits = {{ $project->share_count }}; // Số lượng đơn vị tối đa từ database
    const unitPrice = {{ $project->share_amount }}; // Giá 1 đơn vị từ database
    
    const roiPercentage = {{ $project->roi_percentage }}; // 9%/năm
    const maturityMonths = {{ $project->maturity_time }}; // 2 tháng
    const projectId = {{ $project->id }};
    const scheduleUrl = new URL("{{ route('invest.profit.schedule.pdf') }}");
    const scheduleHtmlUrl = new URL("{{ route('invest.profit.schedule.html') }}");
    
    // Tính lãi suất cho thời hạn đầu tư
    const periodInterestRate = (roiPercentage / 100 / 12) * maturityMonths; // 1.5% cho 2 tháng
    
    const formatDate = (date) => {
        return date.toLocaleDateString('vi-VN', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
    };

    const formatCurrency = (amount) => {
        return amount.toLocaleString('vi-VN') + ' {{ gs('cur_text') }}';
    };

    // Hàm tính và hiển thị ngày đáo hạn và ngày trả lãi hàng tháng
    const setDates = () => {
        // Tính ngày đáo hạn (maturity date)
        const futureDate = new Date();
        futureDate.setMonth(futureDate.getMonth() + maturityMonths);
        document.getElementById('maturity_date').textContent = formatDate(futureDate);
        
        // Tính ngày trả lãi hàng tháng (mặc định ngày hiện tại của tháng)
        const currentDay = today.getDate();
        document.getElementById('interest_payment_date').textContent = currentDay;
    };

    // Hàm cập nhật modal với số tiền cụ thể
    window.updateModalValues = function(amount) {
        amount = parseFloat(amount);
        if (isNaN(amount) || amount < unitPrice) {
            amount = unitPrice;
        }
        
        // Tính số lượng đơn vị dựa trên số tiền
        const quantity = Math.ceil(amount / unitPrice);
        
        document.getElementById('modal_quantity').value = quantity;
        document.getElementById('investment_amount').value = amount;
        
        // Tính lãi dự kiến cho thời hạn đầu tư
        const totalProfit = Math.round(amount * (roiPercentage / 100) * (maturityMonths / 12));
        
        // Cập nhật giá trị hiển thị và giá trị ẩn
        document.getElementById('total_profit').textContent = formatCurrency(totalProfit);
        document.getElementById('modal_total_price').value = amount;
        document.getElementById('modal_unit_price').value = unitPrice;
        document.getElementById('modal_total_earning').value = totalProfit;
        
        // Cập nhật ngày đáo hạn và ngày trả lãi
        setDates();
    }

    // Khởi tạo với giá đơn vị
    window.updateModalValues(unitPrice);

    // Nếu có các sự kiện mở modal, cũng gọi lại updateModalValues để đảm bảo luôn đúng
    const bitModal = document.getElementById('bitModal');
    if (bitModal) {
        bitModal.addEventListener('show.bs.modal', function() {
            // Lấy số tiền từ input ở trang chi tiết dự án
            const projectDetailsAmount = parseFloat(document.getElementById('investment_amount_input')?.value) || unitPrice;
            
            // Cập nhật modal với số tiền đã nhập
            window.updateModalValues(projectDetailsAmount);
        });
    }

    // Load profit schedule modal content
    const loadProfitScheduleModal = (amount) => {
        const modalContent = document.getElementById('profitScheduleContent');
        const downloadBtn = document.getElementById('download-pdf-btn');
        
        // Show loading
        modalContent.innerHTML = `
            <div style="text-align: center; margin: 40px 0;">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-3">Đang tải bảng lãi...</p>
            </div>
        `;

        // Fetch HTML content
        const url = new URL(scheduleHtmlUrl);
        url.searchParams.set('project_id', projectId);
        url.searchParams.set('amount', amount);

        fetch(url.toString())
            .then(response => response.text())
            .then(html => {
                modalContent.innerHTML = html;
                // Hide preloader if exists
                if (window.$ && typeof $ === 'function') {
                    $('.preloader').fadeOut();
                } else {
                    document.querySelectorAll('.preloader').forEach(function(el) {
                        el.style.display = 'none';
                    });
                }
                // Update download PDF link
                if (downloadBtn) {
                    const pdfUrl = new URL(scheduleUrl);
                    pdfUrl.searchParams.set('project_id', projectId);
                    pdfUrl.searchParams.set('amount', amount);
                    downloadBtn.href = pdfUrl.toString();
                }
            })
            .catch(error => {
                modalContent.innerHTML = `
                    <div style="text-align: center; margin: 40px 0; color: #dc3545;">
                        <i class="las la-exclamation-triangle" style="font-size: 3rem;"></i>
                        <p class="mt-3">Có lỗi xảy ra khi tải bảng lãi. Vui lòng thử lại.</p>
                    </div>
                `;
                if (window.$ && typeof $ === 'function') {
                    $('.preloader').fadeOut();
                } else {
                    document.querySelectorAll('.preloader').forEach(function(el) {
                        el.style.display = 'none';
                    });
                }
                console.error('Error loading profit schedule:', error);
            });
    };

    // Load profit schedule when modal opens
    const profitScheduleModal = document.getElementById('profitScheduleModal');
    if (profitScheduleModal) {
        profitScheduleModal.addEventListener('show.bs.modal', function() {
            const amt = parseFloat(document.getElementById('investment_amount').value) || unitPrice;
            loadProfitScheduleModal(amt);
        });
        
        // Handle closing profit schedule modal
        profitScheduleModal.addEventListener('hidden.bs.modal', function() {
            // Ensure the investment modal is still open
            const bitModal = document.getElementById('bitModal');
            if (bitModal && !bitModal.classList.contains('show')) {
                // If investment modal was closed, reopen it
                const bsModal = new bootstrap.Modal(bitModal);
                bsModal.show();
            }
        });
    }
    
    // Handle close button click
    const closeButtons = document.querySelectorAll('#profitScheduleModal .btn-close, #profitScheduleModal .btn-secondary');
    closeButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const profitScheduleModal = document.getElementById('profitScheduleModal');
            const bsModal = bootstrap.Modal.getInstance(profitScheduleModal);
            if (bsModal) {
                bsModal.hide();
            }
        });
    });
});
</script>
