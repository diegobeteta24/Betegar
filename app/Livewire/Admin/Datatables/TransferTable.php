<?php

namespace App\Livewire\Admin\Datatables;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\PurchaseOrder;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Transfer;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateRangeFilter;
use Illuminate\Support\Facades\Mail;
use App\Mail\PdfSend;
use App\Exports\TransfersExport;
use Maatwebsite\Excel\Facades\Excel;


class TransferTable extends DataTableComponent
{

    public function builder(): Builder
    {
        return Transfer::query()
            ->with(['originWarehouse', 'destinationWarehouse'])
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

                   Column::make("Almacen Origen", "originWarehouse.name")
                ->sortable(),
                Column::make("Almacen Destino", "destinationWarehouse.name")
                ->sortable(),

                
            

            Column::make("Actions")
                ->label(function ($row) {
                    return view('admin.transfers.actions', ['transfer' => $row]);
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

        $transfers = count($selected)
            ? Transfer::whereIn('id', $selected)->get()
            : Transfer::all();

        return Excel::download(new TransfersExport($transfers), 'transferencias.xlsx');
    }

    
    public $form =[
        'open' => false,
        'document'=> '',
        'client' => '',
        'email' => '',
        'model'=> null,
        'view_pdf_patch' => 'admin.transfers.pdf',

    ];

    //Metodo para abrir modal de envio de correo
     public function openModal( Transfer $transfer)
    {
        $this->form['open'] = true;
        $this->form['document'] = 'Transferencia #' . $transfer->serie . '-' . $transfer->correlative;
        $this->form['client'] = $transfer->originWarehouse->name;
        $this->form['email'] = '';
        $this->form['model'] = $transfer;

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
