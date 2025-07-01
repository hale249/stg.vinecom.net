<div class="profit-schedule-modal profit-schedule-modal-layout">
    <div class="info-section mb-4">
        <div class="row">
            <div class="col-md-6">
                <p><strong>Dự án đầu tư:</strong> {{ $project->title }}</p>
                <p><strong>Nhà đầu tư:</strong> {{ $user ? $user->fullname : 'Khách' }}</p>
            </div>
            <div class="col-md-6">
                <p><strong>Số tiền đầu tư:</strong> <span class="text-danger fw-bold fs-5">{{ number_format($investment_amount, 0, ',', '.') }} VNĐ</span></p>
                <p><strong>Ngày lập:</strong> {{ date('d/m/Y') }}</p>
            </div>
        </div>
    </div>

    <div class="table-container">
        <div class="table-responsive">
            <table class="table table-bordered table-striped profit-schedule-table">
                <thead class="table-dark">
                    <tr>
                        <th class="text-center" style="width: 5%;">STT</th>
                        <th class="text-center" style="width: 8%;">Kỳ</th>
                        <th class="text-center" style="width: 12%;">Ngày bắt đầu</th>
                        <th class="text-center" style="width: 12%;">Ngày kết thúc</th>
                        <th class="text-center" style="width: 8%;">Số ngày</th>
                        <th class="text-center" style="width: 12%;">Lãi suất (%/năm)</th>
                        <th class="text-end" style="width: 15%;">Gốc đầu kỳ (VNĐ)</th>
                        <th class="text-end" style="width: 15%;">Lãi kỳ (VNĐ)</th>
                        <th class="text-end" style="width: 13%;">Tổng tích lũy (VNĐ)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($schedule as $index => $period)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center">{{ $period['period_no'] }}</td>
                        <td class="text-center">{{ $period['start_date']->format('d/m/Y') }}</td>
                        <td class="text-center">{{ $period['end_date']->format('d/m/Y') }}</td>
                        <td class="text-center">{{ $period['days'] }}</td>
                        <td class="text-center">{{ number_format($period['interest_rate'], 2, ',', '.') }}%</td>
                        <td class="text-end fw-bold">{{ number_format($period['principal'], 0, ',', '.') }}</td>
                        <td class="text-end text-success fw-bold fs-6">{{ number_format($period['period_interest'], 0, ',', '.') }}</td>
                        <td class="text-end text-cumulative-total fw-bold fs-6">{{ number_format($period['cumulative_total'], 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="summary-section mt-4 p-4 bg-light border rounded">
        <h5 class="mb-4 text-center fw-bold">TỔNG KẾT</h5>
        <div class="row">
            <div class="col-md-4">
                <div class="summary-item">
                    <span class="summary-label">Lãi trung bình mỗi kỳ:</span>
                    <span class="summary-value text-dark fw-bold fs-5">
                        ~{{ number_format($schedule ? round(array_sum(array_column($schedule, 'period_interest')) / count($schedule), 0) : 0, 0, ',', '.') }} VNĐ
                    </span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="summary-item">
                    <span class="summary-label">Tổng lãi dự kiến:</span>
                    <span class="summary-value text-success fw-bold fs-5">
                        {{ number_format($schedule ? array_sum(array_column($schedule, 'period_interest')) : 0, 0, ',', '.') }} VNĐ
                    </span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="summary-item">
                    <span class="summary-label">Tổng giá trị đến đáo hạn:</span>
                    <span class="summary-value text-danger fw-bold fs-5">
                        {{ number_format($investment_amount + ($schedule ? array_sum(array_column($schedule, 'period_interest')) : 0), 0, ',', '.') }} VNĐ
                    </span>
                </div>
            </div>
        </div>
        
        <div class="mt-4 pt-3 border-top">
            <p class="text-muted fst-italic mb-0 small">
                <strong>Lưu ý:</strong> Bảng này mang tính chất tham khảo, lãi thực nhận phụ thuộc vào ngày thực tế và điều khoản hợp đồng đầu tư.
            </p>
        </div>
    </div>
</div>

<style>
.profit-schedule-modal-layout {
    display: flex;
    flex-direction: column;
    height: 100%;
    max-height: 100%;
    overflow: hidden;
}

.table-container {
    flex-grow: 1;
    overflow-y: auto;
    min-height: 0;
}

.info-section, .summary-section {
    flex-shrink: 0;
}

.profit-schedule-modal {
    font-family: "Times New Roman", Times, serif;
    font-size: 13pt;
    line-height: 1.6;
    color: #000000;
    max-width: 100%;
}

.profit-schedule-table {
    font-size: 11pt;
    width: 100%;
    table-layout: fixed;
}

.profit-schedule-table th {
    font-weight: bold;
    font-size: 11pt;
    background-color: rgb(55, 63, 106) !important;
    color: white !important;
    border: 1px solid #495057;
    padding: 8px 4px;
    vertical-align: middle;
}

.profit-schedule-table td {
    font-size: 11pt;
    border: 1px solid #dee2e6;
    vertical-align: middle;
    padding: 6px 4px;
    word-wrap: break-word;
}

.text-cumulative-total {
    color: #cca300 !important;
}

.summary-section {
    font-size: 12pt;
    margin-top: 2rem;
}

.summary-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    padding: 10px 0;
}

.summary-label {
    font-weight: bold;
    color: #495057;
    font-size: 12pt;
}

.summary-value {
    font-size: 13pt;
    font-weight: bold;
}

.info-section p {
    margin-bottom: 10px;
    font-size: 12pt;
}

.info-section strong {
    color: #495057;
}

/* Responsive for fullscreen modal */
@media (max-width: 768px) {
    .profit-schedule-modal {
        font-size: 11pt;
    }
    
    .profit-schedule-table {
        font-size: 9pt;
    }
    
    .profit-schedule-table th,
    .profit-schedule-table td {
        font-size: 9pt;
        padding: 4px 2px;
    }
    
    .summary-section {
        font-size: 10pt;
    }
    
    .summary-value {
        font-size: 11pt;
    }
    
    .summary-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 5px;
    }
}

/* Landscape optimization */
@media (min-width: 768px) {
    .profit-schedule-table {
        font-size: 12pt;
    }
    
    .profit-schedule-table th,
    .profit-schedule-table td {
        font-size: 12pt;
        padding: 8px 6px;
    }
    
    .summary-section {
        font-size: 13pt;
    }
    
    .summary-value {
        font-size: 14pt;
    }
}
</style> 