@extends('user.staff.layouts.app')

@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="mb-0">@lang('Báo cáo lãi suất')</h5>
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
                            <a class="dropdown-item" href="#">@lang('Đã thanh toán')</a>
                            <a class="dropdown-item" href="#">@lang('Chờ thanh toán')</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                @if(isset($interests) && count($interests))
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>@lang('STT')</th>
                                    <th>@lang('Mã hợp đồng')</th>
                                    <th>@lang('Dự án')</th>
                                    <th>@lang('Khách hàng')</th>
                                    <th>@lang('Số tiền đầu tư')</th>
                                    <th>@lang('Lãi suất')</th>
                                    <th>@lang('Tiền lãi')</th>
                                    <th>@lang('Ngày thanh toán')</th>
                                    <th>@lang('Trạng thái')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($interests as $key => $item)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td><span class="badge bg-primary">{{ $item->trx ?? '-' }}</span></td>
                                        <td>{{ Str::limit($item->project->name ?? '-', 20) }}</td>
                                        <td>{{ $item->user->fullname ?? '-' }}</td>
                                        <td>{{ showAmount($item->investment_amount) }} {{ $general->cur_text }}</td>
                                        <td>{{ $item->interest_rate ?? '-' }}%</td>
                                        <td>{{ showAmount($item->interest_amount) }} {{ $general->cur_text }}</td>
                                        <td>{{ showDateTime($item->interest_date) }}</td>
                                        <td>{{ $item->status ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if(method_exists($interests, 'links'))
                    <div class="mt-3 px-3 pb-3">
                        {{ $interests->links() }}
                    </div>
                    @endif
                @else
                    <div class="empty-state text-center py-5">
                        <img src="{{ asset('assets/images/empty.svg') }}" alt="Empty" class="mb-3" width="120">
                        <p class="text-muted">@lang('Không có dữ liệu lãi suất.')</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">@lang('Thống kê lãi suất')</h5>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm bg-primary-subtle">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="icon-box bg-primary me-3">
                                        <i class="las la-percentage fs-1 text-white"></i>
                                    </div>
                                    <div>
                                        <h6 class="text-muted mb-1 fs--1">@lang('Tổng tiền lãi')</h6>
                                        <h4 class="mb-0">{{ showAmount($totalInterestAmount) }} {{ $general->cur_text }}</h4>
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
                                        <h6 class="text-muted mb-1 fs--1">@lang('Đã thanh toán')</h6>
                                        <h4 class="mb-0">{{ showAmount($paidInterestAmount) }} {{ $general->cur_text }}</h4>
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
                                        <h6 class="text-muted mb-1 fs--1">@lang('Chờ thanh toán')</h6>
                                        <h4 class="mb-0">{{ showAmount($pendingInterestAmount) }} {{ $general->cur_text }}</h4>
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
                                        <h6 class="text-muted mb-1 fs--1">@lang('Lãi suất TB')</h6>
                                        <h4 class="mb-0">{{ $averageInterestRate ?? '-' }}%</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <h6 class="mb-3">@lang('Biểu đồ lãi suất theo tháng')</h6>
                    <div class="chart-container" style="height: 300px;">
                        <canvas id="interestChart"></canvas>
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
        // Biểu đồ lãi suất
        var ctx = document.getElementById('interestChart').getContext('2d');
        var chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'T8', 'T9', 'T10', 'T11', 'T12'],
                datasets: [
                    {
                        label: 'Tiền lãi',
                        data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                        backgroundColor: 'rgba(23, 162, 184, 0.2)',
                        borderColor: 'rgba(23, 162, 184, 1)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true
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
    });
</script>
@endpush 