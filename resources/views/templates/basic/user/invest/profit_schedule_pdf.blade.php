<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Bảng Lãi Dự Kiến</title>
    <style>
        @font-face {
            font-family: 'Times New Roman';
            src: local('Times New Roman'), local('TimesNewRoman'), url('https://fonts.cdnfonts.com/s/15307/TimesNewRoman.woff') format('woff');
            font-weight: normal;
            font-style: normal;
        }
        @page {
            size: A4 landscape;
            margin: 15mm;
        }
        body {
            font-family: 'Times New Roman', 'DejaVu Sans', Times, serif;
            font-size: 11pt;
            line-height: 1.3;
            color: #000000;
        }
        .header {
            text-align: center;
            font-weight: bold;
            margin-bottom: 15px;
        }
        .main-title {
            margin: 10px 0;
            font-size: 16pt;
            text-transform: uppercase;
            font-weight: bold;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #333;
            padding: 5px 8px;
            text-align: left;
            font-size: 10pt;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .bold { font-weight: bold; }
        .info-section p {
            margin: 3px 0;
            font-size: 12pt;
        }
    </style>
</head>
<body>
    <div class="header">
        <div>CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM</div>
        <div>Độc lập - Tự do - Hạnh phúc</div>
        <div>-------o0o-------</div>
    </div>
    <div class="main-title">BẢNG LÃI DỰ KIẾN</div>

    <div class="info-section">
        <p><span class="bold">Dự án đầu tư:</span> {{ $project->title }}</p>
        <p><span class="bold">Nhà đầu tư:</span> {{ $user ? $user->fullname : 'Khách' }}</p>
        <p><span class="bold">Số tiền đầu tư:</span> <span class="bold">{{ number_format($investment_amount, 0, ',', '.') }} VNĐ</span></p>
        <p><span class="bold">Ngày lập:</span> {{ date('d/m/Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>STT</th>
                <th>Kỳ</th>
                <th>Ngày bắt đầu</th>
                <th>Ngày kết thúc</th>
                <th>Số ngày</th>
                <th>Lãi suất (%/năm)</th>
                <th>Gốc đầu kỳ (VNĐ)</th>
                <th>Lãi kỳ (VNĐ)</th>
                <th>Tổng tích lũy (VNĐ)</th>
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
                <td class="text-right">{{ number_format($period['principal'], 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($period['period_interest'], 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($period['cumulative_total'], 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 20px; padding: 15px; border: 1px solid #ddd; background-color: #f9f9f9; border-radius: 5px;">
        <div style="margin-bottom: 15px;">
            <h4 style="margin: 0 0 10px 0; color: #333; font-size: 14pt;">TỔNG KẾT</h4>
            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                <span style="font-weight: bold;">Lãi trung bình mỗi kỳ:</span>
                <span style="font-weight: bold; color: #007bff;">
                    ~{{ number_format($schedule ? round(array_sum(array_column($schedule, 'period_interest')) / count($schedule), 0) : 0, 0, ',', '.') }} VNĐ
                </span>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                <span style="font-weight: bold;">Tổng lãi dự kiến:</span>
                <span style="font-weight: bold; color: #28a745;">
                    {{ number_format($schedule ? array_sum(array_column($schedule, 'period_interest')) : 0, 0, ',', '.') }} VNĐ
                </span>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                <span style="font-weight: bold;">Tổng giá trị đầu tư đến đáo hạn:</span>
                <span style="font-weight: bold; color: #dc3545;">
                    {{ number_format($investment_amount + ($schedule ? array_sum(array_column($schedule, 'period_interest')) : 0), 0, ',', '.') }} VNĐ
                </span>
            </div>
        </div>
        
        <div style="border-top: 1px solid #ddd; padding-top: 10px;">
            <p style="margin: 0; font-style: italic; color: #666; font-size: 10pt;">
                <strong>Lưu ý:</strong> Bảng này mang tính chất tham khảo, lãi thực nhận phụ thuộc vào ngày thực tế và điều khoản hợp đồng đầu tư.
            </p>
        </div>
    </div>
</body>
</html> 