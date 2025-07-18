<?php

namespace App\Livewire\Admin\Datatables;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class LowStockTable extends DataTableComponent
{
    public function builder(): Builder
    {
        // Umbral mínimo fijo o configurable
        $minStock = config('inventory.min_stock', 1);

        // Seleccionamos todos los productos y calculamos "faltante" en la misma query
        return Product::query()
            ->select([
                'products.*',
                DB::raw("($minStock - products.stock) as faltante"),
            ]);
    }

    public function configure(): void
    {
        // PK y orden por defecto (los que más faltan primero)
        $this->setPrimaryKey('id');
        $this->setDefaultSort('faltante', 'desc');
    }

    public function columns(): array
    {
        // Mismo umbral que en builder()
        $minStock = config('inventory.min_stock', 1);

        return [
            Column::make('ID', 'id')
                ->sortable(),

            Column::make('Producto', 'name')
                ->searchable()
                ->sortable(),

            Column::make('Stock Actual', 'stock')
                ->label(fn($row) => (int) $row->stock)
                ->sortable(fn($q, $d) => $q->orderBy('products.stock', $d)),

            Column::make('Stock Mínimo')
                ->label(fn() => $minStock)
                ->sortable(fn($q, $d) =>
                    // ordenar por valor fijo: no hace nada realmente
                    $q->orderByRaw("$minStock $d")
                ),

            Column::make('Faltante')
                ->label(fn($row) => max(0, $row->faltante))
                ->sortable(fn($q, $d) =>
                    // ordenar por la columna calculada "faltante"
                    $q->orderBy('faltante', $d)
                ),
        ];
    }
}
