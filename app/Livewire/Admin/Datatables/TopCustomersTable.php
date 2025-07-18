<?php

namespace App\Livewire\Admin\Datatables;

use Livewire\Component;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Productable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class TopCustomersTable extends DataTableComponent
{
    public function builder(): Builder
    {
        return Productable::query()
            // Solo registros vinculados a ventas
            ->where('productable_type', 'App\Models\Sale')
            // Unimos a la tabla de ventas para obtener customer_id
            ->join('sales', 'productables.productable_id', '=', 'sales.id')
            // Unimos a la tabla de clientes para obtener su nombre
            ->join('customers', 'sales.customer_id', '=', 'customers.id')
            // Seleccionamos customer_id, nombre y la suma de subtotales
            ->select([
                'customers.id as customer_id',
                'customers.name as name',
                DB::raw('SUM(productables.subtotal) as total_spent'),
                DB::raw('SUM(productables.quantity) as total_items'),
            ])
            // Agrupamos por cliente
            ->groupBy('customers.id', 'customers.name');
    }

    public function configure(): void
    {
        $this->setPrimaryKey('customer_id');
        // Ordenamos por el gasto total descendente
        $this->setDefaultSort('total_spent', 'desc');
    }

    public function columns(): array
    {
        return [
            Column::make('ID')
                ->label(fn($row) => $row->customer_id)
                ->sortable(),

            Column::make('Cliente')
                ->label(fn($row) => $row->name)
                ->sortable(fn($query, $direction) => $query->orderBy('customers.name', $direction))
                ->searchable(fn($query, $search) =>
                    $query->where('customers.name', 'like', "%{$search}%")
                ),

            Column::make('Total Ítems')
                ->label(fn($row) => (int) $row->total_items)
                ->sortable(fn($query, $direction) => $query->orderBy('total_items', $direction)),

            Column::make('Total Gastado')
                ->label(fn($row) => 'Q/. ' . number_format($row->total_spent, 2))
                ->sortable(fn($query, $direction) => $query->orderBy('total_spent', $direction)),
        ];
    }
}
