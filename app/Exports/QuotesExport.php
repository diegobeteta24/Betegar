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
use App\Models\Quote;

class QuotesExport implements 
    FromCollection, 
    WithHeadings, 
    WithMapping, 
    ShouldAutoSize, 
    WithStyles,
    WithEvents
{
    protected $quotes;

    public function __construct($quotes = null)
    {
        // Si no se pasa colección, carga todas las cotizaciones con cliente
        $this->quotes = $quotes ?: Quote::with('customer')->get();
    }

    /**
     * 1) Retorna la colección
     */
    public function collection()
    {
        return $this->quotes;
    }

    /**
     * 2) Mapea cada Quote a un array de valores
     */
    public function map($quote): array
    {
        return [
            $quote->id,
            $quote->voucher_type,
            $quote->serie,
            $quote->correlative,
            $quote->date->format('d/m/Y'),
            optional($quote->customer)->name,
            number_format($quote->total, 2),
            $quote->observation,
            $quote->created_at->format('d/m/Y'),
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
            'Cliente',
            'Total',
            'Observación',
            'Creado el',
        ];
    }

    /**
     * 4) Estilos para la fila de encabezados (A1:I1)
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
