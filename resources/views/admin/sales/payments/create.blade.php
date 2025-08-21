{{-- resources/views/admin/sales/payments/create.blade.php --}}
<x-admin-layout
    title="Registrar Pago | Betegar"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Ventas', 'href' => route('admin.sales.index')],
        ['name' => 'Pago'],
    ]"
>
    <x-wire-card class="max-w-3xl mx-auto">
        <form method="POST" action="{{ route('admin.sales.payments.store', $sale) }}" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-wire-input label="Venta" value="#{{ $sale->serie }}-{{ str_pad($sale->correlative ?? $sale->id, 4, '0', STR_PAD_LEFT) }}" disabled />
                <x-wire-input label="Cliente" value="{{ $sale->customer->name ?? '—' }}" disabled />
                <x-wire-input label="Total" value="Q {{ number_format($sale->total, 2) }}" disabled />
                <x-wire-input label="Pendiente" value="Q {{ number_format($sale->due_amount, 2) }}" disabled />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-wire-native-select name="bank_account_id" label="Cuenta Bancaria">
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->name }} ({{ $acc->currency }})</option>
                        @endforeach
                    </x-wire-native-select>
                </div>
                <div>
                    <x-wire-input name="amount" label="Monto" type="number" step="0.01" value="{{ number_format($sale->due_amount, 2, '.', '') }}" />
                </div>
                <div>
                    <x-wire-native-select name="method" label="Método">
                        <option value="cash">Efectivo</option>
                        <option value="transfer">Transferencia</option>
                        <option value="card">Tarjeta</option>
                        <option value="other">Otro</option>
                    </x-wire-native-select>
                </div>
                <div>
                    <x-wire-input name="reference" label="Referencia (opcional)" />
                </div>
                <div>
                    <x-wire-input name="paid_at" label="Fecha de pago" type="datetime-local" value="{{ now()->format('Y-m-d\TH:i') }}" />
                </div>
            </div>

            <div class="flex justify-end">
                <x-wire-button type="submit" icon="check" green>
                    Registrar y generar recibo
                </x-wire-button>
            </div>
        </form>
    </x-wire-card>
</x-admin-layout>
