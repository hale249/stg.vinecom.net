<?php

namespace App\Exports;

use App\Models\Invest;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithProperties;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Carbon\Carbon;

class ContractRevenueExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize, WithEvents, WithDrawings, WithProperties
{
    protected $contracts;
    protected $totalContractCount;
    protected $totalContractAmount;
    protected $totalEarnings;
    protected $dateRange;
    protected $status;
    protected $statusCounts;

    /**
     * Create a new export instance.
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $contracts
     * @param  int  $totalContractCount
     * @param  float  $totalContractAmount
     * @param  float  $totalEarnings
     * @param  string|null  $dateRange
     * @param  int|null  $status
     * @return void
     */
    public function __construct($contracts, $totalContractCount, $totalContractAmount, $totalEarnings, $dateRange = null, $status = null)
    {
        $this->contracts = $contracts;
        $this->totalContractCount = $totalContractCount;
        $this->totalContractAmount = $totalContractAmount;
        $this->totalEarnings = $totalEarnings;
        $this->dateRange = $dateRange;
        $this->status = $status;
        
        // Calculate contract counts by status
        $this->statusCounts = [
            0 => 0, // Pending
            5 => 0, // Pending Admin Review
            1 => 0, // Accepted
            2 => 0, // Running
            3 => 0, // Completed
            4 => 0, // Closed
            9 => 0  // Canceled
        ];
        
        foreach ($contracts as $contract) {
            if (isset($this->statusCounts[$contract->status])) {
                $this->statusCounts[$contract->status]++;
            }
        }
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->contracts;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Mã hợp đồng',
            'Dự án',
            'Khách hàng',
            'Số lượng',
            'Đơn giá',
            'Tổng giá trị',
            'Lợi nhuận',
            'Ngày tạo',
            'Trạng thái'
        ];
    }

    /**
     * @param mixed $row
     *
     * @return array
     */
    public function map($contract): array
    {
        // Get status text
        $statusText = '';
        switch ($contract->status) {
            case 0:
                $statusText = 'Chờ xử lý';
                break;
            case 5:
                $statusText = 'Chờ duyệt';
                break;
            case 1:
                $statusText = 'Đã chấp nhận';
                break;
            case 2:
                $statusText = 'Đang hoạt động';
                break;
            case 3:
                $statusText = 'Hoàn thành';
                break;
            case 4:
                $statusText = 'Đã đóng';
                break;
            case 9:
                $statusText = 'Đã hủy';
                break;
            default:
                $statusText = 'Không xác định';
        }

        return [
            $contract->invest_no,
            $contract->project->title ?? 'N/A',
            $contract->user->fullname ?? 'N/A',
            $contract->quantity,
            number_format($contract->unit_price, 0, ',', '.') . ' VND',
            number_format($contract->total_price, 0, ',', '.') . ' VND',
            number_format($contract->total_earning, 0, ',', '.') . ' VND',
            Carbon::parse($contract->created_at)->format('d/m/Y H:i'),
            $statusText
        ];
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Doanh số theo hợp đồng';
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row (header row)
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F81BD']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ]
                ]
            ],
            // Style for all data rows
            'A2:I' . ($this->contracts->count() + 1) => [
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC']
                    ]
                ]
            ],
            // Style for numeric columns
            'D2:G' . ($this->contracts->count() + 1) => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_RIGHT
                ]
            ]
        ];
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet;
                
                // Set row height for header
                $sheet->getDelegate()->getRowDimension(1)->setRowHeight(25);
                
                // Add summary section after the data
                $lastRow = $this->contracts->count() + 3;
                
                // Add title for summary section
                $sheet->setCellValue('A' . $lastRow, 'TỔNG KẾT DOANH SỐ');
                $sheet->mergeCells('A' . $lastRow . ':I' . $lastRow);
                $sheet->getStyle('A' . $lastRow)->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E7EFFA']
                    ]
                ]);
                
                // Add filter information
                $lastRow += 2;
                if ($this->dateRange) {
                    $sheet->setCellValue('A' . $lastRow, 'Khoảng thời gian:');
                    $sheet->setCellValue('B' . $lastRow, $this->dateRange);
                    $sheet->getStyle('A' . $lastRow)->applyFromArray(['font' => ['bold' => true]]);
                    $lastRow++;
                }
                
                if ($this->status !== null) {
                    $statusText = '';
                    switch ($this->status) {
                        case 0:
                            $statusText = 'Chờ xử lý';
                            break;
                        case 5:
                            $statusText = 'Chờ duyệt';
                            break;
                        case 1:
                            $statusText = 'Đã chấp nhận';
                            break;
                        case 2:
                            $statusText = 'Đang hoạt động';
                            break;
                        case 3:
                            $statusText = 'Hoàn thành';
                            break;
                        case 4:
                            $statusText = 'Đã đóng';
                            break;
                        case 9:
                            $statusText = 'Đã hủy';
                            break;
                        default:
                            $statusText = 'Tất cả';
                    }
                    $sheet->setCellValue('A' . $lastRow, 'Trạng thái:');
                    $sheet->setCellValue('B' . $lastRow, $statusText);
                    $sheet->getStyle('A' . $lastRow)->applyFromArray(['font' => ['bold' => true]]);
                    $lastRow++;
                }
                
                // Add summary data
                $lastRow += 1;
                $sheet->setCellValue('A' . $lastRow, 'Tổng số hợp đồng đang hoạt động:');
                $sheet->setCellValue('C' . $lastRow, $this->totalContractCount);
                $sheet->getStyle('A' . $lastRow)->applyFromArray(['font' => ['bold' => true]]);
                $sheet->getStyle('C' . $lastRow)->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT]
                ]);
                
                $lastRow++;
                $sheet->setCellValue('A' . $lastRow, 'Tổng giá trị hợp đồng đang hoạt động:');
                $sheet->setCellValue('C' . $lastRow, number_format($this->totalContractAmount, 0, ',', '.') . ' VND');
                $sheet->getStyle('A' . $lastRow)->applyFromArray(['font' => ['bold' => true]]);
                $sheet->getStyle('C' . $lastRow)->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => '0070C0']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT]
                ]);
                
                $lastRow++;
                $sheet->setCellValue('A' . $lastRow, 'Tổng lợi nhuận:');
                $sheet->setCellValue('C' . $lastRow, number_format($this->totalEarnings, 0, ',', '.') . ' VND');
                $sheet->getStyle('A' . $lastRow)->applyFromArray(['font' => ['bold' => true]]);
                $sheet->getStyle('C' . $lastRow)->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => '00B050']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT]
                ]);
                
                // Add export date
                $lastRow += 2;
                $sheet->setCellValue('A' . $lastRow, 'Ngày xuất báo cáo: ' . Carbon::now()->format('d/m/Y H:i'));
                $sheet->getStyle('A' . $lastRow)->applyFromArray(['font' => ['italic' => true]]);
                
                // Auto-fit columns
                foreach (range('A', 'I') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
                
                // Add status summary table
                $lastRow += 2;
                $sheet->setCellValue('A' . $lastRow, 'THỐNG KÊ THEO TRẠNG THÁI');
                $sheet->mergeCells('A' . $lastRow . ':C' . $lastRow);
                $sheet->getStyle('A' . $lastRow)->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E7EFFA']
                    ]
                ]);
                
                $lastRow++;
                $sheet->setCellValue('A' . $lastRow, 'Trạng thái');
                $sheet->setCellValue('B' . $lastRow, 'Số lượng');
                $sheet->setCellValue('C' . $lastRow, 'Tỷ lệ');
                $sheet->getStyle('A' . $lastRow . ':C' . $lastRow)->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'D9D9D9']
                    ]
                ]);
                
                $totalContracts = array_sum($this->statusCounts);
                $totalContracts = $totalContracts > 0 ? $totalContracts : 1; // Avoid division by zero
                
                $lastRow++;
                $sheet->setCellValue('A' . $lastRow, 'Chờ xử lý');
                $sheet->setCellValue('B' . $lastRow, $this->statusCounts[0]);
                $sheet->setCellValue('C' . $lastRow, round(($this->statusCounts[0] / $totalContracts) * 100, 2) . '%');
                
                $lastRow++;
                $sheet->setCellValue('A' . $lastRow, 'Chờ duyệt');
                $sheet->setCellValue('B' . $lastRow, $this->statusCounts[5]);
                $sheet->setCellValue('C' . $lastRow, round(($this->statusCounts[5] / $totalContracts) * 100, 2) . '%');
                
                $lastRow++;
                $sheet->setCellValue('A' . $lastRow, 'Đã chấp nhận');
                $sheet->setCellValue('B' . $lastRow, $this->statusCounts[1]);
                $sheet->setCellValue('C' . $lastRow, round(($this->statusCounts[1] / $totalContracts) * 100, 2) . '%');
                
                $lastRow++;
                $sheet->setCellValue('A' . $lastRow, 'Đang hoạt động');
                $sheet->setCellValue('B' . $lastRow, $this->statusCounts[2]);
                $sheet->setCellValue('C' . $lastRow, round(($this->statusCounts[2] / $totalContracts) * 100, 2) . '%');
                $sheet->getStyle('A' . $lastRow . ':C' . $lastRow)->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E2EFDA']
                    ]
                ]);
                
                $lastRow++;
                $sheet->setCellValue('A' . $lastRow, 'Hoàn thành');
                $sheet->setCellValue('B' . $lastRow, $this->statusCounts[3]);
                $sheet->setCellValue('C' . $lastRow, round(($this->statusCounts[3] / $totalContracts) * 100, 2) . '%');
                
                $lastRow++;
                $sheet->setCellValue('A' . $lastRow, 'Đã đóng');
                $sheet->setCellValue('B' . $lastRow, $this->statusCounts[4]);
                $sheet->setCellValue('C' . $lastRow, round(($this->statusCounts[4] / $totalContracts) * 100, 2) . '%');
                
                $lastRow++;
                $sheet->setCellValue('A' . $lastRow, 'Đã hủy');
                $sheet->setCellValue('B' . $lastRow, $this->statusCounts[9]);
                $sheet->setCellValue('C' . $lastRow, round(($this->statusCounts[9] / $totalContracts) * 100, 2) . '%');
                
                $lastRow++;
                $sheet->setCellValue('A' . $lastRow, 'Tổng cộng');
                $sheet->setCellValue('B' . $lastRow, $totalContracts);
                $sheet->setCellValue('C' . $lastRow, '100%');
                $sheet->getStyle('A' . $lastRow . ':C' . $lastRow)->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'D9D9D9']
                    ]
                ]);
                
                // Style the status summary table
                $sheet->getStyle('A' . ($lastRow - 7) . ':C' . $lastRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000']
                        ]
                    ]
                ]);
                
                $sheet->getStyle('B' . ($lastRow - 7) . ':B' . $lastRow)->applyFromArray([
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_RIGHT
                    ]
                ]);
                
                $sheet->getStyle('C' . ($lastRow - 7) . ':C' . $lastRow)->applyFromArray([
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_RIGHT
                    ]
                ]);
            }
        ];
    }

    /**
     * @return \PhpOffice\PhpSpreadsheet\Worksheet\BaseDrawing[]
     */
    public function drawings()
    {
        // Add logo to the sheet
        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Company Logo');
        
        // Check if logo file exists, otherwise use default image
        $logoPath = public_path('assets/images/logo_icon/logo.png');
        if (!file_exists($logoPath)) {
            $logoPath = public_path('assets/images/default.png');
        }
        
        $drawing->setPath($logoPath);
        $drawing->setHeight(70);
        $drawing->setCoordinates('A1');
        
        return [$drawing];
    }

    /**
     * @return array
     */
    public function properties(): array
    {
        return [
            'creator'        => config('app.name'),
            'lastModifiedBy' => config('app.name'),
            'title'          => 'Báo cáo doanh số theo hợp đồng',
            'description'    => 'Báo cáo doanh số theo hợp đồng',
            'subject'        => 'Báo cáo doanh số',
            'keywords'       => 'doanh số, hợp đồng, báo cáo',
            'category'       => 'Báo cáo',
            'manager'        => 'Admin',
            'company'        => config('app.name'),
        ];
    }
}
