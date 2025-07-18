<?php

namespace App\Livewire\Admin\Datatables;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\PurchaseOrder;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Movement;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateRangeFilter;
use Illuminate\Support\Facades\Mail;
use App\Mail\PdfSend;
use App\Exports\MovementsExport;
use Maatwebsite\Excel\Facades\Excel;


class MovementTable extends DataTableComponent
{
      public function builder(): Builder
    {
        return Movement::query()
            ->with(['warehouse', 'reason'])
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

                 Column::make("Tipo", "type")
                ->sortable()
                ->format(fn($value) => match($value) {
                    1 => 'Entrada',
                    2 => 'Salida',
                }
            ),
               

            Column::make("Serie", "serie")
                ->sortable(),

            Column::make("Correlative", "correlative")
                ->sortable(),

                   Column::make("Almacen", "warehouse.name")
                ->sortable(),

                Column::make("Motivo", "reason.name")
                ->sortable(),
            
            

            Column::make("Actions")
                ->label(function ($row) {
                    return view('admin.movements.actions', ['movement' => $row]);
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

        $movements = count($selected)
            ? Movement::whereIn('id', $selected)->get()
            : Movement::all();

        return Excel::download(new MovementsExport($movements), 'movimientos.xlsx');
    }

  
     public $form =[
        'open' => false,
        'document'=> '',
        'client' => '',
        'email' => '',
        'model'=> null,
        'view_pdf_patch' => 'admin.movements.pdf',

    ];

    //Metodo para abrir modal de envio de correo
     public function openModal( Movement $movement)
    {
        $this->form['open'] = true;
        $this->form['document'] = 'Movimiento #' . $movement->serie . '-' . $movement->correlative;
        $this->form['client'] = $movement->warehouse->name;
        $this->form['email'] = '';
        $this->form['model'] = $movement;

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
