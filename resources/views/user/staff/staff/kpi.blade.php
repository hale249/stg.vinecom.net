@extends('user.staff.layouts.staff_app')

@section('panel')
<div class="row mb-4">
    <div class="col-12 mb-4">
        <div class="d-flex align-items-center gap-3 mb-3">
            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;font-size:2rem;"><i class="las la-bullseye"></i></div>
            <div>
                <h3 class="mb-0 fw-bold">@lang('KPI & Chỉ số')</h3>
                <div class="text-muted small">@lang('Theo dõi hiệu suất làm việc và mục tiêu KPI của bạn mỗi tháng.')</div>
            </div>
        </div>
        <div class="row g-3">
            <div class="col-6 col-md-4">
                <div class="card shadow border-0 h-100 animate__animated animate__fadeInUp" style="min-height:110px;">
                    <div class="card-body text-center">
                        <div class="mb-2"><i class="las la-target la-2x text-primary"></i></div>
                        <div class="fw-semibold">@lang('Chỉ tiêu hợp đồng')</div>
                        <div class="fs-5 fw-bold text-primary">{{ $summary['total_target_contracts'] ?? 0 }}</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4">
                <div class="card shadow border-0 h-100 animate__animated animate__fadeInUp" style="min-height:110px;">
                    <div class="card-body text-center">
                        <div class="mb-2"><i class="las la-check-circle la-2x text-success"></i></div>
                        <div class="fw-semibold">@lang('Thực tế hợp đồng')</div>
                        <div class="fs-5 fw-bold text-success">{{ $summary['total_actual_contracts'] ?? 0 }}</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-4">
                <div class="card shadow border-0 h-100 animate__animated animate__fadeInUp" style="min-height:110px;">
                    <div class="card-body text-center">
                        <div class="mb-2"><i class="las la-chart-line la-2x text-warning"></i></div>
                        <div class="fw-semibold">@lang('KPI trung bình')</div>
                        <div class="fs-5 fw-bold text-warning">{{ number_format($summary['avg_overall_kpi'] ?? 0, 1) }}%</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- KPI Policy Documents Section -->
    @if(isset($kpiDocuments) && $kpiDocuments->count() > 0)
    <div class="col-12 mb-4">
        <div class="card shadow border-0 animate__animated animate__fadeInUp">
            <div class="card-header bg-white border-bottom-0 d-flex flex-wrap gap-2 align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <i class="las la-file-alt text-primary"></i>
                    <h5 class="mb-0">@lang('Tài liệu chính sách KPI')</h5>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @foreach($kpiDocuments as $document)
                    <div class="col-md-6 col-lg-4">
                        <div class="card border h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    @if($document->isPDF())
                                        <i class="far fa-file-pdf text-danger fa-2x me-3"></i>
                                    @elseif($document->isImage())
                                        <i class="far fa-file-image text-primary fa-2x me-3"></i>
                                    @else
                                        <i class="far fa-file-alt text-info fa-2x me-3"></i>
                                    @endif
                                    <div>
                                        <h6 class="mb-1">{{ $document->title }}</h6>
                                        <div class="text-muted small">{{ $document->file_size_formatted }}</div>
                                    </div>
                                </div>
                                @if($document->description)
                                <p class="text-muted small mb-3">{{ Str::limit($document->description, 80) }}</p>
                                @endif
                                <div class="d-flex justify-content-between align-items-center mt-auto">
                                    <a href="{{ route('user.staff.staff.documents.download', $document->id) }}" class="btn btn-sm btn-primary">
                                        <i class="las la-download"></i> @lang('Tải xuống')
                                    </a>
                                    @if($document->isViewable())
                                    <a href="{{ route('user.staff.staff.documents.view', $document->id) }}" class="btn btn-sm btn-outline-secondary" target="_blank">
                                        <i class="las la-eye"></i> @lang('Xem')
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif
    
    <div class="col-12">
        <div class="card shadow border-0 animate__animated animate__fadeInUp">
            <div class="card-header bg-white border-bottom-0 d-flex flex-wrap gap-2 align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <i class="las la-chart-bar text-success"></i>
                    <h5 class="mb-0">@lang('KPI cá nhân')</h5>
                </div>
                <form action="" method="GET" class="d-flex flex-wrap gap-2 align-items-center">
                    <input type="month" name="month" class="form-control form-control-sm" value="{{ $month }}">
                    <button class="btn btn-sm btn-outline-success" type="submit"><i class="las la-filter"></i> @lang('Lọc')</button>
                </form>
            </div>
            <div class="card-body p-0">
                @if($kpis->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="border-radius:12px;overflow:hidden;">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center">@lang('Tháng')</th>
                                    <th class="text-center">@lang('Chỉ tiêu hợp đồng')</th>
                                    <th class="text-center">@lang('Thực tế hợp đồng')</th>
                                    <th class="text-center">@lang('Chỉ tiêu doanh số')</th>
                                    <th class="text-center">@lang('Thực tế doanh số')</th>
                                    <th class="text-center">@lang('KPI (%)')</th>
                                    <th class="text-center">@lang('Trạng thái')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($kpis as $kpi)
                                    <tr>
                                        <td class="text-center">{{ \Carbon\Carbon::createFromFormat('Y-m', $kpi->month_year)->format('m/Y') }}</td>
                                        <td class="text-center">{{ $kpi->target_contracts }}</td>
                                        <td class="text-center">{{ $kpi->actual_contracts }}</td>
                                        <td class="text-center">{{ showAmount($kpi->target_sales) }}</td>
                                        <td class="text-center">{{ showAmount($kpi->actual_sales) }}</td>
                                        <td class="text-center">
                                            @if($kpi->overall_kpi_percentage >= 120)
                                                <span class="badge bg-success-soft text-success">{{ number_format($kpi->overall_kpi_percentage, 1) }}%</span>
                                            @elseif($kpi->overall_kpi_percentage >= 100)
                                                <span class="badge bg-primary-soft text-primary">{{ number_format($kpi->overall_kpi_percentage, 1) }}%</span>
                                            @elseif($kpi->overall_kpi_percentage >= 80)
                                                <span class="badge bg-warning-soft text-warning">{{ number_format($kpi->overall_kpi_percentage, 1) }}%</span>
                                            @else
                                                <span class="badge bg-danger-soft text-danger">{{ number_format($kpi->overall_kpi_percentage, 1) }}%</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($kpi->kpi_status == 'exceeded')
                                                <span class="badge bg-success"><i class="las la-check-circle"></i> Vượt KPI</span>
                                            @elseif($kpi->kpi_status == 'achieved')
                                                <span class="badge bg-primary"><i class="las la-check-circle"></i> Đạt KPI</span>
                                            @elseif($kpi->kpi_status == 'near_achieved')
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
                        {{ $kpis->links() }}
                    </div>
                @else
                    <div class="empty-state text-center py-5 animate__animated animate__fadeIn">
                        <img src="{{ asset('assets/images/empty.svg') }}" alt="Empty" class="mb-3" width="120">
                        <p class="text-muted mb-3">@lang('Không có dữ liệu KPI cho bộ lọc này.')</p>
                        <a href="#" class="btn btn-outline-success"><i class="las la-plus"></i> @lang('Yêu cầu tạo KPI')</a>
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