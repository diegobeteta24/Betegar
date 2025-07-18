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
use App\Models\Movement;

class MovementsExport implements 
    FromCollection, 
    WithHeadings, 
    WithMapping, 
    ShouldAutoSize, 
    WithStyles,
    WithEvents
{
    protected $movements;

    public function __construct($movements = null)
    {
        // Si no se pasa colección, trae todas las relaciones necesarias
        $this->movements = $movements
            ?: Movement::with(['warehouse', 'reason'])->get();
    }

    /**
     * 1) Devuelve la colección a exportar
     */
    public function collection()
    {
        return $this->movements;
    }

    /**
     * 2) Mapea cada Movement a un array de valores
     */
    public function map($movement): array
    {
        return [
            $movement->id,
            $movement->type,
            $movement->serie,
            $movement->correlative,
            $movement->date->format('d/m/Y H:i'),
            optional($movement->warehouse)->name,
            number_format($movement->total, 2),
            $movement->observation,
            optional($movement->reason)->name,
            $movement->created_at->format('d/m/Y'),
        ];
    }

    /**
     * 3) Encabezados de columna
     */
    public function headings(): array
    {
        return [
            'ID',
            'Tipo',
            'Serie',
            'Correlativo',
            'Fecha',
            'Almacén',
            'Total',
            'Observación',
            'Razón',
            'Creado el',
        ];
    }

    /**
     * 4) Aplica estilos a la fila de encabezados (A1:J1)
     */
    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:J1')->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['argb' => 'FFFFFFFF'],
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF1E40AF'], // azul oscuro
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ]);
    }

    /**
     * 5) Congela la primera fila tras generar encabezados
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
