<?php

namespace App\Livewire\Admin\Datatables;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Purchase;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateRangeFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\MultiSelectFilter;
use App\Models\Supplier;
use Illuminate\Support\Facades\Mail;
use App\Mail\PdfSend;
use App\Exports\PurchasesExport;
use Maatwebsite\Excel\Facades\Excel;

class PurchaseTable extends DataTableComponent
{
     public function builder(): Builder
    {
        return Purchase::query()
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
        MultiSelectFilter::make('Proveedor')
            ->options(
                Supplier::query()
                    ->orderBy('name')
                    ->get()
                    ->keyBy('id')
                    ->map(fn($supplier) => $supplier->name)
                    ->toArray()
            )
            ->filter(function ($query, array $selected) {
                $query->whereIn('supplier_id', $selected);
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

                 Column::make("Doc.", "supplier.document_number")
                ->sortable(),

             Column::make("Nombre", "supplier.name")
                ->searchable()
                ->sortable(),
            
            Column::make("Total", "total")
                ->sortable()        
                ->format(fn($value) =>'Q/' . number_format($value, 2, '.', ',')),


            Column::make("Actions")
                ->label(function ($row) {
                    return view('admin.purchases.actions', ['purchase' => $row]);
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

        $purchases = count($selected)
            ? Purchase::whereIn('id', $selected)->get()
            : Purchase::all();

        return Excel::download(new PurchasesExport($purchases), 'compras.xlsx');
    }

   
    //Properties
    public $form =[
        'open' => false,
        'document'=> '',
        'client' => '',
        'email' => '',
        'model'=> null,
        'view_pdf_patch' => 'admin.purchases.pdf',

    ];

    //Metodo para abrir modal de envio de correo
     public function openModal( Purchase $purchase)
    {
        $this->form['open'] = true;
        $this->form['document'] = 'Compra #' . $purchase->serie . '-' . $purchase->correlative;
        $this->form['client'] = $purchase->supplier->document_number . ' - ' . $purchase->supplier->name;
        $this->form['email'] = $purchase->supplier->email;
        $this->form['model'] = $purchase;
      
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

