<div class="kpi-detail-content">
    <div class="row">
        <div class="col-md-6">
            <div class="detail-section mb-4">
                <h6 class="fw-semibold text-primary mb-3">
                    <i class="las la-user me-2"></i>Thông tin nhân viên
                </h6>
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar-sm me-3">
                        <img src="{{ getImage(getFilePath('userProfile') . '/' . $kpi->staff->image, getFileSize('userProfile'), avatar: true) }}" 
                             alt="{{ $kpi->staff->fullname }}" class="rounded-circle">
                    </div>
                    <div>
                        <h6 class="mb-1">{{ $kpi->staff->fullname }}</h6>
                        <small class="text-muted">{{ $kpi->staff->username }}</small>
                    </div>
                </div>
                <p class="mb-2"><strong>Email:</strong> {{ $kpi->staff->email }}</p>
                <p class="mb-0"><strong>Tháng đánh giá:</strong> {{ \Carbon\Carbon::createFromFormat('Y-m', $kpi->month_year)->format('m/Y') }}</p>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="detail-section mb-4">
                <h6 class="fw-semibold text-primary mb-3">
                    <i class="las la-chart-line me-2"></i>Kết quả KPI
                </h6>
                <div class="kpi-summary">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>KPI Tổng thể:</span>
                        <span class="badge {{ $kpi->overall_kpi_percentage >= 100 ? 'bg-success' : ($kpi->overall_kpi_percentage >= 80 ? 'bg-warning' : 'bg-danger') }} fw-semibold">
                            {{ number_format($kpi->overall_kpi_percentage, 1) }}%
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Trạng thái:</span>
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
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Ngày tạo:</span>
                        <span>{{ $kpi->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="detail-section mb-4">
                <h6 class="fw-semibold text-primary mb-3">
                    <i class="las la-file-contract me-2"></i>Chỉ tiêu Hợp đồng
                </h6>
                <div class="progress-item mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span>Chỉ tiêu:</span>
                        <span class="fw-semibold">{{ $kpi->target_contracts }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span>Thực tế:</span>
                        <span class="fw-semibold">{{ $kpi->actual_contracts }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Tỷ lệ hoàn thành:</span>
                        <span class="badge {{ $kpi->contract_completion_rate >= 100 ? 'bg-success' : ($kpi->contract_completion_rate >= 80 ? 'bg-warning' : 'bg-danger') }}">
                            {{ number_format($kpi->contract_completion_rate, 1) }}%
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="detail-section mb-4">
                <h6 class="fw-semibold text-primary mb-3">
                    <i class="las la-hand-holding-usd me-2"></i>Chỉ tiêu Doanh số
                </h6>
                <div class="progress-item mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span>Chỉ tiêu:</span>
                        <span class="fw-semibold">{{ number_format($kpi->target_sales, 0, ',', '.') }} VNĐ</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span>Thực tế:</span>
                        <span class="fw-semibold">{{ number_format($kpi->actual_sales, 0, ',', '.') }} VNĐ</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Tỷ lệ hoàn thành:</span>
                        <span class="badge {{ $kpi->sales_completion_rate >= 100 ? 'bg-success' : ($kpi->sales_completion_rate >= 80 ? 'bg-warning' : 'bg-danger') }}">
                            {{ number_format($kpi->sales_completion_rate, 1) }}%
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($kpi->target_customers > 0 || $kpi->actual_customers > 0)
    <div class="row">
        <div class="col-md-6">
            <div class="detail-section mb-4">
                <h6 class="fw-semibold text-primary mb-3">
                    <i class="las la-users me-2"></i>Chỉ tiêu Khách hàng
                </h6>
                <div class="progress-item mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span>Chỉ tiêu:</span>
                        <span class="fw-semibold">{{ $kpi->target_customers }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span>Thực tế:</span>
                        <span class="fw-semibold">{{ $kpi->actual_customers }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Tỷ lệ hoàn thành:</span>
                        <span class="badge {{ $kpi->customer_completion_rate >= 100 ? 'bg-success' : ($kpi->customer_completion_rate >= 80 ? 'bg-warning' : 'bg-danger') }}">
                            {{ number_format($kpi->customer_completion_rate, 1) }}%
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($kpi->notes)
    <div class="detail-section">
        <h6 class="fw-semibold text-primary mb-3">
            <i class="las la-sticky-note me-2"></i>Ghi chú
        </h6>
        <div class="notes-content p-3 bg-light rounded">
            {{ $kpi->notes }}
        </div>
    </div>
    @endif
</div>

<style>
.kpi-detail-content {
    font-size: 0.9rem;
}

.detail-section {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 8px;
    border-left: 4px solid #667eea;
}

.progress-item {
    background: white;
    padding: 1rem;
    border-radius: 6px;
    border: 1px solid #e9ecef;
}

.notes-content {
    border-left: 3px solid #667eea;
    background: #f8f9fa !important;
}

.avatar-sm {
    width: 50px;
    height: 50px;
}

.avatar-sm img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
</style> 