<?php
// app/Livewire/Admin/Datatables/SupplierTable.php

namespace App\Livewire\Admin\Datatables;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Builder;
use App\Exports\SuppliersExport;
use Maatwebsite\Excel\Facades\Excel;

class SupplierTable extends DataTableComponent
{
     public function builder(): Builder
    {
        return Supplier::query()->with('identity');
    }

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setDefaultSort('id', 'desc');
    }

    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->sortable(),

            Column::make("Num Doc", "document_number")
                ->sortable()
                ->searchable(),

            Column::make("Nombre", "name")
                ->searchable()
                ->sortable(),

            Column::make("Correo", "email")
                ->sortable(),

            Column::make("Cel", "phone")
                ->sortable(),

            Column::make("Acciones")
                ->label(function($row) {
                    return view('admin.suppliers.actions', ['supplier' => $row]);
                }),
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

        $suppliers = count($selected)
            ? Supplier::whereIn('id', $selected)->get()
            : Supplier::all();

        return Excel::download(new SuppliersExport($suppliers), 'proveedores.xlsx');
    }

   
}
