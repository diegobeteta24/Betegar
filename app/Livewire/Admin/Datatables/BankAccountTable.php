<?php

namespace App\Livewire\Admin\Datatables;

use App\Models\BankAccount;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class BankAccountTable extends DataTableComponent
{
    protected $model = BankAccount::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setDefaultSort('id', 'desc');
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')->sortable(),
            Column::make('Nombre', 'name')->searchable()->sortable(),
            Column::make('Moneda', 'currency')->sortable(),
            Column::make('Saldo inicial', 'initial_balance')
                ->format(fn($value) => number_format((float)$value, 2))
                ->sortable(),
            Column::make('Saldo actual')
                ->label(fn($row) => 'Q '.number_format((float)$row->current_balance, 2)),
            Column::make('Descripción', 'description')->searchable(),
        ];
    }
}
