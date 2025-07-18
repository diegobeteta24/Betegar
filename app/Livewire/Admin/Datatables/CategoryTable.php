<?php

namespace App\Livewire\Admin\Datatables;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Category;
use Illuminate\Database\Eloquent\Builder;
use App\Exports\CategoriesExport;
use Maatwebsite\Excel\Facades\Excel;



class CategoryTable extends DataTableComponent
{
    protected $model = Category::class;

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
            Column::make("Nombre", "name")
             ->searchable()   
            ->sortable(),
            Column::make("Descripción", "description")
                ->sortable(),

            Column::make("Acciones")
                ->label(function($row){

                    return view('admin.categories.actions', ['category' => $row]);

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

        $categories = count($selected)
            ? Category::whereIn('id', $selected)->get()
            : Category::all();

        return Excel::download(new CategoriesExport($categories), 'categorias.xlsx');
    }
}
