<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\{
    FromCollection,
    WithHeadings,
    WithMapping,
    ShouldAutoSize,
    WithStyles,
    WithEvents
};
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use App\Models\Product;

class ProductsExport implements 
    FromCollection, 
    WithHeadings, 
    WithMapping, 
    ShouldAutoSize, 
    WithStyles,
    WithEvents
{
    protected $products;

    public function __construct($products = null)
    {
        $this->products = $products ?: Product::with('category')->get();
    }

    public function collection()
    {
        return $this->products;
    }

    public function map($product): array
    {
        return [
            $product->id,
            $product->name,
            $product->description,
            $product->sku,
            number_format($product->price, 2),
            $product->stock,                               // <-- Stock añadido
            optional($product->category)->name,
            $product->created_at->format('d/m/Y'),
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nombre',
            'Descripción',
            'SKU',
            'Precio',
            'Stock',                                       // <-- Encabezado de Stock
            'Categoría',
            'Fecha de creación',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:H1')->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['argb' => 'FFFFFFFF'],
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF1E40AF'],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                /** @var Worksheet $sheet */
                $sheet = $event->sheet->getDelegate();
                $sheet->freezePane('A2');
            },
        ];
    }
}
