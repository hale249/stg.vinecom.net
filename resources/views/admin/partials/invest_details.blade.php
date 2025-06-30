<div class="card mb-3">
    <div class="card-header bg-primary">
        <h5 class="mb-0 text-white">@lang('Thông tin đầu tư')</h5>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-6">
                <ul class="list-group list-group-flush">
                    <!-- Invest No -->
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="las la-hashtag text-primary me-2"></i>@lang('Mã đầu tư')</span>
                        <span class="fw-bold">{{ $invest->invest_no }}</span>
                    </li>
                    <!-- Total Invest -->
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="las la-money-bill-wave text-primary me-2"></i>@lang('Tổng tiền đầu tư')</span>
                        <span class="fw-bold">{{ showAmount($invest->total_price) }} VND</span>
                    </li>
                    <!-- Payment Type -->
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="las la-credit-card text-primary me-2"></i>@lang('Loại thanh toán')</span>
                        {!! $invest->paymentTypeBadge !!}
                    </li>
                    <!-- Payment Method -->
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="las la-wallet text-primary me-2"></i>@lang('Phương thức thanh toán')</span>
                        @if ($invest->deposit)
                            <span class="fw-bold">{{ __($invest->deposit->gateway->name) }}</span>
                        @else
                            <span class="fw-bold">@lang('Ví điện tử')</span>
                        @endif
                    </li>
                    <!-- Payment TRX -->
                    @if (@$invest->deposit->trx)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="las la-receipt text-primary me-2"></i>@lang('Mã giao dịch')</span>
                            <span class="fw-bold">{{ @$invest->deposit->trx }}</span>
                        </li>
                    @endif
                    <!-- Payment Status -->
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="las la-check-circle text-primary me-2"></i>@lang('Trạng thái thanh toán')</span>
                        {!! $invest->paymentStatusBadge !!}
                    </li>
                    <!-- Contract Status -->
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="las la-file-contract text-primary me-2"></i>@lang('Trạng thái hợp đồng')</span>
                        {!! $invest->statusBadge !!}
                    </li>
                </ul>
            </div>
            <div class="col-md-6">
                <ul class="list-group list-group-flush">
                    <!-- Order Date -->
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="las la-calendar-check text-primary me-2"></i>@lang('Ngày đặt hàng')</span>
                        <span class="fw-bold">{{ showDateTime($invest->created_at) }}</span>
                    </li>
                    
                    <!-- Start Date -->
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="las la-calendar-plus text-primary me-2"></i>@lang('Ngày bắt đầu')</span>
                        <span class="fw-bold">{{ $invest->status == Status::INVEST_RUNNING ? showDateTime($invest->updated_at) : 'Chưa kích hoạt' }}</span>
                    </li>
                    
                    <!-- Maturity Date -->
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="las la-calendar-times text-primary me-2"></i>@lang('Ngày đáo hạn')</span>
                        <span class="fw-bold">{{ $invest->project_closed ? showDateTime($invest->project_closed) : 'Chưa xác định' }}</span>
                    </li>
                    
                    <!-- Duration -->
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="las la-clock text-primary me-2"></i>@lang('Thời hạn')</span>
                        <span class="fw-bold">{{ $invest->project_duration }} @lang('Tháng')</span>
                    </li>
                    
                    <!-- Next Payment -->
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="las la-calendar-day text-primary me-2"></i>@lang('Thanh toán lãi tiếp theo')</span>
                        <span class="fw-bold">{{ $invest->next_time ? showDateTime($invest->next_time) : 'Chưa xác định' }}</span>
                    </li>
                    
                    <!-- Last Payment -->
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="las la-history text-primary me-2"></i>@lang('Thanh toán lãi gần nhất')</span>
                        <span class="fw-bold">{{ $invest->last_time ? showDateTime($invest->last_time) : 'Chưa có' }}</span>
                    </li>
                    
                    <!-- Periods Paid -->
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="las la-list-ol text-primary me-2"></i>@lang('Số kỳ đã thanh toán')</span>
                        <span class="fw-bold">{{ $invest->period }} @lang('kỳ')</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Referral Information Card -->
<div class="card mb-3">
    <div class="card-header bg-primary">
        <h5 class="mb-0 text-white">@lang('Thông tin giới thiệu')</h5>
    </div>
    <div class="card-body">
        @php
            $referrer = null;
            if ($invest->referral_code) {
                $referrer = \App\Models\User::where('referral_code', $invest->referral_code)->first();
            }
        @endphp
        
        @if ($referrer)
            <div class="row">
                <div class="col-md-6">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="las la-user text-primary me-2"></i>@lang('Người giới thiệu')</span>
                            <a href="{{ route('admin.users.detail', $referrer->id) }}" class="fw-bold text-primary">
                                {{ $referrer->username }}
                            </a>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="las la-user-tag text-primary me-2"></i>@lang('Tên đầy đủ')</span>
                            <span class="fw-bold">{{ $referrer->fullname }}</span>
                        </li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="las la-envelope text-primary me-2"></i>@lang('Email')</span>
                            <span class="fw-bold">{{ $referrer->email }}</span>
                        </li>
                        @if ($referrer->is_staff)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="las la-user-tie text-primary me-2"></i>@lang('Vai trò')</span>
                                <span class="badge bg-primary">{{ ucfirst(str_replace('_', ' ', $referrer->role)) }}</span>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        @else
            <div class="text-center py-3">
                <i class="las la-user-slash fa-3x text-muted"></i>
                <p class="mt-2">@lang('Không có thông tin người giới thiệu')</p>
                @if ($invest->referral_code)
                    <p class="small text-muted">@lang('Mã giới thiệu:') {{ $invest->referral_code }}</p>
                @endif
            </div>
        @endif
    </div>
</div>

<div class="project-details">
    <div class="row">
        <!-- Project Details -->
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header bg-primary">
                    <h5 class="mb-0 text-white">@lang('Thông tin dự án')</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <!-- Project Name -->
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="las la-project-diagram text-primary me-2"></i>@lang('Tên dự án')</span>
                            <a href="{{ route('admin.project.edit', $invest->project->id) }}" class="fw-bold text-primary">
                                {{ __(strLimit($invest->project->title, 25)) }}
                            </a>
                        </li>
                        <!-- Quantity -->
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="las la-sort-numeric-up text-primary me-2"></i>@lang('Số lượng')</span>
                            <span class="fw-bold">{{ $invest->quantity > 0 ? $invest->quantity : 1 }}</span>
                        </li>
                        <!-- Price -->
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="las la-tag text-primary me-2"></i>@lang('Giá')</span>
                            <span class="fw-bold">{{ showAmount($invest->unit_price > 0 ? $invest->unit_price : $invest->total_price) }} VND</span>
                        </li>
                        <!-- Subtotal -->
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="las la-money-bill text-primary me-2"></i>@lang('Tổng cộng')</span>
                            <span class="fw-bold">{{ showAmount($invest->total_price) }} VND</span>
                        </li>
                        <!-- Project Type -->
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="las la-info-circle text-primary me-2"></i>@lang('Loại')</span>
                            <span class="fw-bold">
                                @if ($invest->project->return_type == Status::LIFETIME)
                                    @lang('Dài hạn')
                                @else
                                    @lang('Định kỳ')
                                @endif
                            </span>
                        </li>
                        <!-- Conditional Display of Return Repeat Times or Project Duration -->
                        @if ($invest->project->return_type != Status::LIFETIME)
                            <!-- Return Repeat Times -->
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="las la-redo-alt text-primary me-2"></i>@lang('Số kỳ thanh toán')</span>
                                <span class="fw-bold">{{ $invest->project->repeat_times }}
                                    {{ __($invest->project->time->name ?? 'Tháng') }}</span>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>

        </div>

        <!-- Investment Details -->
        <div class="col-md-6">
            @php
                // Calculate Total Earnings and Profit
                $quantity = $invest->quantity > 0 ? $invest->quantity : 1;
                $roiAmount = $invest->roi_amount;
                $roiPercentage = $invest->roi_percentage > 0 ? $invest->roi_percentage : 0;
                $capitalBack = $invest->project->capital_back;
                $returnType = $invest->project->return_type;
                $projectDuration = $invest->project_duration;
                $repeatTimes = $invest->repeat_times;
                $timeName = $invest->project->time->name ?? 'Tháng';
                $currencySymbol = gs('cur_sym');

                $totalEarnings = 0;

                if ($returnType == Status::LIFETIME) {
                    $totalMonths = $projectDuration;
                    $payHours = $invest->project->time->hours ?? 24;
                    $payAmount = $roiAmount > 0 ? $roiAmount : 0;

                    $totalHours = $totalMonths * 720;

                    $totalPayments = floor($totalHours / $payHours);

                    $totalEarnings = $totalPayments * $payAmount * $quantity;
                } else {
                    $payAmount = $roiAmount > 0 ? $roiAmount : 0;
                    $totalEarnings = $payAmount * $repeatTimes * $quantity;
                }

                // Profit Earning (excluding capital back)
                $profitEarning = $totalEarnings;

                // Total Earning (including capital back if applicable)
                $totalEarning = $profitEarning;
                if ($capitalBack == Status::YES) {
                    $totalEarning += $invest->total_price;
                }

                // Earning ROI Amount per interval
                $earningROIAmount = $payAmount * $quantity;
                
                // Calculate next ROI payment date
                $nextPaymentDate = $invest->next_time ? \Carbon\Carbon::parse($invest->next_time) : null;
                
                // Calculate actual ROI received so far
                $actualROI = $invest->total_earning;
                
                // Calculate remaining ROI
                $remainingROI = $profitEarning - $actualROI;
                if ($remainingROI < 0) $remainingROI = 0;
                
                // Calculate percentage of completion
                $completionPercentage = $profitEarning > 0 ? ($actualROI / $profitEarning) * 100 : 0;
                if ($completionPercentage > 100) $completionPercentage = 100;
            @endphp
            <div class="card">
                <div class="card-header bg-primary">
                    <h5 class="mb-0 text-white">@lang('Chi tiết lợi nhuận')</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <!-- ROI Percentage -->
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="las la-percentage text-primary me-2"></i>@lang('Tỷ lệ ROI')</span>
                            <span class="fw-bold">{{ getAmount($roiPercentage) }}@lang('%')</span>
                        </li>
                        <!-- Earning ROI Amount -->
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="las la-hand-holding-usd text-primary me-2"></i>@lang('Lợi nhuận ROI')</span>
                            <span class="fw-bold">{{ showAmount($earningROIAmount) }} /
                                {{ __($timeName) }}</span>
                        </li>
                        <!-- Next ROI Payment Date -->
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="las la-calendar-alt text-primary me-2"></i>@lang('Thanh toán ROI tiếp theo')</span>
                            <span class="fw-bold">{{ $nextPaymentDate ? showDateTime($nextPaymentDate) : 'Chưa xác định' }}</span>
                        </li>
                        <!-- ROI Received -->
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="las la-coins text-primary me-2"></i>@lang('ROI đã nhận')</span>
                            <span class="fw-bold">{{ showAmount($actualROI) }} VND</span>
                        </li>
                        <!-- Remaining ROI -->
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="las la-hourglass-half text-primary me-2"></i>@lang('ROI còn lại')</span>
                            <span class="fw-bold">{{ showAmount($remainingROI) }} VND</span>
                        </li>
                        
                        <!-- Progress bar -->
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span><i class="las la-chart-line text-primary me-2"></i>@lang('Tiến độ thanh toán')</span>
                                <span class="fw-bold">{{ number_format($completionPercentage, 2) }}%</span>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $completionPercentage }}%;" 
                                     aria-valuenow="{{ $completionPercentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </li>
                        
                        <!-- Profit Earning -->
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="las la-chart-pie text-primary me-2"></i>@lang('Lợi nhuận thu được')</span>
                            @if ($invest->project->return_type != Status::LIFETIME)
                                <span class="fw-bold">{{ showAmount($profitEarning) }} VND</span>
                            @else
                                <span class="fw-bold">{{ showAmount($earningROIAmount) }} /
                                    {{ __($timeName) }}</span>
                            @endif
                        </li>
                        <!-- Total Earning -->
                        @if ($invest->project->return_type != Status::LIFETIME)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="las la-calculator text-primary me-2"></i>@lang('Tổng thu nhập')</span>
                                <span class="fw-bold">{{ showAmount($totalEarning) }} VND</span>
                            </li>
                        @endif
                        <!-- Capital Back -->
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="las la-undo-alt text-primary me-2"></i>@lang('Hoàn vốn')</span>
                            <span class="fw-bold">
                                @if ($capitalBack == Status::YES)
                                    <span class="badge bg-success">@lang('Có')</span>
                                @else
                                    <span class="badge bg-danger">@lang('Không')</span>
                                @endif
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@if ($invest->deposit)
    <!-- Display payment information without referencing payment_type -->
    <div class="card mt-3">
        <div class="card-header bg-primary">
            <h5 class="mb-0 text-white">@lang('Thông tin thanh toán')</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="las la-credit-card text-primary me-2"></i>@lang('Phương thức thanh toán')</span>
                            <span class="fw-bold">{{ __($invest->deposit->gateway->name) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="las la-receipt text-primary me-2"></i>@lang('Mã giao dịch')</span>
                            <span class="fw-bold">{{ $invest->deposit->trx }}</span>
                        </li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="las la-money-bill-wave text-primary me-2"></i>@lang('Số tiền thanh toán')</span>
                            <span class="fw-bold">{{ showAmount($invest->deposit->amount) }} VND</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="las la-calendar-check text-primary me-2"></i>@lang('Ngày thanh toán')</span>
                            <span class="fw-bold">{{ showDateTime($invest->deposit->created_at) }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endif
