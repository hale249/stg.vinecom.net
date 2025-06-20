@extends('user.staff.layouts.app')

@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="mb-0">@lang('Cảnh báo hợp đồng')</h5>
            </div>
            <div class="card-body p-0">
                <div class="row g-0">
                    <div class="col-md-6 border-end">
                        <div class="p-3">
                            <h6 class="mb-3 text-primary"><i class="las la-bell"></i> @lang('Cảnh báo lãi suất sắp tới')</h6>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>@lang('Mã hợp đồng')</th>
                                            <th>@lang('Dự án')</th>
                                            <th>@lang('Ngày thanh toán')</th>
                                            <th>@lang('Còn lại')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($interestAlerts as $alert)
                                        <tr>
                                            <td><span class="badge bg-primary">{{ $alert->trx }}</span></td>
                                            <td>{{ Str::limit($alert->project->name ?? '-', 20) }}</td>
                                            <td>{{ showDateTime($alert->next_time) }}</td>
                                            <td>
                                                @php
                                                    $daysRemaining = \Carbon\Carbon::parse($alert->next_time)->diffInDays(\Carbon\Carbon::now());
                                                @endphp
                                                <span class="badge {{ $daysRemaining <= 7 ? 'bg-danger' : ($daysRemaining <= 15 ? 'bg-warning' : 'bg-success') }}">
                                                    {{ $daysRemaining }} @lang('ngày')
                                                </span>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center">@lang('Không có cảnh báo lãi suất.')</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3">
                            <h6 class="mb-3 text-warning"><i class="las la-bell"></i> @lang('Cảnh báo đáo hạn hợp đồng')</h6>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>@lang('Mã hợp đồng')</th>
                                            <th>@lang('Dự án')</th>
                                            <th>@lang('Ngày đáo hạn')</th>
                                            <th>@lang('Còn lại')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($maturityAlerts as $alert)
                                        <tr>
                                            <td><span class="badge bg-primary">{{ $alert->trx }}</span></td>
                                            <td>{{ Str::limit($alert->project->name ?? '-', 20) }}</td>
                                            <td>{{ showDateTime($alert->project_closed) }}</td>
                                            <td>
                                                @php
                                                    $daysRemaining = \Carbon\Carbon::parse($alert->project_closed)->diffInDays(\Carbon\Carbon::now());
                                                @endphp
                                                <span class="badge {{ $daysRemaining <= 7 ? 'bg-danger' : ($daysRemaining <= 15 ? 'bg-warning' : 'bg-success') }}">
                                                    {{ $daysRemaining }} @lang('ngày')
                                                </span>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center">@lang('Không có cảnh báo đáo hạn.')</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('style')
<style>
    .badge {
        font-size: 0.95em;
    }
</style>
@endpush 