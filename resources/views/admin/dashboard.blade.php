@extends('admin.layouts.app')

@section('panel')

    <div class="row gy-4 justify-content-center mb-4">
        <div class="col-12 col-md-6 col-xl-4">
            <x-widget
                style="6"
                link="{{route('admin.users.all')}}"
                icon="las la-users"
                title="Tổng số người dùng"
                value="{{$widget['total_users']}}"
                bg="primary"
            />
        </div>
        <div class="col-12 col-md-6 col-xl-4">
            <x-widget
                style="6"
                link="{{route('admin.users.active')}}"
                icon="las la-user-check"
                title="Người dùng đang hoạt động"
                value="{{$widget['verified_users']}}"
                bg="success"
            />
        </div>
    </div>

    <div class="row gy-4 mt-2">
        <div class="col-xxl-3 col-sm-6">
            <x-widget
                style="6"
                link="{{ route('admin.report.invest.history') }}"
                title="Tổng GTHĐ đầu tư"
                icon="las la-chart-bar"
                value="{{ showAmount($invest['total_invests']) }}"
                bg="primary"
            />
        </div><!-- dashboard-w1 end -->
        <div class="col-xxl-3 col-sm-6">
            <x-widget
                style="6"
                link="{{ route('admin.report.transaction') }}?remark=profit"
                title="Tổng lợi tức"
                icon="las la-chart-pie"
                value="{{ showAmount($invest['total_interests']) }}"
                bg="1"
            />
        </div><!-- dashboard-w1 end -->
        <div class="col-xxl-3 col-sm-6">
            <x-widget
                style="6"
                link="{{ route('admin.report.invest.history') }}?status={{ Status::INVEST_RUNNING }}"
                title="Hợp đồng đang hoạt động"
                icon="las la-chart-area"
                value="{{ showAmount($invest['running_invests'], 0, true, false, false) }}"
                bg="12"
            />
        </div><!-- dashboard-w1 end -->
        <div class="col-xxl-3 col-sm-6">
            <x-widget
                style="6"
                link="{{ route('admin.report.invest.history') }}?status={{ Status::INVEST_COMPLETED }}"
                title="Tổng GTHĐ tất toán"
                icon="las la-chart-line"
                value="{{ showAmount($invest['completed_invests']) }}"
                bg="9"
            />
        </div><!-- dashboard-w1 end -->
    </div><!-- row end-->

    <!-- New Dashboard Widgets -->
    <div class="row mb-none-30 mt-5">
        <!-- Top Performing Projects -->
        <div class="col-xl-4 col-lg-6 mb-30">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="las la-trophy text-warning me-2"></i>Dự án hiệu quả nhất
                    </h5>
                    <small class="text-muted">30 ngày qua</small>
                </div>
                <div class="card-body">
                    <div class="top-projects-list">
                        @php
                            $topProjects = \App\Models\Invest::with('project')
                                ->where('created_at', '>=', now()->subDays(30))
                                ->where('status', \App\Constants\Status::INVEST_RUNNING)
                                ->selectRaw('project_id, COUNT(*) as invest_count, SUM(total_price) as total_amount')
                                ->groupBy('project_id')
                                ->orderBy('total_amount', 'desc')
                                ->limit(5)
                                ->get();
                        @endphp

                        @forelse($topProjects as $index => $invest)
                            <div class="d-flex justify-content-between align-items-center mb-3 p-2 rounded {{ $index === 0 ? 'bg-light' : '' }}">
                                <div class="d-flex align-items-center">
                                    <div class="rank-badge me-3">
                                        <span class="badge badge-{{ $index === 0 ? 'warning' : ($index === 1 ? 'info' : 'secondary') }} rounded-pill">
                                            #{{ $index + 1 }}
                                        </span>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">{{ $invest->project->title ?? 'N/A' }}</h6>
                                        <small class="text-muted">{{ $invest->invest_count }} hợp đồng</small>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <strong class="text-success">{{ showAmount($invest->total_amount) }}</strong>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <i class="las la-chart-line text-muted" style="font-size: 3rem;"></i>
                                <p class="text-muted mt-2">Chưa có dữ liệu</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Investment Status Distribution -->
        <div class="col-xl-4 col-lg-6 mb-30">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="las la-chart-pie text-primary me-2"></i>Phân bố trạng thái hợp đồng
                    </h5>
                </div>
                <div class="card-body">
                    @php
                        $statusStats = [
                            'running' => \App\Models\Invest::where('status', \App\Constants\Status::INVEST_RUNNING)->count(),
                            'completed' => \App\Models\Invest::where('status', \App\Constants\Status::INVEST_COMPLETED)->count(),
                            'pending' => \App\Models\Invest::where('status', \App\Constants\Status::INVEST_PENDING)->count(),
                            'cancelled' => \App\Models\Invest::where('status', \App\Constants\Status::INVEST_CANCELED)->count(),
                        ];
                        $totalContracts = array_sum($statusStats);
                    @endphp

                    <div class="status-stats">
                        <div class="stat-item mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <div class="status-indicator bg-success me-2"></div>
                                    <span>Đang chạy</span>
                                </div>
                                <div class="text-end">
                                    <strong>{{ $statusStats['running'] }}</strong>
                                    <small class="text-muted d-block">
                                        {{ $totalContracts > 0 ? number_format(($statusStats['running'] / $totalContracts) * 100, 1) : 0 }}%
                                    </small>
                                </div>
                            </div>
                            <div class="progress mt-1" style="height: 4px;">
                                <div class="progress-bar bg-success" style="width: {{ $totalContracts > 0 ? ($statusStats['running'] / $totalContracts) * 100 : 0 }}%"></div>
                            </div>
                        </div>

                        <div class="stat-item mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <div class="status-indicator bg-primary me-2"></div>
                                    <span>Hoàn thành</span>
                                </div>
                                <div class="text-end">
                                    <strong>{{ $statusStats['completed'] }}</strong>
                                    <small class="text-muted d-block">
                                        {{ $totalContracts > 0 ? number_format(($statusStats['completed'] / $totalContracts) * 100, 1) : 0 }}%
                                    </small>
                                </div>
                            </div>
                            <div class="progress mt-1" style="height: 4px;">
                                <div class="progress-bar bg-primary" style="width: {{ $totalContracts > 0 ? ($statusStats['completed'] / $totalContracts) * 100 : 0 }}%"></div>
                            </div>
                        </div>

                        <div class="stat-item mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <div class="status-indicator bg-warning me-2"></div>
                                    <span>Chờ duyệt</span>
                                </div>
                                <div class="text-end">
                                    <strong>{{ $statusStats['pending'] }}</strong>
                                    <small class="text-muted d-block">
                                        {{ $totalContracts > 0 ? number_format(($statusStats['pending'] / $totalContracts) * 100, 1) : 0 }}%
                                    </small>
                                </div>
                            </div>
                            <div class="progress mt-1" style="height: 4px;">
                                <div class="progress-bar bg-warning" style="width: {{ $totalContracts > 0 ? ($statusStats['pending'] / $totalContracts) * 100 : 0 }}%"></div>
                            </div>
                        </div>

                        <div class="stat-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <div class="status-indicator bg-danger me-2"></div>
                                    <span>Đã hủy</span>
                                </div>
                                <div class="text-end">
                                    <strong>{{ $statusStats['cancelled'] }}</strong>
                                    <small class="text-muted d-block">
                                        {{ $totalContracts > 0 ? number_format(($statusStats['cancelled'] / $totalContracts) * 100, 1) : 0 }}%
                                    </small>
                                </div>
                            </div>
                            <div class="progress mt-1" style="height: 4px;">
                                <div class="progress-bar bg-danger" style="width: {{ $totalContracts > 0 ? ($statusStats['cancelled'] / $totalContracts) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity & Quick Actions -->
        <div class="col-xl-4 col-lg-6 mb-30">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="las la-bolt text-info me-2"></i>Hoạt động gần đây & Thao tác nhanh
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Quick Actions -->
                    <div class="quick-actions mb-4">
                        <h6 class="text-muted mb-3">Thao tác nhanh</h6>
                        <div class="row g-2">
                            <div class="col-6">
                                <a href="{{ route('admin.invest.review') }}" class="btn btn-outline--warning btn-sm w-100">
                                    <i class="las la-eye"></i>
                                    <span class="d-block">Duyệt HĐ</span>
                                    @php
                                        $pendingCount = \App\Models\Invest::where('status', \App\Constants\Status::INVEST_PENDING)->count();
                                    @endphp
                                    @if($pendingCount > 0)
                                        <span class="badge badge--danger">{{ $pendingCount }}</span>
                                    @endif
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('admin.users.all') }}" class="btn btn-outline--info btn-sm w-100">
                                    <i class="las la-users"></i>
                                    <span class="d-block">Quản lý KH</span>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('admin.project.index') }}" class="btn btn-outline--success btn-sm w-100">
                                    <i class="las la-project-diagram"></i>
                                    <span class="d-block">Dự án</span>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('admin.report.transaction') }}" class="btn btn-outline--primary btn-sm w-100">
                                    <i class="las la-chart-line"></i>
                                    <span class="d-block">Báo cáo</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="recent-activity">
                        <h6 class="text-muted mb-3">Hoạt động gần đây</h6>
                        @php
                            $recentInvests = \App\Models\Invest::with(['user', 'project'])
                                ->latest()
                                ->limit(4)
                                ->get();
                        @endphp

                        @forelse($recentInvests as $invest)
                            <div class="activity-item d-flex align-items-center mb-3">
                                <div class="activity-icon me-3">
                                    <i class="las la-{{ $invest->status == \App\Constants\Status::INVEST_RUNNING ? 'play-circle text-success' : ($invest->status == \App\Constants\Status::INVEST_PENDING ? 'clock text-warning' : 'check-circle text-primary') }}"></i>
                                </div>
                                <div class="activity-content flex-grow-1">
                                    <p class="mb-1 small">
                                        <strong>{{ $invest->user->fullname ?? 'N/A' }}</strong>
                                        {{ $invest->status == \App\Constants\Status::INVEST_RUNNING ? 'đã ký hợp đồng' : ($invest->status == \App\Constants\Status::INVEST_PENDING ? 'tạo hợp đồng mới' : 'hoàn thành hợp đồng') }}
                                    </p>
                                    <small class="text-muted">
                                        {{ showAmount($invest->total_price) }} • {{ $invest->created_at->diffForHumans() }}
                                    </small>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-3">
                                <i class="las la-history text-muted" style="font-size: 2rem;"></i>
                                <p class="text-muted mt-2 small">Chưa có hoạt động</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Summary Section -->
    <div class="row gy-4 mt-2">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <h5 class="card-title">Tổng quan cảnh báo hợp đồng</h5>
                        <a href="{{ route('admin.alert.dashboard') }}" class="btn btn-sm btn-outline--primary">
                            <i class="las la-eye"></i> Xem tất cả
                        </a>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="alert {{ $alertSummary['interest_alerts'] > 0 ? 'alert-warning' : 'alert-info' }} d-flex align-items-center">
                                <div class="alert-icon me-3">
                                    <i class="las {{ $alertSummary['interest_alerts'] > 0 ? 'la-exclamation-triangle' : 'la-info-circle' }} fs-1"></i>
                                </div>
                                <div>
                                    <h5 class="mt-1">Thanh toán lãi ({{ $alertSummary['alert_period'] }} ngày)</h5>
                                    <p class="mb-0">
                                        {{ $alertSummary['interest_alerts'] }} hợp đồng đến hạn thanh toán lãi
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="alert alert-danger d-flex align-items-center">
                                <div class="alert-icon me-3">
                                    <i class="las la-exclamation-triangle fs-1"></i>
                                </div>
                                <div>
                                    <h5 class="mt-1">Đáo hạn hợp đồng ({{ $alertSummary['alert_period'] }} ngày)</h5>
                                    <p class="mb-0">
                                        {{ $alertSummary['maturity_alerts'] }} hợp đồng sắp đến ngày đáo hạn
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Alert Summary Section -->
    <!-- Add Recent Investments Table -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="las la-list text-primary me-2"></i>Hợp đồng gần đây
                    </h5>
                    <a href="{{ route('admin.report.invest.history') }}" class="btn btn-sm btn-outline--primary">
                        <i class="las la-list-alt"></i> Xem tất cả
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>Mã HĐ</th>
                                    <th>Người dùng</th>
                                    <th>Dự án</th>
                                    <th>Số lượng</th>
                                    <th>Số tiền</th>
                                    <th>Lợi nhuận</th>
                                    <th>Hình thức trả</th>
                                    <th>Cần trả</th>
                                    <th>Đã trả</th>
                                    <th>Trạng thái</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentInvests as $invest)
                                    <tr>
                                        <td>{{ $invest->invest_no }}</td>
                                        <td>
                                            <span class="fw-bold">{{ $invest->user->fullname }}</span>
                                            <br>
                                            <span class="small"><a href="{{ route('admin.users.detail', $invest->user_id) }}"><span>@</span>{{ $invest->user->username }}</a></span>
                                        </td>
                                        <td>{{ __($invest->project->title) }}</td>
                                        <td>{{ __($invest->quantity) }} @lang('Units')</td>
                                        <td>{{ showAmount($invest->total_price) }}</td>
                                        <td>{{ showAmount($invest->total_earning) }}</td>
                                        <td> @php echo $invest->project->typeBadge @endphp </td>
                                        <td>{{ $invest->project->return_type != Status::LIFETIME ? showAmount($invest->recurring_pay) : '**' }}</td>
                                        <td>{{ showAmount($invest->paid) }}</td>
                                        <td>@php echo $invest->statusBadge @endphp</td>
                                        <td>
                                            <div class="button--group">
                                                <a class="btn btn-outline--primary btn-sm" href="{{ route('admin.invest.details', $invest->id) }}">
                                                    <i class="las la-desktop"></i> Chi tiết
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">Không có dữ liệu</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Investment Statistics Charts -->
    <div class="row mb-none-30 mt-30">
        <div class="col-xl-6 mb-30">
            <div class="card h-100 shadow-sm border-0 modern-chart-card">
                <div class="card-header bg-gradient-primary text-white border-0 rounded-top">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="chart-icon me-3">
                                <i class="las la-chart-bar fa-2x opacity-75"></i>
                            </div>
                            <div>
                                <h5 class="card-title mb-0 text-white fw-bold">Số lượng hợp đồng</h5>
                                <small class="text-white-75" id="investCountSubtitle">12 tháng gần nhất</small>
                            </div>
                        </div>
                        <div class="chart-actions">
                            <button class="btn btn-sm btn-light btn-refresh rounded-pill" data-chart="investCountChart" title="Làm mới dữ liệu">
                                <i class="las la-sync-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <!-- Date Filter Section -->
                    <div class="chart-filter-section p-3 bg-light border-bottom">
                        <div class="row align-items-center g-2">
                            <div class="col-md-8">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-white border-end-0">
                                        <i class="las la-calendar text-primary"></i>
                                    </span>
                                    <input type="text" class="form-control border-start-0 date-range-picker"
                                           id="investCountDateRange"
                                           placeholder="Chọn khoảng thời gian"
                                           autocomplete="off"
                                           style="cursor: pointer !important;">
                                </div>
                            </div>
                            <div class="col-md-4 text-end">
                                <button class="btn btn-sm btn-outline-secondary clear-filter rounded-pill" data-target="investCountDateRange">
                                    <i class="las la-times"></i> Xóa bộ lọc
                                </button>
                                
                            </div>
                        </div>
                        
                        <!-- Quick Filter Buttons -->
                        <div class="quick-filters mt-2">
                            <div class="d-flex flex-wrap gap-2">
                                <button class="btn btn-sm btn-outline-primary quick-filter-btn" data-chart="investCount" data-filter="today">
                                    <i class="las la-calendar-day"></i> Hôm nay
                                </button>
                                <button class="btn btn-sm btn-outline-primary quick-filter-btn" data-chart="investCount" data-filter="this_week">
                                    <i class="las la-calendar-week"></i> Tuần này
                                </button>
                                <button class="btn btn-sm btn-outline-primary quick-filter-btn" data-chart="investCount" data-filter="this_month">
                                    <i class="las la-calendar-alt"></i> Tháng này
                                </button>
                                <button class="btn btn-sm btn-outline-primary quick-filter-btn" data-chart="investCount" data-filter="this_quarter">
                                    <i class="las la-chart-pie"></i> Quý này
                                </button>
                                <button class="btn btn-sm btn-outline-primary quick-filter-btn" data-chart="investCount" data-filter="this_year">
                                    <i class="las la-calendar"></i> Năm nay
                                </button>
                                <button class="btn btn-sm btn-outline-primary quick-filter-btn" data-chart="investCount" data-filter="last_12months">
                                    <i class="las la-history"></i> 12 tháng qua
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Chart Container -->
                    <div class="chart-wrapper p-3">
                        <div id="investCountChart" class="modern-chart-container">
                            <div class="chart-loading text-center py-5">
                                <div class="spinner-border text-primary mb-3" role="status">
                                    <span class="visually-hidden">Đang tải...</span>
                                </div>
                                <p class="text-muted mb-0">Đang tải dữ liệu biểu đồ...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6 mb-30">
            <div class="card h-100 shadow-sm border-0 modern-chart-card">
                <div class="card-header bg-gradient-success text-white border-0 rounded-top">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="chart-icon me-3">
                                <i class="las la-money-bill-wave fa-2x opacity-75"></i>
                            </div>
                            <div>
                                <h5 class="card-title mb-0 text-white fw-bold">Tổng tiền đầu tư</h5>
                                <small class="text-white-75" id="investAmountSubtitle">12 tháng gần nhất</small>
                            </div>
                        </div>
                        <div class="chart-actions">
                            <button class="btn btn-sm btn-light btn-refresh rounded-pill" data-chart="investAmountChart" title="Làm mới dữ liệu">
                                <i class="las la-sync-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <!-- Date Filter Section -->
                    <div class="chart-filter-section p-3 bg-light border-bottom">
                        <div class="row align-items-center g-2">
                            <div class="col-md-8">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-white border-end-0">
                                        <i class="las la-calendar text-success"></i>
                                    </span>
                                    <input type="text" class="form-control border-start-0 date-range-picker"
                                           id="investAmountDateRange"
                                           placeholder="Chọn khoảng thời gian"
                                           autocomplete="off"
                                           style="cursor: pointer !important;">
                                </div>
                            </div>
                            <div class="col-md-4 text-end">
                                <button class="btn btn-sm btn-outline-secondary clear-filter rounded-pill" data-target="investAmountDateRange">
                                    <i class="las la-times"></i> Xóa bộ lọc
                                </button>
                            </div>
                        </div>
                        
                        <!-- Quick Filter Buttons -->
                        <div class="quick-filters mt-2">
                            <div class="d-flex flex-wrap gap-2">
                                <button class="btn btn-sm btn-outline-primary quick-filter-btn" data-chart="investAmount" data-filter="today">
                                    <i class="las la-calendar-day"></i> Hôm nay
                                </button>
                                <button class="btn btn-sm btn-outline-primary quick-filter-btn" data-chart="investAmount" data-filter="this_week">
                                    <i class="las la-calendar-week"></i> Tuần này
                                </button>
                                <button class="btn btn-sm btn-outline-primary quick-filter-btn" data-chart="investAmount" data-filter="this_month">
                                    <i class="las la-calendar-alt"></i> Tháng này
                                </button>
                                <button class="btn btn-sm btn-outline-primary quick-filter-btn" data-chart="investAmount" data-filter="this_quarter">
                                    <i class="las la-chart-pie"></i> Quý này
                                </button>
                                <button class="btn btn-sm btn-outline-primary quick-filter-btn" data-chart="investAmount" data-filter="this_year">
                                    <i class="las la-calendar"></i> Năm nay
                                </button>
                                <button class="btn btn-sm btn-outline-primary quick-filter-btn" data-chart="investAmount" data-filter="last_12months">
                                    <i class="las la-history"></i> 12 tháng qua
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Chart Container -->
                    <div class="chart-wrapper p-3">
                        <div id="investAmountChart" class="modern-chart-container">
                            <div class="chart-loading text-center py-5">
                                <div class="spinner-border text-success mb-3" role="status">
                                    <span class="visually-hidden">Đang tải...</span>
                                </div>
                                <p class="text-muted mb-0">Đang tải dữ liệu biểu đồ...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>





    @include('admin.partials.cron_modal')
@endsection
@push('breadcrumb-plugins')
    <button class="btn btn-outline--primary btn-sm" data-bs-toggle="modal" data-bs-target="#cronModal">
        <i class="las la-server"></i>Thiết lập Cron
    </button>
@endpush

@push('script-lib')
    <script src="{{ asset('assets/admin/js/vendor/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/moment.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/daterangepicker.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/charts.js') }}"></script>
@endpush

@push('style-lib')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/css/daterangepicker.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/css/dashboard-animations.css') }}">
@endpush

@push('style')
    <style>
        .apexcharts-menu {
            min-width: 120px !important;
        }
        
        #investCountChart .apexcharts-yaxis-label tspan,
        #investCountChart .apexcharts-tooltip-text-y-value {
            font-weight: 600;
        }
        
        #investCountChart .apexcharts-datalabel {
            font-weight: bold;
        }
        
        /* Modern Chart Card Styles */
        .modern-chart-card {
            transition: all 0.3s ease;
            border-radius: 15px !important;
            overflow: hidden;
        }
        
        /* Quick Filter Button Styles */
        .quick-filters {
            transition: all 0.3s ease;
        }
        
        .quick-filter-btn {
            border-radius: 20px;
            font-size: 0.8rem;
            padding: 0.25rem 0.7rem;
            transition: all 0.2s ease;
            white-space: nowrap;
        }
        
        .quick-filter-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 3px 5px rgba(0,0,0,0.1);
        }
        
        .quick-filter-btn.btn-primary {
            font-weight: 600;
        }
        
        .chart-filter-section {
            background: rgba(248, 249, 250, 0.5);
            border-radius: 0 0 10px 10px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        /* Loading Animation */
        .btn-refresh.spinning i {
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Restore other important styles */
        .modern-chart-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.1) !important;
        }

        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        }

        .bg-gradient-success {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%) !important;
        }

        .chart-icon {
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .text-white-75 {
            color: rgba(255,255,255,0.75) !important;
        }

        /* Chart Container */
        .modern-chart-container {
            min-height: 400px;
            position: relative;
            border-radius: 10px;
            background: #fff;
        }

        #investCountChart, #investAmountChart {
            min-height: 400px !important;
            width: 100% !important;
        }

        /* Loading Animation */
        .chart-loading {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 10;
        }

        .spinner-border {
            width: 3rem;
            height: 3rem;
        }
        
        .input-group-text {
            border: 1px solid #dee2e6;
        }

        .date-range-picker {
            cursor: pointer !important;
            background-color: #fff !important;
            pointer-events: auto !important;
            user-select: none !important;
            position: relative !important;
            z-index: 1 !important;
        }

        .date-range-picker:focus {
            border-color: #80bdff !important;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25) !important;
            outline: none !important;
        }

        .date-range-picker:hover {
            background-color: #f8f9fa !important;
            border-color: #007bff !important;
        }

        /* Ensure input group doesn't block clicks */
        .input-group {
            position: relative !important;
        }

        .input-group-text {
            pointer-events: none !important;
        }

        .date-range-picker.has-value {
            background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%) !important;
            border-color: #007bff !important;
            font-weight: 600 !important;
            color: #0056b3 !important;
        }

        .date-range-picker::placeholder {
            color: #6c757d !important;
            font-style: italic;
        }

        /* Refresh Button */
        .btn-refresh {
            transition: all 0.3s ease;
        }

        .btn-refresh:hover {
            transform: rotate(180deg);
        }
        
        /* Clear Filter Button */
        .clear-filter {
            transition: all 0.3s ease;
        }

        .clear-filter:hover {
            background-color: #dc3545;
            border-color: #dc3545;
            color: white;
        }
        
        /* Make sure the y-axis values are clearly visible */
        .apexcharts-yaxis text {
            font-weight: 600 !important;
        }
        
        /* Date range picker styles */
        .daterangepicker {
            z-index: 9999 !important;
            border: none !important;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15) !important;
            border-radius: 15px !important;
            overflow: hidden;
            font-family: inherit;
            position: absolute !important;
            margin-top: 5px !important;
        }

        .daterangepicker .ranges {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important;
            border-right: 1px solid #dee2e6 !important;
            border-radius: 15px 0 0 15px !important;
            width: 280px !important;
            float: left !important;
        }

        .daterangepicker .ranges ul {
            width: 100% !important;
            margin: 0 !important;
            padding: 10px !important;
        }

        .daterangepicker .ranges li {
            padding: 12px 20px !important;
            margin: 2px 8px !important;
            border-radius: 8px !important;
            transition: all 0.3s ease !important;
            cursor: pointer !important;
            font-size: 14px !important;
            font-weight: 500 !important;
            display: block !important;
            width: auto !important;
        }
        
        /* Fix for daterangepicker positioning */
        .daterangepicker.show-calendar {
            top: auto !important;
        }
        
        .daterangepicker.opensright:before,
        .daterangepicker.opensright:after,
        .daterangepicker.opensleft:before,
        .daterangepicker.opensleft:after {
            display: none !important;
        }
        
        /* Make the dropdown appear directly below the button */
        .chart-filter-section {
            position: relative !important;
        }
        
        /* Ensure input group doesn't block clicks */
        .input-group {
            position: relative !important;
            z-index: 1 !important;
        }
        
        /* Make date range picker look like a dropdown */
        .date-range-picker {
            cursor: pointer !important;
            background-color: #fff !important;
            pointer-events: auto !important;
            user-select: none !important;
            position: relative !important;
            z-index: 1 !important;
            padding-right: 30px !important;
        }
        
        .date-range-picker:after {
            content: "\f107";
            font-family: "Line Awesome Free";
            font-weight: 900;
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
        }
        
        /* Add dropdown styling */
        .input-group-text {
            border: 1px solid #dee2e6;
        }
        
        /* Ensure dropdown appears below the button */
        .chart-filter-section .input-group {
            position: static !important;
        }
        
        .chart-filter-section .daterangepicker {
            top: 100% !important;
            left: 0 !important;
            right: auto !important;
            width: auto !important;
            min-width: 600px !important;
        }
        
        /* Style the dropdown toggle */
        .input-group .input-group-text {
            background-color: #f8f9fa !important;
            border-right: none !important;
        }
        
        .input-group .form-control {
            border-left: none !important;
        }
        
        /* Quarterly filter highlight */
        .quick-filter-btn[data-filter="this_quarter"] {
            border-color: #6f42c1;
            color: #6f42c1;
        }
        
        .quick-filter-btn[data-filter="this_quarter"].btn-primary {
            background-color: #6f42c1;
            color: white;
        }
        
        /* Fix for date range display */
        .date-range-picker.has-value {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
@endpush

@push('script')
    <script>
        "use strict";

        // Check if ApexCharts is loaded
        if (typeof ApexCharts === 'undefined') {
            console.error('ApexCharts is not loaded!');
        } else {
            console.log('ApexCharts loaded successfully');
        }

        const dateRangeOptions = {
            autoUpdateInput: false,
            applyButtonClasses: 'btn btn--primary',
            cancelButtonClasses: 'btn btn--secondary',
            showDropdowns: true,
            showWeekNumbers: true,
            timePicker: false,
            timePickerIncrement: 1,
            timePicker24Hour: true,
            opens: 'left',
            drops: 'auto',
            buttonClasses: 'btn btn-sm',
            locale: {
                cancelLabel: 'Hủy',
                applyLabel: 'Áp dụng',
                fromLabel: 'Từ ngày',
                toLabel: 'Đến ngày',
                customRangeLabel: '📅 Tùy chỉnh khoảng thời gian',
                weekLabel: 'T',
                daysOfWeek: ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'],
                monthNames: ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6', 'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'],
                firstDay: 1,
                format: 'DD/MM/YYYY'
            },
            ranges: {
                '📅 Hôm nay': [moment(), moment()],
                '📅 15 ngày qua': [moment().subtract(14, 'days'), moment()],
                '📅 30 ngày qua': [moment().subtract(29, 'days'), moment()],

                '📊 Tuần trước': [moment().subtract(1, 'week').startOf('week'), moment().subtract(1, 'week').endOf('week')],
                '📊 Tháng này': [moment().startOf('month'), moment().endOf('month')],
                '📊 Tháng trước': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],

                '🗓️ Quý này': [moment().startOf('quarter'), moment().endOf('quarter')],
                '🗓️ Quý trước': [moment().subtract(1, 'quarter').startOf('quarter'), moment().subtract(1, 'quarter').endOf('quarter')]
            },
            maxDate: moment(),
            minDate: moment().subtract(5, 'years')
        }

        const changeDatePickerText = (element, startDate, endDate) => {
            // Format dates in Vietnamese style
            const startFormatted = startDate.format('DD/MM/YYYY');
            const endFormatted = endDate.format('DD/MM/YYYY');

            // Check if same day
            if (startDate.isSame(endDate, 'day')) {
                $(element).val(`📅 ${startFormatted}`);
            } else {
                $(element).val(`📅 ${startFormatted} - ${endFormatted}`);
            }

            // Add a nice visual indicator
            $(element).addClass('has-value');
        }

        // Store chart instances for later reference
        let investCountChartInstance = null;
        let investAmountChartInstance = null;
        
        // Function to load the contract count chart
        function loadInvestCountChart(startDate = null, endDate = null) {
            // Show modern loading state
            $('#investCountChart').html(`
                <div class="chart-loading text-center py-5">
                    <div class="spinner-border text-primary mb-3" role="status">
                        <span class="visually-hidden">Đang tải...</span>
                    </div>
                    <p class="text-muted mb-0">Đang tải dữ liệu biểu đồ...</p>
                </div>
            `);

            let url = "{{ route('admin.invest.report.statistics') }}";

            // Add date range parameters if provided
            if (startDate && endDate) {
                url += `?start_date=${startDate.format('YYYY-MM-DD')}&end_date=${endDate.format('YYYY-MM-DD')}`;
                // Update subtitle
                $('#investCountSubtitle').text(`${startDate.format('DD/MM/YYYY')} - ${endDate.format('DD/MM/YYYY')}`);
            } else {
                $('#investCountSubtitle').text('12 tháng gần nhất');
            }

            $.get(url, function(response) {
                console.log('Invest Count Chart Response:', response);
                const investCountChartEl = document.getElementById('investCountChart');

                if (investCountChartEl && response && response.months && response.invest_counts) {
                    // Format data to ensure integers for contract counts
                    const formattedCounts = response.invest_counts.map(count => Math.round(count));

                    // Destroy previous chart instance if it exists
                    if (investCountChartInstance) {
                        investCountChartInstance.destroy();
                    }

                    // Determine chart title based on date range
                    let chartTitle = 'Số lượng hợp đồng';
                    if (startDate && endDate) {
                        if (startDate.isSame(endDate, 'day')) {
                            chartTitle = `Số lượng hợp đồng ngày ${startDate.format('DD/MM/YYYY')}`;
                        } else {
                            const daysDiff = endDate.diff(startDate, 'days');
                            if (daysDiff <= 31) {
                                chartTitle = `Số lượng hợp đồng theo ngày (${startDate.format('DD/MM')} - ${endDate.format('DD/MM/YYYY')})`;
                            } else {
                                chartTitle = `Số lượng hợp đồng theo tháng (${startDate.format('MM/YYYY')} - ${endDate.format('MM/YYYY')})`;
                            }
                        }
                    } else {
                        chartTitle = 'Số lượng hợp đồng theo tháng (12 tháng gần nhất)';
                    }

                    try {
                        // Clear loading state
                        $('#investCountChart').html('');

                        // Create modern chart with better styling
                        const chartOptions = {
                            series: [{ name: chartTitle, data: formattedCounts }],
                            chart: {
                                type: 'bar',
                                height: 400,
                                toolbar: {
                                    show: true,
                                    tools: {
                                        download: true,
                                        selection: false,
                                        zoom: false,
                                        zoomin: false,
                                        zoomout: false,
                                        pan: false,
                                        reset: false
                                    }
                                },
                                animations: {
                                    enabled: true,
                                    easing: 'easeinout',
                                    speed: 800
                                }
                            },
                            plotOptions: {
                                bar: {
                                    horizontal: false,
                                    columnWidth: '60%',
                                    borderRadius: 8,
                                    dataLabels: {
                                        position: 'top'
                                    }
                                }
                            },
                            dataLabels: {
                                enabled: true,
                                formatter: function (val) {
                                    return val;
                                },
                                offsetY: -20,
                                style: {
                                    fontSize: '12px',
                                    colors: ["#304758"]
                                }
                            },
                            xaxis: {
                                categories: response.months,
                                labels: {
                                    style: {
                                        fontSize: '12px'
                                    }
                                }
                            },
                            yaxis: {
                                title: {
                                    text: 'Số lượng hợp đồng'
                                },
                                labels: {
                                    formatter: function (val) {
                                        return Math.round(val);
                                    }
                                }
                            },
                            colors: ['#667eea'],
                            grid: {
                                borderColor: '#e7e7e7',
                                row: {
                                    colors: ['#f3f3f3', 'transparent'],
                                    opacity: 0.5
                                }
                            },
                            tooltip: {
                                y: {
                                    formatter: function (val) {
                                        return val + " hợp đồng";
                                    }
                                }
                            }
                        };

                        investCountChartInstance = new ApexCharts(investCountChartEl, chartOptions);
                        investCountChartInstance.render();
                        console.log('Invest Count Chart created successfully');
                    } catch (error) {
                        console.error('Error creating invest count chart:', error);
                        $('#investCountChart').html(`
                            <div class="text-center py-5 text-danger">
                                <i class="las la-exclamation-triangle fa-3x mb-3"></i>
                                <p class="mb-0">Lỗi tải biểu đồ</p>
                                <small class="text-muted">${error.message}</small>
                            </div>
                        `);
                    }
                } else {
                    console.error('Missing chart element or data for invest count chart');
                    $('#investCountChart').html(`
                        <div class="text-center py-5 text-warning">
                            <i class="las la-info-circle fa-3x mb-3"></i>
                            <p class="mb-0">Không có dữ liệu hiển thị</p>
                        </div>
                    `);
                }
            }).fail(function(xhr, status, error) {
                console.error('Failed to load invest count chart data:', error);
                $('#investCountChart').html(`
                    <div class="text-center py-5 text-danger">
                        <i class="las la-exclamation-triangle fa-3x mb-3"></i>
                        <p class="mb-0">Lỗi tải dữ liệu</p>
                        <small class="text-muted">Vui lòng thử lại sau</small>
                    </div>
                `);
            });
        }
        
        // Function to load the investment amount chart
        function loadInvestAmountChart(startDate = null, endDate = null) {
            // Show modern loading state
            $('#investAmountChart').html(`
                <div class="chart-loading text-center py-5">
                    <div class="spinner-border text-success mb-3" role="status">
                        <span class="visually-hidden">Đang tải...</span>
                    </div>
                    <p class="text-muted mb-0">Đang tải dữ liệu biểu đồ...</p>
                </div>
            `);

            let url = "{{ route('admin.invest.report.statistics') }}";

            // Add date range parameters if provided
            if (startDate && endDate) {
                url += `?start_date=${startDate.format('YYYY-MM-DD')}&end_date=${endDate.format('YYYY-MM-DD')}`;
                // Update subtitle
                $('#investAmountSubtitle').text(`${startDate.format('DD/MM/YYYY')} - ${endDate.format('DD/MM/YYYY')}`);
            } else {
                $('#investAmountSubtitle').text('12 tháng gần nhất');
            }

            $.get(url, function(response) {
                console.log('Invest Amount Chart Response:', response);
                const investAmountChartEl = document.getElementById('investAmountChart');

                if (investAmountChartEl && response && response.months && response.invest_amounts) {
                    // Destroy previous chart instance if it exists
                    if (investAmountChartInstance) {
                        investAmountChartInstance.destroy();
                    }

                    // Determine chart title based on date range
                    let chartTitle = 'Tổng số tiền đầu tư';
                    if (startDate && endDate) {
                        if (startDate.isSame(endDate, 'day')) {
                            chartTitle = `Tổng số tiền đầu tư ngày ${startDate.format('DD/MM/YYYY')}`;
                        } else {
                            const daysDiff = endDate.diff(startDate, 'days');
                            if (daysDiff <= 31) {
                                chartTitle = `Tổng số tiền đầu tư theo ngày (${startDate.format('DD/MM')} - ${endDate.format('DD/MM/YYYY')})`;
                            } else {
                                chartTitle = `Tổng số tiền đầu tư theo tháng (${startDate.format('MM/YYYY')} - ${endDate.format('MM/YYYY')})`;
                            }
                        }
                    } else {
                        chartTitle = 'Tổng số tiền đầu tư theo tháng (12 tháng gần nhất)';
                    }

                    try {
                        // Clear loading state
                        $('#investAmountChart').html('');

                        // Format amounts for display
                        const formattedAmounts = response.invest_amounts.map(amount => parseFloat(amount));

                        // Create modern chart with better styling
                        const chartOptions = {
                            series: [{ name: chartTitle, data: formattedAmounts }],
                            chart: {
                                type: 'bar',
                                height: 400,
                                toolbar: {
                                    show: true,
                                    tools: {
                                        download: true,
                                        selection: false,
                                        zoom: false,
                                        zoomin: false,
                                        zoomout: false,
                                        pan: false,
                                        reset: false
                                    }
                                },
                                animations: {
                                    enabled: true,
                                    easing: 'easeinout',
                                    speed: 800
                                }
                            },
                            plotOptions: {
                                bar: {
                                    horizontal: false,
                                    columnWidth: '60%',
                                    borderRadius: 8,
                                    dataLabels: {
                                        position: 'top'
                                    }
                                }
                            },
                            dataLabels: {
                                enabled: true,
                                formatter: function (val) {
                                    return new Intl.NumberFormat('vi-VN').format(val);
                                },
                                offsetY: -20,
                                style: {
                                    fontSize: '12px',
                                    colors: ["#304758"]
                                }
                            },
                            xaxis: {
                                categories: response.months,
                                labels: {
                                    style: {
                                        fontSize: '12px'
                                    }
                                }
                            },
                            yaxis: {
                                title: {
                                    text: 'Số tiền ({{ __($general->cur_text ?? "VND") }})'
                                },
                                labels: {
                                    formatter: function (val) {
                                        return new Intl.NumberFormat('vi-VN').format(val);
                                    }
                                }
                            },
                            colors: ['#11998e'],
                            grid: {
                                borderColor: '#e7e7e7',
                                row: {
                                    colors: ['#f3f3f3', 'transparent'],
                                    opacity: 0.5
                                }
                            },
                            tooltip: {
                                y: {
                                    formatter: function (val) {
                                        return new Intl.NumberFormat('vi-VN').format(val) + " {{ __($general->cur_text ?? 'VND') }}";
                                    }
                                }
                            }
                        };

                        investAmountChartInstance = new ApexCharts(investAmountChartEl, chartOptions);
                        investAmountChartInstance.render();
                        console.log('Invest Amount Chart created successfully');
                    } catch (error) {
                        console.error('Error creating invest amount chart:', error);
                        $('#investAmountChart').html(`
                            <div class="text-center py-5 text-danger">
                                <i class="las la-exclamation-triangle fa-3x mb-3"></i>
                                <p class="mb-0">Lỗi tải biểu đồ</p>
                                <small class="text-muted">${error.message}</small>
                            </div>
                        `);
                    }
                } else {
                    console.error('Missing chart element or data for invest amount chart');
                    $('#investAmountChart').html(`
                        <div class="text-center py-5 text-warning">
                            <i class="las la-info-circle fa-3x mb-3"></i>
                            <p class="mb-0">Không có dữ liệu hiển thị</p>
                        </div>
                    `);
                }
            }).fail(function(xhr, status, error) {
                console.error('Failed to load invest amount chart data:', error);
                $('#investAmountChart').html(`
                    <div class="text-center py-5 text-danger">
                        <i class="las la-exclamation-triangle fa-3x mb-3"></i>
                        <p class="mb-0">Lỗi tải dữ liệu</p>
                        <small class="text-muted">Vui lòng thử lại sau</small>
                    </div>
                `);
            });
        }
        
        // Function to update card titles based on date range
        function updateCardTitles(startDate, endDate) {
            let countTitle, amountTitle, countDescription, amountDescription;

            if (startDate.isSame(endDate, 'day')) {
                // Single day
                countTitle = `Biểu đồ số lượng hợp đồng ngày ${startDate.format('DD/MM/YYYY')}`;
                amountTitle = `Tổng số tiền đầu tư ngày ${startDate.format('DD/MM/YYYY')}`;
                countDescription = `Thống kê số lượng hợp đồng được ký kết ngày ${startDate.format('DD/MM/YYYY')}`;
                amountDescription = `Thống kê tổng giá trị đầu tư ngày ${startDate.format('DD/MM/YYYY')}`;
            } else {
                const daysDiff = endDate.diff(startDate, 'days');
                if (daysDiff <= 31) {
                    // Daily range
                    countTitle = `Biểu đồ số lượng hợp đồng theo ngày`;
                    amountTitle = `Tổng số tiền đầu tư theo ngày`;
                    countDescription = `Thống kê số lượng hợp đồng từ ${startDate.format('DD/MM')} đến ${endDate.format('DD/MM/YYYY')}`;
                    amountDescription = `Thống kê tổng giá trị đầu tư từ ${startDate.format('DD/MM')} đến ${endDate.format('DD/MM/YYYY')}`;
                } else {
                    // Monthly range
                    countTitle = `Biểu đồ số lượng hợp đồng theo tháng`;
                    amountTitle = `Tổng số tiền đầu tư theo tháng`;
                    countDescription = `Thống kê số lượng hợp đồng từ ${startDate.format('MM/YYYY')} đến ${endDate.format('MM/YYYY')}`;
                    amountDescription = `Thống kê tổng giá trị đầu tư từ ${startDate.format('MM/YYYY')} đến ${endDate.format('MM/YYYY')}`;
                }
            }

            // Update titles and descriptions
            $('#investCountDateRange').closest('.card').find('.card-title').html(`<i class="las la-chart-bar text-primary me-2"></i>${countTitle}`);
            $('#investCountDateRange').closest('.card').find('.chart-info p').text(countDescription);

            $('#investAmountDateRange').closest('.card').find('.card-title').html(`<i class="las la-money-bill-wave text-success me-2"></i>${amountTitle}`);
            $('#investAmountDateRange').closest('.card').find('.chart-info p').text(amountDescription);
        }

        // Function to reset card titles to default
        function resetCardTitles() {
            $('#investCountDateRange').closest('.card').find('.card-title').html('<i class="las la-chart-bar text-primary me-2"></i>Biểu đồ số lượng hợp đồng theo tháng');
            $('#investCountDateRange').closest('.card').find('.chart-info p').text('Thống kê số lượng hợp đồng được ký kết theo từng tháng (12 tháng gần nhất)');

            $('#investAmountDateRange').closest('.card').find('.card-title').html('<i class="las la-money-bill-wave text-success me-2"></i>Tổng số tiền đầu tư theo tháng');
            $('#investAmountDateRange').closest('.card').find('.chart-info p').text('Thống kê tổng giá trị đầu tư theo từng tháng (12 tháng gần nhất)');
        }

        // Handle quick filter button clicks
        $('.quick-filter-btn').on('click', function() {
            const chartType = $(this).data('chart');
            const filterType = $(this).data('filter');
            let startDate, endDate;
            
            // Reset all buttons to outline style
            $(`.quick-filter-btn[data-chart="${chartType}"]`).removeClass('btn-primary').addClass('btn-outline-primary');
            
            // Make the clicked button active
            $(this).removeClass('btn-outline-primary').addClass('btn-primary');
            
            // Set start and end dates based on filter type
            switch (filterType) {
                case 'today':
                    startDate = moment().startOf('day');
                    endDate = moment().endOf('day');
                    break;
                case 'this_week':
                    startDate = moment().startOf('week');
                    endDate = moment().endOf('week');
                    break;
                case 'this_month':
                    startDate = moment().startOf('month');
                    endDate = moment().endOf('month');
                    break;
                case 'this_quarter':
                    startDate = moment().startOf('quarter');
                    endDate = moment().endOf('quarter');
                    break;
                case 'this_year':
                    startDate = moment().startOf('year');
                    endDate = moment().endOf('year');
                    break;
                case 'last_12months':
                    startDate = moment().subtract(11, 'months').startOf('month');
                    endDate = moment().endOf('month');
                    break;
                default:
                    startDate = moment().subtract(11, 'months').startOf('month');
                    endDate = moment().endOf('month');
                    break;
            }
            
            // Update the date range picker
            const dateRangeId = chartType === 'investCount' ? 'investCountDateRange' : 'investAmountDateRange';
            const $dateRangePicker = $(`#${dateRangeId}`);
            
            if ($dateRangePicker.data('daterangepicker')) {
                $dateRangePicker.data('daterangepicker').setStartDate(startDate);
                $dateRangePicker.data('daterangepicker').setEndDate(endDate);
                changeDatePickerText(`#${dateRangeId}`, startDate, endDate);
            }
            
            // Reload the appropriate chart
            if (chartType === 'investCount') {
                $('#investCountSubtitle').text(`${startDate.format('DD/MM/YYYY')} - ${endDate.format('DD/MM/YYYY')}`);
                loadInvestCountChart(startDate, endDate);
            } else if (chartType === 'investAmount') {
                $('#investAmountSubtitle').text(`${startDate.format('DD/MM/YYYY')} - ${endDate.format('DD/MM/YYYY')}`);
                loadInvestAmountChart(startDate, endDate);
            }
        });

        // Initialize date range pickers
        $(document).ready(function() {
            // Wait for all scripts to load
            setTimeout(function() {
                console.log('Initializing date range pickers...');

                // Force remove any existing daterangepicker instances
                $('#investCountDateRange, #investAmountDateRange').each(function() {
                    if ($(this).data('daterangepicker')) {
                        $(this).data('daterangepicker').remove();
                    }
                });

                // Initialize with full options
                try {
                    // Common options for both pickers
                    const commonOptions = {
                        autoUpdateInput: false,
                        applyButtonClasses: 'btn btn--primary',
                        cancelButtonClasses: 'btn btn--secondary',
                        showDropdowns: true,
                        showWeekNumbers: true,
                        timePicker: false,
                        opens: 'left',
                        drops: 'auto',
                        buttonClasses: 'btn btn-sm',
                        positionFixed: true,
                        parentEl: '.chart-filter-section',
                        locale: {
                            cancelLabel: 'Hủy',
                            applyLabel: 'Áp dụng',
                            fromLabel: 'Từ ngày',
                            toLabel: 'Đến ngày',
                            customRangeLabel: '📅 Tùy chỉnh khoảng thời gian',
                            weekLabel: 'T',
                            daysOfWeek: ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'],
                            monthNames: ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6', 'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'],
                            firstDay: 1,
                            format: 'DD/MM/YYYY'
                        },
                        ranges: dateRangeOptions.ranges
                    };
                    
                    // Create custom containers for each daterangepicker
                    $('.chart-filter-section').each(function(index) {
                        $(this).attr('id', 'chart-filter-section-' + index);
                    });
                    
                    // Set parent container for first chart
                    const countOptions = {...commonOptions, parentEl: '#chart-filter-section-0'};
                    $('#investCountDateRange').daterangepicker(countOptions, function(start, end) {
                        changeDatePickerText('#investCountDateRange', start, end);
                        loadInvestCountChart(start, end);
                    });

                    // Set parent container for second chart
                    const amountOptions = {...commonOptions, parentEl: '#chart-filter-section-1'};
                    $('#investAmountDateRange').daterangepicker(amountOptions, function(start, end) {
                        changeDatePickerText('#investAmountDateRange', start, end);
                        loadInvestAmountChart(start, end);
                    });
                    
                    // Fix positioning issues
                    $('.daterangepicker').css({
                        'z-index': '9999',
                        'position': 'absolute'
                    });
                    
                    // Override the daterangepicker's move method to fix positioning
                    const originalMove = $.fn.daterangepicker.Constructor.prototype.move;
                    $.fn.daterangepicker.Constructor.prototype.move = function() {
                        originalMove.call(this);
                        
                        // Get the input element and its parent
                        const $input = $(this.element);
                        const $parent = $input.closest('.chart-filter-section');
                        
                        if ($parent.length) {
                            // Get positions
                            const inputOffset = $input.offset();
                            const parentOffset = $parent.offset();
                            const inputHeight = $input.outerHeight();
                            
                            // Position directly below the input
                            this.container.css({
                                'top': inputHeight + 5,
                                'left': 0,
                                'right': 'auto'
                            });
                        }
                    };
                    
                    // Trigger move to apply positioning
                    $('#investCountDateRange, #investAmountDateRange').each(function() {
                        if ($(this).data('daterangepicker')) {
                            $(this).data('daterangepicker').move();
                        }
                    });
                    
                    console.log('Date range pickers initialized successfully');
                } catch (error) {
                    console.error('Error initializing date range pickers:', error);
                }
            }, 500);
        });

        // Handle cancel/clear events for date range pickers
        $('#investCountDateRange').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('').removeClass('has-value');
            $('#investCountSubtitle').text('12 tháng gần nhất');
            loadInvestCountChart();
        });

        $('#investAmountDateRange').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('').removeClass('has-value');
            $('#investAmountSubtitle').text('12 tháng gần nhất');
            loadInvestAmountChart();
        });
        
        // Load charts initially without date range (default to last 12 months)
        // Add a small delay to ensure DOM is fully ready
        setTimeout(function() {
            console.log('Starting to load charts...');
            console.log('investCountChart element:', document.getElementById('investCountChart'));
            console.log('investAmountChart element:', document.getElementById('investAmountChart'));

            loadInvestCountChart();
            loadInvestAmountChart();
        }, 500);
        
        // Handle refresh button clicks
        $('.btn-refresh').on('click', function() {
            const chartId = $(this).data('chart');
            const $btn = $(this);

            // Add spinning animation
            $btn.addClass('spinning');

            // Remove spinning animation after 2 seconds
            setTimeout(() => {
                $btn.removeClass('spinning');
            }, 2000);

            if (chartId === 'investCountChart') {
                // Get current date range values if set
                const dateRangePicker = $('#investCountDateRange').data('daterangepicker');
                if (dateRangePicker && dateRangePicker.startDate && dateRangePicker.endDate) {
                    loadInvestCountChart(dateRangePicker.startDate, dateRangePicker.endDate);
                } else {
                    loadInvestCountChart();
                }
            } else if (chartId === 'investAmountChart') {
                // Get current date range values if set
                const dateRangePicker = $('#investAmountDateRange').data('daterangepicker');
                if (dateRangePicker && dateRangePicker.startDate && dateRangePicker.endDate) {
                    loadInvestAmountChart(dateRangePicker.startDate, dateRangePicker.endDate);
                } else {
                    loadInvestAmountChart();
                }
            }
        });

        // Handle clear filter button clicks
        $('.clear-filter').on('click', function() {
            const targetId = $(this).data('target');
            const $target = $('#' + targetId);

            // Clear the date range picker
            $target.val('').removeClass('has-value');
            if ($target.data('daterangepicker')) {
                $target.data('daterangepicker').setStartDate(moment().subtract(11, 'months').startOf('month'));
                $target.data('daterangepicker').setEndDate(moment().endOf('month'));
            }

            // Reload charts with default data
            if (targetId === 'investCountDateRange') {
                $('#investCountSubtitle').text('12 tháng gần nhất');
                loadInvestCountChart();
            } else if (targetId === 'investAmountDateRange') {
                $('#investAmountSubtitle').text('12 tháng gần nhất');
                loadInvestAmountChart();
            }
        });
    </script>
@endpush
