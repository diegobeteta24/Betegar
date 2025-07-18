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
use App\Models\Purchase;

class PurchasesExport implements 
    FromCollection, 
    WithHeadings, 
    WithMapping, 
    ShouldAutoSize, 
    WithStyles,
    WithEvents
{
    protected $purchases;

    public function __construct($purchases = null)
    {
        // Si no pasa colección, carga todas con relaciones
        $this->purchases = $purchases
            ?: Purchase::with(['purchaseOrder', 'supplier', 'warehouse'])->get();
    }

    /**
     * 1) Devuelve la colección a exportar
     */
    public function collection()
    {
        return $this->purchases;
    }

    /**
     * 2) Mapea cada Purchase a un array de valores
     */
    public function map($purchase): array
    {
        return [
            $purchase->id,
            $purchase->voucher_type,
            $purchase->serie,
            $purchase->correlative,
            $purchase->date->format('d/m/Y'),
            optional($purchase->purchaseOrder)->id,
            optional($purchase->supplier)->name,
            optional($purchase->warehouse)->name,
            number_format($purchase->total, 2),
            $purchase->observation,
            $purchase->created_at->format('d/m/Y'),
        ];
    }

    /**
     * 3) Encabezados de columna
     */
    public function headings(): array
    {
        return [
            'ID',
            'Voucher Type',
            'Serie',
            'Correlativo',
            'Fecha',
            'Purchase Order ID',
            'Proveedor',
            'Almacén',
            'Total',
            'Observación',
            'Creado el',
        ];
    }

    /**
     * 4) Aplica estilos a la fila de encabezados (A1:K1)
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
     * 5) Congela la primera fila tras escribir encabezados
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
