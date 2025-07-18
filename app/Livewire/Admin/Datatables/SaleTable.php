<?php

namespace App\Livewire\Admin\Datatables;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Sale;

use Illuminate\Database\Eloquent\Builder;
use App\Models\Purchase;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateRangeFilter;
use Illuminate\Support\Facades\Mail;
use App\Mail\PdfSend;
use App\Exports\SalesExport;
use Maatwebsite\Excel\Facades\Excel;

class SaleTable extends DataTableComponent
{

     public function builder(): Builder
    {
        return Sale::query()
            ->with(['customer'])
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

                 Column::make("Doc.", "customer.document_number")
                ->sortable(),

             Column::make("Nombre", "customer.name")
                ->sortable(),
            
            Column::make("Total", "total")
                ->sortable()        
                ->format(fn($value) =>'Q/' . number_format($value, 2, '.', ',')),


            Column::make("Actions")
                ->label(function ($row) {
                    return view('admin.sales.actions', ['sale' => $row]);
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

        $sales = count($selected)
            ? Sale::whereIn('id', $selected)->get()
            : Sale::all();

        return Excel::download(new SalesExport($sales), 'ventas.xlsx');
    }

   
        //Properties
    public $form =[
        'open' => false,
        'document'=> '',
        'client' => '',
        'email' => '',
        'model'=> null,
        'view_pdf_patch' => 'admin.sales.pdf',

    ];

    //Metodo para abrir modal de envio de correo
     public function openModal( Sale $sale)
    {
        $this->form['open'] = true;
        $this->form['document'] = 'Venta #' . $sale->serie . '-' . $sale->correlative;
        $this->form['client'] = $sale->customer->document_number . ' - ' . $sale->customer->name;
        $this->form['email'] = $sale->customer->email;
        $this->form['model'] = $sale;

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
