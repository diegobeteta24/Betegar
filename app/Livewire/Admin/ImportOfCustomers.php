<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\CustomersImport;
// (Opcional) Podríamos crear un CustomersTemplateExport dedicado si se necesita más formato.

class ImportOfCustomers extends Component
{
    use WithFileUploads;

    public $file;
    public $errors = [];
    public $importedCount = 0;

    public function downloadTemplate()
    {
        // We'll build a simple inline template without dedicated export class (or create one later)
        $template = [[
            'identity_id' => 1,
            'document_number' => 'CF',
            'name' => 'Cliente Ejemplo',
            'address' => 'Dirección ejemplo',
            'email' => 'cliente@example.com',
            'phone' => '5555-5555',
        ]];
        $headings = array_keys($template[0]);
        $export = new class($template,$headings) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings {public function __construct(private $rows, private $headings){} public function array(): array {return $this->rows;} public function headings(): array {return $this->headings;}};
        return \Maatwebsite\Excel\Facades\Excel::download($export,'customers_template.xlsx');
    }

    public function importCustomers()
    {
        $this->validate([
            'file' => 'required|file|mimes:xlsx,csv,xls',
        ]);

        $import = new CustomersImport();
        Excel::import($import, $this->file);

        $this->errors = $import->getErrors();
        $this->importedCount = $import->getImportedCount();

        if (count($this->errors) === 0) {
            return redirect()
                ->route('admin.customers.index')
                ->with('sweet-alert', [
                    'icon'    => 'success',
                    'title'   => '¡Clientes importados!',
                    'text'    => "Se han importado {$this->importedCount} clientes correctamente.",
                    'timer'   => 3000,
                    'showConfirmButton' => false,
                ]);
        }
    }

    public function render()
    {
        return view('livewire.admin.import-of-customers');
    }
}
