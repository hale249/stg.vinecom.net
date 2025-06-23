@extends('user.staff.layouts.app')

@section('panel')
<div class="kpi-dashboard-modern">
    <!-- Header Section -->
    <div class="dashboard-header mb-4">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="header-content">
                    <h1 class="page-title mb-2">
                        <i class="las la-bullseye text-primary me-2"></i>
                        Quản lý KPI & Chỉ số
                    </h1>
                    <p class="text-muted mb-0">Theo dõi và đánh giá hiệu suất làm việc của nhân viên</p>
                </div>
            </div>
            <div class="col-lg-4 text-lg-end">
                <button class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#addKpiModal">
                    <i class="las la-plus me-2"></i>
                    Thêm KPI
                </button>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-section mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form action="" method="GET" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Tháng/Năm</label>
                        <input type="month" name="month" class="form-control" value="{{ request('month', now()->format('Y-m')) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Nhân viên</label>
                        <select name="user_id" class="form-select">
                            <option value="">Tất cả nhân viên</option>
                            @foreach($staffMembers as $staff)
                                <option value="{{ $staff->id }}" {{ request('user_id') == $staff->id ? 'selected' : '' }}>
                                    {{ $staff->fullname }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Trạng thái KPI</label>
                        <select name="kpi_status" class="form-select">
                            <option value="">Tất cả trạng thái</option>
                            <option value="exceeded" {{ request('kpi_status') == 'exceeded' ? 'selected' : '' }}>Vượt KPI</option>
                            <option value="achieved" {{ request('kpi_status') == 'achieved' ? 'selected' : '' }}>Đạt KPI</option>
                            <option value="near_achieved" {{ request('kpi_status') == 'near_achieved' ? 'selected' : '' }}>Gần đạt</option>
                            <option value="not_achieved" {{ request('kpi_status') == 'not_achieved' ? 'selected' : '' }}>Không đạt</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-primary w-100" type="submit">
                            <i class="las la-filter me-2"></i>
                            Lọc dữ liệu
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="summary-cards mb-4">
        <div class="row g-3">
            <div class="col-lg-3 col-md-6">
                <div class="summary-card bg-gradient-primary">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="icon-wrapper">
                                <i class="las la-chart-line"></i>
                            </div>
                            <div class="content">
                                <h3 class="mb-1 text-white">{{ number_format($summary['avg_overall_kpi'], 1) }}%</h3>
                                <p class="mb-0 text-white-50">KPI Trung bình</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="summary-card bg-gradient-success">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="icon-wrapper">
                                <i class="las la-trophy"></i>
                            </div>
                            <div class="content">
                                <h3 class="mb-1 text-white">{{ $summary['exceeded_kpi_count'] + $summary['achieved_kpi_count'] }}</h3>
                                <p class="mb-0 text-white-50">Đạt & Vượt KPI</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="summary-card bg-gradient-warning">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="icon-wrapper">
                                <i class="las la-hand-holding-usd"></i>
                            </div>
                            <div class="content">
                                <h3 class="mb-1 text-white">{{ showAmount($summary['total_actual_sales']) }}</h3>
                                <p class="mb-0 text-white-50">Doanh số thực tế</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="summary-card bg-gradient-info">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="icon-wrapper">
                                <i class="las la-file-contract"></i>
                            </div>
                            <div class="content">
                                <h3 class="mb-1 text-white">{{ $summary['total_actual_contracts'] }}</h3>
                                <p class="mb-0 text-white-50">Hợp đồng thực tế</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="charts-section mb-4">
        <div class="row g-3">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-transparent border-0">
                        <h5 class="mb-0">
                            <i class="las la-chart-bar me-2 text-primary"></i>
                            Biểu đồ KPI theo tháng
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="kpiLineChart" height="300"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-transparent border-0">
                        <h5 class="mb-0">
                            <i class="las la-chart-pie me-2 text-primary"></i>
                            Phân bố trạng thái KPI
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="kpiPieChart" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- KPI Table Section -->
    <div class="kpi-table-section">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="las la-table me-2 text-primary"></i>
                    Chi tiết KPI theo tháng
                </h5>
                <div class="header-actions">
                    <button class="btn btn-outline-primary btn-sm me-2">
                        <i class="las la-download me-1"></i>
                        Xuất Excel
                    </button>
                    <button class="btn btn-outline-success btn-sm">
                        <i class="las la-print me-1"></i>
                        In báo cáo
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                @if($kpis->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="border-0">Nhân viên</th>
                                    <th class="border-0 text-center">Tháng</th>
                                    <th class="border-0 text-center">Chỉ tiêu HĐ</th>
                                    <th class="border-0 text-center">Thực tế HĐ</th>
                                    <th class="border-0 text-center">Chỉ tiêu DS</th>
                                    <th class="border-0 text-center">Thực tế DS</th>
                                    <th class="border-0 text-center">KPI (%)</th>
                                    <th class="border-0 text-center">Trạng thái</th>
                                    <th class="border-0 text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($kpis as $kpi)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm me-3">
                                                    <img src="{{ getImage(getFilePath('userProfile') . '/' . $kpi->staff->image, getFileSize('userProfile'), avatar: true) }}" 
                                                         alt="{{ $kpi->staff->fullname }}" class="rounded-circle">
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $kpi->staff->fullname }}</h6>
                                                    <small class="text-muted">{{ $kpi->staff->username }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-light text-dark">
                                                {{ \Carbon\Carbon::createFromFormat('Y-m', $kpi->month_year)->format('m/Y') }}
                                            </span>
                                        </td>
                                        <td class="text-center">{{ $kpi->target_contracts }}</td>
                                        <td class="text-center">
                                            <span class="fw-semibold">{{ $kpi->actual_contracts }}</span>
                                        </td>
                                        <td class="text-center">{{ showAmount($kpi->target_sales) }}</td>
                                        <td class="text-center">
                                            <span class="fw-semibold">{{ showAmount($kpi->actual_sales) }}</span>
                                        </td>
                                        <td class="text-center">
                                            @if($kpi->overall_kpi_percentage >= 120)
                                                <span class="badge bg-success-subtle text-success fw-semibold">
                                                    {{ number_format($kpi->overall_kpi_percentage, 1) }}%
                                                </span>
                                            @elseif($kpi->overall_kpi_percentage >= 100)
                                                <span class="badge bg-primary-subtle text-primary fw-semibold">
                                                    {{ number_format($kpi->overall_kpi_percentage, 1) }}%
                                                </span>
                                            @elseif($kpi->overall_kpi_percentage >= 80)
                                                <span class="badge bg-warning-subtle text-warning fw-semibold">
                                                    {{ number_format($kpi->overall_kpi_percentage, 1) }}%
                                                </span>
                                            @else
                                                <span class="badge bg-danger-subtle text-danger fw-semibold">
                                                    {{ number_format($kpi->overall_kpi_percentage, 1) }}%
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($kpi->kpi_status == 'exceeded')
                                                <span class="badge bg-success">
                                                    <i class="las la-trophy me-1"></i>Vượt KPI
                                                </span>
                                            @elseif($kpi->kpi_status == 'achieved')
                                                <span class="badge bg-primary">
                                                    <i class="las la-check-circle me-1"></i>Đạt KPI
                                                </span>
                                            @elseif($kpi->kpi_status == 'near_achieved')
                                                <span class="badge bg-warning">
                                                    <i class="las la-exclamation-triangle me-1"></i>Gần đạt
                                                </span>
                                            @else
                                                <span class="badge bg-danger">
                                                    <i class="las la-times-circle me-1"></i>Không đạt
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                <button class="btn btn-sm btn-outline-primary" title="Xem chi tiết">
                                                    <i class="las la-eye"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-warning" title="Chỉnh sửa">
                                                    <i class="las la-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger" title="Xóa">
                                                    <i class="las la-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-state text-center py-5">
                        <div class="empty-icon mb-3">
                            <i class="las la-chart-line la-3x text-muted"></i>
                        </div>
                        <h5 class="text-muted">Không có dữ liệu KPI</h5>
                        <p class="text-muted">Chưa có dữ liệu KPI cho bộ lọc này. Hãy thử thay đổi bộ lọc hoặc thêm KPI mới.</p>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addKpiModal">
                            <i class="las la-plus me-2"></i>
                            Thêm KPI đầu tiên
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Pagination -->
    @if($kpis->hasPages())
        <div class="pagination-wrapper mt-4">
            {{ $kpis->links() }}
        </div>
    @endif
</div>

<!-- Add KPI Modal -->
<div class="modal fade" id="addKpiModal" tabindex="-1" aria-labelledby="addKpiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addKpiModalLabel">
                    <i class="las la-plus-circle me-2 text-primary"></i>
                    Thêm KPI mới
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('user.staff.manager.hr.kpi.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nhân viên <span class="text-danger">*</span></label>
                            <select name="staff_id" class="form-select" required>
                                <option value="">Chọn nhân viên</option>
                                @foreach($staffMembers as $staff)
                                    <option value="{{ $staff->id }}">{{ $staff->fullname }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tháng/Năm <span class="text-danger">*</span></label>
                            <input type="month" name="month_year" class="form-control" value="{{ now()->format('Y-m') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Chỉ tiêu hợp đồng</label>
                            <input type="number" name="target_contracts" class="form-control" placeholder="Nhập chỉ tiêu hợp đồng">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Thực tế hợp đồng</label>
                            <input type="number" name="actual_contracts" class="form-control" placeholder="Nhập thực tế hợp đồng">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Chỉ tiêu doanh số (VNĐ)</label>
                            <input type="number" name="target_sales" class="form-control" placeholder="Nhập chỉ tiêu doanh số">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Thực tế doanh số (VNĐ)</label>
                            <input type="number" name="actual_sales" class="form-control" placeholder="Nhập thực tế doanh số">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Ghi chú</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="Nhập ghi chú (nếu có)"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="las la-save me-2"></i>
                        Lưu KPI
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('style')
<style>
.kpi-dashboard-modern {
    background: #f8f9fa;
    min-height: 100vh;
    padding: 20px 0;
}

.dashboard-header {
    background: white;
    padding: 30px;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
}

.page-title {
    font-size: 2rem;
    font-weight: 700;
    color: #2c3e50;
    margin: 0;
}

.summary-cards .summary-card {
    border-radius: 16px;
    border: none;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    height: 100%;
    min-height: 120px;
}

.summary-cards .summary-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.12);
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.bg-gradient-success {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
}

.bg-gradient-warning {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
}

.bg-gradient-info {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    color: white;
}

.summary-card .card-body {
    padding: 1.5rem;
    height: 100%;
    display: flex;
    align-items: center;
}

.summary-card .icon-wrapper {
    width: 60px;
    height: 60px;
    background: rgba(255,255,255,0.2);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin-right: 15px;
    flex-shrink: 0;
}

.summary-card .content {
    flex: 1;
    min-width: 0;
}

.summary-card .content h3 {
    font-size: 1.8rem;
    font-weight: 700;
    margin: 0;
    line-height: 1.2;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.summary-card .content p {
    margin: 0;
    font-size: 0.9rem;
    font-weight: 500;
    line-height: 1.4;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.text-white-50 {
    color: rgba(255, 255, 255, 0.7) !important;
}

.filter-section .card {
    border-radius: 16px;
    border: none;
}

.charts-section .card {
    border-radius: 16px;
    border: none;
}

.kpi-table-section .card {
    border-radius: 16px;
    border: none;
}

.table {
    margin: 0;
}

.table th {
    font-weight: 600;
    color: #495057;
    background: #f8f9fa;
    border: none;
    padding: 15px 12px;
}

.table td {
    padding: 15px 12px;
    border: none;
    border-bottom: 1px solid #f1f3f4;
    vertical-align: middle;
}

.table tbody tr:hover {
    background: #f8f9fa;
}

.avatar-sm {
    width: 40px;
    height: 40px;
}

.avatar-sm img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.badge {
    font-size: 0.75rem;
    padding: 0.5em 0.75em;
}

.btn-group .btn {
    border-radius: 6px;
    margin: 0 2px;
}

.empty-state {
    padding: 60px 20px;
}

.empty-icon {
    color: #6c757d;
}

.header-actions .btn {
    border-radius: 8px;
    font-weight: 500;
}

.modal-content {
    border-radius: 16px;
    border: none;
    box-shadow: 0 20px 60px rgba(0,0,0,0.15);
}

.modal-header {
    border-bottom: 1px solid #e9ecef;
    padding: 20px 25px;
}

.modal-body {
    padding: 25px;
}

.modal-footer {
    border-top: 1px solid #e9ecef;
    padding: 20px 25px;
}

.form-control, .form-select {
    border-radius: 8px;
    border: 1px solid #e9ecef;
    padding: 10px 15px;
}

.form-control:focus, .form-select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.btn {
    border-radius: 8px;
    font-weight: 500;
    padding: 10px 20px;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
    transform: translateY(-1px);
}

.pagination-wrapper {
    display: flex;
    justify-content: center;
}

@media (max-width: 768px) {
    .dashboard-header {
        padding: 20px;
    }
    
    .page-title {
        font-size: 1.5rem;
    }
    
    .summary-card .content h3 {
        font-size: 1.4rem;
    }
    
    .table-responsive {
        font-size: 0.9rem;
    }
}
</style>
@endpush

@push('script-lib')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@push('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Line chart (KPI theo tháng)
    const ctxLine = document.getElementById('kpiLineChart').getContext('2d');
    new Chart(ctxLine, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartData->keys()) !!},
            datasets: [
                {
                    label: 'Chỉ tiêu doanh số',
                    data: {!! json_encode($chartData->pluck('target_sales')) !!},
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointRadius: 6,
                    pointBackgroundColor: '#667eea',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                },
                {
                    label: 'Thực tế doanh số',
                    data: {!! json_encode($chartData->pluck('actual_sales')) !!},
                    borderColor: '#11998e',
                    backgroundColor: 'rgba(17, 153, 142, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointRadius: 6,
                    pointBackgroundColor: '#11998e',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { 
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 20,
                        font: {
                            size: 12,
                            weight: '600'
                        }
                    }
                },
                title: { display: false }
            },
            scales: {
                y: { 
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0,0,0,0.05)'
                    }
                },
                x: {
                    grid: {
                        color: 'rgba(0,0,0,0.05)'
                    }
                }
            },
            elements: {
                line: {
                    borderWidth: 3
                }
            }
        }
    });

    // Pie chart (Phân bố trạng thái KPI)
    const ctxPie = document.getElementById('kpiPieChart').getContext('2d');
    new Chart(ctxPie, {
        type: 'doughnut',
        data: {
            labels: ['Vượt KPI', 'Đạt KPI', 'Gần đạt', 'Không đạt'],
            datasets: [{
                data: [
                    {{ $summary['exceeded_kpi_count'] }},
                    {{ $summary['achieved_kpi_count'] }},
                    {{ $summary['near_achieved_count'] }},
                    {{ $summary['not_achieved_count'] }}
                ],
                backgroundColor: [
                    '#11998e',
                    '#667eea',
                    '#f093fb',
                    '#f5576c'
                ],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { 
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 15,
                        font: {
                            size: 11,
                            weight: '500'
                        }
                    }
                }
            },
            cutout: '60%'
        }
    });
});
</script>
@endpush 