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
use App\Models\Transfer;

class TransfersExport implements 
    FromCollection, 
    WithHeadings, 
    WithMapping, 
    ShouldAutoSize, 
    WithStyles,
    WithEvents
{
    protected $transfers;

    public function __construct($transfers = null)
    {
        // Si no se pasa colección, carga todas con relaciones
        $this->transfers = $transfers
            ?: Transfer::with(['originWarehouse', 'destinationWarehouse'])->get();
    }

    /**
     * 1) Retorna la colección a exportar
     */
    public function collection()
    {
        return $this->transfers;
    }

    /**
     * 2) Mapea cada Transfer a un array de valores
     */
    public function map($transfer): array
    {
        return [
            $transfer->id,
            $transfer->serie,
            $transfer->correlative,
            $transfer->date->format('d/m/Y'),
            number_format($transfer->total, 2),
            $transfer->observation,
            optional($transfer->originWarehouse)->name,
            optional($transfer->destinationWarehouse)->name,
            $transfer->created_at->format('d/m/Y'),
        ];
    }

    /**
     * 3) Encabezados de columna
     */
    public function headings(): array
    {
        return [
            'ID',
            'Serie',
            'Correlativo',
            'Fecha',
            'Total',
            'Observación',
            'Bodega Origen',
            'Bodega Destino',
            'Creado el',
        ];
    }

    /**
     * 4) Aplica estilos a la fila de encabezados (A1:I1)
     */
    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:I1')->applyFromArray([
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
