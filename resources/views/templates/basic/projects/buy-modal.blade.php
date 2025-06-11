<div class="modal fade custom--modal" id="bitModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Pay Via for ') {{ __($project->title) }}</h5>
                <button class="btn-close modal-icon" data-bs-dismiss="modal" type="button" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('user.invest.order') }}" method="post">
                    @csrf
                    <input type="hidden" name="project_id" value="{{ $project->id }}">
                    <input type="hidden" name="quantity" id="modal_quantity" value="1">
                    <input type="hidden" name="payment_type" id="payment_type" value="">
                    <input type="hidden" name="total_price" id="modal_total_price" value="">
                    <input type="hidden" name="unit_price" id="modal_unit_price" value="{{ $project->share_amount }}">
                    <input type="hidden" name="total_earning" id="modal_total_earning" value="">

                    <div class="payment-options-wrapper mb-3">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>@lang('Investment Amount')</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="investment_amount" name="amount" value="{{ $project->share_amount }}" readonly>
                                        <span class="input-group-text">{{ gs('cur_text') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>@lang('Referral Code') ({{ __('Optional') }})</label>
                                    <input type="text" class="form-control" name="referral_code" placeholder="@lang('Enter referral code')">
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="contract-content mb-3">
                                    <h6>@lang('Contract Terms')</h6>
                                    <div class="contract-text" style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; padding: 15px; margin-bottom: 15px;">
                                        {!! $project->contract_content !!}
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="contract_confirmed" id="contract_confirmed" required>
                                        <label class="form-check-label" for="contract_confirmed">
                                            @lang('I have read and agree to the contract terms')
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="payment-options">
                                    <h6>@lang('Select Payment Method')</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="payment-option">
                                                <input type="radio" name="payment_type" id="wallet_payment" value="2" class="d-none">
                                                <label for="wallet_payment" class="payment-option-label">
                                                    <i class="las la-wallet"></i>
                                                    <span>@lang('Wallet Balance')</span>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="payment-option">
                                                <input type="radio" name="payment_type" id="online_payment" value="1" class="d-none">
                                                <label for="online_payment" class="payment-option-label">
                                                    <i class="las la-credit-card"></i>
                                                    <span>@lang('Online Payment')</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn-primary">@lang('Confirm Investment')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.payment-option-label {
    display: block;
    padding: 15px;
    border: 1px solid #ddd;
    border-radius: 5px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s;
}

.payment-option-label:hover {
    border-color: #007bff;
}

input[type="radio"]:checked + .payment-option-label {
    border-color: #007bff;
    background-color: #f8f9fa;
}

.contract-text {
    font-size: 14px;
    line-height: 1.5;
}
</style>
