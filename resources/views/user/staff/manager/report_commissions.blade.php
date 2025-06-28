@extends('user.staff.layouts.app')

@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="mb-0">@lang('Báo cáo hoa hồng')</h5>
                <div class="d-flex gap-2">
                    <form action="" method="GET" class="d-flex gap-2">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control form-control-sm" placeholder="Tìm kiếm...">
                            <button class="btn btn-sm btn-primary" type="submit"><i class="las la-search"></i></button>
                        </div>
                    </form>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="las la-filter"></i> @lang('Lọc')
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="#">@lang('Tất cả')</a>
                            <a class="dropdown-item" href="#">@lang('Đã nhận')</a>
                            <a class="dropdown-item" href="#">@lang('Chờ nhận')</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead>
                            <tr>
                                <th>@lang('STT')</th>
                                <th>@lang('Mã hợp đồng')</th>
                                <th>@lang('Dự án')</th>
                                <th>@lang('Nhân viên')</th>
                                <th>@lang('Khách hàng')</th>
                                <th>@lang('Số tiền đầu tư')</th>
                                <th>@lang('Tỉ lệ hoa hồng')</th>
                                <th>@lang('Tiền hoa hồng')</th>
                                <th>@lang('Ngày nhận')</th>
                                <th>@lang('Trạng thái')</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="10" class="text-center">@lang('Đang phát triển...')</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">@lang('Thống kê hoa hồng')</h5>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm bg-primary-subtle">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="icon-box bg-primary me-3">
                                        <i class="las la-hand-holding-usd fs-1 text-white"></i>
                                    </div>
                                    <div>
                                        <h6 class="text-muted mb-1 fs--1">@lang('Tổng hoa hồng')</h6>
                                        <h4 class="mb-0">{{ showAmount($totalCommission) }} {{ $general->cur_text }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm bg-success-subtle">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="icon-box bg-success me-3">
                                        <i class="las la-check-circle fs-1 text-white"></i>
                                    </div>
                                    <div>
                                        <h6 class="text-muted mb-1 fs--1">@lang('Đã nhận')</h6>
                                        <h4 class="mb-0">{{ showAmount($receivedCommission) }} {{ $general->cur_text }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm bg-warning-subtle">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="icon-box bg-warning me-3">
                                        <i class="las la-clock fs-1 text-white"></i>
                                    </div>
                                    <div>
                                        <h6 class="text-muted mb-1 fs--1">@lang('Chờ nhận')</h6>
                                        <h4 class="mb-0">{{ showAmount($pendingCommission) }} {{ $general->cur_text }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm bg-info-subtle">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="icon-box bg-info me-3">
                                        <i class="las la-chart-line fs-1 text-white"></i>
                                    </div>
                                    <div>
                                        <h6 class="text-muted mb-1 fs--1">@lang('Tỉ lệ TB')</h6>
                                        <h4 class="mb-0">{{ $averageCommissionRate }}%</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-md-6">
                        <h6 class="mb-3">@lang('Biểu đồ hoa hồng theo tháng')</h6>
                        <div class="chart-container" style="height: 300px;">
                            <canvas id="commissionChart"></canvas>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="mb-3">@lang('Hoa hồng theo nhân viên')</h6>
                        <div class="chart-container" style="height: 300px;">
                            <canvas id="staffCommissionChart"></canvas>
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
    .icon-box {
        width: 50px;
        height: 50px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>
@endpush

@push('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Biểu đồ hoa hồng theo tháng
        var ctx = document.getElementById('commissionChart').getContext('2d');
        var chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'T8', 'T9', 'T10', 'T11', 'T12'],
                datasets: [
                    {
                        label: 'Hoa hồng',
                        data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                        backgroundColor: 'rgba(255, 193, 7, 0.5)',
                        borderColor: 'rgba(255, 193, 7, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        
        // Biểu đồ hoa hồng theo nhân viên
        var ctx2 = document.getElementById('staffCommissionChart').getContext('2d');
        var chart2 = new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: ['Nhân viên 1', 'Nhân viên 2', 'Nhân viên 3', 'Nhân viên 4'],
                datasets: [
                    {
                        data: [25, 25, 25, 25],
                        backgroundColor: [
                            'rgba(40, 167, 69, 0.7)',
                            'rgba(23, 162, 184, 0.7)',
                            'rgba(255, 193, 7, 0.7)',
                            'rgba(220, 53, 69, 0.7)'
                        ],
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    });
</script>
@endpush 