<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\WarehousesTemplateExport;
use Livewire\WithFileUploads;
use App\Imports\WarehousesImport;

class ImportOfWarehouses extends Component
{
    use WithFileUploads;
    
    public $file;
    public $errors = [];
    public $importedCount = 0;

    public function downloadTemplate()
    {
        return Excel::download(new WarehousesTemplateExport, 'warehouses_template.xlsx');
    }

    public function importWarehouses()
    {
        $this->validate([
            'file' => 'required|file|mimes:xlsx,csv,xls',
        ]);

        $warehousesImport = new WarehousesImport();

        Excel::import($warehousesImport, $this->file);
        $this->errors = $warehousesImport->getErrors();
        $this->importedCount = $warehousesImport->getImportedCount();

        if (count($this->errors) == 0) {
            return redirect()
        ->route('admin.warehouses.index')
        ->with('sweet-alert', [
            'icon'    => 'success',
            'title'   => '¡Almacenes importados!',
            'text'    => "Se han importado {$this->importedCount} almacenes correctamente.",
            'timer'   => 3000,
            'showConfirmButton' => false,
        ]);
        } else {
            
        }
    }

    public function render()
    {
        return view('livewire.admin.import-of-warehouses');
    }
}
