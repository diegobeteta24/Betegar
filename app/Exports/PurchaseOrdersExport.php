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
use App\Models\PurchaseOrder;

class PurchaseOrdersExport implements 
    FromCollection, 
    WithHeadings, 
    WithMapping, 
    ShouldAutoSize, 
    WithStyles,
    WithEvents
{
    protected $orders;

    public function __construct($orders = null)
    {
        // Si no pasas colección, coge todas con el proveedor cargado
        $this->orders = $orders 
            ?: PurchaseOrder::with('supplier')->get();
    }

    /**
     * 1) Devuelve la colección a exportar
     */
    public function collection()
    {
        return $this->orders;
    }

    /**
     * 2) Mapea cada PurchaseOrder a un array
     */
    public function map($order): array
    {
        return [
            $order->id,
            $order->voucher_type,
            $order->serie,
            $order->correlative,
            $order->date->format('d/m/Y H:i'),
            optional($order->supplier)->name,
            number_format($order->total, 2),
            $order->observation,
            $order->created_at->format('d/m/Y'),
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
            'Proveedor',
            'Total',
            'Observación',
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
                'startColor' => ['argb' => 'FF1E40AF'],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ]);
    }

    /**
     * 5) Congela la primera fila
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
