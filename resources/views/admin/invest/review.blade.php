@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table--light style--two table">
                            <thead>
                                <tr>
                                    <th>@lang('Invest No')</th>
                                    <th>@lang('Project')</th>
                                    <th>@lang('Investor')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Quantity')</th>
                                    <th>@lang('Date')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($invests as $invest)
                                    <tr>
                                        <td>{{ $invest->invest_no }}</td>
                                        <td>{{ __($invest->project->title) }}</td>
                                        <td>{{ __($invest->user->fullname) }}</td>
                                        <td>{{ showAmount($invest->total_price) }}</td>
                                        <td>{{ $invest->quantity }}</td>
                                        <td>{{ showDateTime($invest->created_at) }}</td>
                                        <td>
                                            <div class="button-group">
                                                <a class="btn btn-sm btn-outline--primary" href="{{ route('admin.invest.review.contract', $invest->id) }}">
                                                    <i class="las la-eye"></i> @lang('View')
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline--success confirmationBtn"
                                                    data-action="{{ route('admin.invest.approve', $invest->id) }}"
                                                    data-question="@lang('Are you sure to approve this investment?')">
                                                    <i class="las la-check"></i> @lang('Approve')
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline--danger confirmationBtn"
                                                    data-action="{{ route('admin.invest.reject', $invest->id) }}"
                                                    data-question="@lang('Are you sure to reject this investment?')">
                                                    <i class="las la-times"></i> @lang('Reject')
                                                </button>
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

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <x-search-form placeholder="Invest No/Project" />
@endpush 