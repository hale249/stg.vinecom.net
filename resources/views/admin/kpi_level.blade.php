@extends('admin.layouts.app')

@section('panel')
<div class="container-fluid kpi-dashboard-modern">
    <div class="row mb-4">
        <div class="col-lg-8">
            <h1 class="page-title mb-2">
                <i class="las la-layer-group text-primary me-2"></i>
                Chính sách phòng kinh doanh
            </h1>
            <p class="text-muted mb-0">Quản lý chính sách lương, KPI và hoa hồng cho từng chức danh kinh doanh</p>
            
            <!-- Danh sách chính sách đã thêm -->
            <div class="mt-3">
                <span class="fw-semibold me-1">Chính sách đã thêm:</span>
                @if($kpis->count() > 0)
                    @foreach($kpis->take(5) as $kpi)
                        <span class="badge bg-primary me-2 mb-1">{{ $kpi->level_name ?? $kpi->id }}</span>
                    @endforeach
                    @if($kpis->count() > 5)
                        <span class="badge bg-secondary">+{{ $kpis->count() - 5 }}</span>
                    @endif
                @else
                    <span class="text-muted">Chưa có chính sách nào</span>
                @endif
            </div>
        </div>
        <div class="col-lg-4 text-lg-end">
            <a href="{{ route('admin.kpi.level.populate') }}" class="btn btn-success me-2">
                <i class="las la-file-import me-1"></i>
                Nhập dữ liệu mẫu
            </a>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addKpiModal">
                <i class="las la-plus-circle me-1"></i>
                Thêm chính sách mới
            </button>
        </div>
    </div>
    <!-- Filter Section -->
    <div class="filter-section mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h5 class="mb-3"><i class="las la-filter me-2 text-primary"></i>Bộ lọc dữ liệu</h5>
                <form action="" method="GET" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Tháng/Năm</label>
                        <input type="month" name="month" class="form-control" value="{{ request('month', now()->format('Y-m')) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Chức danh</label>
                        <select name="level_name" class="form-select">
                            <option value="">Tất cả chức danh</option>
                            @foreach($levels as $level)
                                <option value="{{ $level->id }}" {{ request('level_name') == $level->id ? 'selected' : '' }}>{{ $level->name }}</option>
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
                        <div class="d-flex gap-2">
                            <button class="btn btn-primary flex-grow-1" type="submit">
                                <i class="las la-filter me-2"></i>
                                Lọc dữ liệu
                            </button>
                            <a href="{{ route('admin.kpi.level.index') }}" class="btn btn-outline-secondary">
                                <i class="las la-redo-alt"></i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- KPI Policies Table Section -->
    <div class="policies-table-section mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0">
                <h5 class="mb-0">
                    <i class="las la-list me-2 text-primary"></i>
                    Chính sách đã thêm
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 50px;">STT</th>
                                <th>Chức danh</th>
                                <th>KPI</th>
                                <th>Tháng thứ 1 (50%)</th>
                                <th>Tháng thứ 2 (70%)</th>
                                <th>KPI tuyển dụng</th>
                                <th class="bhxh-field">Lương BHXH <i class="las la-info-circle" data-bs-toggle="tooltip" title="Không tính vào tổng lương"></i></th>
                                <th>Lương CB</th>
                                <th>Lương KD</th>
                                <th>Tổng lương <small class="text-muted">(không gồm BHXH)</small></th>
                                <th>Thưởng KD</th>
                                <th>HH quản lý</th>
                                <th>Tổng thu nhập <small class="text-muted">(không gồm BHXH)</small></th>
                                <th>Note</th>
                                <th class="text-center" style="width: 100px;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($kpis->count() > 0)
                                @foreach($kpis as $kpi)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>
                                        @if(isset($kpi->level))
                                            <span class="fw-semibold">{{ $kpi->level->name }}</span>
                                        @else
                                            <span class="fw-semibold">{{ $kpi->level_name ?? 'N/A' }}</span>
                                        @endif
                                        @if(isset($kpi->role_level))
                                            @php
                                                $roleLevelName = [
                                                    'staff_level' => 'Cấp nhân viên',
                                                    'mid_manager_level' => 'Cấp quản lý trung gian',
                                                    'senior_manager_level' => 'Cấp quản lý cấp cao',
                                                    'regional_director_level' => 'Cấp lãnh đạo vùng'
                                                ][$kpi->role_level] ?? 'Chưa phân loại';

                                                $roleLevelClass = [
                                                    'staff_level' => 'info',
                                                    'mid_manager_level' => 'primary',
                                                    'senior_manager_level' => 'warning',
                                                    'regional_director_level' => 'success'
                                                ][$kpi->role_level] ?? 'secondary';
                                            @endphp
                                            <br>
                                            <small class="badge bg-{{ $roleLevelClass }}">{{ $roleLevelName }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="fw-semibold text-primary">{{ number_format($kpi->kpi_default ?? 0, 0, ',', '.') }} ₫</span>
                                            <small class="text-muted">T1: {{ number_format($kpi->kpi_month_1 ?? 0, 0, ',', '.') }} ₫</small>
                                            <small class="text-muted">T2: {{ number_format($kpi->kpi_month_2 ?? 0, 0, ',', '.') }} ₫</small>
                                        </div>
                                    </td>
                                    <td>{{ number_format($kpi->kpi_month_1 ?? 0, 0, ',', '.') }} ₫</td>
                                    <td>{{ number_format($kpi->kpi_month_2 ?? 0, 0, ',', '.') }} ₫</td>
                                    <td>{{ $kpi->kpi_tuyen_dung ?? 'N/A' }}</td>
                                    <td class="text-danger fw-semibold">{{ number_format($kpi->luong_bhxh ?? 0, 0, ',', '.') }} ₫</td>
                                    <td>{{ number_format($kpi->luong_co_ban ?? 0, 0, ',', '.') }} ₫</td>
                                    <td>{{ number_format($kpi->luong_kinh_doanh ?? 0, 0, ',', '.') }} ₫</td>
                                    <td>{{ number_format(($kpi->luong_co_ban ?? 0) + ($kpi->luong_kinh_doanh ?? 0), 0, ',', '.') }} ₫</td>
                                    <td>{{ number_format($kpi->thuong_kinh_doanh ?? 0, 0, ',', '.') }} ₫</td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span>{{ number_format($kpi->hh_quan_ly ?? 0, 0, ',', '.') }} ₫</span>
                                            <small class="text-muted">{{ number_format($kpi->hh_quan_ly_percent ?? 0, 2) }}%</small>
                                        </div>
                                    </td>
                                    <td>{{ number_format(($kpi->luong_co_ban ?? 0) + ($kpi->luong_kinh_doanh ?? 0) + ($kpi->thuong_kinh_doanh ?? 0) + ($kpi->hh_quan_ly ?? 0), 0, ',', '.') }} ₫</td>
                                    <td>{{ $kpi->notes ?? '' }}</td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center">
                                            <button type="button" class="btn btn-sm btn-primary me-1 btn-edit-kpi" data-bs-toggle="modal" data-bs-target="#editKpiModal" data-id="{{ $kpi->id }}">
                                                <i class="las la-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger" data-id="{{ $kpi->id }}">
                                                <i class="las la-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="15" class="text-center py-3">Chưa có chính sách nào được thêm</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- KPI Charts Section -->
    <div class="kpi-charts-section mb-4">
        <div class="row g-3">
            <!-- Monthly KPI Chart -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="las la-chart-bar me-2 text-primary"></i>
                            Biểu đồ KPI theo tháng
                        </h5>
                        <div class="chart-actions">
                            <select class="form-select form-select-sm" id="kpiMonthlyChartYear">
                                <option value="2024" selected>2024</option>
                                <option value="2023">2023</option>
                            </select>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart-container" style="height: 300px;">
                            <canvas id="kpiBarChart" style="display: block; width: 100%; height: 300px; border: 1px solid #f0f0f0;"></canvas>
                        </div>
                        <div class="text-center mt-3">
                            <small class="text-muted">Dữ liệu được lấy từ chi tiết KPI theo tháng</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- KPI Status Distribution Chart -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-transparent">
                        <h5 class="mb-0">
                            <i class="las la-chart-pie me-2 text-primary"></i>
                            Phân bố trạng thái KPI
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container" style="height: 300px;">
                            <canvas id="kpiPieChart" style="display: block; width: 100%; height: 300px; border: 1px solid #f0f0f0;"></canvas>
                        </div>
                        <div class="text-center mt-3">
                            <small class="text-muted">Dữ liệu được lấy từ chi tiết KPI theo tháng</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly KPI Details Section -->
    <div class="kpi-details-section mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="las la-calendar-check me-2 text-primary"></i>
                    Chi tiết KPI theo tháng
                </h5>
                <div class="d-flex gap-2">
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" id="exportOptionsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="las la-download me-1"></i> Xuất dữ liệu
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="exportOptionsDropdown">
                            <li><a class="dropdown-item" href="#" onclick="exportToExcel()"><i class="las la-file-excel me-2 text-success"></i>Xuất Excel</a></li>
                            <li><a class="dropdown-item" href="#" onclick="printReport()"><i class="las la-print me-2 text-primary"></i>In báo cáo</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 50px;">STT</th>
                                <th>Thời gian</th>
                                <th>Nhân viên</th>
                                <th>Chức danh</th>
                                <th>Chỉ tiêu HĐ</th>
                                <th>Đạt được</th>
                                <th>% HĐ</th>
                                <th>Chỉ tiêu DT</th>
                                <th>Đạt được</th>
                                <th>% DT</th>
                                <th>KPI (%)</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                // Tạo dữ liệu mẫu cho bảng và biểu đồ
                                $kpiMonths = [];
                                $kpiUsers = [
                                    ['name' => 'Nguyễn Văn A', 'position' => 'QLKD 1'],
                                    ['name' => 'Trần Thị B', 'position' => 'GĐKD 1'],
                                    ['name' => 'Lê Văn C', 'position' => 'QLKD 2'],
                                    ['name' => 'Phạm Thị D', 'position' => 'GDTT 1'],
                                    ['name' => 'Hoàng Văn E', 'position' => 'GĐ Vùng 1']
                                ];
                                
                                $kpiData = [];
                                $monthlyKpiData = [
                                    'T1/2024' => [],
                                    'T2/2024' => [],
                                    'T3/2024' => [],
                                    'T4/2024' => [],
                                    'T5/2024' => [],
                                    'T6/2024' => []
                                ];
                                
                                $kpiStatusCounts = [
                                    'exceeded' => 0,
                                    'achieved' => 0,
                                    'near_achieved' => 0,
                                    'not_achieved' => 0
                                ];
                                
                                // Tạo dữ liệu giả
                                $i = 1;
                                foreach (array_keys($monthlyKpiData) as $month) {
                                    foreach ($kpiUsers as $user) {
                                        $targetContracts = rand(3, 10);
                                        $actualContracts = rand(1, 15);
                                        $targetSales = rand(200, 800) * 1000000;
                                        $actualSales = rand(100, 1000) * 1000000;
                                        
                                        $contractPercentage = $targetContracts > 0 ? round(($actualContracts / $targetContracts) * 100, 1) : 0;
                                        $salesPercentage = $targetSales > 0 ? round(($actualSales / $targetSales) * 100, 1) : 0;
                                        $overallPercentage = round(($contractPercentage + $salesPercentage) / 2, 1);
                                        
                                        $status = 'not_achieved';
                                        if ($overallPercentage >= 120) {
                                            $status = 'exceeded';
                                        } elseif ($overallPercentage >= 100) {
                                            $status = 'achieved';
                                        } elseif ($overallPercentage >= 85) {
                                            $status = 'near_achieved';
                                        }
                                        
                                        $kpiStatusCounts[$status]++;
                                        $monthlyKpiData[$month][] = $overallPercentage;
                                        
                                        $kpiData[] = [
                                            'id' => $i++,
                                            'month' => $month,
                                            'user' => $user['name'],
                                            'position' => $user['position'],
                                            'targetContracts' => $targetContracts,
                                            'actualContracts' => $actualContracts,
                                            'contractPercentage' => $contractPercentage,
                                            'targetSales' => $targetSales,
                                            'actualSales' => $actualSales,
                                            'salesPercentage' => $salesPercentage,
                                            'overallPercentage' => $overallPercentage,
                                            'status' => $status
                                        ];
                                    }
                                }
                                
                                // Tính trung bình KPI theo tháng cho biểu đồ
                                $monthlyAverageKpi = [];
                                foreach ($monthlyKpiData as $month => $values) {
                                    if (count($values) > 0) {
                                        $monthlyAverageKpi[$month] = array_sum($values) / count($values);
                                    } else {
                                        $monthlyAverageKpi[$month] = 0;
                                    }
                                }
                            @endphp
                            
                            @foreach($kpiData as $item)
                                <tr>
                                    <td class="text-center">{{ $item['id'] }}</td>
                                    <td>{{ $item['month'] }}</td>
                                    <td>{{ $item['user'] }}</td>
                                    <td>{{ $item['position'] }}</td>
                                    <td class="text-end">{{ $item['targetContracts'] }}</td>
                                    <td class="text-end">{{ $item['actualContracts'] }}</td>
                                    <td class="text-end {{ $item['contractPercentage'] >= 100 ? 'text-success' : ($item['contractPercentage'] >= 85 ? 'text-warning' : 'text-danger') }} fw-bold">
                                        {{ $item['contractPercentage'] }}%
                                    </td>
                                    <td class="text-end">{{ number_format($item['targetSales'], 0, ',', '.') }} ₫</td>
                                    <td class="text-end">{{ number_format($item['actualSales'], 0, ',', '.') }} ₫</td>
                                    <td class="text-end {{ $item['salesPercentage'] >= 100 ? 'text-success' : ($item['salesPercentage'] >= 85 ? 'text-warning' : 'text-danger') }} fw-bold">
                                        {{ $item['salesPercentage'] }}%
                                    </td>
                                    <td class="text-end fw-bold 
                                        {{ $item['overallPercentage'] >= 120 ? 'text-success' : ($item['overallPercentage'] >= 100 ? 'text-primary' : ($item['overallPercentage'] >= 85 ? 'text-warning' : 'text-danger')) }}">
                                        {{ $item['overallPercentage'] }}%
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $statusConfig = [
                                                'exceeded' => ['text' => 'Vượt KPI', 'class' => 'success'],
                                                'achieved' => ['text' => 'Đạt KPI', 'class' => 'primary'],
                                                'near_achieved' => ['text' => 'Gần đạt', 'class' => 'warning'],
                                                'not_achieved' => ['text' => 'Không đạt', 'class' => 'danger']
                                            ];
                                            $status = $statusConfig[$item['status']];
                                        @endphp
                                        <span class="badge bg-{{ $status['class'] }}">{{ $status['text'] }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
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
                                <i class="las la-chart-bar"></i>
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
                                <h3 class="mb-1 text-white">{{ number_format($summary['total_actual_sales']) }}</h3>
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
                        <canvas id="kpiBarChart" height="300"></canvas>
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
                    <button class="btn btn-outline-primary btn-sm me-2" onclick="exportToExcel()">
                        <i class="las la-download me-1"></i>
                        Xuất Excel
                    </button>
                    <button class="btn btn-outline-success btn-sm" onclick="printReport()">
                        <i class="las la-print me-1"></i>
                        In báo cáo
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                @if($kpis->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Nhân viên</th>
                                    <th>Tháng</th>
                                    <th>Chỉ tiêu HĐ</th>
                                    <th>Thực tế HĐ</th>
                                    <th>Chỉ tiêu DS</th>
                                    <th>Thực tế DS</th>
                                    <th>KPI (%)</th>
                                    <th>Trạng thái</th>
                                    <th>Ghi chú</th>
                                    <th class="text-center" style="width: 100px;">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($kpis as $kpi)
                                <tr>
                                    <td>{{ $kpi->staff_name ?? 'N/A' }}</td>
                                    <td>{{ $kpi->month ?? now()->format('m/Y') }}</td>
                                    <td>{{ $kpi->target_contracts ?? 5 }}</td>
                                    <td>{{ $kpi->actual_contracts ?? 0 }}</td>
                                    <td>{{ number_format($kpi->target_sales ?? $kpi->kpi_default ?? 0, 0, ',', '.') }} ₫</td>
                                    <td>{{ number_format($kpi->actual_sales ?? 0, 0, ',', '.') }} ₫</td>
                                    <td>
                                        <div class="progress" style="height: 6px;">
                                            <div class="progress-bar bg-{{ $kpi->overall_kpi_percentage > 100 ? 'success' : ($kpi->overall_kpi_percentage >= 80 ? 'warning' : 'danger') }}" 
                                                 role="progressbar" 
                                                 style="width: {{ min(100, $kpi->overall_kpi_percentage ?? 0) }}%"></div>
                                        </div>
                                        <span class="mt-1 d-block small">{{ number_format($kpi->overall_kpi_percentage ?? 0, 1) }}%</span>
                                    </td>
                                    <td>
                                        @php
                                            $status = $kpi->kpi_status ?? 'pending';
                                            $statusClass = [
                                                'exceeded' => 'success',
                                                'achieved' => 'primary',
                                                'near_achieved' => 'warning',
                                                'not_achieved' => 'danger',
                                                'pending' => 'secondary'
                                            ][$status];
                                            $statusText = [
                                                'exceeded' => 'Vượt KPI',
                                                'achieved' => 'Đạt KPI',
                                                'near_achieved' => 'Gần đạt',
                                                'not_achieved' => 'Không đạt',
                                                'pending' => 'Chờ đánh giá'
                                            ][$status];
                                        @endphp
                                        <span class="badge bg-{{ $statusClass }}">{{ $statusText }}</span>
                                    </td>
                                    <td>{{ $kpi->notes ?? '' }}</td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center">
                                            <button type="button" class="btn btn-sm btn-primary me-1 btn-edit-kpi-detail" 
                                                    data-bs-toggle="modal" data-bs-target="#editKpiDetailModal" 
                                                    data-id="{{ $kpi->id }}">
                                                <i class="las la-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger" data-id="{{ $kpi->id }}">
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
                    <div class="alert alert-info m-3">
                        <i class="las la-info-circle me-2"></i>
                        Chưa có dữ liệu KPI nào. Vui lòng thêm KPI mới.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Add KPI Modal -->
<div class="modal fade" id="addKpiModal" tabindex="-1" aria-labelledby="addKpiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addKpiModalLabel">
                    <i class="las la-plus-circle me-2 text-primary"></i>
                    Chính sách phòng kinh doanh
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.kpi.level.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <!-- Thông tin chức danh -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Chức danh <span class="text-danger">*</span></label>
                            <input type="text" name="level_name" class="form-control" placeholder="VD: QLKD 1, GĐKD 2" required>
                            <small class="text-muted">Nhập tên chức danh (VD: QLKD 1, GĐKD 2)</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Cấp bậc <span class="text-danger">*</span></label>
                            <select name="role_level" class="form-select" required>
                                <option value="">-- Chọn cấp bậc --</option>
                                <option value="staff_level">Cấp nhân viên (QLKD 1 → 3)</option>
                                <option value="mid_manager_level">Cấp quản lý trung gian (GĐKD 1, 2)</option>
                                <option value="senior_manager_level">Cấp quản lý cấp cao (GDTT 1, 2)</option>
                                <option value="regional_director_level">Cấp lãnh đạo vùng (GĐ Vùng 1, 2)</option>
                            </select>
                            <small class="text-muted">Chọn cấp bậc phù hợp với chức danh</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">KPI tuyển dụng</label>
                            <input type="text" name="kpi_tuyen_dung" class="form-control" placeholder="VD: Cá nhân, CN + TĐ 05 NS">
                            <small class="text-muted">Loại KPI tuyển dụng áp dụng</small>
                        </div>
                        
                        <!-- Thông tin KPI -->
                        <div class="col-12">
                            <hr>
                            <h6 class="fw-bold mb-3">Thiết lập KPI</h6>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">KPI <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" name="kpi_default" class="form-control" placeholder="VD: 500.000.000" value="500.000.000" min="0" required>
                                <span class="input-group-text">VNĐ</span>
                            </div>
                            <small class="text-muted">KPI T1 (50%) và T2 (70%) sẽ tự động tính</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">KPI Tháng 1 (50%)</label>
                            <div class="input-group">
                                <input type="text" name="kpi_month_1" class="form-control" placeholder="VD: 250.000.000" value="250.000.000" min="0" required>
                                <span class="input-group-text">VNĐ</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">KPI Tháng 2 (70%)</label>
                            <div class="input-group">
                                <input type="text" name="kpi_month_2" class="form-control" placeholder="VD: 350.000.000" value="350.000.000" min="0" required>
                                <span class="input-group-text">VNĐ</span>
                            </div>
                        </div>
                        
                        <!-- Thông tin lương -->
                        <div class="col-12">
                            <hr>
                            <h6 class="fw-bold mb-3">Thông tin lương</h6>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold bhxh-field">Lương đóng BHXH <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" name="luong_bhxh" class="form-control bhxh-input" placeholder="VD: 5.350.000" required>
                                <span class="input-group-text bhxh-input">VNĐ</span>
                            </div>
                            <small class="bhxh-note">Không tính vào tổng lương</small>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Lương cơ bản <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" name="luong_co_ban" class="form-control" placeholder="VD: 6.000.000" required>
                                <span class="input-group-text">VNĐ</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Lương kinh doanh <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" name="luong_kinh_doanh" class="form-control" placeholder="VD: 7.000.000" required>
                                <span class="input-group-text">VNĐ</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Thưởng kinh doanh</label>
                            <div class="input-group">
                                <input type="text" name="thuong_kinh_doanh" class="form-control" placeholder="VD: 7.500.000">
                                <span class="input-group-text">VNĐ</span>
                            </div>
                            <small class="text-muted">Thưởng KD (thanh toán sau khi đủ hs)</small>
                        </div>
                        
                        <!-- Thông tin hoa hồng -->
                        <div class="col-12">
                            <hr>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Hoa hồng quản lý</label>
                            <div class="input-group">
                                <input type="text" name="hh_quan_ly" class="form-control" placeholder="VD: 4.000.000" step="1" min="0" value="0" readonly>
                                <span class="input-group-text">VNĐ</span>
                            </div>
                            <small class="text-muted">Tự động tính dựa trên % của KPI mặc định</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Hoa hồng quản lý (%)</label>
                            <div class="input-group">
                                <input type="number" name="hh_quan_ly_percent" class="form-control" placeholder="VD: 0.1" step="0.01" min="0" value="0.1">
                                <span class="input-group-text">%</span>
                            </div>
                            <small class="text-muted">Tỷ lệ hoa hồng quản lý (ví dụ: 0.1%)</small>
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label fw-semibold">Ghi chú</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Nhập ghi chú (nếu có)"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="las la-save me-2"></i>
                        Lưu chính sách
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit KPI Modal -->
<div class="modal fade" id="editKpiModal" tabindex="-1" aria-labelledby="editKpiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editKpiModalLabel">
                    <i class="las la-edit me-2 text-warning"></i>
                    Chỉnh sửa chính sách
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editKpiForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row g-3">
                        <!-- Thông tin chức danh -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Chức danh <span class="text-danger">*</span></label>
                            <input type="text" name="level_name" id="edit_level_name" class="form-control" placeholder="VD: QLKD 1, GĐKD 2, GĐ Vùng 1" required>
                            <small class="text-muted">Nhập tên chức danh (VD: QLKD 1, GĐKD 2)</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Cấp bậc <span class="text-danger">*</span></label>
                            <select name="role_level" id="edit_role_level" class="form-select" required>
                                <option value="">-- Chọn cấp bậc --</option>
                                <option value="staff_level">Cấp nhân viên (QLKD 1 → 3)</option>
                                <option value="mid_manager_level">Cấp quản lý trung gian (GĐKD 1, 2)</option>
                                <option value="senior_manager_level">Cấp quản lý cấp cao (GDTT 1, 2)</option>
                                <option value="regional_director_level">Cấp lãnh đạo vùng (GĐ Vùng 1, 2)</option>
                            </select>
                            <small class="text-muted">Chọn cấp bậc phù hợp với chức danh</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">KPI tuyển dụng</label>
                            <input type="text" name="kpi_tuyen_dung" id="edit_kpi_tuyen_dung" class="form-control" placeholder="VD: Cá nhân, CN + TĐ 05 NS">
                            <small class="text-muted">Loại KPI tuyển dụng áp dụng</small>
                        </div>
                        
                        <!-- Thông tin KPI -->
                        <div class="col-12">
                            <hr>
                            <h6 class="fw-bold mb-3">Thiết lập KPI</h6>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">KPI <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" name="kpi_default" id="edit_kpi_default" class="form-control" placeholder="VD: 500.000.000" min="0" required>
                                <span class="input-group-text">VNĐ</span>
                            </div>
                            <small class="text-muted">KPI T1 (50%) và T2 (70%) sẽ tự động tính</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">KPI Tháng 1 (50%)</label>
                            <div class="input-group">
                                <input type="text" name="kpi_month_1" id="edit_kpi_month_1" class="form-control" placeholder="VD: 250.000.000" min="0" required>
                                <span class="input-group-text">VNĐ</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">KPI Tháng 2 (70%)</label>
                            <div class="input-group">
                                <input type="text" name="kpi_month_2" id="edit_kpi_month_2" class="form-control" placeholder="VD: 350.000.000" min="0" required>
                                <span class="input-group-text">VNĐ</span>
                            </div>
                        </div>
                        
                        <!-- Thông tin lương -->
                        <div class="col-12">
                            <hr>
                            <h6 class="fw-bold mb-3">Thông tin lương</h6>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold bhxh-field">Lương đóng BHXH <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" name="luong_bhxh" id="edit_luong_bhxh" class="form-control bhxh-input" placeholder="VD: 5.350.000" required>
                                <span class="input-group-text bhxh-input">VNĐ</span>
                            </div>
                            <small class="bhxh-note">Không tính vào tổng lương</small>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Lương cơ bản <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" name="luong_co_ban" id="edit_luong_co_ban" class="form-control" placeholder="VD: 6.000.000" required>
                                <span class="input-group-text">VNĐ</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Lương kinh doanh <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" name="luong_kinh_doanh" id="edit_luong_kinh_doanh" class="form-control" placeholder="VD: 7.000.000" required>
                                <span class="input-group-text">VNĐ</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Thưởng kinh doanh</label>
                            <div class="input-group">
                                <input type="text" name="thuong_kinh_doanh" id="edit_thuong_kinh_doanh" class="form-control" placeholder="VD: 7.500.000">
                                <span class="input-group-text">VNĐ</span>
                            </div>
                            <small class="text-muted">Thưởng KD (thanh toán sau khi đủ hs)</small>
                        </div>
                        
                        <!-- Thông tin hoa hồng -->
                        <div class="col-12">
                            <hr>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Hoa hồng quản lý</label>
                            <div class="input-group">
                                <input type="text" name="hh_quan_ly" id="edit_hh_quan_ly" class="form-control" placeholder="VD: 4.000.000" step="1" min="0" value="0" readonly>
                                <span class="input-group-text">VNĐ</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Hoa hồng quản lý (%)</label>
                            <div class="input-group">
                                <input type="number" name="hh_quan_ly_percent" id="edit_hh_quan_ly_percent" class="form-control" placeholder="VD: 0.1" step="0.01" min="0" value="0.1">
                                <span class="input-group-text">%</span>
                            </div>
                            <small class="text-muted">Tỷ lệ hoa hồng quản lý (ví dụ: 0.1%)</small>
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label fw-semibold">Ghi chú</label>
                            <textarea name="notes" id="edit_notes" class="form-control" rows="2" placeholder="Nhập ghi chú (nếu có)"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="las la-save me-2"></i>
                        Cập nhật chính sách
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit KPI Detail Modal -->
<div class="modal fade" id="editKpiDetailModal" tabindex="-1" aria-labelledby="editKpiDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editKpiDetailModalLabel">
                    <i class="las la-edit me-2 text-warning"></i>
                    Cập nhật KPI theo tháng
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editKpiDetailForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nhân viên <span class="text-danger">*</span></label>
                            <input type="text" name="staff_name" id="edit_staff_name" class="form-control" placeholder="Tên nhân viên" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tháng <span class="text-danger">*</span></label>
                            <input type="month" name="month" id="edit_month" class="form-control" value="{{ now()->format('Y-m') }}" required>
                        </div>
                        
                        <!-- Thông tin hợp đồng -->
                        <div class="col-12">
                            <hr>
                            <h6 class="fw-bold mb-3">Thông tin hợp đồng & doanh số</h6>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Chỉ tiêu hợp đồng <span class="text-danger">*</span></label>
                            <input type="number" name="target_contracts" id="edit_target_contracts" class="form-control" placeholder="VD: 5" min="0" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Thực tế hợp đồng</label>
                            <input type="number" name="actual_contracts" id="edit_actual_contracts" class="form-control" placeholder="VD: 3" min="0">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Chỉ tiêu doanh số <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" name="target_sales" id="edit_target_sales" class="form-control" placeholder="VD: 500.000.000" min="0" required>
                                <span class="input-group-text">VNĐ</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Thực tế doanh số</label>
                            <div class="input-group">
                                <input type="text" name="actual_sales" id="edit_actual_sales" class="form-control" placeholder="VD: 350.000.000" min="0">
                                <span class="input-group-text">VNĐ</span>
                            </div>
                        </div>
                        
                        <!-- Thông tin KPI -->
                        <div class="col-12">
                            <hr>
                            <h6 class="fw-bold mb-3">KPI & Trạng thái</h6>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Hoàn thành hợp đồng</label>
                            <div class="input-group">
                                <input type="number" name="contract_completion_rate" id="edit_contract_completion_rate" class="form-control" step="0.1" min="0" readonly>
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Hoàn thành doanh số</label>
                            <div class="input-group">
                                <input type="number" name="sales_completion_rate" id="edit_sales_completion_rate" class="form-control" step="0.1" min="0" readonly>
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Tổng KPI</label>
                            <div class="input-group">
                                <input type="number" name="overall_kpi_percentage" id="edit_overall_kpi_percentage" class="form-control" step="0.1" min="0" readonly>
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Trạng thái</label>
                            <select name="kpi_status" id="edit_kpi_status" class="form-select">
                                <option value="exceeded">Vượt KPI</option>
                                <option value="achieved">Đạt KPI</option>
                                <option value="near_achieved">Gần đạt</option>
                                <option value="not_achieved">Không đạt</option>
                                <option value="pending">Chờ đánh giá</option>
                            </select>
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label fw-semibold">Ghi chú</label>
                            <textarea name="notes" id="edit_kpi_detail_notes" class="form-control" rows="2" placeholder="Nhập ghi chú (nếu có)"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="las la-save me-2"></i>
                        Cập nhật KPI
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('style')
<style>
    /* Chung */
    .kpi-dashboard-modern {
        font-family: 'Nunito', sans-serif;
    }
    .kpi-dashboard-modern .card {
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15) !important;
        border-radius: 0.5rem;
    }
    .kpi-dashboard-modern .card-header {
        background-color: transparent;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid rgba(0,0,0,.05);
    }
    
    /* KPI Charts Section */
    .kpi-charts-section .chart-container {
        position: relative;
        margin: auto;
    }
    .kpi-charts-section .card-header .form-select {
        width: auto;
        min-width: 100px;
    }
    
    /* KPI Details Table */
    .kpi-details-section .table th {
        font-weight: 600;
        white-space: nowrap;
    }
    .kpi-details-section .table td {
        vertical-align: middle;
    }
    
    /* Status Colors */
    .status-indicator {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
    }
    
    /* Print Styles */
    @media print {
        .card {
            box-shadow: none !important;
            border: 1px solid #ddd;
        }
        .no-print {
            display: none !important;
        }
        .chart-container {
            page-break-inside: avoid;
        }
    }
    
    /* Chart Tooltips */
    .chart-tooltip {
        background-color: rgba(0, 0, 0, 0.8);
        color: #fff;
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 13px;
    }
    
    /* Summary Stats */
    .summary-card {
        border-radius: 0.5rem;
        padding: 1rem;
        height: 100%;
    }
    .summary-card .icon-wrapper {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: rgba(255,255,255,0.2);
        margin-right: 1rem;
    }
    .summary-card .icon-wrapper i {
        font-size: 24px;
        color: #fff;
    }
    .summary-card.bg-gradient-primary {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        color: #fff;
    }
    .summary-card.bg-gradient-success {
        background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);
        color: #fff;
    }
    .summary-card.bg-gradient-warning {
        background: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%);
        color: #fff;
    }
    .summary-card.bg-gradient-danger {
        background: linear-gradient(135deg, #e74a3b 0%, #be2617 100%);
        color: #fff;
    }
    
    /* BHXH Styles (Original) */
    .bhxh-field {
        color: #dc3545 !important;
        font-weight: 500;
    }
    
    .bhxh-input {
        border-color: #dc3545 !important;
        color: #dc3545 !important;
    }
    
    .bhxh-note {
        font-size: 0.75rem;
        color: #dc3545;
        font-style: italic;
    }
</style>
@endpush

@push('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, checking for Chart.js');
    
    // Kiểm tra xem Chart.js đã được tải chưa
    if (typeof Chart === 'undefined') {
        console.error('Chart.js chưa được tải, đang tải động');
        var script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js';
        script.onload = initCharts;
        document.head.appendChild(script);
    } else {
        console.log('Chart.js đã sẵn sàng, khởi tạo biểu đồ');
        initCharts();
    }
    
    function initCharts() {
        console.log('Bắt đầu khởi tạo biểu đồ');
        
        // Kiểm tra các phần tử canvas
        const barCtx = document.getElementById('kpiBarChart');
        const pieCtx = document.getElementById('kpiPieChart');
        
        if (!barCtx || !pieCtx) {
            console.error('Không tìm thấy phần tử canvas:', { 
                barChart: barCtx ? 'OK' : 'Không tìm thấy', 
                pieChart: pieCtx ? 'OK' : 'Không tìm thấy'
            });
            return;
        }
        
        console.log('Đã tìm thấy phần tử canvas, tiếp tục khởi tạo');
        
        // Dữ liệu biểu đồ cột
        const monthLabels = [
            @foreach(array_keys($monthlyAverageKpi) as $month)
                '{{ $month }}',
            @endforeach
        ];
        
        const kpiData = [
            @foreach($monthlyAverageKpi as $average)
                {{ round($average, 1) }},
            @endforeach
        ];
        
        console.log('Dữ liệu biểu đồ cột:', { labels: monthLabels, data: kpiData });
        
        // Dữ liệu biểu đồ tròn
        const statusData = [
            {{ $kpiStatusCounts['exceeded'] }},
            {{ $kpiStatusCounts['achieved'] }},
            {{ $kpiStatusCounts['near_achieved'] }},
            {{ $kpiStatusCounts['not_achieved'] }}
        ];
        
        console.log('Dữ liệu biểu đồ tròn:', statusData);
        
        // Biểu đồ KPI theo tháng
        const barCtxObj = barCtx.getContext('2d');
        const barChart = new Chart(barCtxObj, {
            type: 'bar',
            data: {
                labels: monthLabels,
                datasets: [
                    {
                        label: 'KPI (%)',
                        data: kpiData,
                        backgroundColor: '#4e73df',
                        borderWidth: 0,
                        borderRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.raw + '%';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 150,
                        grid: {
                            drawBorder: false
                        },
                        ticks: {
                            font: {
                                size: 12
                            },
                            callback: function(value) {
                                return value + '%';
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            font: {
                                size: 12
                            }
                        }
                    }
                }
            }
        });
        
        console.log('Đã tạo biểu đồ cột');
        
        // Biểu đồ tròn phân bố trạng thái KPI
        const pieCtxObj = pieCtx.getContext('2d');
        const pieChart = new Chart(pieCtxObj, {
            type: 'doughnut',
            data: {
                labels: ['Vượt KPI', 'Đạt KPI', 'Gần đạt', 'Không đạt'],
                datasets: [
                    {
                        data: statusData,
                        backgroundColor: [
                            '#1cc88a', // success
                            '#4e73df', // primary
                            '#f6c23e', // warning
                            '#e74a3b', // danger
                        ],
                        borderWidth: 0
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((acc, val) => acc + val, 0);
                                const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                },
                cutout: '70%'
            }
        });
        
        console.log('Đã tạo biểu đồ tròn');
        
        // Xử lý thay đổi năm của biểu đồ KPI theo tháng
        document.getElementById('kpiMonthlyChartYear').addEventListener('change', function() {
            // Trong ứng dụng thực tế, sẽ gọi AJAX để lấy dữ liệu theo năm được chọn
            console.log('Đã thay đổi năm: ' + this.value);
            // Giả lập dữ liệu khác khi thay đổi năm
            if (this.value === '2023') {
                barChart.data.datasets[0].data = [78.5, 85.2, 92.8, 101.5, 110.2, 120.8];
            } else {
                barChart.data.datasets[0].data = kpiData;
            }
            barChart.update();
        });
    }
    
    // Export to Excel function
    window.exportToExcel = function() {
        alert('Chức năng xuất Excel đang được phát triển');
    };
    
    // Print report function
    window.printReport = function() {
        window.print();
    };
    
    // Edit button action
    document.querySelectorAll('.btn-edit-kpi').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            // Sau này sẽ dùng AJAX để lấy dữ liệu thật
            const editModal = new bootstrap.Modal(document.getElementById('editKpiModal'));
            
            // Cập nhật action của form
            document.getElementById('editKpiForm').action = '{{ route("admin.kpi.level.index") }}/' + id;
            
            // Hiển thị modal
            editModal.show();
        });
    });
    
    // Delete button action
    document.querySelectorAll('.btn-delete-kpi').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            if (confirm('Bạn có chắc chắn muốn xóa KPI này không?')) {
                // Sau này sẽ gọi AJAX để xóa
                console.log('Đã xóa KPI có ID: ' + id);
            }
        });
    });
});

$(document).ready(function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Format numbers with dots (Vietnamese format)
    function formatNumber(number) {
        return new Intl.NumberFormat('vi-VN').format(number);
    }
    
    // Parse numbers with dots
    function parseFormattedNumber(formattedNumber) {
        if (typeof formattedNumber !== 'string') {
            return 0;
        }
        return parseInt(formattedNumber.replace(/\./g, '').replace(/[^\d]/g, '')) || 0;
    }
    
    // Format initial values when page loads
    $('input[name="kpi_default"], input[name="kpi_month_1"], input[name="kpi_month_2"], input[name="luong_bhxh"], input[name="luong_co_ban"], input[name="luong_kinh_doanh"], input[name="thuong_kinh_doanh"], input[name="hh_quan_ly"]').each(function() {
        const rawValue = parseFormattedNumber($(this).val());
        if (rawValue > 0) {
            $(this).val(formatNumber(rawValue));
        }
    });
    
    // Calculate KPI month values and management commission
    function calculateKpiMonths(kpiDefault, isEditForm) {
        // Calculate Month 1 (50%)
        const kpiMonth1 = Math.round(kpiDefault * 0.5);
        if (isEditForm) {
            $('#edit_kpi_month_1').val(formatNumber(kpiMonth1));
        } else {
            $('input[name="kpi_month_1"]').val(formatNumber(kpiMonth1));
        }
        
        // Calculate Month 2 (70%)
        const kpiMonth2 = Math.round(kpiDefault * 0.7);
        if (isEditForm) {
            $('#edit_kpi_month_2').val(formatNumber(kpiMonth2));
        } else {
            $('input[name="kpi_month_2"]').val(formatNumber(kpiMonth2));
        }
    }
    
    // Calculate management commission based on KPI default and percentage
    function calculateManagementCommission(isEditForm) {
        const kpiDefault = parseFormattedNumber(isEditForm ? $('#edit_kpi_default').val() : $('input[name="kpi_default"]').val());
        const hhPercent = parseFloat(isEditForm ? $('#edit_hh_quan_ly_percent').val() : $('input[name="hh_quan_ly_percent"]').val()) || 0;
        
        // Calculate management commission
        const hhQuanLy = Math.round(kpiDefault * (hhPercent / 100));
        
        if (isEditForm) {
            $('#edit_hh_quan_ly').val(formatNumber(hhQuanLy));
        } else {
            $('input[name="hh_quan_ly"]').val(formatNumber(hhQuanLy));
        }
    }
    
    // Handle KPI default change
    $('input[name="kpi_default"], #edit_kpi_default').on('input', function() {
        const kpiDefault = parseFormattedNumber($(this).val());
        const isEditForm = $(this).attr('id') === 'edit_kpi_default';
        
        // Calculate KPI months
        calculateKpiMonths(kpiDefault, isEditForm);
        
        // Calculate management commission
        calculateManagementCommission(isEditForm);
    });
    
    // Handle management commission percentage change
    $('input[name="hh_quan_ly_percent"], #edit_hh_quan_ly_percent').on('input', function() {
        const isEditForm = $(this).attr('id') === 'edit_hh_quan_ly_percent';
        calculateManagementCommission(isEditForm);
    });
    
    // Calculate KPI completion rates when target or actual values change
    $('#edit_target_contracts, #edit_actual_contracts, #edit_target_sales, #edit_actual_sales').on('input', function() {
        calculateKpiCompletionRates();
    });
    
    function calculateKpiCompletionRates() {
        // Get values
        const targetContracts = parseInt($('#edit_target_contracts').val()) || 0;
        const actualContracts = parseInt($('#edit_actual_contracts').val()) || 0;
        const targetSales = parseFormattedNumber($('#edit_target_sales').val()) || 0;
        const actualSales = parseFormattedNumber($('#edit_actual_sales').val()) || 0;
        
        // Calculate contract completion rate
        let contractCompletionRate = 0;
        if (targetContracts > 0) {
            contractCompletionRate = (actualContracts / targetContracts) * 100;
        }
        
        // Calculate sales completion rate
        let salesCompletionRate = 0;
        if (targetSales > 0) {
            salesCompletionRate = (actualSales / targetSales) * 100;
        }
        
        // Calculate overall KPI percentage (average of contract and sales)
        const overallKpiPercentage = (contractCompletionRate + salesCompletionRate) / 2;
        
        // Set values rounded to 1 decimal place
        $('#edit_contract_completion_rate').val(contractCompletionRate.toFixed(1));
        $('#edit_sales_completion_rate').val(salesCompletionRate.toFixed(1));
        $('#edit_overall_kpi_percentage').val(overallKpiPercentage.toFixed(1));
        
        // Set KPI status based on overall percentage
        let kpiStatus = 'pending';
        if (overallKpiPercentage >= 120) {
            kpiStatus = 'exceeded';
        } else if (overallKpiPercentage >= 100) {
            kpiStatus = 'achieved';
        } else if (overallKpiPercentage >= 80) {
            kpiStatus = 'near_achieved';
        } else if (overallKpiPercentage > 0) {
            kpiStatus = 'not_achieved';
        }
        
        $('#edit_kpi_status').val(kpiStatus);
    }
    
    // Initialize calculation on page load
    $('input[name="kpi_default"]').trigger('input');
    $('#edit_kpi_default').trigger('input');
    
    // Format all currency input fields
    $('input[name="kpi_default"], input[name="kpi_month_1"], input[name="kpi_month_2"], input[name="luong_bhxh"], input[name="luong_co_ban"], input[name="luong_kinh_doanh"], input[name="thuong_kinh_doanh"], input[name="hh_quan_ly"], #edit_kpi_default, #edit_kpi_month_1, #edit_kpi_month_2, #edit_luong_bhxh, #edit_luong_co_ban, #edit_luong_kinh_doanh, #edit_thuong_kinh_doanh, #edit_hh_quan_ly, #edit_target_sales, #edit_actual_sales').on('input', function() {
        const value = parseFormattedNumber($(this).val());
        $(this).val(formatNumber(value));
    });
    
    // Before form submission, convert all formatted numbers back to plain numbers
    $('form').on('submit', function() {
        $('input[name="kpi_default"], input[name="kpi_month_1"], input[name="kpi_month_2"], input[name="luong_bhxh"], input[name="luong_co_ban"], input[name="luong_kinh_doanh"], input[name="thuong_kinh_doanh"], input[name="hh_quan_ly"], #edit_kpi_default, #edit_kpi_month_1, #edit_kpi_month_2, #edit_luong_bhxh, #edit_luong_co_ban, #edit_luong_kinh_doanh, #edit_thuong_kinh_doanh, #edit_hh_quan_ly, #edit_target_sales, #edit_actual_sales').each(function() {
            const plainValue = parseFormattedNumber($(this).val());
            $(this).val(plainValue);
        });
        return true;
    });
    
    // Handle edit button click and populate form
    $('.btn-edit-kpi').on('click', function() {
        const kpiId = $(this).data('id');
        const row = $(this).closest('tr');
        
        // Get data from the row
        const levelName = row.find('td:eq(1)').text().trim().split('\n')[0].trim();
        
        // Get role level from badge class
        let roleLevel = '';
        const roleBadge = row.find('td:eq(1) .badge');
        if (roleBadge.length > 0) {
            if (roleBadge.hasClass('bg-info')) {
                roleLevel = 'staff_level';
            } else if (roleBadge.hasClass('bg-primary')) {
                roleLevel = 'mid_manager_level';
            } else if (roleBadge.hasClass('bg-warning')) {
                roleLevel = 'senior_manager_level';
            } else if (roleBadge.hasClass('bg-success')) {
                roleLevel = 'regional_director_level';
            }
        }
        
        const kpiDefault = parseFormattedNumber(row.find('td:eq(2) .fw-semibold').text());
        const kpiMonth1 = parseFormattedNumber(row.find('td:eq(3)').text());
        const kpiMonth2 = parseFormattedNumber(row.find('td:eq(4)').text());
        const kpiTuyenDung = row.find('td:eq(5)').text().trim();
        const luongBHXH = parseFormattedNumber(row.find('td:eq(6)').text());
        const luongCB = parseFormattedNumber(row.find('td:eq(7)').text());
        const luongKD = parseFormattedNumber(row.find('td:eq(8)').text());
        const thuongKD = parseFormattedNumber(row.find('td:eq(10)').text());
        const hhQuanLy = parseFormattedNumber(row.find('td:eq(11) span').first().text());
        const hhQuanLyPercent = parseFloat(row.find('td:eq(11) small').text().replace('%', '').trim()) || 0;
        const notes = row.find('td:eq(13)').text().trim();
        
        // Set form action
        $('#editKpiForm').attr('action', '/admin/kpi-level/' + kpiId);
        
        // Populate form fields
        $('#edit_level_name').val(levelName);
        $('#edit_role_level').val(roleLevel);
        $('#edit_kpi_tuyen_dung').val(kpiTuyenDung);
        $('#edit_kpi_default').val(formatNumber(kpiDefault));
        $('#edit_kpi_month_1').val(formatNumber(kpiMonth1));
        $('#edit_kpi_month_2').val(formatNumber(kpiMonth2));
        $('#edit_luong_bhxh').val(formatNumber(luongBHXH));
        $('#edit_luong_co_ban').val(formatNumber(luongCB));
        $('#edit_luong_kinh_doanh').val(formatNumber(luongKD));
        $('#edit_thuong_kinh_doanh').val(formatNumber(thuongKD));
        $('#edit_hh_quan_ly').val(formatNumber(hhQuanLy));
        $('#edit_hh_quan_ly_percent').val(hhQuanLyPercent);
        $('#edit_notes').val(notes);
    });
    
    // Handle edit KPI detail button click and populate form
    $('.btn-edit-kpi-detail').on('click', function() {
        const kpiId = $(this).data('id');
        const row = $(this).closest('tr');
        
        // Get data from the row
        const staffName = row.find('td:eq(0)').text().trim();
        const month = row.find('td:eq(1)').text().trim();
        const targetContracts = parseInt(row.find('td:eq(2)').text().trim()) || 0;
        const actualContracts = parseInt(row.find('td:eq(3)').text().trim()) || 0;
        const targetSales = parseFormattedNumber(row.find('td:eq(4)').text());
        const actualSales = parseFormattedNumber(row.find('td:eq(5)').text());
        const kpiPercentage = parseFloat(row.find('td:eq(6) span').text().replace('%', '').trim()) || 0;
        const kpiStatus = row.find('td:eq(7) span').attr('class').includes('success') ? 'exceeded' : 
                         (row.find('td:eq(7) span').attr('class').includes('primary') ? 'achieved' : 
                         (row.find('td:eq(7) span').attr('class').includes('warning') ? 'near_achieved' : 
                         (row.find('td:eq(7) span').attr('class').includes('danger') ? 'not_achieved' : 'pending')));
        const notes = row.find('td:eq(8)').text().trim();
        
        // Convert month format if needed (e.g., "05/2023" to "2023-05")
        let monthValue = month;
        if (month.includes('/')) {
            const parts = month.split('/');
            if (parts.length === 2) {
                monthValue = parts[1] + '-' + parts[0].padStart(2, '0');
            }
        }
        
        // Set form action
        $('#editKpiDetailForm').attr('action', '/admin/kpi-detail/' + kpiId);
        
        // Populate form fields
        $('#edit_staff_name').val(staffName);
        $('#edit_month').val(monthValue);
        $('#edit_target_contracts').val(targetContracts);
        $('#edit_actual_contracts').val(actualContracts);
        $('#edit_target_sales').val(formatNumber(targetSales));
        $('#edit_actual_sales').val(formatNumber(actualSales));
        $('#edit_contract_completion_rate').val((actualContracts / targetContracts * 100).toFixed(1));
        $('#edit_sales_completion_rate').val((actualSales / targetSales * 100).toFixed(1));
        $('#edit_overall_kpi_percentage').val(kpiPercentage.toFixed(1));
        $('#edit_kpi_status').val(kpiStatus);
        $('#edit_kpi_detail_notes').val(notes);
        
        // Calculate rates
        calculateKpiCompletionRates();
    });
    
    // Show success message
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Thành công!',
            text: '{{ session('success') }}',
            confirmButtonText: 'Đóng',
            confirmButtonColor: '#0d6efd'
        });
    @endif
});
</script>

// Tạo nút khởi tạo lại biểu đồ khi có lỗi
<script>
    setTimeout(function() {
        const chartContainers = document.querySelectorAll('.chart-container');
        chartContainers.forEach(container => {
            // Kiểm tra xem biểu đồ có hiển thị không
            const canvas = container.querySelector('canvas');
            if (canvas && (!canvas.__chartjs || canvas.height === 0)) {
                // Biểu đồ không hiển thị, thêm nút khởi tạo lại
                const refreshButton = document.createElement('button');
                refreshButton.className = 'btn btn-sm btn-primary position-absolute';
                refreshButton.style.top = '50%';
                refreshButton.style.left = '50%';
                refreshButton.style.transform = 'translate(-50%, -50%)';
                refreshButton.innerHTML = '<i class="las la-sync"></i> Tải lại biểu đồ';
                refreshButton.addEventListener('click', function() {
                    location.reload();
                });
                container.style.position = 'relative';
                container.appendChild(refreshButton);
            }
        });
    }, 2000);
</script>
@endpush 