<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PurchaseOrdersImport;

class ImportOfPurchaseOrders extends Component
{
    use WithFileUploads;

    public $file;
    public $errors = [];
    public $importedCount = 0;

    public function downloadTemplate()
    {
        $template = [[
            'date' => now()->format('Y-m-d'),
            'supplier_id' => 1,
            'warehouse_id' => 1,
            'total' => 250,
            'observation' => 'Ejemplo',
        ]];
        $headings = array_keys($template[0]);
        $export = new class($template,$headings) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings {public function __construct(private $rows, private $headings){} public function array(): array {return $this->rows;} public function headings(): array {return $this->headings;}};
        return Excel::download($export,'purchase_orders_template.xlsx');
    }

    public function importPurchaseOrders()
    {
        $this->validate(['file' => 'required|file|mimes:xlsx,csv,xls']);
        $import = new PurchaseOrdersImport();
        Excel::import($import,$this->file);
        $this->errors = $import->getErrors();
        $this->importedCount = $import->getImportedCount();
        if(!$this->errors){
            return redirect()->route('admin.purchase-orders.index')->with('sweet-alert',[
                'icon'=>'success','title'=>'¡Órdenes de Compra importadas!','text'=>"Se importaron {$this->importedCount} órdenes.",'timer'=>3000,'showConfirmButton'=>false]);
        }
    }

    public function render(){ return view('livewire.admin.import-of-purchase-orders'); }
}
