<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ProductsTemplateExport implements FromArray, WithHeadings, WithStyles, ShouldAutoSize, WithEvents
{
    public function array(): array
    {
        return [
            [
                'Producto de ejemplo',
                'Descripción del producto de ejemplo',
                'SKU12345',
                '10.99',
                '1',
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'name',
            'description',
            'sku',
            'price',
            'category_id',
        ];
    }

    /**
     * Aplica estilos a la hoja.
     */
    public function styles(Worksheet $sheet)
    {
        // Estiliza fila de encabezados (A1:E1)
        $sheet->getStyle('A1:E1')->applyFromArray([
            'font' => [
                'bold'  => true,
                'size'  => 12,
                'color' => ['argb' => 'FFFFFFFF'],
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF4CAF50'],  // verde
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);
    }

    /**
     * Registra eventos para la hoja.
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Congela la primera fila
                $sheet->freezePane('A2');

                // Ajusta altura de la fila de encabezados
                $sheet->getRowDimension(1)->setRowHeight(20);
            },
        ];
    }
}
