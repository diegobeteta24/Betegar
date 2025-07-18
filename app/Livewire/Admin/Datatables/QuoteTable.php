<?php

namespace App\Livewire\Admin\Datatables;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\PurchaseOrder;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Quote;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateRangeFilter;
use Illuminate\Support\Facades\Mail;
use App\Mail\PdfSend;
use App\Exports\QuotesExport;
use Maatwebsite\Excel\Facades\Excel;

class QuoteTable extends DataTableComponent
{
     public function builder(): Builder
    {
        return Quote::query()
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
                    return view('admin.quotes.actions', ['quote' => $row]);
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

        $quotes = count($selected)
            ? Quote::whereIn('id', $selected)->get()
            : Quote::all();

        return Excel::download(new QuotesExport($quotes), 'cotizaciones.xlsx');
    }

   
    //Properties
    public $form =[
        'open' => false,
        'document'=> '',
        'client' => '',
        'email' => '',
        'model'=> null,
        'view_pdf_patch' => 'admin.quotes.pdf',

    ];

    //Metodo para abrir modal de envio de correo
     public function openModal( Quote $quote)
    {
        $this->form['open'] = true;
        $this->form['document'] = 'Cotizacion #' . $quote->serie . '-' . $quote->correlative;
        $this->form['client'] = $quote->customer->document_number . ' - ' . $quote->customer->name;
        $this->form['email'] = $quote->customer->email;
        $this->form['model'] = $quote;

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
