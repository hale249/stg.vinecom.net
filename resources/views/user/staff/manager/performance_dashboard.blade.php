@extends('user.staff.layouts.app')

@section('panel')
<div class="row mb-4">
    <div class="col-lg-12">
        <div class="card mb-4">
            <div class="card-header d-flex flex-wrap gap-2 align-items-center justify-content-between">
                <h5 class="mb-0">@lang('Dashboard Hiệu suất làm việc')</h5>
                <form action="" method="GET" class="d-flex flex-wrap gap-2 align-items-center">
                    <input type="month" name="month" class="form-control form-control-sm" value="{{ request('month', $month) }}">
                    <select name="user_id" class="form-select form-select-sm">
                        <option value="">@lang('Tất cả nhân viên')</option>
                        @foreach($staffMembers as $staff)
                            <option value="{{ $staff->id }}" @if(request('user_id') == $staff->id) selected @endif>{{ $staff->fullname ?? $staff->username }}</option>
                        @endforeach
                    </select>
                    <select name="project_id" class="form-select form-select-sm">
                        <option value="">@lang('Tất cả dự án')</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" @if(request('project_id') == $project->id) selected @endif>{{ $project->title }}</option>
                        @endforeach
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
                            @forelse($performanceData as $row)
                                <tr>
                                    <td>{{ $row['staff']->fullname ?? $row['staff']->username }}</td>
                                    <td>{{ \Carbon\Carbon::parse($month.'-01')->format('m/Y') }}</td>
                                    <td>{{ $row['contracts'] }}</td>
                                    <td>{{ number_format($row['sales'], 0, ',', '.') }}</td>
                                    <td><span class="badge {{ $row['kpi_percent'] >= 100 ? 'bg-success' : ($row['kpi_percent'] >= 80 ? 'bg-warning' : 'bg-danger') }}">{{ round($row['kpi_percent']) }}%</span></td>
                                    <td>
                                        @if($row['kpi_status'] == 'exceeded')
                                            <span class="badge bg-success"><i class="las la-trophy"></i> Xuất sắc</span>
                                        @elseif($row['kpi_status'] == 'achieved')
                                            <span class="badge bg-success"><i class="las la-thumbs-up"></i> Đạt KPI</span>
                                        @elseif($row['kpi_status'] == 'near_achieved')
                                            <span class="badge bg-warning"><i class="las la-thumbs-up"></i> Gần đạt</span>
                                        @else
                                            <span class="badge bg-danger"><i class="las la-thumbs-down"></i> Cần cải thiện</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center text-muted">@lang('Không có dữ liệu hiệu suất cho bộ lọc này.')</td></tr>
                            @endforelse
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
        const labels = @json(collect($performanceData)->pluck('staff')->map(function($s){return $s->fullname ?? $s->username;}));
        const contracts = @json(collect($performanceData)->pluck('contracts'));
        const sales = @json(collect($performanceData)->pluck('sales'));
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Số hợp đồng hoàn thành',
                        backgroundColor: '#6366f1',
                        data: contracts,
                    },
                    {
                        label: 'Tổng doanh số',
                        backgroundColor: '#22c55e',
                        data: sales,
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