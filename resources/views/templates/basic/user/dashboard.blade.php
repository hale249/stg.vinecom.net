@extends($activeTemplate . 'layouts.master')

@section('content')
    @php
        $general = gs(); // Get general settings
    @endphp
    <div class="dashboard-inner__block">
        <div class="notice"></div>
        <!-- KYC Section - Giữ nguyên -->
        @php
            $showKycDiv =
                auth()->user()->kv == Status::KYC_PENDING ||
                auth()->user()->kv == Status::KYC_UNVERIFIED ||
                (auth()->user()->kv == Status::KYC_UNVERIFIED && auth()->user()->kyc_rejection_reason);
        @endphp
        <div class="col-xsm-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 @if (!$showKycDiv) d-none @endif">
            <!-- Giữ nguyên phần KYC -->
        </div>

        <!-- Dashboard Stats Section - Cải thiện -->
        <div class="dashboard-stats-section mb-4">
            <h4 class="dashboard-welcome-title mb-4">Xin chào, {{ auth()->user()->fullname }}!</h4>
            <div class="row gy-4">
                <!-- Balance Card -->
                <div class="col-xsm-6 col-sm-6 col-md-4 col-lg-6 col-xl-4">
                    <div class="dashboard-analytics-card">
                        <div class="dashboard-analytics-card__top">
                            <div class="dashboard-analytics-card__thumb">
                                @include('components.balance-svg')
                            </div>
                        </div>

                        <div class="dashboard-analytics-card__content">
                            <span class="dashboard-analytics-card__name">@lang('Total Balance')</span>
                            <h5 class="dashboard-analytics-card__total">{{ __(showAmount($user->balance)) }}</h5>
                        </div>

                        <div class="dashboard-analytics-card__overlay-icon">
                            @include('components.balance-overlay-svg')
                        </div>
                    </div>
                </div>

                <!-- Projects Card -->
                <div class="col-xsm-6 col-sm-6 col-md-4 col-lg-6 col-xl-4">
                    <div class="dashboard-analytics-card">
                        <div class="dashboard-analytics-card__top">
                            <div class="dashboard-analytics-card__thumb">
                                @include('components.project-svg')
                            </div>
                            <a class="btn btn--xsm btn--outline" href="{{ route('user.projects') }}">@lang('See all')</a>
                        </div>

                        <div class="dashboard-analytics-card__content">
                            <span class="dashboard-analytics-card__name">@lang('Total Invested Projects')</span>
                            <h5 class="dashboard-analytics-card__total">{{ $investData['invest_count'] }}</h5>
                        </div>

                        <div class="dashboard-analytics-card__overlay-icon">
                            @include('components.project-overlay-svg')
                        </div>
                    </div>
                </div>

                <!-- Invest Card -->
                <div class="col-xsm-6 col-sm-6 col-md-4 col-lg-6 col-xl-4">
                    <div class="dashboard-analytics-card">
                        <div class="dashboard-analytics-card__top">
                            <div class="dashboard-analytics-card__thumb">
                                @include('components.invest-svg')
                            </div>
                            <a class="btn btn--xsm btn--outline" href="{{ route('user.transactions') }}">@lang('See all')</a>
                        </div>

                        <div class="dashboard-analytics-card__content">
                            <span class="dashboard-analytics-card__name">@lang('Tổng đầu tư đang hoạt động')</span>
                            <h5 class="dashboard-analytics-card__total">{{ __(showAmount($investData['total_invest'])) }}</h5>
                        </div>

                        <div class="dashboard-analytics-card__overlay-icon">
                            @include('components.invest-overlay-svg')
                        </div>
                    </div>
                </div>

                <!-- Profit Card -->
                <div class="col-xsm-6 col-sm-6 col-md-4 col-lg-6 col-xl-4">
                    <div class="dashboard-analytics-card">
                        <div class="dashboard-analytics-card__top">
                            <div class="dashboard-analytics-card__thumb">
                                @include('components.profit-svg')
                            </div>
                            <a class="btn btn--xsm btn--outline" href="{{ route('user.transactions') }}">@lang('See all')</a>
                        </div>

                        <div class="dashboard-analytics-card__content">
                            <span class="dashboard-analytics-card__name">@lang('Tổng lợi tức theo giá trị HĐ')</span>
                            <h5 class="dashboard-analytics-card__total">{{ __(showAmount($investData['total_earning'])) }}</h5>
                        </div>

                        <div class="dashboard-analytics-card__overlay-icon">
                            @include('components.profit-overlay-svg')
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity Section - Thêm mới -->
        <div class="recent-activity-section mb-4">
            <div class="section-header d-flex justify-content-between align-items-center mb-3">
                <h5 class="section-title mb-0">Hoạt động gần đây</h5>
                <a href="{{ route('user.transactions') }}" class="btn btn-sm btn--outline">Xem tất cả</a>
            </div>
            <div class="activity-container p-3 rounded" style="background-color: hsl(var(--white)); border: 1px solid hsl(var(--black)/0.1);">
                <!-- Hiển thị 5 giao dịch gần nhất -->
                @if(isset($transactions) && count($transactions) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Thời gian</th>
                                    <th>Loại</th>
                                    <th>Số tiền</th>
                                    <th>Chi tiết</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transactions as $transaction)
                                <tr>
                                    <td>{{ showDateTime($transaction->created_at) }}</td>
                                    <td>{{ __($transaction->remark) }}</td>
                                    <td class="@if($transaction->trx_type == '+')text-success @else text-danger @endif">
                                        {{ $transaction->trx_type }} {{ showAmount($transaction->amount) }} {{ $general->cur_text ?? '' }}
                                    </td>
                                    <td>{{ __($transaction->details) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-state text-center py-4">
                        <i class="fas fa-history fa-3x mb-3 text-muted"></i>
                        <p>Chưa có hoạt động nào gần đây</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    @include($activeTemplate . 'partials.invest_data')

    @if (auth()->user()->kv == Status::KYC_UNVERIFIED && auth()->user()->kyc_rejection_reason)
        <div class="modal fade" id="kycRejectionReason">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h6 class="modal-title">@lang('KYC Document Rejection Reason')</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>{{ auth()->user()->kyc_rejection_reason }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('style')
<style>
    .dashboard-welcome-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: hsl(var(--base-d-800));
    }
    
    .section-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: hsl(var(--base-d-800));
    }
    
    .empty-state {
        color: hsl(var(--gray-three));
    }
</style>
@endpush
