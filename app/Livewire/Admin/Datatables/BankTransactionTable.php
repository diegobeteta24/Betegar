<?php

namespace App\Livewire\Admin\Datatables;

use App\Models\BankTransaction;
use App\Models\BankAccount;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class BankTransactionTable extends DataTableComponent
{
    /**
     * Select options for Account filter (array required by package version).
     * @var array<string,string>
     */
    public array $accountOptions = [];

    public function builder(): Builder
    {
        // Include a running balance per account at the moment of each transaction (saldo luego de aplicar el movimiento)
        // MySQL 8+ window function to compute cumulative sum per account ordered by date, id
        $running = "(
            `bank_accounts`.`initial_balance` +
            SUM(CASE WHEN `bank_transactions`.`type` = 'credit' THEN `bank_transactions`.`amount` ELSE -`bank_transactions`.`amount` END)
            OVER (PARTITION BY `bank_transactions`.`bank_account_id` ORDER BY `bank_transactions`.`date`, `bank_transactions`.`id`)
        )";

        return BankTransaction::query()
            ->select('bank_transactions.*')
            ->addSelect(DB::raw($running.' as balance_after'))
            ->join('bank_accounts','bank_accounts.id','=','bank_transactions.bank_account_id')
            ->with(['account']);
    }

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setDefaultSort('date', 'desc');
        $this->setDefaultSort('id', 'desc');
    }

    public function boot(): void
    {
        // Build options array once for the SelectFilter
        $this->accountOptions = ['' => 'Todas'] + BankAccount::orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
    }

    public function filters(): array
    {
        return [
            SelectFilter::make('Cuenta')
                ->options($this->accountOptions)
                ->filter(function (Builder $builder, string $value) {
                    if ($value !== '') {
                        $builder->where('bank_account_id', $value);
                    }
                }),
        ];
    }

    public function columns(): array
    {
        return [
            Column::make('Fecha', 'date')
                ->format(fn($value) => optional($value)->format('d/m/Y H:i'))
                ->sortable(),
            Column::make('Cuenta', 'account.name')
                ->sortable()
                ->searchable(),
            Column::make('Tipo', 'type')->sortable(),
            Column::make('Monto', 'amount')
                ->format(fn($value) => 'Q '.number_format((float)$value, 2))
                ->sortable(),
            Column::make('Saldo', 'balance_after')
                ->label(fn($row) => 'Q '.number_format((float)($row->balance_after ?? 0), 2))
                ->sortable(),
            Column::make('Descripción', 'description')
                ->searchable(),
            Column::make('Origen')
                ->label(fn($row) => class_basename($row->transactionable_type).'#'.$row->transactionable_id),
        ];
    }
}
