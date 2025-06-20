@extends('user.staff.layouts.app')

@section('panel')
    <div class="row mb-none-30">
        <div class="col-lg-12 mb-4">
            <div class="card">
                <div class="card-body d-flex flex-wrap gap-3 align-items-center justify-content-between">
                    <div>
                        <h5 class="welcome-text mb-0">@lang('Xin chào'), {{ auth()->user()->firstname }}!</h5>
                        <p class="mb-0 mt-1">@lang('Chào mừng trở lại với Trang quản lý của bạn')</p>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('user.staff.manager.team_members') }}" class="btn btn-sm btn-outline-primary">
                            <i class="las la-users me-1"></i> @lang('Quản lý nhóm')
                        </a>
                        <a href="{{ route('user.staff.manager.approval_requests') }}" class="btn btn-sm btn-outline-success">
                            <i class="las la-check-circle me-1"></i> @lang('Phê duyệt hợp đồng')
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <!-- KPI Cards -->
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <div class="mb-2"><i class="las la-trophy la-2x text-success"></i></div>
                    <h5 class="mb-1">@lang('Nhân viên vượt KPI')</h5>
                    <h3 class="fw-bold text-success">3</h3>
                    <div class="text-muted small">@lang('Trong tháng này')</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <div class="mb-2"><i class="las la-money-bill-wave la-2x text-primary"></i></div>
                    <h5 class="mb-1">@lang('Tổng lương tháng')</h5>
                    <h3 class="fw-bold text-primary">42.800.000</h3>
                    <div class="text-muted small">@lang('VNĐ')</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <div class="mb-2"><i class="las la-chart-line la-2x text-info"></i></div>
                    <h5 class="mb-1">@lang('Tổng doanh số')</h5>
                    <h3 class="fw-bold text-info">3.400.000.000</h3>
                    <div class="text-muted small">@lang('VNĐ trong tháng')</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <div class="mb-2"><i class="las la-bullseye la-2x text-warning"></i></div>
                    <h5 class="mb-1">@lang('Tỉ lệ hoàn thành KPI')</h5>
                    <h3 class="fw-bold text-warning">89%</h3>
                    <div class="text-muted small">@lang('Bình quân toàn nhóm')</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row gy-4 mb-30">
        <div class="col-xxl-3 col-sm-6">
            <div class="card bg-white overflow-hidden">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="text-muted text-uppercase mb-2 fs--2">@lang('Thành viên nhóm')</h6>
                            <h4 class="fs-4 mb-1 number-font">{{ $stats['team_members'] ?? 0 }}</h4>
                            <p class="mb-0"><span class="badge bg-success-subtle text-success"><i class="las la-users me-1"></i> @lang('Quản lý')</span></p>
                        </div>
                        <div class="col-auto">
                            <div class="icon-box bg-primary-subtle">
                                <i class="las la-users fs-1 text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer p-0">
                    <a href="{{ route('user.staff.manager.team_members') }}" class="d-block text-center py-2 fs--2 bg-light text-muted">@lang('Xem tất cả') <i class="las la-angle-right fs--1 ms-1"></i></a>
                </div>
            </div>
        </div>
        
        <div class="col-xxl-3 col-sm-6">
            <div class="card bg-white overflow-hidden">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="text-muted text-uppercase mb-2 fs--2">@lang('Tổng số hợp đồng')</h6>
                            <h4 class="fs-4 mb-1 number-font">{{ $stats['total_contracts'] ?? 0 }}</h4>
                            <p class="mb-0"><span class="badge bg-info-subtle text-info"><i class="las la-file-contract me-1"></i> @lang('Nhóm')</span></p>
                        </div>
                        <div class="col-auto">
                            <div class="icon-box bg-success-subtle">
                                <i class="las la-file-contract fs-1 text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer p-0">
                    <a href="{{ route('user.staff.manager.contracts') }}" class="d-block text-center py-2 fs--2 bg-light text-muted">@lang('Xem tất cả') <i class="las la-angle-right fs--1 ms-1"></i></a>
                </div>
            </div>
        </div>
        
        <div class="col-xxl-3 col-sm-6">
            <div class="card bg-white overflow-hidden">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="text-muted text-uppercase mb-2 fs--2">@lang('Hợp đồng hoạt động')</h6>
                            <h4 class="fs-4 mb-1 number-font">{{ $stats['active_contracts'] ?? 0 }}</h4>
                            <p class="mb-0"><span class="badge bg-success-subtle text-success"><i class="las la-check-circle me-1"></i> @lang('Đang chạy')</span></p>
                        </div>
                        <div class="col-auto">
                            <div class="icon-box bg-warning-subtle">
                                <i class="las la-check-circle fs-1 text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer p-0">
                    <a href="{{ route('user.staff.manager.contracts') }}" class="d-block text-center py-2 fs--2 bg-light text-muted">@lang('Xem tất cả') <i class="las la-angle-right fs--1 ms-1"></i></a>
                </div>
            </div>
        </div>
        
        <div class="col-xxl-3 col-sm-6">
            <div class="card bg-white overflow-hidden">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="text-muted text-uppercase mb-2 fs--2">@lang('Tổng khách hàng')</h6>
                            <h4 class="fs-4 mb-1 number-font">{{ $stats['total_customers'] ?? 0 }}</h4>
                            <p class="mb-0"><span class="badge bg-primary-subtle text-primary"><i class="las la-user-friends me-1"></i> @lang('Khách hàng')</span></p>
                        </div>
                        <div class="col-auto">
                            <div class="icon-box bg-info-subtle">
                                <i class="las la-user-friends fs-1 text-info"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer p-0">
                    <a href="#" class="d-block text-center py-2 fs--2 bg-light text-muted">@lang('Xem tất cả') <i class="las la-angle-right fs--1 ms-1"></i></a>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-none-30">
        <div class="col-lg-7 mb-30">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h6 class="card-title mb-0">@lang('Cảnh báo thanh toán sắp tới')</h6>
                    <div>
                        <a href="{{ route('user.staff.manager.alerts') }}" class="btn btn-sm btn-outline-primary">@lang('Xem tất cả')</a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
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
                                @forelse($interestAlerts->take(5) as $alert)
                                <tr>
                                    <td>{{ $alert->trx }}</td>
                                    <td>{{ Str::limit($alert->project->name, 20) }}</td>
                                    <td>{{ showDateTime($alert->next_time) }}</td>
                                    <td>
                                        @php
                                            $daysRemaining = \Carbon\Carbon::parse($alert->next_time)->diffInDays(\Carbon\Carbon::now());
                                        @endphp
                                        <span class="badge {{ $daysRemaining <= 7 ? 'bg-danger' : ($daysRemaining <= 15 ? 'bg-warning' : 'bg-success') }}">
                                            {{ $daysRemaining }} @lang('ngày')
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('user.staff.manager.contracts') }}" class="btn btn-sm btn-outline-primary">
                                            <i class="las la-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">@lang('Không có dữ liệu')</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-5 mb-30">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h6 class="card-title mb-0">@lang('Cảnh báo hợp đồng đáo hạn')</h6>
                    <div>
                        <a href="{{ route('user.staff.manager.alerts') }}" class="btn btn-sm btn-outline-primary">@lang('Xem tất cả')</a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>@lang('Mã hợp đồng')</th>
                                    <th>@lang('Ngày đáo hạn')</th>
                                    <th>@lang('Còn lại')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($maturityAlerts->take(5) as $alert)
                                <tr>
                                    <td>{{ $alert->trx }}</td>
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
                                    <td colspan="3" class="text-center">@lang('Không có dữ liệu')</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
<style>
    .icon-box {
        width: 65px;
        height: 65px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 15px;
    }
    
    .fs--1 {
        font-size: 0.875rem;
    }
    
    .fs--2 {
        font-size: 0.75rem;
    }
    
    .welcome-text {
        font-weight: 600;
    }
    
    .bg-primary-subtle {
        background-color: rgba(99, 102, 241, 0.15);
    }
    
    .bg-success-subtle {
        background-color: rgba(16, 185, 129, 0.15);
    }
    
    .bg-warning-subtle {
        background-color: rgba(245, 158, 11, 0.15);
    }
    
    .bg-info-subtle {
        background-color: rgba(59, 130, 246, 0.15);
    }
    
    .number-font {
        font-weight: 600;
    }
    
    .card-footer {
        border-top: 1px solid rgba(0,0,0,.05);
    }
</style>
@endpush 