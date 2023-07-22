<?php

namespace App\Exports;

use App\Models\Delivery;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class DeliveryExport implements FromArray, WithHeadings,WithTitle
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
            'Үүссэн огноо',
            'Төлөв',
            'Жолооч',
            'Tracking',
            'Хот',
            'Дүүрэг',
            'Хороо',
            'Хотхон',
            'Байр',
            'Орц',
            'Орны код',
            'Давхар',
            'Тоот',
            'Өртөг',
            'авсан Дүн',
            'Хүргэлтийн үнэ',

            'Тайлбар',
            'Тайлбар1',
            'Тайлбар2',
            'Шинэчилсэн огноо',

            'Бараа'


        ];
    }

   

    public function title(): string
    {
        return 'req.xlsx';
    }
}