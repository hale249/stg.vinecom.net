@extends('user.staff.layouts.staff_app')

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
                                            <td><span class="badge bg-primary">{{ $alert->invest_no }}</span></td>
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
                                            <td><span class="badge bg-primary">{{ $alert->invest_no }}</span></td>
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
        
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">@lang('Cảnh báo theo tháng')</h5>
            </div>
            <div class="card-body">
                <div class="accordion" id="monthlyAlerts">
                    @forelse($monthlyAlerts as $month => $data)
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading{{ $loop->index }}">
                                <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $loop->index }}" aria-expanded="{{ $loop->first ? 'true' : 'false' }}" aria-controls="collapse{{ $loop->index }}">
                                    {{ $data['month'] }} 
                                    <span class="ms-2 badge {{ count($data['interest_alerts']) > 0 || count($data['maturity_alerts']) > 0 ? 'bg-warning' : 'bg-success' }}">
                                        {{ count($data['interest_alerts']) + count($data['maturity_alerts']) }} @lang('cảnh báo')
                                    </span>
                                </button>
                            </h2>
                            <div id="collapse{{ $loop->index }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" aria-labelledby="heading{{ $loop->index }}" data-bs-parent="#monthlyAlerts">
                                <div class="accordion-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover align-middle mb-0">
                                            <thead>
                                                <tr>
                                                    <th>@lang('Loại')</th>
                                                    <th>@lang('Mã hợp đồng')</th>
                                                    <th>@lang('Dự án')</th>
                                                    <th>@lang('Ngày')</th>
                                                    <th>@lang('Còn lại')</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($data['interest_alerts'] as $alert)
                                                    <tr>
                                                        <td><span class="badge bg-primary">@lang('Lãi suất')</span></td>
                                                        <td>{{ $alert->invest_no }}</td>
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
                                                @endforeach
                                                
                                                @foreach($data['maturity_alerts'] as $alert)
                                                    <tr>
                                                        <td><span class="badge bg-warning">@lang('Đáo hạn')</span></td>
                                                        <td>{{ $alert->invest_no }}</td>
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
                                                @endforeach
                                                
                                                @if(count($data['interest_alerts']) == 0 && count($data['maturity_alerts']) == 0)
                                                    <tr>
                                                        <td colspan="5" class="text-center">@lang('Không có cảnh báo trong tháng này.')</td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-3">@lang('Không có dữ liệu cảnh báo theo tháng.')</div>
                    @endforelse
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