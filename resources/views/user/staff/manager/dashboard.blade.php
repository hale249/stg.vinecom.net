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
            <div class="card shadow-sm border-0 h-100 kpi-card">
                <div class="card-body text-center d-flex flex-column justify-content-center">
                    <div class="mb-3">
                        <div class="icon-circle bg-success-light mb-2">
                            <i class="las la-trophy la-2x text-success"></i>
                        </div>
                    </div>
                    <h5 class="mb-2">@lang('Nhân viên vượt KPI')</h5>
                    @php
                        // Lấy số nhân viên vượt KPI từ dữ liệu thực tế
                        $exceededKpiCount = \App\Models\StaffKPI::where('manager_id', auth()->id())
                            ->where('month_year', now()->format('Y-m'))
                            ->where('kpi_status', 'exceeded')
                            ->count();
                    @endphp
                    <h3 class="fw-bold text-success counter-value">{{ $exceededKpiCount }}</h3>
                    <div class="text-muted small">@lang('Trong tháng này')</div>
                </div>
                <div class="card-footer p-0 bg-transparent border-0">
                    <a href="{{ route('user.staff.manager.hr.kpi') }}" class="d-block text-center py-2 fs--2 bg-light text-primary text-decoration-none">
                        @lang('Xem chi tiết') <i class="las la-angle-right fs--1 ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-0 h-100 kpi-card">
                <div class="card-body text-center d-flex flex-column justify-content-center">
                    <div class="mb-3">
                        <div class="icon-circle bg-primary-light mb-2">
                            <i class="las la-money-bill-wave la-2x text-primary"></i>
                        </div>
                    </div>
                    <h5 class="mb-2">@lang('Tổng lương tháng')</h5>
                    @php
                        // Lấy tổng lương tháng từ dữ liệu thực tế
                        $totalSalary = \App\Models\StaffSalary::where('manager_id', auth()->id())
                            ->where('month_year', now()->format('Y-m'))
                            ->sum('total_salary');
                    @endphp
                    <h3 class="fw-bold text-primary counter-value">{{ number_format($totalSalary, 0, ',', '.') }}</h3>
                    <div class="text-muted small">@lang('VNĐ')</div>
                </div>
                <div class="card-footer p-0 bg-transparent border-0">
                    <a href="{{ route('user.staff.manager.hr.salary') }}" class="d-block text-center py-2 fs--2 bg-light text-primary text-decoration-none">
                        @lang('Xem chi tiết') <i class="las la-angle-right fs--1 ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-0 h-100 kpi-card">
                <div class="card-body text-center d-flex flex-column justify-content-center">
                    <div class="mb-3">
                        <div class="icon-circle bg-info-light mb-2">
                            <i class="las la-chart-line la-2x text-info"></i>
                        </div>
                    </div>
                    <h5 class="mb-2">@lang('Tổng doanh số')</h5>
                    @php
                        // Lấy tổng doanh số từ dữ liệu thực tế
                        $staffIds = auth()->user()->staffMembers->pluck('id')->toArray();
                        $staffIds[] = auth()->id();
                        
                        $totalSales = \App\Models\Invest::whereIn('user_id', $staffIds)
                            ->whereYear('created_at', now()->year)
                            ->whereMonth('created_at', now()->month)
                            ->sum('total_price');
                    @endphp
                    <h3 class="fw-bold text-info counter-value">{{ number_format($totalSales, 0, ',', '.') }}</h3>
                    <div class="text-muted small">@lang('VNĐ trong tháng')</div>
                </div>
                <div class="card-footer p-0 bg-transparent border-0">
                    <a href="{{ route('user.staff.manager.contracts') }}" class="d-block text-center py-2 fs--2 bg-light text-primary text-decoration-none">
                        @lang('Xem chi tiết') <i class="las la-angle-right fs--1 ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-0 h-100 kpi-card">
                <div class="card-body text-center d-flex flex-column justify-content-center">
                    <div class="mb-3">
                        <div class="icon-circle bg-warning-light mb-2">
                            <i class="las la-bullseye la-2x text-warning"></i>
                        </div>
                    </div>
                    <h5 class="mb-2">@lang('Tỉ lệ hoàn thành KPI')</h5>
                    @php
                        // Lấy tỉ lệ hoàn thành KPI trung bình từ dữ liệu thực tế
                        $avgKpiPercentage = \App\Models\StaffKPI::where('manager_id', auth()->id())
                            ->where('month_year', now()->format('Y-m'))
                            ->avg('overall_kpi_percentage');
                        
                        // Làm tròn thành số nguyên
                        $avgKpiPercentage = round($avgKpiPercentage);
                    @endphp
                    <h3 class="fw-bold text-warning counter-value">{{ $avgKpiPercentage }}%</h3>
                    <div class="text-muted small">@lang('Bình quân toàn nhóm')</div>
                </div>
                <div class="card-footer p-0 bg-transparent border-0">
                    <a href="{{ route('user.staff.manager.hr.kpi') }}" class="d-block text-center py-2 fs--2 bg-light text-primary text-decoration-none">
                        @lang('Xem chi tiết') <i class="las la-angle-right fs--1 ms-1"></i>
                    </a>
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
                            <p><span class="status badge bg-success text-white py-2 px-3"><i class="las la-check-circle me-1"></i> @lang('Đang hoạt động')</span></p>
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
    
    <!-- Thông tin đầu tư theo giới thiệu -->
    <div class="row mb-30">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="mb-0">@lang('Đầu tư theo giới thiệu của bạn')</h5>
                    <div>
                        <a href="{{ route('user.staff.manager.contracts') }}" class="btn btn-sm btn-outline-primary">@lang('Xem tất cả')</a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>@lang('Mã đầu tư')</th>
                                    <th>@lang('Khách hàng')</th>
                                    <th>@lang('Dự án')</th>
                                    <th>@lang('Số tiền')</th>
                                    <th>@lang('Ngày tạo')</th>
                                    <th>@lang('Trạng thái')</th>
                                    <th>@lang('Hành động')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $referralInvests = \App\Models\Invest::where('referral_code', auth()->user()->username)
                                        ->with(['user', 'project'])
                                        ->latest()
                                        ->take(5)
                                        ->get();
                                @endphp
                                
                                @forelse($referralInvests as $invest)
                                <tr>
                                    <td><span class="badge bg-primary">{{ $invest->invest_no }}</span></td>
                                    <td>{{ $invest->user->fullname ?? '-' }}</td>
                                    <td>{{ Str::limit($invest->project->title ?? '-', 20) }}</td>
                                    <td>{{ showAmount($invest->total_price) }} {{ $general->cur_text }}</td>
                                    <td>{{ showDateTime($invest->created_at) }}</td>
                                    <td>{!! $invest->statusBadge !!}</td>
                                    <td>
                                        <a href="{{ route('user.staff.manager.contracts') }}" class="btn btn-sm btn-outline-primary">
                                            <i class="las la-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">@lang('Không có dữ liệu')</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
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


</div>

@include('user.staff.manager.partials.honor_modal')
@endsection

@push('script')
<script>
    (function($) {
        'use strict';
        
        // Animate counter numbers
        function animateCounters() {
            $('.counter-value').each(function() {
                const $this = $(this);
                const countTo = $this.text().replace(/[^\d.-]/g, '');
                
                if (!$this.data('counted') && countTo > 0) {
                    $this.data('counted', true);
                    
                    // Check if it's a percentage or a number with comma separators
                    const isPercentage = $this.text().includes('%');
                    const hasCommas = $this.text().includes('.');
                    
                    $({ countNum: 0 }).animate({
                        countNum: countTo
                    }, {
                        duration: 1000,
                        easing: 'swing',
                        step: function() {
                            let formattedValue;
                            
                            if (isPercentage) {
                                formattedValue = Math.floor(this.countNum) + '%';
                            } else if (hasCommas) {
                                formattedValue = Math.floor(this.countNum).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                            } else {
                                formattedValue = Math.floor(this.countNum);
                            }
                            
                            $this.text(formattedValue);
                        },
                        complete: function() {
                            // Ensure the final number is exactly what was intended
                            if (isPercentage) {
                                $this.text(Math.round(this.countNum) + '%');
                            } else if (hasCommas) {
                                $this.text(Math.floor(this.countNum).toString().replace(/\B(?=(\d{3})+(?!\d))/g, "."));
                            } else {
                                $this.text(Math.floor(this.countNum));
                            }
                        }
                    });
                }
            });
        }
        
        // Run animations when document is ready
        $(document).ready(function() {
            // Add hover effect to cards
            $('.kpi-card').hover(
                function() {
                    $(this).addClass('shadow-lg');
                },
                function() {
                    $(this).removeClass('shadow-lg');
                }
            );
            
            // Start counter animations after a small delay
            setTimeout(animateCounters, 300);
        });
        
    })(jQuery);
</script>
@endpush

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
    
    .icon-circle {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        margin: 0 auto;
        transition: all 0.3s ease;
    }
    
    .kpi-card {
        transition: all 0.3s ease;
        border-radius: 12px;
        overflow: hidden;
    }
    
    .kpi-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    
    .kpi-card:hover .icon-circle {
        transform: scale(1.1);
    }
    
    .counter-value {
        font-size: 1.75rem;
        transition: all 0.3s ease;
    }
    
    .kpi-card:hover .counter-value {
        transform: scale(1.05);
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
    
    .bg-primary-subtle, .bg-primary-light {
        background-color: rgba(99, 102, 241, 0.15);
    }
    
    .bg-success-subtle, .bg-success-light {
        background-color: rgba(16, 185, 129, 0.15);
    }
    
    .bg-warning-subtle, .bg-warning-light {
        background-color: rgba(245, 158, 11, 0.15);
    }
    
    .bg-info-subtle, .bg-info-light {
        background-color: rgba(59, 130, 246, 0.15);
    }
    
    .number-font {
        font-weight: 600;
    }
    
    .card-footer {
        border-top: 1px solid rgba(0,0,0,.05);
    }
    
    /* Animation for counter */
    @keyframes countUp {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .counter-value {
        animation: countUp 1s ease-out forwards;
    }
</style>
@endpush 