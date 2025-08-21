<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ServicesTemplateExport;
use App\Imports\ServicesImport;

class ImportOfServices extends Component
{
    use WithFileUploads;

    public $file;
    public $errors = [];
    public $importedCount = 0;

    public function downloadTemplate()
    {
        return Excel::download(new ServicesTemplateExport, 'services_template.xlsx');
    }

    public function importServices()
    {
        $this->validate([
            'file' => 'required|file|mimes:xlsx,csv,xls',
        ]);

        $import = new ServicesImport();
        Excel::import($import, $this->file);
        $this->errors = $import->getErrors();
        $this->importedCount = $import->getImportedCount();

        if(count($this->errors)===0){
            return redirect()->route('admin.services.index')->with('sweet-alert',[
                'icon'=>'success',
                'title'=>'¡Servicios importados!',
                'text'=>"Se importaron {$this->importedCount} servicios.",
                'timer'=>3000,
                'showConfirmButton'=>false,
            ]);
        }
    }

    public function render()
    {
        return view('livewire.admin.import-of-services');
    }
}
