<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\DB;

class DeliveryDetailedReportExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithEvents
{
    protected $data;
    protected $total_delivered;
    protected $total_declined;
    protected $total_all;
    protected $total_price;
    protected $total_number;
    protected $filters;
    protected $rowCount = 0;

    public function __construct($data, $totals, $filters)
    {
        $this->data = $data;
        $this->total_delivered = $totals['total_delivered'];
        $this->total_declined = $totals['total_declined'];
        $this->total_all = $totals['total_all'];
        $this->total_price = $totals['total_price'];
        $this->total_number = $totals['total_number'];
        $this->filters = $filters;
    }

    public function collection()
    {
        $exportData = collect();
        
        // Row 1: Title
        $exportData->push(['Хүргэлтийн дэлгэрэнгүй тайлан', '', '', '', '', '', '', '', '', '', '', '', '']);
        $this->rowCount++;
        
        // Row 2: Export date
        $exportData->push(['Экспорт хийсэн огноо:', now()->format('Y-m-d H:i:s'), '', '', '', '', '', '', '', '', '', '']);
        $this->rowCount++;
        
        // Row 3: Empty row
        $exportData->push([]);
        $this->rowCount++;
        
        // Add filter info
        if (!empty($this->filters)) {
            foreach ($this->filters as $key => $value) {
                if ($key === 'start_date' && isset($this->filters['end_date'])) {
                    $exportData->push(['Хугацаа:', $value . ' - ' . $this->filters['end_date'], '', '', '', '', '', '', '', '', '', '']);
                    $this->rowCount++;
                } elseif ($key === 'end_date') {
                    continue; // Skip end_date as it's already shown with start_date
                } elseif ($value) {
                    $label = $this->getFilterLabel($key);
                    $exportData->push([$label . ':', $value, '', '', '', '', '', '', '', '', '', '']);
                    $this->rowCount++;
                }
            }
            // Empty row after filters
            $exportData->push([]);
            $this->rowCount++;
        }
        
        // Add empty row before table
        $exportData->push([]);
        $this->rowCount++;
        
        // Add table headers (Row where table starts)
        $exportData->push([
            '#',
            'Үүсгэсэн огноо',
            'Хүргэсэн огноо',
            'Харилцагч',
            'Утас',
            'Хаяг',
            'Төлөв',
            'Жолооч',
            'Үнэ',
            'Тоо',
            'Тэмдэглэл',
            'Z-код'
        ]);
        $tableStartRow = $this->rowCount; // Store table start row
        $this->rowCount++;
        
        // Add data rows
        foreach ($this->data as $delivery) {
            // Get driver name
            $driverName = '';
            if ($delivery->driver) {
                $driver = DB::table('users')->where('id', $delivery->driver)->first();
                $driverName = $driver ? $driver->name : '';
            }
            
            // Get status text
            $statusText = $this->getStatusText($delivery->status);
            
            $exportData->push([
                $delivery->track ?? '',
                $delivery->created_at ? date('Y-m-d H:i:s', strtotime($delivery->created_at)) : '',
                $delivery->delivered_at ? date('Y-m-d H:i:s', strtotime($delivery->delivered_at)) : '',
                $delivery->shop ?? '',
                $delivery->phone ?? '',
                $delivery->address ?? '',
                $statusText,
                $driverName,
                $delivery->price ?? 0,
                $delivery->number ?? 0,
                $delivery->note ?? '',
                $delivery->zcode ?? ''
            ]);
            $this->rowCount++;
        }
        
        // Add empty row before totals
        $exportData->push([]);
        $this->rowCount++;
        
        // Add total row
        $exportData->push([
            'НИЙТ:',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            (float)$this->total_price,
            (float)$this->total_number,
            '',
            ''
        ]);
        $totalRow = $this->rowCount;
        $this->rowCount++;
        
        // Add summary row
        $exportData->push([
            'Хүргэсэн: ' . (int)$this->total_delivered . ' | Цуцалсан: ' . (int)$this->total_declined . ' | Нийт: ' . (int)$this->total_all,
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            ''
        ]);
        $summaryRow = $this->rowCount;
        $this->rowCount++;
        
        // Store row counts for styling
        $this->tableStartRow = $tableStartRow;
        $this->totalRow = $totalRow;
        $this->summaryRow = $summaryRow;
        
        return $exportData;
    }

    public function headings(): array
    {
        return [];
    }

    public function styles(Worksheet $sheet)
    {
        // Style title (Row 1)
        $sheet->mergeCells('A1:L1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
        
        // Style filter info rows (Rows 2 to tableStartRow-2)
        for ($i = 2; $i < $this->tableStartRow - 1; $i++) {
            $sheet->getStyle("A{$i}")->getFont()->setBold(true);
        }
        
        // Style table header (tableStartRow)
        $sheet->getStyle("A{$this->tableStartRow}:L{$this->tableStartRow}")->getFont()->setBold(true);
        $sheet->getStyle("A{$this->tableStartRow}:L{$this->tableStartRow}")->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE9ECEF');
        
        // Style total row
        $sheet->getStyle("A{$this->totalRow}:L{$this->totalRow}")->getFont()->setBold(true);
        $sheet->getStyle("A{$this->totalRow}:L{$this->totalRow}")->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFF8F9FA');
        
        // Style summary row
        $sheet->getStyle("A{$this->summaryRow}:L{$this->summaryRow}")->getFont()->setBold(true);
        $sheet->getStyle("A{$this->summaryRow}:L{$this->summaryRow}")->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE9ECEF');
        
        // Add borders to table (from header to total row)
        $lastDataRow = $this->totalRow - 2; // -2 because of empty row before totals
        $sheet->getStyle("A{$this->tableStartRow}:L{$lastDataRow}")
            ->getBorders()->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        
        // Add borders to total and summary rows
        $sheet->getStyle("A{$this->totalRow}:L{$this->summaryRow}")
            ->getBorders()->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        
        // Format currency columns (columns I and J - price and number)
        $sheet->getStyle("I{$this->tableStartRow}:I{$lastDataRow}")
            ->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle("J{$this->tableStartRow}:J{$lastDataRow}")
            ->getNumberFormat()->setFormatCode('#,##0');
        
        // Format totals row
        $sheet->getStyle("I{$this->totalRow}:J{$this->totalRow}")
            ->getNumberFormat()->setFormatCode('#,##0');
        
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Auto-size all columns
                foreach (range('A', 'L') as $column) {
                    $event->sheet->getDelegate()->getColumnDimension($column)->setAutoSize(true);
                }
            },
        ];
    }

    private function getFilterLabel($key)
    {
        $labels = [
            'start_date' => 'Хугацаа',
            'driver_id' => 'Жолооч',
            'customer_id' => 'Харилцагч'
        ];
        
        return $labels[$key] ?? $key;
    }

    private function getStatusText($status)
    {
        $statuses = [
            1 => 'Шинэ',
            2 => 'Жолоочид',
            3 => 'Хүргэгдсэн',
            4 => 'Цуцалсан',
            5 => 'Буцаагдсан'
        ];
        
        return $statuses[$status] ?? 'Тодорхойгүй';
    }
}
