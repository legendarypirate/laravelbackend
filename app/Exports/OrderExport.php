<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class OrderExport implements FromArray, WithHeadings,WithTitle
{
    
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
        $this->sheetNames = [];
    }

    public function array():array
    {
        return $this->data;
    }
    

    public function headings(): array
    {
        return [
            'Дугаар',
            'Нэр',
            'Утас',
            'Хаяг',
            'Тэмдэглэл',
            'Үүссэн огноо',
            'Төлөв',
            'Жолооч',
        ];
    }

    public function title(): string
    {
        return 'delivery.xlsx';
    }
}