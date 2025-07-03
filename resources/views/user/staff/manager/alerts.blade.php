@extends('user.staff.layouts.app')

@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="mb-0">
                    @lang('Cảnh báo hợp đồng') 
                    <span class="badge bg-info ms-2">{{ request('alert_period', 30) }} @lang('ngày tới')</span>
                </h5>
                <div class="d-flex align-items-center">
                    <form action="{{ route('user.staff.manager.alerts') }}" method="GET">
                        <div class="input-group">
                            <select name="alert_period" class="form-select form-select-sm">
                                <option value="7" {{ request('alert_period', 30) == 7 ? 'selected' : '' }}>7 ngày</option>
                                <option value="15" {{ request('alert_period', 30) == 15 ? 'selected' : '' }}>15 ngày</option>
                                <option value="30" {{ request('alert_period', 30) == 30 ? 'selected' : '' }}>30 ngày</option>
                                <option value="60" {{ request('alert_period', 30) == 60 ? 'selected' : '' }}>60 ngày</option>
                                <option value="90" {{ request('alert_period', 30) == 90 ? 'selected' : '' }}>90 ngày</option>
                            </select>
                            <button type="submit" class="btn btn-sm btn-primary">
                                <i class="las la-filter"></i> @lang('Lọc')
                            </button>
                        </div>
                    </form>
                </div>
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
                                            <th>@lang('Hành động')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($interestAlerts as $alert)
                                        <tr>
                                            <td><span class="badge bg-primary">{{ $alert->invest_no }}</span></td>
                                            <td>{{ Str::limit($alert->project->title ?? '-', 20) }}</td>
                                            <td>{{ showDateTime($alert->next_time) }}</td>
                                            <td>
                                                @php
                                                    $now = \Carbon\Carbon::now();
                                                    $nextTime = \Carbon\Carbon::parse($alert->next_time);
                                                    $isPast = $nextTime->isPast();
                                                    
                                                    if ($isPast) {
                                                        $daysRemaining = "Quá hạn " . (int)$now->diffInDays($nextTime);
                                                        $badgeClass = 'bg-danger';
                                                    } else {
                                                        $daysRemaining = (int)$now->diffInDays($nextTime);
                                                        $badgeClass = $daysRemaining <= 7 ? 'bg-danger' : ($daysRemaining <= 15 ? 'bg-warning' : 'bg-success');
                                                    }
                                                @endphp
                                                <span class="badge {{ $badgeClass }}">
                                                    {{ $daysRemaining }} @if(!$isPast) @lang('ngày') @endif
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('user.staff.manager.contract', $alert->id) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="las la-eye"></i> @lang('Chi tiết')
                                                </a>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center empty-state">
                                                <i class="las la-bell-slash fa-3x text-muted opacity-50"></i>
                                                <p class="text-muted">@lang('Không có cảnh báo lãi suất.')</p>
                                            </td>
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
                                            <th>@lang('Hành động')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($maturityAlerts as $alert)
                                        <tr>
                                            <td><span class="badge bg-primary">{{ $alert->invest_no }}</span></td>
                                            <td>{{ Str::limit($alert->project->title ?? '-', 20) }}</td>
                                            <td>{{ showDateTime($alert->project_closed) }}</td>
                                            <td>
                                                @php
                                                    $now = \Carbon\Carbon::now();
                                                    $closedDate = \Carbon\Carbon::parse($alert->project_closed);
                                                    $isPast = $closedDate->isPast();
                                                    
                                                    if ($isPast) {
                                                        $daysRemaining = "Quá hạn " . (int)$now->diffInDays($closedDate);
                                                        $badgeClass = 'bg-danger';
                                                    } else {
                                                        $daysRemaining = (int)$now->diffInDays($closedDate);
                                                        $badgeClass = $daysRemaining <= 7 ? 'bg-danger' : ($daysRemaining <= 15 ? 'bg-warning' : 'bg-success');
                                                    }
                                                @endphp
                                                <span class="badge {{ $badgeClass }}">
                                                    {{ $daysRemaining }} @if(!$isPast) @lang('ngày') @endif
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('user.staff.manager.contract', $alert->id) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="las la-eye"></i> @lang('Chi tiết')
                                                </a>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center empty-state">
                                                <i class="las la-calendar-times fa-3x text-muted opacity-50"></i>
                                                <p class="text-muted">@lang('Không có cảnh báo đáo hạn.')</p>
                                            </td>
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
    
    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 2rem 1rem;
    }
    
    .empty-state i {
        display: block;
        margin-bottom: 1rem;
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.03);
    }
    
    .card-title .badge {
        vertical-align: middle;
    }
</style>
@endpush 