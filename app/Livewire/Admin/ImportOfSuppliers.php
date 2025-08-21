<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SuppliersImport;

class ImportOfSuppliers extends Component
{
    use WithFileUploads;

    public $file;
    public $errors = [];
    public $importedCount = 0;

    public function downloadTemplate()
    {
        $template = [[
            'identity_id' => 1,
            'document_number' => 'CF',
            'name' => 'Proveedor Ejemplo',
            'address' => 'Dirección ejemplo',
            'email' => 'proveedor@example.com',
            'phone' => '5555-0000',
        ]];
        $headings = array_keys($template[0]);
        $export = new class($template,$headings) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings {public function __construct(private $rows, private $headings){} public function array(): array {return $this->rows;} public function headings(): array {return $this->headings;}};
        return \Maatwebsite\Excel\Facades\Excel::download($export,'suppliers_template.xlsx');
    }

    public function importSuppliers()
    {
        $this->validate([
            'file' => 'required|file|mimes:xlsx,csv,xls',
        ]);

        $import = new SuppliersImport();
        Excel::import($import, $this->file);

        $this->errors = $import->getErrors();
        $this->importedCount = $import->getImportedCount();

        if (count($this->errors) === 0) {
            return redirect()
                ->route('admin.suppliers.index')
                ->with('sweet-alert', [
                    'icon'    => 'success',
                    'title'   => '¡Proveedores importados!',
                    'text'    => "Se han importado {$this->importedCount} proveedores correctamente.",
                    'timer'   => 3000,
                    'showConfirmButton' => false,
                ]);
        }
    }

    public function render()
    {
        return view('livewire.admin.import-of-suppliers');
    }
}
