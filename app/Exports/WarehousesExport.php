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
use App\Models\Warehouse;

class WarehousesExport implements 
    FromCollection, 
    WithHeadings, 
    WithMapping, 
    ShouldAutoSize, 
    WithStyles,
    WithEvents
{
    protected $warehouses;

    public function __construct($warehouses = null)
    {
        // Si no pasas la colección, carga todas las bodegas
        $this->warehouses = $warehouses ?: Warehouse::all();
    }

    /**
     * 1) Colección a exportar
     */
    public function collection()
    {
        return $this->warehouses;
    }

    /**
     * 2) Mapea cada Warehouse a un array de valores
     */
    public function map($warehouse): array
    {
        return [
            $warehouse->id,
            $warehouse->name,
            $warehouse->location,
            $warehouse->created_at->format('d/m/Y'),
        ];
    }

    /**
     * 3) Encabezados de columna
     */
    public function headings(): array
    {
        return [
            'ID',
            'Nombre',
            'Ubicación',
            'Fecha de creación',
        ];
    }

    /**
     * 4) Aplica estilos a la fila de encabezados (A1:D1)
     */
    public function styles(Worksheet $sheet)
    {
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
     * 5) Eventos: congelar la fila de encabezados
     */
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
