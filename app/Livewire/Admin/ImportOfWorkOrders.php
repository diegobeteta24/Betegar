<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\WorkOrdersImport;

class ImportOfWorkOrders extends Component
{
    use WithFileUploads;

    public $file;
    public $errors = [];
    public $importedCount = 0;

    public function downloadTemplate()
    {
        $template = [[
            'customer_id' => 1,
            'address' => 'Dirección ejemplo',
            'objective' => 'Objetivo de la orden',
            'status' => 'pending',
            'technicians' => '2|5',
        ]];
        $headings = array_keys($template[0]);
        $export = new class($template,$headings) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings {public function __construct(private $rows, private $headings){} public function array(): array {return $this->rows;} public function headings(): array {return $this->headings;}};
        return Excel::download($export,'work_orders_template.xlsx');
    }

    public function importWorkOrders()
    {
        $this->validate(['file' => 'required|file|mimes:xlsx,csv,xls']);
        $import = new WorkOrdersImport();
        Excel::import($import,$this->file);
        $this->errors = $import->getErrors();
        $this->importedCount = $import->getImportedCount();
        if(!$this->errors){
            return redirect()->route('admin.work-orders.index')->with('sweet-alert',[
                'icon'=>'success','title'=>'¡Órdenes de Trabajo importadas!','text'=>"Se importaron {$this->importedCount} órdenes.",'timer'=>3000,'showConfirmButton'=>false]);
        }
    }

    public function render(){ return view('livewire.admin.import-of-work-orders'); }
}
