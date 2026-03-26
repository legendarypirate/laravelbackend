<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class DeliveryExport implements FromArray, WithHeadings, WithTitle
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'Track ID',
            'Огноо',
            'Хүргэсэн огноо',
        'Жолооч тэмдэглэл',

            'Төрөл',
            'Харилцагч',
            'z-код',
            'Мерчант нэр',
            'Утас 1, Утас 2',
            'Илгээгчийн хаяг[дэлгэрэнгүй]',
            'Барааны мэдээлэл',
            'Тоо ширхэг',
            'Хүлээн авагчийн нэр',
            'Утас 1, Утас 2',
            'Хүлээн авагчийн хаяг',
            'Нэмэлт тайлбар(ирэхээсээ өмнө залгах г.м)',
            'Барааны тооцоо',
            'Баталгаажилт',
            'Жолооч',
            'Төлөв',
        ];
    }

    public function title(): string
    {
        return 'Delivery Export';
    }
}
