<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ServicesTemplateExport implements FromArray, WithHeadings, ShouldAutoSize
{
    public function array(): array
    {
        return [[
            'Servicio de ejemplo',
            'Descripción ejemplo',
            '123', // category_id
            '50.00',
        ]];
    }

    public function headings(): array
    {
        return ['name','description','category_id','price'];
    }
}
