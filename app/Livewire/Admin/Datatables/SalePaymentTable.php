<?php

namespace App\Livewire\Admin\Datatables;

use App\Models\SalePayment;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class SalePaymentTable extends DataTableComponent
{
    public function builder(): Builder
    {
        return SalePayment::query()->with(['sale.customer', 'account']);
    }

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setDefaultSort('paid_at', 'desc');
        $this->setDefaultSort('id', 'desc');
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')->sortable(),
            Column::make('Venta')
                ->label(function ($row) {
                    if (!$row->sale) return '—';
                    $serie = $row->sale->serie ?? '—';
                    $base  = $row->sale->correlative ?? $row->sale->id ?? 0;
                    $corr  = str_pad((string)$base, 4, '0', STR_PAD_LEFT);
                    return "#{$serie}-{$corr}";
                }),
            Column::make('Cliente')
                ->label(fn($row) => $row->sale?->customer->name ?? '—')
                ->searchable(),
            Column::make('Cuenta', 'account.name')->sortable()->searchable(),
            Column::make('Monto', 'amount')
                ->format(fn($value) => 'Q '.number_format((float)$value, 2))
                ->sortable(),
            Column::make('Método', 'method')->sortable(),
            Column::make('Referencia', 'reference')->searchable(),
            Column::make('Pagado en', 'paid_at')
                ->format(fn($value) => optional($value)->format('d/m/Y H:i'))
                ->sortable(),
            Column::make('Acciones')
                ->label(fn($row) => view('admin.sales.payments.actions', ['payment' => $row])),
        ];
    }
}
