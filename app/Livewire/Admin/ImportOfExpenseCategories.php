<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ExpenseCategoriesImport;

class ImportOfExpenseCategories extends Component
{
    use WithFileUploads;

    public $file;
    public $errors = [];
    public $importedCount = 0;

    public function downloadTemplate()
    {
        $template = [[ 'name' => 'Viáticos' ]];
        $headings = array_keys($template[0]);
        $export = new class($template,$headings) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings {public function __construct(private $rows, private $headings){} public function array(): array {return $this->rows;} public function headings(): array {return $this->headings;}};
        return \Maatwebsite\Excel\Facades\Excel::download($export,'expense_categories_template.xlsx');
    }

    public function importExpenseCategories()
    {
        $this->validate([
            'file' => 'required|file|mimes:xlsx,csv,xls',
        ]);

        $import = new ExpenseCategoriesImport();
        Excel::import($import, $this->file);
        $this->errors = $import->getErrors();
        $this->importedCount = $import->getImportedCount();

        if (count($this->errors) === 0) {
            return redirect()->route('admin.expense-categories.index')
                ->with('sweet-alert', [
                    'icon' => 'success',
                    'title'=> '¡Categorías importadas!',
                    'text' => "Se han importado {$this->importedCount} categorías de gasto correctamente.",
                    'timer'=> 3000,
                    'showConfirmButton' => false,
                ]);
        }
    }

    public function render()
    {
        return view('livewire.admin.import-of-expense-categories');
    }
}
