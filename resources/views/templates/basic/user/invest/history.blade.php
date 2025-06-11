@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="card custom--card">
                    <div class="card-header">
                        <h5 class="card-title">@lang('Investment History')</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive--md">
                            <table class="table custom--table">
                                <thead>
                                    <tr>
                                        <th>@lang('Invest No')</th>
                                        <th>@lang('Project')</th>
                                        <th>@lang('Amount')</th>
                                        <th>@lang('Quantity')</th>
                                        <th>@lang('Status')</th>
                                        <th>@lang('Date')</th>
                                        <th>@lang('Action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($invests as $invest)
                                        <tr>
                                            <td>{{ $invest->invest_no }}</td>
                                            <td>{{ __($invest->project->title) }}</td>
                                            <td>{{ showAmount($invest->total_price) }} {{ __($general->cur_text) }}</td>
                                            <td>{{ $invest->quantity }}</td>
                                            <td>{!! $invest->statusBadge !!}</td>
                                            <td>{{ showDateTime($invest->created_at) }}</td>
                                            <td>
                                                <a href="{{ route('user.invest.contract', $invest->id) }}" class="btn btn-sm btn-outline--primary">
                                                    <i class="las la-desktop"></i> @lang('View')
                                                </a>
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
                        @if ($invests->hasPages())
                            {{ paginateLinks($invests) }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection 