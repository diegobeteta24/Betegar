<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProductsTemplateExport;
use Livewire\WithFileUploads;
use App\Imports\ProductsImport;



class ImportOfProducts extends Component
{
    use WithFileUploads;
    public $file;
    public $errors = [];
    public $importedCount = 0;

    public function downloadTemplate()
    {
        return Excel::download(new ProductsTemplateExport, 'products_template.xlsx');
    }

    public function importProducts()
    {
        $this->validate([
            'file' => 'required|file|mimes:xlsx,csv,xls',
        ]);

        $productsImport = new ProductsImport();

        Excel::import($productsImport, $this->file);
        $this->errors = $productsImport->getErrors();
        $this->importedCount = $productsImport->getImportedCount();

        if (count($this->errors) == 0) {
            return redirect()
        ->route('admin.products.index')
        ->with('sweet-alert', [
            'icon'    => 'success',
            'title'   => '¡Productos importados!',
            'text'    => "Se han importado {$this->importedCount} productos correctamente.",
            'timer'   => 3000,
            'showConfirmButton' => false,
        ]);
        
    } 
    }

    public function render()
    {
        return view('livewire.admin.import-of-products');
    }
}
