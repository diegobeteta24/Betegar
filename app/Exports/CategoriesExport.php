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
use App\Models\Category; // Modelo en singular

class CategoriesExport implements 
    FromCollection, 
    WithHeadings, 
    WithMapping, 
    ShouldAutoSize, 
    WithStyles,
    WithEvents
{
    protected $categories;

    public function __construct($categories = null)
    {
        // Si no pasas colección, carga todas
        $this->categories = $categories ?: Category::all();
    }

    /**
     * 1) Devuelve la colección
     */
    public function collection()
    {
        return $this->categories;
    }

    /**
     * 2) Mapea cada categoría a un array de celdas
     */
    public function map($category): array
    {
        return [
            $category->id,
            $category->name,
            $category->description,
            $category->created_at->format('d/m/Y'), // Fecha de creación
        ];
    }

    /**
     * 3) Los encabezados, uno por cada columna del map()
     */
    public function headings(): array
    {
        return [
            'ID',
            'Nombre',
            'Descripción',
            'Fecha de creación',
        ];
    }

    /**
     * 4) Estilos en la fila 1
     */
    public function styles(Worksheet $sheet)
    {
        // A1:D1 porque tenemos 4 columnas
        $sheet->getStyle('A1:D1')->applyFromArray([
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

    /**
     * 5) Eventos adicionales
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                /** @var Worksheet $sheet */
                $sheet = $event->sheet->getDelegate();

                // Congela la primera fila (menú de encabezados)
                $sheet->freezePane('A2');
            },
        ];
    }
}
