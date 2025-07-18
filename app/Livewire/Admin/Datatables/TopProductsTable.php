<?php

namespace App\Livewire\Admin\Datatables;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Productable;
use Illuminate\Database\Eloquent\Builder;


class TopProductsTable extends DataTableComponent
{
    public function builder(): Builder
    {
       return Productable::query()
     ->where('productable_type', 'App\Models\Sale')
   ->join('products', 'productables.product_id', '=', 'products.id')
   ->selectRaw('
       products.id as product_id,
       products.name as name,
       products.description as description,
       SUM(productables.quantity) as quantity,
       SUM(productables.subtotal) as subtotal
   ')
    ->groupBy('products.id', 'products.name', 'products.description');
   
   
    }

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setDefaultSort('subtotal', 'desc');
    }

    public function columns(): array
    {
        return [
            Column::make("Id")
                ->label(function ($row) {
                    return $row->id;
                })
                ->sortable(),
            Column::make("Producto")
                ->label(function ($row) {
                    return $row->name;
                })
                ->sortable(function ($query, $direction) {
                    return $query->orderBy('name', $direction);
                })
                ->searchable(function ($query, $search) {
                    return $query->orWhere('products.name', 'like', "%{$search}%");
                }),
           
            Column::make("Cantidad")
                ->label(function ($row) {       
                    return $row->quantity;
                })
                ->sortable(function ($query, $direction) {
                    return $query->orderBy('quantity', $direction);
                }),

            Column::make("Subtotal")
                ->label(function ($row) {
                    return 'Q/. ' . $row->subtotal;
                })
                ->sortable(function ($query, $direction) {
                    return $query->orderBy('subtotal', $direction);
                }),
           
        ];
    }
}
