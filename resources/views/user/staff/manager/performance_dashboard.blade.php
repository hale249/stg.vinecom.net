@extends('user.staff.layouts.app')

@section('panel')
<div class="row mb-4">
    <div class="col-lg-12">
        <div class="card mb-4">
            <div class="card-header d-flex flex-wrap gap-2 align-items-center justify-content-between">
                <h5 class="mb-0">@lang('Dashboard Hiệu suất làm việc')</h5>
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
                <canvas id="performanceChart" height="120"></canvas>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">@lang('Chi tiết hiệu suất theo tháng')</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead>
                            <tr>
                                <th>@lang('Nhân viên')</th>
                                <th>@lang('Tháng')</th>
                                <th>@lang('Số hợp đồng hoàn thành')</th>
                                <th>@lang('Tổng doanh số')</th>
                                <th>@lang('Tỉ lệ hoàn thành KPI')</th>
                                <th>@lang('Đánh giá')</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Nguyễn A</td>
                                <td>06/2025</td>
                                <td>7</td>
                                <td>2.200.000.000</td>
                                <td><span class="badge bg-success">110%</span></td>
                                <td><span class="badge bg-success"><i class="las la-trophy"></i> Xuất sắc</span></td>
                            </tr>
                            <tr>
                                <td>Trần B</td>
                                <td>06/2025</td>
                                <td>4</td>
                                <td>800.000.000</td>
                                <td><span class="badge bg-warning">80%</span></td>
                                <td><span class="badge bg-warning"><i class="las la-thumbs-up"></i> Đạt yêu cầu</span></td>
                            </tr>
                            <tr>
                                <td>Lê C</td>
                                <td>06/2025</td>
                                <td>2</td>
                                <td>400.000.000</td>
                                <td><span class="badge bg-danger">40%</span></td>
                                <td><span class="badge bg-danger"><i class="las la-thumbs-down"></i> Cần cải thiện</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="empty-state text-center py-5 d-none">
                    <img src="{{ asset('assets/images/empty.svg') }}" alt="Empty" class="mb-3" width="120">
                    <p class="text-muted">@lang('Không có dữ liệu hiệu suất cho bộ lọc này.')</p>
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
        const ctx = document.getElementById('performanceChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Nguyễn A', 'Trần B', 'Lê C'],
                datasets: [
                    {
                        label: 'Số hợp đồng hoàn thành',
                        backgroundColor: '#6366f1',
                        data: [7, 4, 2],
                    },
                    {
                        label: 'Tổng doanh số',
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