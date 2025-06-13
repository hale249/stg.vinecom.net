<!-- Main Modal -->
<div class="modal fade custom--modal" id="bitModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Đầu tư vào <span class="text-primary" style="color: #FFD700 !important;">{{ __($project->title) }}</span></h5>
                
            </div>
            <div class="modal-body">
                <form action="{{ route('user.invest.order') }}" method="post" class="investment-form">
                    @csrf
                    <input type="hidden" name="project_id" value="{{ $project->id }}">
                    <input type="hidden" name="quantity" id="modal_quantity" value="1">
                    <input type="hidden" name="payment_type" value="2">
                    <input type="hidden" name="total_price" id="modal_total_price" value="">
                    <input type="hidden" name="unit_price" id="modal_unit_price" value="{{ $project->share_amount }}">
                    <input type="hidden" name="total_earning" id="modal_total_earning" value="">

                    <div class="payment-options-wrapper">
                        <!-- Investment Amount Section -->
                        <div class="investment-section mb-3">
                            <div class="section-title mb-2">
                                <h6 class="fw-bold mb-0">Số tiền đầu tư</h6>
                            </div>
                            <div class="form-group">
                                <div class="input-group input-group-lg">
                                    <input type="number" class="form-control form-control-lg" id="investment_amount" name="amount" value="{{ (int)$project->share_amount }}" readonly>
                                    <span class="input-group-text" style="border: 2px solid #FFD700 !important; background: #FFD700 !important; color: #000 !important;">{{ gs('cur_text') }}</span>
                                </div>
                            </div>
                        </div>

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
                                    <input type="text" class="form-control" name="referral_code" placeholder="Nhập mã giới thiệu">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary btn-lg px-5">Xác nhận đầu tư</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
/* Optimized Modal Size */
.modal-dialog {
    max-width: 500px;
    margin: 1.75rem auto;
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
}

.modal-footer {
    padding: 0.75rem 1.25rem;
    background: var(--bs-body-bg);
    border-radius: 0 0 16px 16px;
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
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const today = new Date();
    const maturityDate = new Date('{{ $project->maturity_date }}');
    const unitPrice = {{ $project->share_amount }};
    const roiPercentage = {{ $project->roi_percentage }};
    
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

    const updateModalValues = (quantity) => {
        const totalAmount = unitPrice * quantity;
        const totalProfit = (totalAmount * roiPercentage) / 100;
        
        // Update investment amount
        document.getElementById('investment_amount').value = totalAmount;
        
        // Update total profit
        document.getElementById('total_profit').textContent = formatCurrency(totalProfit);
        
        // Update hidden inputs
        document.getElementById('modal_quantity').value = quantity;
        document.getElementById('modal_total_price').value = totalAmount;
        document.getElementById('modal_total_earning').value = totalProfit;
    };

    const setDates = () => {
        document.getElementById('maturity_date').textContent = formatDate(maturityDate);
        
        // Set interest payment date to current day only
        document.getElementById('interest_payment_date').textContent = today.getDate();
    };

    updateModalValues(1);
    setDates();

    const quantityInput = document.querySelector('.product-qty__value');
    if (quantityInput) {
        quantityInput.addEventListener('change', function() {
            const quantity = parseInt(this.value) || 1;
            // Ensure quantity is between 1 and max
            const maxValue = parseInt(this.max) || 18;
            const validQuantity = Math.min(Math.max(1, quantity), maxValue);
            this.value = validQuantity;
            updateModalValues(validQuantity);
        });
    }


    const bitModal = document.getElementById('bitModal');
    if (bitModal) {
        bitModal.addEventListener('show.bs.modal', function() {
            const currentQuantity = parseInt(quantityInput.value) || 1;
            updateModalValues(currentQuantity);
        });
    }
});
</script>
