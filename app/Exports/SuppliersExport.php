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
use App\Models\Supplier;

class SuppliersExport implements 
    FromCollection, 
    WithHeadings, 
    WithMapping, 
    ShouldAutoSize, 
    WithStyles,
    WithEvents
{
    protected $suppliers;

    public function __construct($suppliers = null)
    {
        // Si no pasas la colección, carga todos los proveedores
        $this->suppliers = $suppliers ?: Supplier::all();
    }

    /**
     * 1) Colección a exportar
     */
    public function collection()
    {
        return $this->suppliers;
    }

    /**
     * 2) Mapea cada Supplier a un array de valores
     */
    public function map($supplier): array
    {
        return [
            $supplier->id,
            $supplier->identity_id,
            $supplier->document_number,
            $supplier->name,
            $supplier->address,
            $supplier->email,
            $supplier->phone,
            $supplier->created_at->format('d/m/Y'),
        ];
    }

    /**
     * 3) Encabezados de columna
     */
    public function headings(): array
    {
        return [
            'ID',
            'Identity ID',
            'Número de documento',
            'Nombre',
            'Dirección',
            'Email',
            'Teléfono',
            'Fecha de creación',
        ];
    }

    /**
     * 4) Aplica estilos a la fila de encabezados (A1:H1)
     */
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

    /**
     * 5) Congela la fila de encabezados
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
