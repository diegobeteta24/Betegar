<?php

namespace App\Livewire\Admin\Datatables;

use App\Models\WorkOrder;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class WorkOrderTable extends DataTableComponent
{
    public function builder(): Builder
    {
        return WorkOrder::query()->with(['customer','technicians']);
    }

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setDefaultSort('id', 'desc');
    }

    public function columns(): array
    {
        return [
            Column::make('ID','id')->sortable(),
            Column::make('Cliente','customer.name')->sortable()->searchable(),
            Column::make('Dirección','address')->searchable(),
            Column::make('Objetivo','objective')->searchable(),
            Column::make('Estado','status')->sortable(),
            Column::make('Técnicos')
                ->label(fn($row) => $row->technicians->pluck('name')->implode(', ')),
            Column::make('Acciones')
                ->label(fn($row) => view('admin.work_orders.actions', ['order' => $row])),
        ];
    }
}
