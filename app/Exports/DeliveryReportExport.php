<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DeliveryReportExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithEvents
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
        $exportData->push(['Хүргэлтийн тайлан', '', '', '', '', '']);
        $this->rowCount++;
        
        // Row 2: Export date
        $exportData->push(['Экспорт хийсэн огноо:', now()->format('Y-m-d H:i:s'), '', '', '', '']);
        $this->rowCount++;
        
        // Row 3: Empty row
        $exportData->push([]);
        $this->rowCount++;
        
        // Add filter info
        if (!empty($this->filters)) {
            foreach ($this->filters as $key => $value) {
                if ($key === 'start_date' && isset($this->filters['end_date'])) {
                    $exportData->push(['Хугацаа:', $value . ' - ' . $this->filters['end_date'], '', '', '', '']);
                    $this->rowCount++;
                } elseif ($key === 'end_date') {
                    continue; // Skip end_date as it's already shown with start_date
                } elseif ($value) {
                    $label = $this->getFilterLabel($key);
                    $exportData->push([$label . ':', $value, '', '', '', '']);
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
        $exportData->push(['Огноо', 'Хүргэсэн', 'Цуцалсан', 'Нийт', 'Нийт үнэ', 'Нийт тоо']);
        $tableStartRow = $this->rowCount; // Store table start row
        $this->rowCount++;
        
        // Add data rows
        foreach ($this->data as $row) {
            $exportData->push([
                $row->date,
                (int)$row->delivered_count, // Keep as number
                (int)$row->declined_count,  // Keep as number
                (int)$row->total_count,     // Keep as number
                (float)$row->total_price,   // Keep as number for Excel
                (float)$row->total_number   // Keep as number for Excel
            ]);
            $this->rowCount++;
        }
        
        // Add total row
        $exportData->push([
            'НИЙТ:',
            (int)$this->total_delivered,
            (int)$this->total_declined,
            (int)$this->total_all,
            (float)$this->total_price,
            (float)$this->total_number
        ]);
        $totalRow = $this->rowCount;
        $this->rowCount++;
        
        // Store row counts for styling
        $this->tableStartRow = $tableStartRow;
        $this->totalRow = $totalRow;
        
        return $exportData;
    }

    public function headings(): array
    {
        return [];
    }

    public function styles(Worksheet $sheet)
    {
        // Style title (Row 1)
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
        
        // Style filter info rows (Rows 2 to tableStartRow-2)
        for ($i = 2; $i < $this->tableStartRow - 1; $i++) {
            $sheet->getStyle("A{$i}")->getFont()->setBold(true);
        }
        
        // Style table header (tableStartRow)
        $sheet->getStyle("A{$this->tableStartRow}:F{$this->tableStartRow}")->getFont()->setBold(true);
        $sheet->getStyle("A{$this->tableStartRow}:F{$this->tableStartRow}")->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE9ECEF');
        
        // Style total row
        $sheet->getStyle("A{$this->totalRow}:F{$this->totalRow}")->getFont()->setBold(true);
        $sheet->getStyle("A{$this->totalRow}:F{$this->totalRow}")->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFF8F9FA');
        
        // Add borders to table (from header to total row)
        $sheet->getStyle("A{$this->tableStartRow}:F{$this->totalRow}")
            ->getBorders()->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        
        // Format currency columns (columns E and F)
        $lastDataRow = $this->totalRow - 1;
        $sheet->getStyle("E{$this->tableStartRow}:E{$lastDataRow}")
            ->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle("F{$this->tableStartRow}:F{$lastDataRow}")
            ->getNumberFormat()->setFormatCode('#,##0');
        
        // Format totals row
        $sheet->getStyle("E{$this->totalRow}:F{$this->totalRow}")
            ->getNumberFormat()->setFormatCode('#,##0');
        
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Auto-size all columns
                $event->sheet->getDelegate()->getColumnDimension('A')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('B')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('C')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('D')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('E')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('F')->setAutoSize(true);
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
}
