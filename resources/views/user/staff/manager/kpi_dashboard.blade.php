@extends('user.staff.layouts.app')

@section('panel')
<div class="row mb-4">
    <div class="col-lg-12">
        <div class="card mb-4">
            <div class="card-header d-flex flex-wrap gap-2 align-items-center justify-content-between">
                <h5 class="mb-0">@lang('Dashboard KPI theo tháng')</h5>
                <form action="" method="GET" class="d-flex flex-wrap gap-2 align-items-center">
                    <input type="month" name="month" class="form-control form-control-sm" value="{{ request('month', '2025-06') }}">
                    <select name="user_id" class="form-select form-select-sm">
                        <option value="">@lang('Tất cả nhân viên')</option>
                        <option value="1">Nguyễn A</option>
                        <option value="2">Trần B</option>
                    </select>
                    <select name="project_id" class="form-select form-select-sm">
                        <option value="">@lang('Tất cả dự án')</option>
                        <option value="1">Dự án Alpha</option>
                        <option value="2">Dự án Beta</option>
                    </select>
                    <button class="btn btn-sm btn-primary" type="submit"><i class="las la-filter"></i> @lang('Lọc')</button>
                </form>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4 mb-4 mb-lg-0">
                        <canvas id="kpiChart" height="120"></canvas>
                        <hr class="my-4">
                        <h6 class="mb-3">@lang('Biểu đồ KPI theo tháng')</h6>
                        <canvas id="kpiLineChart" height="120"></canvas>
                    </div>
                    <div class="col-lg-4 mb-4 mb-lg-0">
                        <h6 class="mb-3">@lang('Tỉ lệ hoàn thành KPI')</h6>
                        <canvas id="kpiPieChart" height="260"></canvas>
                    </div>
                    <div class="col-lg-4 d-flex align-items-center justify-content-center">
                        <div>
                            <h6 class="mb-3">@lang('Tổng quan KPI')</h6>
                            <ul class="list-unstyled mb-0">
                                <li><span class="badge bg-success me-2">@lang('Vượt KPI')</span> 1</li>
                                <li><span class="badge bg-warning me-2">@lang('Gần đạt')</span> 1</li>
                                <li><span class="badge bg-danger me-2">@lang('Không đạt')</span> 1</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card h-100">
                            <div class="card-header">
                                <h6 class="mb-0">@lang('Chi tiết KPI theo tháng')</h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th>@lang('Nhân viên')</th>
                                                <th>@lang('Tháng')</th>
                                                <th>@lang('Chỉ tiêu hợp đồng')</th>
                                                <th>@lang('Thực tế hợp đồng')</th>
                                                <th>@lang('Chỉ tiêu doanh số')</th>
                                                <th>@lang('Thực tế doanh số')</th>
                                                <th>@lang('KPI (%)')</th>
                                                <th>@lang('Trạng thái')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Nguyễn A</td>
                                                <td>06/2025</td>
                                                <td>5</td>
                                                <td>7</td>
                                                <td>2.000.000.000</td>
                                                <td>2.200.000.000</td>
                                                <td><span class="badge bg-success">110%</span></td>
                                                <td><span class="badge bg-success"><i class="las la-check-circle"></i> Vượt KPI</span></td>
                                            </tr>
                                            <tr>
                                                <td>Trần B</td>
                                                <td>06/2025</td>
                                                <td>5</td>
                                                <td>4</td>
                                                <td>1.000.000.000</td>
                                                <td>800.000.000</td>
                                                <td><span class="badge bg-warning">80%</span></td>
                                                <td><span class="badge bg-warning"><i class="las la-exclamation-triangle"></i> Gần đạt</span></td>
                                            </tr>
                                            <tr>
                                                <td>Lê C</td>
                                                <td>06/2025</td>
                                                <td>5</td>
                                                <td>2</td>
                                                <td>1.000.000.000</td>
                                                <td>400.000.000</td>
                                                <td><span class="badge bg-danger">40%</span></td>
                                                <td><span class="badge bg-danger"><i class="las la-times-circle"></i> Không đạt</span></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="empty-state text-center py-5 d-none">
                                    <img src="{{ asset('assets/images/empty.svg') }}" alt="Empty" class="mb-3" width="120">
                                    <p class="text-muted">@lang('Không có dữ liệu KPI cho bộ lọc này.')</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script-lib')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush
@push('script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Bar chart (the original)
        const ctx = document.getElementById('kpiChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Nguyễn A', 'Trần B', 'Lê C'],
                datasets: [
                    {
                        label: 'Chỉ tiêu doanh số',
                        backgroundColor: '#6366f1',
                        data: [2000000000, 1000000000, 1000000000],
                    },
                    {
                        label: 'Thực tế doanh số',
                        backgroundColor: ['#22c55e', '#f59e42', '#ef4444'],
                        data: [2200000000, 800000000, 400000000],
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top' },
                    title: { display: false }
                },
                scales: {
                    x: { stacked: false },
                    y: { beginAtZero: true }
                }
            }
        });

        // Line chart (KPI theo tháng)
        const ctxLine = document.getElementById('kpiLineChart').getContext('2d');
        new Chart(ctxLine, {
            type: 'line',
            data: {
                labels: ['01/2025', '02/2025', '03/2025', '04/2025', '05/2025', '06/2025'],
                datasets: [
                    {
                        label: 'Chỉ tiêu doanh số',
                        data: [1000000000, 1200000000, 1300000000, 1400000000, 1500000000, 1600000000],
                        borderColor: '#6366f1',
                        backgroundColor: 'rgba(99,102,241,0.1)',
                        tension: 0.4,
                        fill: true,
                        pointRadius: 4,
                        pointBackgroundColor: '#6366f1',
                    },
                    {
                        label: 'Thực tế doanh số',
                        data: [1100000000, 1250000000, 1350000000, 1450000000, 1550000000, 1700000000],
                        borderColor: '#22c55e',
                        backgroundColor: 'rgba(34,197,94,0.1)',
                        tension: 0.4,
                        fill: true,
                        pointRadius: 4,
                        pointBackgroundColor: '#22c55e',
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top' },
                    title: { display: false }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });

        // Pie chart (Tỉ lệ hoàn thành KPI)
        const ctxPie = document.getElementById('kpiPieChart').getContext('2d');
        new Chart(ctxPie, {
            type: 'pie',
            data: {
                labels: ['Vượt KPI', 'Gần đạt', 'Không đạt'],
                datasets: [{
                    data: [1, 1, 1],
                    backgroundColor: ['#22c55e', '#f59e42', '#ef4444'],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' },
                    title: { display: false }
                }
            }
        });
    });
</script>
@endpush
@push('style')
<style>
    .badge.bg-success { background: #22c55e!important; }
    .badge.bg-warning { background: #f59e42!important; color: #fff; }
    .badge.bg-danger { background: #ef4444!important; }
</style>
@endpush 