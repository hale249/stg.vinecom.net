@extends('admin.layouts.app')

@section('panel')
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card shadow-sm rounded-4 mb-4">
            <div class="card-header bg-light border-bottom-0 rounded-top-4">
                <h5 class="card-title fw-bold mb-0">Cài đặt cảnh báo</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.alert.settings') }}" method="post" class="row g-3 align-items-end">
                    @csrf
                    <div class="col-md-6">
                        <label class="form-label">Thời gian cảnh báo <i class="las la-info-circle text-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="Số ngày trước khi thanh toán/đáo hạn để hiển thị cảnh báo khẩn"></i></label>
                        <div class="input-group">
                            <input type="number" class="form-control" name="alert_period" value="{{ $alertPeriod }}" min="1" max="180" required>
                            <span class="input-group-text">Ngày</span>
                        </div>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn--primary w-100 h-45">Lưu cài đặt</button>
                        <a href="{{ route('admin.alert.dashboard') }}" class="btn btn-outline-secondary w-100 h-45">Reset</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-12">
        <div class="card shadow-sm rounded-4 mb-4">
            <div class="card-header bg-light border-bottom-0 rounded-top-4">
                <h5 class="card-title fw-bold mb-0">Bộ lọc dữ liệu</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.alert.dashboard') }}" method="get" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Dự án</label>
                        <select class="form-select" name="project_id">
                            <option value="">Tất cả dự án</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}" @selected(request()->project_id == $project->id)>{{ $project->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Khoảng thời gian</label>
                        <select class="form-select" name="date_range">
                            <option value="all" @selected(request()->date_range == 'all')>Tất cả thời gian</option>
                            <option value="30days" @selected(request()->date_range == '30days')>30 ngày tới</option>
                            <option value="60days" @selected(request()->date_range == '60days')>60 ngày tới</option>
                            <option value="90days" @selected(request()->date_range == '90days')>90 ngày tới</option>
                            <option value="custom" @selected(request()->date_range == 'custom')>Tùy chọn</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Trạng thái hợp đồng</label>
                        <select class="form-select" name="contract_status">
                            <option value="active" @selected(request()->contract_status == 'active')>Đang hoạt động</option>
                            <option value="expired" @selected(request()->contract_status == 'expired')>Đã hết hạn</option>
                            <option value="all" @selected(request()->contract_status == 'all')>Tất cả</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn--primary w-100 h-45"><i class="las la-filter"></i> Lọc</button>
                        <a href="{{ route('admin.alert.dashboard') }}" class="btn btn-outline-secondary w-100 h-45">Reset</a>
                    </div>
                    <div class="row custom-date-range mt-2" style="{{ request()->date_range != 'custom' ? 'display: none;' : '' }}">
                        <div class="col-md-6">
                            <label class="form-label">Ngày bắt đầu</label>
                            <input type="date" class="form-control" name="start_date" value="{{ request()->start_date }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Ngày kết thúc</label>
                            <input type="date" class="form-control" name="end_date" value="{{ request()->end_date }}">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4 g-3">
    <div class="col-xl-3 col-md-6">
        <div class="widget-two box--shadow2 b-radius--5 bg--white h-100 d-flex flex-column justify-content-between clickable-widget" title="Xem hợp đồng đang hoạt động">
            <div class="widget-two__icon b-radius--5 bg--primary">
                <i class="las la-file-contract"></i>
            </div>
            <div class="widget-two__content">
                <h3 class="fw-bold fs-2">{{ $stats['total_active_contracts'] }}</h3>
                <p>Hợp đồng đang hoạt động</p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="widget-two box--shadow2 b-radius--5 bg--white h-100 d-flex flex-column justify-content-between clickable-widget" title="Xem hợp đồng thanh toán lãi tháng này">
            <div class="widget-two__icon b-radius--5 bg--success">
                <i class="las la-money-bill-wave"></i>
            </div>
            <div class="widget-two__content">
                <h3 class="fw-bold fs-2">{{ $stats['current_month_interest'] }}</h3>
                <p>Thanh toán lãi tháng này</p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="widget-two box--shadow2 b-radius--5 bg--white h-100 d-flex flex-column justify-content-between clickable-widget" title="Xem hợp đồng đáo hạn tháng này">
            <div class="widget-two__icon b-radius--5 bg--danger">
                <i class="las la-hourglass-end"></i>
            </div>
            <div class="widget-two__content">
                <h3 class="fw-bold fs-2">{{ $stats['current_month_maturity'] }}</h3>
                <p>Đáo hạn tháng này</p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="widget-two box--shadow2 b-radius--5 bg--white h-100 d-flex flex-column justify-content-between clickable-widget" title="Xem hợp đồng sắp tới trong tháng sau">
            <div class="widget-two__icon b-radius--5 bg--warning">
                <i class="las la-calendar-check"></i>
            </div>
            <div class="widget-two__content">
                <h3 class="fw-bold fs-2">{{ $stats['next_month_interest'] + $stats['next_month_maturity'] }}</h3>
                <p>Sắp tới trong tháng sau</p>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-12">
        <div class="card shadow-sm rounded-4 mb-4">
            <div class="card-header bg-light border-bottom-0 rounded-top-4 d-flex align-items-center justify-content-between">
                <h5 class="card-title fw-bold mb-0">Biểu đồ cảnh báo hợp đồng theo tháng</h5>
                <div id="chart-loading" class="spinner-border text-primary d-none" role="status" style="width: 1.5rem; height: 1.5rem;"><span class="visually-hidden">Đang tải...</span></div>
            </div>
            <div class="card-body">
                <div id="alertChart" style="height: 400px;"></div>
                <div class="mt-2 text-muted small">* Di chuột vào cột để xem chi tiết số lượng hợp đồng từng loại.</div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card shadow-sm rounded-4 mb-4">
            <div class="card-header bg-light border-bottom-0 rounded-top-4">
                <h5 class="card-title fw-bold mb-0">Chi tiết cảnh báo hợp đồng</h5>
            </div>
            <div class="card-body">
                <ul class="nav nav-tabs" id="alertTabs" role="tablist">
                    @foreach($monthlyData as $monthKey => $monthData)
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $loop->first ? 'active' : '' }} {{ $monthData['is_urgent'] ? 'text-danger' : '' }}" 
                                id="month-{{ $monthKey }}-tab" 
                                data-bs-toggle="tab" 
                                data-bs-target="#month-{{ $monthKey }}" 
                                type="button" 
                                role="tab">
                                {{ $monthData['month'] }}
                                <span class="badge bg-{{ $monthData['is_urgent'] ? 'danger' : 'primary' }} rounded-pill">
                                    {{ $monthData['interest_alerts'] + $monthData['maturity_alerts'] }}
                                </span>
                            </button>
                        </li>
                    @endforeach
                </ul>
                <div class="tab-content mt-4" id="alertTabsContent">
                    @foreach($monthlyData as $monthKey => $monthData)
                        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" 
                            id="month-{{ $monthKey }}" 
                            role="tabpanel">
                            <h5 class="mb-3">Thanh toán lãi ({{ $monthData['interest_alerts'] }})</h5>
                            @if(count($monthData['interest_contracts']) > 0)
                                <div class="table-responsive--sm table-responsive">
                                    <table class="table table--light style--two align-middle">
                                        <thead class="sticky-top bg-light">
                                            <tr>
                                                <th>Mã HĐ</th>
                                                <th>ID</th>
                                                <th>Dự án</th>
                                                <th>Ngày thanh toán</th>
                                                <th>Số ngày còn lại</th>
                                                <th>Số tiền</th>
                                                <th>Hành động</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($monthData['interest_contracts'] as $contract)
                                                <tr class="{{ $contract['days_remaining'] <= $alertPeriod ? 'table-danger border-danger' : '' }}">
                                                    <td>{{ $contract['invest_no'] }}</td>
                                                    <td>{{ $contract['id'] }}</td>
                                                    <td>{{ $contract['project_name'] }}</td>
                                                    <td>{{ $contract['payment_date'] }}</td>
                                                    <td>
                                                        @if($contract['days_remaining'] <= $alertPeriod)
                                                            <span class="badge bg-danger">{{ $contract['days_remaining'] }} ngày</span>
                                                        @else
                                                            <span class="badge bg-success">{{ $contract['days_remaining'] }} ngày</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ showAmount($contract['amount']) }} VND</td>
                                                    <td>
                                                        <a href="{{ route('admin.report.invest.history') }}?search={{ $contract['invest_no'] }}" class="btn btn-sm btn-outline--primary">
                                                            <i class="las la-desktop"></i> Chi tiết
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info">Không có khoản thanh toán lãi nào trong giai đoạn này.</div>
                            @endif
                            <h5 class="mt-4 mb-3">Đáo hạn ({{ $monthData['maturity_alerts'] }})</h5>
                            @if(count($monthData['maturity_contracts']) > 0)
                                <div class="table-responsive--sm table-responsive">
                                    <table class="table table--light style--two align-middle">
                                        <thead class="sticky-top bg-light">
                                            <tr>
                                                <th>Mã HĐ</th>
                                                <th>ID</th>
                                                <th>Dự án</th>
                                                <th>Ngày đáo hạn</th>
                                                <th>Số ngày còn lại</th>
                                                <th>Số tiền</th>
                                                <th>Hành động</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($monthData['maturity_contracts'] as $contract)
                                                <tr class="{{ $contract['days_remaining'] <= $alertPeriod ? 'table-danger border-danger' : '' }}">
                                                    <td>{{ $contract['invest_no'] }}</td>
                                                    <td>{{ $contract['id'] }}</td>
                                                    <td>{{ $contract['project_name'] }}</td>
                                                    <td>{{ $contract['maturity_date'] }}</td>
                                                    <td>
                                                        @if($contract['days_remaining'] <= $alertPeriod)
                                                            <span class="badge bg-danger">{{ $contract['days_remaining'] }} ngày</span>
                                                        @else
                                                            <span class="badge bg-success">{{ $contract['days_remaining'] }} ngày</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ showAmount($contract['amount']) }} VND</td>
                                                    <td>
                                                        <a href="{{ route('admin.report.invest.history') }}?search={{ $contract['invest_no'] }}" class="btn btn-sm btn-outline--primary">
                                                            <i class="las la-desktop"></i> Chi tiết
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info">Không có hợp đồng đáo hạn nào trong giai đoạn này.</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script-lib')
    <script src="{{ asset('assets/admin/js/vendor/apexcharts.min.js') }}"></script>
@endpush

@push('script')
<script>
    "use strict";
    // Handle custom date range display
    document.querySelector('select[name=date_range]').addEventListener('change', function() {
        let customDateRange = document.querySelector('.custom-date-range');
        if (this.value === 'custom') {
            customDateRange.style.display = 'flex';
        } else {
            customDateRange.style.display = 'none';
        }
    });
    // Chart setup
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('chart-loading').classList.remove('d-none');
        const monthlyData = @json($monthlyData);
        const months = [];
        const interestAlerts = [];
        const maturityAlerts = [];
        for (const [key, data] of Object.entries(monthlyData)) {
            months.push(data.month);
            interestAlerts.push(data.interest_alerts);
            maturityAlerts.push(data.maturity_alerts);
        }
        const options = {
            series: [{
                name: 'Thanh toán lãi',
                data: interestAlerts
            }, {
                name: 'Đáo hạn hợp đồng',
                data: maturityAlerts
            }],
            chart: {
                type: 'bar',
                height: 400,
                stacked: false,
                toolbar: {
                    show: true
                }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '55%',
                    endingShape: 'rounded'
                },
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            xaxis: {
                categories: months,
            },
            yaxis: {
                title: {
                    text: 'Số lượng hợp đồng'
                }
            },
            colors: ['#00e396', '#ff4560'],
            fill: {
                opacity: 1
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return val + " hợp đồng"
                    }
                }
            },
            legend: {
                show: true,
                position: 'top',
                labels: {
                    colors: '#333',
                    useSeriesColors: false
                }
            }
        };
        const chart = new ApexCharts(document.querySelector("#alertChart"), options);
        chart.render().then(() => {
            document.getElementById('chart-loading').classList.add('d-none');
        });
    });
</script>
@endpush

@push('breadcrumb-plugins')
    <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-outline--primary">
        <i class="las la-undo"></i> Quay lại trang tổng quan
    </a>
@endpush
