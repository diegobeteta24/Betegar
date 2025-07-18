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
use App\Models\Customer;

class CustomersExport implements 
    FromCollection,
    WithHeadings,
    WithMapping,
    ShouldAutoSize,
    WithStyles,
    WithEvents
{
    protected $customers;

    public function __construct($customers = null)
    {
        // Si no se pasan, exporta todos los clientes
        $this->customers = $customers ?: Customer::all();
    }

    /**
     * 1) Retorna la colección
     */
    public function collection()
    {
        return $this->customers;
    }

    /**
     * 2) Mapea cada Customer a un array de valores
     */
    public function map($customer): array
    {
        return [
            $customer->id,
            $customer->identity_id,
            $customer->document_number,
            $customer->name,
            $customer->address,
            $customer->email,
            $customer->phone,
            $customer->created_at->format('d/m/Y'),
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
     * 4) Estilos para la fila de encabezados (A1:H1)
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
