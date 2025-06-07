@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('ID')</th>
                                    <th>@lang('Project Name')</th>
                                    <th>@lang('Payment Status')</th>
                                    <th>@lang('Invest Status')</th>
                                    <th>@lang('Capital Back') | @lang('is Backed?')</th>
                                    <th>@lang('Paid | Remaining')</th>
                                    <th>@lang('Created At')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($invests as $invest)
                                    <tr>
                                        <td>{{ __($invest->invest_no) }}</td>
                                        <td>
                                            <a href="{{ route('admin.project.edit', $invest->project->id) }}">
                                                {{ __($invest->project->title) }}
                                            </a>
                                        </td>
                                        <td>@php echo $invest->paymentStatusBadge @endphp</td>
                                        <td>@php echo $invest->statusBadge @endphp</td>
                                        <td>@php echo $invest->capitalBackBadge @endphp
                                            @if ($invest->project->capital_back == Status::YES)
                                                @php echo $invest->isBackedBadge @endphp
                                            @endif
                                        </td>

                                        <td>
                                            @php
                                                $remaining = getInvestmentRemaining($invest);
                                            @endphp

                                            <span data-toggle="tooltip" data-placement="top"
                                                title="@lang('Paid: ') {{ __($invest->period) }} @lang('returns')">
                                                {{ __($invest->period) }}
                                            </span>

                                            @if ($invest->project->return_type != Status::LIFETIME)
                                                |
                                                <span data-toggle="tooltip" data-placement="top"
                                                    title="@lang('Remaining: ') {{ __($remaining) }} @lang('returns')">
                                                    {{ __($remaining) }}
                                                </span>
                                            @endif
                                        </td>


                                        <td>{{ showDateTime($invest->created_at) }}</td>
                                        <td>
                                            <div class="button-group">
                                                <a class="btn btn-outline--primary btn-sm editBtn"
                                                    href="{{ route('admin.invest.details', $invest->id) }}">
                                                    <i class="las la-desktop"></i>@lang('Details')
                                                </a>

                                                @if ($invest->status == Status::INVEST_PENDING && $invest->payment_status == Status::INVEST_PAYMENT_PENDING)
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline--danger confirmationBtn"
                                                        data-question="@lang('Are you sure to cancel this investment?')"
                                                        data-action="{{ route('admin.invest.status', $invest->id) }}">
                                                        <i class="lar la-times-circle"></i>
                                                        @lang('Cancel')
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse

                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($invests->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($invests) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div id="orderStatusModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Confirmation Alert!')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form method="POST">
                    @csrf
                    <div class="modal-body">
                        <p class="modal-detail"></p>
                        <input type="hidden" name="status">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('No')</button>
                        <button type="submit" class="btn btn--primary">@lang('Yes')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <x-search-form placeholder="Search here..." />
@endpush
