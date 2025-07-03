<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithProperties;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use Carbon\Carbon;

class TransactionExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize, WithEvents, WithProperties
{
    protected $transactions;
    protected $filterParams;
    protected $plusTotal;
    protected $minusTotal;
    protected $search;
    protected $trx_type;
    protected $remark;
    protected $dateRange;

    /**
     * Create a new export instance.
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $transactions
     * @param  array  $filterParams
     * @return void
     */
    public function __construct($transactions, $filterParams = [])
    {
        $this->transactions = $transactions;
        $this->filterParams = $filterParams;
        $this->search = $filterParams['search'] ?? null;
        $this->trx_type = $filterParams['trx_type'] ?? null;
        $this->remark = $filterParams['remark'] ?? null;
        $this->dateRange = $filterParams['date'] ?? null;
        
        // Calculate totals
        $this->plusTotal = $transactions->where('trx_type', '+')->sum('amount');
        $this->minusTotal = $transactions->where('trx_type', '-')->sum('amount');
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->transactions;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Người dùng',
            'Mã giao dịch',
            'Ngày giao dịch',
            'Số tiền',
            'Số dư',
            'Chi tiết'
        ];
    }

    /**
     * @param mixed $transaction
     * @return array
     */
    public function map($transaction): array
    {
        return [
            $transaction->user ? $transaction->user->username . ' - ' . $transaction->user->fullname : 'N/A',
            $transaction->trx,
            Carbon::parse($transaction->created_at)->format('d/m/Y H:i:s'),
            $transaction->trx_type . ' ' . number_format($transaction->amount, 0, ',', '.'),
            number_format($transaction->post_balance, 0, ',', '.'),
            $transaction->details
        ];
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Danh sách giao dịch';
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        $lastRow = $this->transactions->count() + 1;
        
        return [
            // Style the first row (header row)
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4']
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
            'A2:F' . $lastRow => [
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
            // Style for amount column (D)
            'D2:E' . $lastRow => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_RIGHT
                ]
            ],
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
                $lastDataRow = $this->transactions->count() + 1;
                
                // Set row height for header
                $sheet->getDelegate()->getRowDimension(1)->setRowHeight(25);
                
                // Format each row based on transaction type
                for ($i = 2; $i <= $lastDataRow; $i++) {
                    $cellValue = $sheet->getDelegate()->getCell('D'.$i)->getValue();
                    if (strpos($cellValue, '+') === 0) {
                        // Green color for plus transactions
                        $sheet->getDelegate()->getStyle('D'.$i)->getFont()->setColor(new Color('1F8242'));
                    } elseif (strpos($cellValue, '-') === 0) {
                        // Red color for minus transactions
                        $sheet->getDelegate()->getStyle('D'.$i)->getFont()->setColor(new Color('C00000'));
                    }
                }
                
                // Add summary section
                $summaryRow = $lastDataRow + 2;
                
                // Add title for summary section
                $sheet->setCellValue('A' . $summaryRow, 'THỐNG KÊ GIAO DỊCH');
                $sheet->mergeCells('A' . $summaryRow . ':F' . $summaryRow);
                $sheet->getStyle('A' . $summaryRow)->applyFromArray([
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
                $summaryRow += 2;
                
                // Add filter criteria
                if ($this->search) {
                    $sheet->setCellValue('A' . $summaryRow, 'Tìm kiếm:');
                    $sheet->setCellValue('B' . $summaryRow, $this->search);
                    $sheet->getStyle('A' . $summaryRow)->applyFromArray(['font' => ['bold' => true]]);
                    $summaryRow++;
                }
                
                if ($this->trx_type) {
                    $sheet->setCellValue('A' . $summaryRow, 'Loại giao dịch:');
                    $sheet->setCellValue('B' . $summaryRow, $this->trx_type == '+' ? 'Cộng' : 'Trừ');
                    $sheet->getStyle('A' . $summaryRow)->applyFromArray(['font' => ['bold' => true]]);
                    $summaryRow++;
                }
                
                if ($this->remark) {
                    $sheet->setCellValue('A' . $summaryRow, 'Ghi chú:');
                    $sheet->setCellValue('B' . $summaryRow, $this->remark);
                    $sheet->getStyle('A' . $summaryRow)->applyFromArray(['font' => ['bold' => true]]);
                    $summaryRow++;
                }
                
                if ($this->dateRange) {
                    $sheet->setCellValue('A' . $summaryRow, 'Thời gian:');
                    $sheet->setCellValue('B' . $summaryRow, $this->dateRange);
                    $sheet->getStyle('A' . $summaryRow)->applyFromArray(['font' => ['bold' => true]]);
                    $summaryRow++;
                }
                
                // Add empty row
                $summaryRow++;
                
                // Add transaction totals
                $sheet->setCellValue('A' . $summaryRow, 'Tổng giao dịch cộng:');
                $sheet->setCellValue('B' . $summaryRow, number_format($this->plusTotal, 0, ',', '.'));
                $sheet->getStyle('A' . $summaryRow)->applyFromArray(['font' => ['bold' => true]]);
                $sheet->getStyle('B' . $summaryRow)->applyFromArray([
                    'font' => ['color' => ['rgb' => '1F8242']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT]
                ]);
                
                $summaryRow++;
                $sheet->setCellValue('A' . $summaryRow, 'Tổng giao dịch trừ:');
                $sheet->setCellValue('B' . $summaryRow, number_format($this->minusTotal, 0, ',', '.'));
                $sheet->getStyle('A' . $summaryRow)->applyFromArray(['font' => ['bold' => true]]);
                $sheet->getStyle('B' . $summaryRow)->applyFromArray([
                    'font' => ['color' => ['rgb' => 'C00000']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT]
                ]);
                
                $summaryRow++;
                $sheet->setCellValue('A' . $summaryRow, 'Cân đối:');
                $sheet->setCellValue('B' . $summaryRow, number_format($this->plusTotal - $this->minusTotal, 0, ',', '.'));
                $sheet->getStyle('A' . $summaryRow)->applyFromArray(['font' => ['bold' => true]]);
                
                $balanceColor = ($this->plusTotal - $this->minusTotal) >= 0 ? '1F8242' : 'C00000';
                $sheet->getStyle('B' . $summaryRow)->applyFromArray([
                    'font' => ['color' => ['rgb' => $balanceColor]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT]
                ]);
                
                // Add total transaction count
                $summaryRow += 2;
                $sheet->setCellValue('A' . $summaryRow, 'Tổng số giao dịch:');
                $sheet->setCellValue('B' . $summaryRow, $this->transactions->count());
                $sheet->getStyle('A' . $summaryRow)->applyFromArray(['font' => ['bold' => true]]);
                $sheet->getStyle('B' . $summaryRow)->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT]
                ]);
                
                // Add export date
                $summaryRow += 2;
                $sheet->setCellValue('A' . $summaryRow, 'Ngày xuất báo cáo:');
                $sheet->setCellValue('B' . $summaryRow, Carbon::now()->format('d/m/Y H:i:s'));
                $sheet->getStyle('A' . $summaryRow)->applyFromArray(['font' => ['bold' => true]]);
            }
        ];
    }

    /**
     * @return array
     */
    public function properties(): array
    {
        return [
            'creator' => 'VineCom System',
            'title' => 'Danh sách giao dịch',
            'description' => 'Báo cáo danh sách giao dịch hệ thống',
            'subject' => 'Báo cáo giao dịch',
            'keywords' => 'giao dịch,transaction,export,excel',
            'category' => 'Báo cáo',
            'company' => 'VineCom',
        ];
    }
} 