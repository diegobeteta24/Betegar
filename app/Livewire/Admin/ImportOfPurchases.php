<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PurchasesImport;

class ImportOfPurchases extends Component
{
    use WithFileUploads;

    public $file;
    public $errors = [];
    public $importedCount = 0;

    public function downloadTemplate()
    {
        $template = [[
            'voucher_type' => 'FAC',
            'serie' => 'F001',
            'correlative' => '123',
            'date' => now()->format('Y-m-d'),
            'purchase_order_id' => null,
            'supplier_id' => 1,
            'warehouse_id' => 1,
            'bank_account_id' => null,
            'total' => 150.50,
            'observation' => 'Ejemplo',
        ]];
        $headings = array_keys($template[0]);
        $export = new class($template,$headings) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings {public function __construct(private $rows, private $headings){} public function array(): array {return $this->rows;} public function headings(): array {return $this->headings;}};
        return Excel::download($export,'purchases_template.xlsx');
    }

    public function importPurchases()
    {
        $this->validate(['file' => 'required|file|mimes:xlsx,csv,xls']);
        $import = new PurchasesImport();
        Excel::import($import,$this->file);
        $this->errors = $import->getErrors();
        $this->importedCount = $import->getImportedCount();
        if(!$this->errors){
            return redirect()->route('admin.purchases.index')->with('sweet-alert',[
                'icon'=>'success','title'=>'¡Compras importadas!','text'=>"Se importaron {$this->importedCount} compras.",'timer'=>3000,'showConfirmButton'=>false]);
        }
    }

    public function render(){ return view('livewire.admin.import-of-purchases'); }
}
