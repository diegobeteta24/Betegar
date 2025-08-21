<?php

namespace App\Livewire\Admin\Datatables;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;

class ServiceTable extends DataTableComponent
{
    protected $listeners = ['serviceCreated' => '$refresh'];
    public function builder(): Builder
    {
        return Product::query()->where('type','service')->with('category');
    }

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setDefaultSort('id','desc');
    }

    public function columns(): array
    {
        return [
            Column::make('ID','id')->sortable(),
            Column::make('Nombre','name')->searchable()->sortable(),
            Column::make('Categoría','category.name')->sortable(),
            Column::make('Precio','price')->sortable(),
            Column::make('Acciones')->label(fn($row) => view('admin.services.actions',['service'=>$row])),
        ];
    }
}
