<?php

namespace App\Livewire\Admin\Datatables;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\PurchaseOrder;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateRangeFilter;
use Illuminate\Support\Facades\Mail;
use App\Mail\PdfSend;
use App\Exports\PurchaseOrdersExport;
use Maatwebsite\Excel\Facades\Excel;



class PurchaseOrderTable extends DataTableComponent
{
    
    public function builder(): Builder
    {
        return PurchaseOrder::query()
            ->with(['supplier'])
            ->orderBy('id', 'desc');
    }

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setDefaultSort('id', 'desc');
         $this->setConfigurableAreas([
            'after-wrapper' =>[

            'admin.pdf.modal',

            ],
        ]);
    }
    public function filters (): array
    {
return [
        DateRangeFilter::make('Fecha')
        ->config([
            'placeholder' => 'Seleccionar rango de fechas',
        ])
        ->filter(function ($query, array $dateRange) {
            $query->whereBetween('date', [
                $dateRange['minDate'] ,
                $dateRange['maxDate'] 
            ]);

        }),
    ];
    }

    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->sortable(),
                Column::make("Date", "date")
                ->sortable()
                ->format(function ($value) {
                    return $value ? $value->format('Y-m-d') : '';
                }),

            Column::make("Serie", "serie")
                ->sortable(),

            Column::make("Correlative", "correlative")
                ->sortable(),

             Column::make("Doc", "supplier.document_number")
                ->sortable(),

             Column::make("Nombre", "supplier.name")
                ->sortable(),
            
            Column::make("Total", "total")
                ->sortable()        
                ->format(fn($value) =>'Q/' . number_format($value, 2, '.', ',')),


            Column::make("Actions")
                ->label(function ($row) {
                    return view('admin.purchase_orders.actions', ['purchaseOrder' => $row]);
                })
           
            
        ];
    }
    public function bulkActions(): array
    {
        return [
            'exportSelected' => 'Exportar',
        ];
    }
    public function exportSelected()
    {
        $selected = $this->getSelected();

        $purchase_orders = count($selected)
            ? PurchaseOrder::whereIn('id', $selected)->get()
            : PurchaseOrder::all();

        return Excel::download(new PurchaseOrdersExport($purchase_orders), 'ordenes_de_compra.xlsx');
    }

    
       //Properties
    public $form =[
        'open' => false,
        'document'=> '',
        'client' => '',
        'email' => '',
        'model'=> null,
        'view_pdf_patch' => 'admin.purchase_orders.pdf',

    ];

    //Metodo para abrir modal de envio de correo
     public function openModal( PurchaseOrder $purchaseOrder)
    {
        $this->form['open'] = true;
        $this->form['document'] = 'Orden de compra #' . $purchaseOrder->serie . '-' . $purchaseOrder->correlative;
        $this->form['client'] = $purchaseOrder->supplier->document_number . ' - ' . $purchaseOrder->supplier->name;
        $this->form['email'] = $purchaseOrder->supplier->email;
        $this->form['model'] = $purchaseOrder;

    }

    //Send Email
    public function sendEmail()
    {
        //Validar email
        $this->validate([
            'form.email' => 'required|email',
        ]);

       //Llamar a mailable
         Mail::to($this->form['email'])
         ->send(new PdfSend($this->form));

       $this->dispatch('swal', [
            'title' => 'Correo enviado',
            'text' => 'El correo ha sido enviado correctamente.',
            'icon' => 'success',
        ]);

       $this->reset('form');
    }
}
