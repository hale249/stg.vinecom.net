<div class="modal fade custom--modal" id="bitModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">@lang('Invest in ') <span class="text-primary">{{ __($project->title) }}</span></h5>
                <button class="btn-close modal-icon" data-bs-dismiss="modal" type="button" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('user.invest.order') }}" method="post" class="investment-form">
                    @csrf
                    <input type="hidden" name="project_id" value="{{ $project->id }}">
                    <input type="hidden" name="quantity" id="modal_quantity" value="1">
                    <input type="hidden" name="payment_type" id="payment_type" value="">
                    <input type="hidden" name="total_price" id="modal_total_price" value="">
                    <input type="hidden" name="unit_price" id="modal_unit_price" value="{{ $project->share_amount }}">
                    <input type="hidden" name="total_earning" id="modal_total_earning" value="">

                    <div class="payment-options-wrapper">
                        <!-- Investment Amount Section -->
                        <div class="investment-section mb-4">
                            <div class="section-title mb-3">
                                <h6 class="fw-bold mb-0">@lang('Investment Amount')</h6>
                                <p class="text-muted small mb-0">@lang('Fixed investment amount per share')</p>
                            </div>
                            <div class="form-group">
                                <div class="input-group input-group-lg">
                                    <input type="number" class="form-control form-control-lg" id="investment_amount" name="amount" value="{{ $project->share_amount }}" readonly>
                                    <span class="input-group-text bg-primary text-white">{{ gs('cur_text') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Referral Code Section -->
                        <div class="referral-section mb-4">
                            <div class="section-title mb-3">
                                <h6 class="fw-bold mb-0">@lang('Referral Code')</h6>
                                <p class="text-muted small mb-0">@lang('Enter referral code if you have one')</p>
                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="las la-user-friends"></i>
                                    </span>
                                    <input type="text" class="form-control" name="referral_code" placeholder="@lang('Enter referral code')">
                                </div>
                            </div>
                        </div>

                        <!-- Payment Methods Section -->
                        <div class="payment-section">
                            <div class="section-title mb-3">
                                <h6 class="fw-bold mb-0">@lang('Select Payment Method')</h6>
                                <p class="text-muted small mb-0">@lang('Choose your preferred payment method')</p>
                            </div>
                            <div class="payment-options">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="payment-option">
                                            <input type="radio" name="payment_type" id="wallet_payment" value="2" class="d-none">
                                            <label for="wallet_payment" class="payment-option-label">
                                                <div class="payment-icon">
                                                    <i class="las la-wallet"></i>
                                                </div>
                                                <div class="payment-info">
                                                    <span class="payment-title">@lang('Wallet Balance')</span>
                                                    <span class="payment-subtitle">@lang('Pay from your wallet')</span>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="payment-option">
                                            <input type="radio" name="payment_type" id="online_payment" value="1" class="d-none">
                                            <label for="online_payment" class="payment-option-label">
                                                <div class="payment-icon">
                                                    <i class="las la-credit-card"></i>
                                                </div>
                                                <div class="payment-info">
                                                    <span class="payment-title">@lang('Online Payment')</span>
                                                    <span class="payment-subtitle">@lang('Pay with card or bank')</span>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">@lang('Cancel')</button>
                        <button type="submit" class="btn btn-primary btn-lg px-5">@lang('Confirm Investment')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.modal-content {
    border: none;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.modal-header {
    padding: 1.5rem 1.5rem 0.5rem;
}

.modal-body {
    padding: 1.5rem;
}

.modal-footer {
    padding: 1.5rem;
}

.section-title {
    margin-bottom: 1rem;
}

.payment-option-label {
    display: flex;
    align-items: center;
    padding: 1.25rem;
    border: 2px solid #e9ecef;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
    background-color: #fff;
    height: 100%;
}

.payment-option-label:hover {
    border-color: #0d6efd;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(13, 110, 253, 0.1);
}

input[type="radio"]:checked + .payment-option-label {
    border-color: #0d6efd;
    background-color: #f8f9ff;
}

.payment-icon {
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #f8f9fa;
    border-radius: 12px;
    margin-right: 1rem;
}

.payment-icon i {
    font-size: 1.5rem;
    color: #0d6efd;
}

.payment-info {
    display: flex;
    flex-direction: column;
}

.payment-title {
    font-weight: 600;
    color: #212529;
    margin-bottom: 0.25rem;
}

.payment-subtitle {
    font-size: 0.875rem;
    color: #6c757d;
}

.btn-primary {
    padding: 0.75rem 2rem;
    font-weight: 600;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 5px 15px rgba(13, 110, 253, 0.2);
}

.btn-light {
    padding: 0.75rem 2rem;
    font-weight: 600;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.input-group-text {
    border: none;
}

.form-control {
    border: 1px solid #e9ecef;
    padding: 0.75rem 1rem;
    border-radius: 8px;
}

.form-control:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
}

/* Animation for Modal */
.modal.fade .modal-dialog {
    transform: scale(0.95);
    transition: transform 0.3s ease-out;
}

.modal.show .modal-dialog {
    transform: scale(1);
}

/* Investment Amount Section Enhancement */
.investment-section .input-group-lg {
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.investment-section .input-group-text {
    font-weight: 600;
}

/* Referral Code Section Enhancement */
.referral-section .input-group {
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.referral-section .input-group-text i {
    color: #6c757d;
}

/* Payment Section Enhancement */
.payment-section {
    margin-top: 2rem;
}

.payment-option {
    height: 100%;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .modal-dialog {
        margin: 1rem;
    }
    
    .payment-option-label {
        padding: 1rem;
    }
    
    .payment-icon {
        width: 40px;
        height: 40px;
    }
    
    .payment-icon i {
        font-size: 1.25rem;
    }
}
</style>
