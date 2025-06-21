@extends('user.staff.layouts.staff_app')

@section('panel')
<div class="row mb-4">
    <div class="col-12 mb-4">
        <div class="d-flex align-items-center gap-3 mb-3">
            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;font-size:2rem;"><i class="las la-money-bill-wave"></i></div>
            <div>
                <h3 class="mb-0 fw-bold">@lang('Lương & Thu nhập')</h3>
                <div class="text-muted small">@lang('Theo dõi thu nhập, hoa hồng và thưởng của bạn mỗi tháng.')</div>
            </div>
        </div>
        <div class="row g-3">
            <div class="col-6 col-md-3">
                <div class="card shadow border-0 h-100 animate__animated animate__fadeInUp" style="min-height:110px;">
                    <div class="card-body text-center">
                        <div class="mb-2"><i class="las la-wallet la-2x text-primary"></i></div>
                        <div class="fw-semibold">@lang('Lương cứng')</div>
                        <div class="fs-5 fw-bold text-primary">{{ showAmount($summary['total_base_salary'] ?? 0) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card shadow border-0 h-100 animate__animated animate__fadeInUp" style="min-height:110px;">
                    <div class="card-body text-center">
                        <div class="mb-2"><i class="las la-coins la-2x text-success"></i></div>
                        <div class="fw-semibold">@lang('Hoa hồng')</div>
                        <div class="fs-5 fw-bold text-success">{{ showAmount($summary['total_commission'] ?? 0) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card shadow border-0 h-100 animate__animated animate__fadeInUp" style="min-height:110px;">
                    <div class="card-body text-center">
                        <div class="mb-2"><i class="las la-gift la-2x text-info"></i></div>
                        <div class="fw-semibold">@lang('Thưởng')</div>
                        <div class="fs-5 fw-bold text-info">{{ showAmount($summary['total_bonus'] ?? 0) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card shadow border-0 h-100 animate__animated animate__fadeInUp" style="min-height:110px;">
                    <div class="card-body text-center">
                        <div class="mb-2"><i class="las la-hand-holding-usd la-2x text-warning"></i></div>
                        <div class="fw-semibold">@lang('Tổng lương')</div>
                        <div class="fs-5 fw-bold text-warning">{{ showAmount($summary['total_salary'] ?? 0) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="card shadow border-0 animate__animated animate__fadeInUp">
            <div class="card-header bg-white border-bottom-0 d-flex flex-wrap gap-2 align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <i class="las la-calendar-alt text-primary"></i>
                    <h5 class="mb-0">@lang('Bảng lương cá nhân')</h5>
                </div>
                <form action="" method="GET" class="d-flex flex-wrap gap-2 align-items-center">
                    <input type="month" name="month" class="form-control form-control-sm" value="{{ $month }}">
                    <button class="btn btn-sm btn-outline-primary" type="submit"><i class="las la-filter"></i> @lang('Lọc')</button>
                </form>
            </div>
            <div class="card-body p-0">
                @if($salaries->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="border-radius:12px;overflow:hidden;">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center">@lang('Tháng')</th>
                                    <th class="text-center">@lang('Lương cứng')</th>
                                    <th class="text-center">@lang('Doanh số')</th>
                                    <th class="text-center">@lang('Hoa hồng')</th>
                                    <th class="text-center">@lang('Thưởng')</th>
                                    <th class="text-center">@lang('Khấu trừ')</th>
                                    <th class="text-center">@lang('Tổng lương')</th>
                                    <th class="text-center">@lang('KPI (%)')</th>
                                    <th class="text-center">@lang('Trạng thái')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($salaries as $salary)
                                    <tr>
                                        <td class="text-center">{{ \Carbon\Carbon::createFromFormat('Y-m', $salary->month_year)->format('m/Y') }}</td>
                                        <td class="text-center">{{ showAmount($salary->base_salary) }}</td>
                                        <td class="text-center">{{ showAmount($salary->sales_amount) }}</td>
                                        <td class="text-center">{{ showAmount($salary->commission_amount) }}</td>
                                        <td class="text-center">{{ showAmount($salary->bonus_amount) }}</td>
                                        <td class="text-center">{{ showAmount($salary->deduction_amount) }}</td>
                                        <td class="text-center fw-bold">{{ showAmount($salary->total_salary) }}</td>
                                        <td class="text-center">
                                            @if($salary->kpi_percentage >= 120)
                                                <span class="badge bg-success-soft text-success">{{ number_format($salary->kpi_percentage, 1) }}%</span>
                                            @elseif($salary->kpi_percentage >= 100)
                                                <span class="badge bg-primary-soft text-primary">{{ number_format($salary->kpi_percentage, 1) }}%</span>
                                            @elseif($salary->kpi_percentage >= 80)
                                                <span class="badge bg-warning-soft text-warning">{{ number_format($salary->kpi_percentage, 1) }}%</span>
                                            @else
                                                <span class="badge bg-danger-soft text-danger">{{ number_format($salary->kpi_percentage, 1) }}%</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($salary->kpi_status == 'exceeded')
                                                <span class="badge bg-success"><i class="las la-check-circle"></i> Vượt KPI</span>
                                            @elseif($salary->kpi_status == 'achieved')
                                                <span class="badge bg-primary"><i class="las la-check-circle"></i> Đạt KPI</span>
                                            @elseif($salary->kpi_status == 'near_achieved')
                                                <span class="badge bg-warning"><i class="las la-exclamation-triangle"></i> Gần đạt</span>
                                            @else
                                                <span class="badge bg-danger"><i class="las la-times-circle"></i> Không đạt</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 px-3 pb-3">
                        {{ $salaries->links() }}
                    </div>
                @else
                    <div class="empty-state text-center py-5 animate__animated animate__fadeIn">
                        <img src="{{ asset('assets/images/empty.svg') }}" alt="Empty" class="mb-3" width="120">
                        <p class="text-muted mb-3">@lang('Không có dữ liệu bảng lương cho bộ lọc này.')</p>
                        <a href="#" class="btn btn-outline-primary"><i class="las la-plus"></i> @lang('Yêu cầu tạo bảng lương')</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('style')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<style>
    .badge.bg-success-soft { background: #d1fae5!important; color: #16a34a!important; }
    .badge.bg-primary-soft { background: #dbeafe!important; color: #2563eb!important; }
    .badge.bg-warning-soft { background: #fef3c7!important; color: #d97706!important; }
    .badge.bg-danger-soft { background: #fee2e2!important; color: #dc2626!important; }
    .card { border-radius: 1rem; }
    .table { border-radius: 1rem; overflow: hidden; }
    .table thead th { vertical-align: middle; }
    .table td, .table th { vertical-align: middle; }
    .empty-state img { opacity: 0.7; }
    @media (max-width: 767px) {
        .card-body.text-center .fs-5 { font-size: 1.1rem!important; }
        .card-body.text-center .mb-2 { font-size: 1.3rem!important; }
    }
</style>
@endpush 