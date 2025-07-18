<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CategoriesTemplateExport;
use Livewire\WithFileUploads;
use App\Imports\CategoriesImport;



class ImportOfCategories extends Component
{
    use WithFileUploads;
    
    public $file;
    public $errors = [];
    public $importedCount = 0;

    public function downloadTemplate()
    {
        return Excel::download(new CategoriesTemplateExport, 'categories_template.xlsx');
    }

    public function importCategories()
    {
        $this->validate([
            'file' => 'required|file|mimes:xlsx,csv,xls',
        ]);

        $categoriesImport = new CategoriesImport();

        Excel::import($categoriesImport, $this->file);
        $this->errors = $categoriesImport->getErrors();
        $this->importedCount = $categoriesImport->getImportedCount();

        if (count($this->errors) == 0) {
            return redirect()
        ->route('admin.categories.index')
        ->with('sweet-alert', [
            'icon'    => 'success',
            'title'   => '¡Categorías importadas!',
            'text'    => "Se han importado {$this->importedCount} categorías correctamente.",
            'timer'   => 3000,
            'showConfirmButton' => false,
        ]);
        } else {
            
        }
    }

    public function render()
    {
        return view('livewire.admin.import-of-categories');
    }
}
