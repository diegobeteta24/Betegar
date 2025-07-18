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
use App\Models\Sale;

class SalesExport implements 
    FromCollection, 
    WithHeadings, 
    WithMapping, 
    ShouldAutoSize, 
    WithStyles,
    WithEvents
{
    protected $sales;

    public function __construct($sales = null)
    {
        // Si no se pasa colección, carga todas las ventas con relaciones
        $this->sales = $sales
            ?: Sale::with(['quote', 'customer', 'warehouse'])->get();
    }

    /**
     * 1) Retorna la colección a exportar
     */
    public function collection()
    {
        return $this->sales;
    }

    /**
     * 2) Mapea cada Sale a un array de valores
     */
    public function map($sale): array
    {
        return [
            $sale->id,
            $sale->voucher_type,
            $sale->serie,
            $sale->correlative,
            $sale->date->format('d/m/Y'),
            optional($sale->quote)->id,
            optional($sale->customer)->name,
            optional($sale->warehouse)->name,
            number_format($sale->total, 2),
            $sale->observation,
            $sale->created_at->format('d/m/Y'),
        ];
    }

    /**
     * 3) Encabezados de columna
     */
    public function headings(): array
    {
        return [
            'ID',
            'Tipo de comprobante',
            'Serie',
            'Correlativo',
            'Fecha',
            'Cotización ID',
            'Cliente',
            'Almacén',
            'Total',
            'Observación',
            'Creado el',
        ];
    }

    /**
     * 4) Estilos para la fila de encabezados (A1:K1)
     */
    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:K1')->applyFromArray([
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
