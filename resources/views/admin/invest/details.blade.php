@extends('admin.layouts.app')

@section('panel')
    <div class="row mb-none-30">
        <div class="col-xl-3 col-lg-5 col-md-5 mb-30 text-center">
            <div class="card b-radius--10 overflow-hidden box--shadow1">
                <div class="card-body p-0">
                    <div class="p-3 bg--white">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                <img src="{{ getImage(getFilePath('userProfile') . '/' . @$invest->user->image, getFileSize('userProfile'), avatar: true) }}"
                                    alt="@lang('Ảnh hồ sơ')" class="b-radius--10" style="max-width: 100px;">
                            </div>
                            <div>
                                <h4 class="mb-1">
                                    <a href="{{ route('admin.users.detail', $invest->user->id) }}"
                                        class="text--primary">{{ @$invest->user->fullname }}</a>
                                </h4>
                                <p class="mb-0">{{ @$invest->user->email }}</p>
                            </div>
                        </div>

                        <div class="border-top pt-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text--small">@lang('Họ tên')</span>
                                <span class="text--small"><strong>{{ @$invest->user->fullname }}</strong></span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text--small">@lang('Tên đăng nhập')</span>
                                <span class="text--small"><strong>{{ @$invest->user->username }}</strong></span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text--small">@lang('Email')</span>
                                <span class="text--small"><strong>{{ @$invest->user->email }}</strong></span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text--small">@lang('Mã đầu tư')</span>
                                <span class="text--small"><strong>{{ $invest->invest_no }}</strong></span>
                            </div>
                        </div>

                        <div class="mt-3">
                            <a href="{{ route('admin.users.notification.single', $invest->user->id) }}"
                                class="btn btn-outline--primary btn-sm w-100"><i class="las la-paper-plane"></i>
                                @lang('Gửi thông báo')</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-9 col-lg-7 col-md-7 mb-30">
            @include('admin.partials.invest_details')
        </div>

        <div class="col-12">
            <h5 class="my-2">@lang('Lịch sử thanh toán lãi')</h5>
            <div class="card mb-5">
                <div class="card-body p-0">
                    <div class="table-responsive--md  table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Mã giao dịch')</th>
                                    <th>@lang('Thời gian')</th>
                                    <th>@lang('Số tiền')</th>
                                    <th>@lang('Số dư sau giao dịch')</th>
                                    <th>@lang('Chi tiết')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $trx)
                                    <tr>
                                        <td><strong>{{ $trx->trx }}</strong></td>
                                        <td>{{ showDateTime($trx->created_at) }}<br>{{ diffForHumans($trx->created_at) }}
                                        </td>

                                        <td class="budget">
                                            <span
                                                class="fw-bold @if ($trx->trx_type == '+') text--success @else text--danger @endif">
                                                {{ $trx->trx_type }} {{ showAmount($trx->amount) }}
                                            </span>
                                        </td>

                                        <td class="budget">
                                            {{ showAmount($trx->post_balance) }}
                                        </td>

                                        <td>{{ __($trx->details) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table><!-- table end -->
                    </div>
                </div>
                @if ($transactions->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($transactions) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('admin.invest.index') }}" />

    <!-- Document Management Button -->
    <a href="{{ route('admin.invest.documents.index', $invest->id) }}" class="btn btn-sm btn-outline--info">
        <i class="las la-file-upload"></i>
        @lang('Tài liệu hợp đồng')
    </a>

    <!-- Stop/Start Returns Button -->
    @if ($invest->project->return_type == Status::LIFETIME && $invest->status == Status::INVEST_RUNNING)
        <!-- Button for stopping returns -->
        <button type="button" class="btn btn-sm btn-outline--danger confirmationBtn" data-question="@lang('Bạn có chắc chắn muốn dừng trả lãi cho khoản đầu tư này?')"
            data-action="{{ route('admin.invest.stop.returns', $invest->id) }}">
            <i class="las la-times"></i>
            @lang('Dừng trả lãi')
        </button>
    @elseif ($invest->project->return_type == Status::LIFETIME && $invest->status == Status::INVEST_CLOSED)
        <!-- Button for starting returns -->
        <button type="button" class="btn btn-sm btn-outline--success confirmationBtn" data-question="@lang('Bạn có chắc chắn muốn bắt đầu trả lãi cho khoản đầu tư này?')"
            data-action="{{ route('admin.invest.start.returns', $invest->id) }}">
            <i class="las la-play-circle"></i>
            @lang('Bắt đầu trả lãi')
        </button>
    @endif
@endpush
