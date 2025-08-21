<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\QuotesImport;

class ImportOfQuotes extends Component
{
    use WithFileUploads;

    public $file;
    public $errors = [];
    public $importedCount = 0;

    public function downloadTemplate()
    {
        $template = [[
            'voucher_type' => 'COT',
            'serie' => 'A',
            'correlative' => '1001',
            'date' => now()->format('Y-m-d'),
            'customer_id' => 1,
            'subtotal' => 100,
            'discount_percent' => 0,
            'discount_amount' => 0,
            'total' => 100,
            'observation' => 'Ejemplo',
        ]];
        $headings = array_keys($template[0]);
        $export = new class($template,$headings) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings {public function __construct(private $rows, private $headings){} public function array(): array {return $this->rows;} public function headings(): array {return $this->headings;}};
        return Excel::download($export,'quotes_template.xlsx');
    }

    public function importQuotes()
    {
        $this->validate(['file' => 'required|file|mimes:xlsx,csv,xls']);
        $import = new QuotesImport();
        Excel::import($import,$this->file);
        $this->errors = $import->getErrors();
        $this->importedCount = $import->getImportedCount();
        if(count($this->errors)===0){
            return redirect()->route('admin.quotes.index')->with('sweet-alert',[
                'icon'=>'success','title'=>'¡Cotizaciones importadas!','text'=>"Se importaron {$this->importedCount} cotizaciones.",'timer'=>3000,'showConfirmButton'=>false]);
        }
    }

    public function render(){ return view('livewire.admin.import-of-quotes'); }
}
