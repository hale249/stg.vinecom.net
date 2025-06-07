@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="dashboard-inner__block">
        <div class="dashboard-card">
            <div class="dashboard-card__header d-flex justify-content-between align-items-center">
                <h6 class="dashboard-card__title mb-0">@lang('Withdrawals')</h6>
                <form class="d-flex align-items-center">
                    <div class="position-relative">
                        <input class="form-control form--control with-search-icon" type="search" name="search"
                            value="{{ request()->search }}" placeholder="@lang('Search by transactions')">
                        <button type="submit" class="search-icon-button">
                            <i class="las la-search search-icon"></i>
                        </button>
                    </div>
                </form>
            </div>
            <div class="dashboard-card__body">
                <div class="table-responsive">
                    <table class="table table--responsive--sm">
                        <thead>
                            <tr>
                                <th>@lang('Gateway | Transaction')</th>
                                <th class="text-center">@lang('Initiated')</th>
                                <th class="text-center">@lang('Amount')</th>
                                <th class="text-center">@lang('Conversion')</th>
                                <th class="text-center">@lang('Status')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($withdraws as $withdraw)
                                @php
                                    $details = [];
                                    foreach ($withdraw->withdraw_information as $key => $info) {
                                        $details[] = $info;
                                        if ($info->type == 'file') {
                                            $details[$key]->value = route(
                                                'user.download.attachment',
                                                encrypt(getFilePath('verify') . '/' . $info->value),
                                            );
                                        }
                                    }
                                @endphp
                                <tr>
                                    <td>
                                        <div class="td-wrapper">
                                            <span class="fw-bold"><span class="text--base text-nowrap">
                                                    {{ __(@$withdraw->method->name) }}</span></span>
                                            <br>
                                            <small>{{ $withdraw->trx }}</small>
                                        </div>
                                    </td>
                                    <td class="text-end text-md-center">
                                        <div class="td-wrapper">
                                            {{ showDateTime($withdraw->created_at) }}
                                            <br> {{ diffForHumans($withdraw->created_at) }}
                                        </div>
                                    </td>
                                    <td class="text-end text-md-center">
                                        <div class="td-wrapper">
                                            {{ showAmount($withdraw->amount) }} - <span class="text--danger"
                                                data-bs-toggle="tooltip"
                                                title="@lang('Processing Charge')">{{ showAmount($withdraw->charge) }} </span>
                                            <br>
                                            <strong class="fs-12" data-bs-toggle="tooltip" title="@lang('Amount after charge')">
                                                {{ showAmount($withdraw->amount - $withdraw->charge) }}
                                            </strong>
                                        </div>
                                    </td>
                                    <td class="text-end text-md-center">
                                        <div class="td-wrapper">
                                            {{ showAmount(1) }}
                                            = {{ showAmount($withdraw->rate, currencyFormat: false) }}
                                            {{ __($withdraw->currency) }}
                                            <br>
                                            <strong
                                                class="fs-12">{{ showAmount($withdraw->final_amount, currencyFormat: false) }}
                                                {{ __($withdraw->currency) }}</strong>
                                        </div>
                                    </td>
                                    <td class="text-end text-md-center">
                                        @php echo $withdraw->statusBadge @endphp
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn--xsm btn--outline action-btn detailBtn"
                                            data-user_data="{{ json_encode($details) }}"
                                            @if ($withdraw->status == Status::PAYMENT_REJECT) data-admin_feedback="{{ $withdraw->admin_feedback }}" @endif
                                            data-bs-toggle="modal" data-bs-target="#detailModal">
                                            <i class="las la-desktop"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text--base text-end text-md-center" colspan="100%">{{ __($emptyMessage) }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($withdraws->hasPages())
                <div class="card-footer">
                    {{ paginateLinks($withdraws) }}
                </div>
            @endif
        </div>
    </div>

    {{-- APPROVE MODAL --}}
    <div id="detailModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Details')</h5>
                    <span type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </span>
                </div>
                <div class="modal-body">
                    <ul class="list-group userData"></ul>
                    <div class="feedback"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        (function($) {
            "use strict";
            $('.detailBtn').on('click', function() {
                var modal = $('#detailModal');
                var userData = $(this).data('user_data');
                var html = ``;
                userData.forEach(element => {
                    if (element.type != 'file') {
                        html += `
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>${element.name}</span>
                            <span>${element.value}</span>
                        </li>`;
                    } else {
                        html += `
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>${element.name}</span>
                            <span><a href="${element.value}" class="text--base"><i class="fa-regular fa-file"></i> @lang('Attachment')</a></span>
                        </li>`;
                    }
                });
                modal.find('.userData').html(html);

                if ($(this).data('admin_feedback') !== undefined) {
                    var adminFeedback = `
                        <div class="my-3">
                            <strong>@lang('Admin Feedback')</strong>
                            <p>${$(this).data('admin_feedback')}</p>
                        </div>
                    `;
                } else {
                    var adminFeedback = '';
                }

                modal.find('.feedback').html(adminFeedback);
                modal.modal('show');
            });

            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title], [data-title], [data-bs-title]'))
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        })(jQuery);
    </script>
@endpush
