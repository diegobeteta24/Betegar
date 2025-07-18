<?php

namespace App\Livewire\Admin\Datatables;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Columns\ImageColumn;
use App\Models\Inventory;
use Illuminate\Support\Facades\DB;
use App\Exports\ProductsExport;
use Maatwebsite\Excel\Facades\Excel;

class ProductTable extends DataTableComponent
{
       public function builder(): Builder
    {
        return Product::query()->with(['category', 'images']);
    }
    



    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setDefaultSort('id', 'desc');
        $this->setConfigurableAreas([
            'after-wrapper' =>[

            'admin.products.modal',

            ],
        ]);
    }

    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->sortable(),

            ImageColumn::make("Imagen")
            ->location(
                fn($row) => $row->image
            )->attributes(
                fn($row) => [
                     'class' => 'image-product',
                ]
            ),
            
                
            Column::make("Nombre", "name")
            ->searchable()
            ->sortable(),   
            Column::make("Categoría", "category.name")
                ->searchable()
                ->sortable(), 

            Column::make("Precio", "price")
                ->sortable(),

            Column::make("Stock", "stock")
                ->sortable()
                ->format(function($value, $row) {
                    return view('admin.products.stock', ['stock' => $value, 'product' => $row]);
                }),

            Column::make("Acciones")
                ->label(function($row){

                    return view('admin.products.actions', ['product' => $row]);

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
       
        $products= count($selected)
            ? Product::whereIn('id', $selected)->get()
            : Product::all();

            return Excel::download(new ProductsExport($products), 'productos.xlsx');
    }



    //Properties
    public $openModal = false;
    public $inventories = [];


    // Mostrar Stock de productos por almacén
    public function showStock($productId)
    {
        $this->openModal = true;
        $latestInventories = Inventory::where('product_id', $productId)
         ->select('warehouse_id', DB::raw('MAX(id) as id'))
        ->groupBy('warehouse_id')
        ->pluck('id');

        $this->inventories = Inventory::whereIn('id', $latestInventories)
        ->with(['warehouse'])  
        ->get();
          

  
    }
}
