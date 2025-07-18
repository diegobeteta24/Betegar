<?php
// app/Livewire/Admin/Datatables/WarehouseTable.php

namespace App\Livewire\Admin\Datatables;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Builder;
use App\Exports\WarehousesExport;
use Maatwebsite\Excel\Facades\Excel;



class WarehouseTable extends DataTableComponent
{
    public function builder(): Builder
    {
        return Warehouse::query();
    }

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setDefaultSort('id', 'desc');
    }

    public function columns(): array
    {
        return [
            Column::make("Id", "id")->sortable(),
            Column::make("Nombre", "name")->searchable()->sortable(),
            Column::make("Ubicación", "location")->searchable()->sortable(),
            Column::make("Acciones")
                ->label(fn($row) => view('admin.warehouses.actions', ['warehouse' => $row])),
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

        $warehouses = count($selected)
            ? Warehouse::whereIn('id', $selected)->get()
            : Warehouse::all();

        return Excel::download(new WarehousesExport($warehouses), 'almacenes.xlsx');
    }

    
}
