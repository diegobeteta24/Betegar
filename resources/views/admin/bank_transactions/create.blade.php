{{-- resources/views/admin/bank_transactions/create.blade.php --}}
<x-admin-layout
    title="Nuevo Movimiento Bancario | Betegar"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Transacciones Bancarias', 'href' => route('admin.bank-transactions.index')],
        ['name' => 'Nuevo'],
    ]"
>
    <x-wire-card class="max-w-3xl mx-auto">
        <form method="POST" action="{{ route('admin.bank-transactions.store') }}" class="space-y-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-wire-native-select label="Cuenta" name="bank_account_id" required>
                        <option value="">-- Selecciona --</option>
                        @foreach($accounts as $a)
                            <option value="{{ $a->id }}">{{ $a->name }} ({{ $a->currency }})</option>
                        @endforeach
                    </x-wire-native-select>
                </div>
                <div>
                    <x-wire-native-select label="Tipo" name="mode" required>
                        <option value="income">Ingreso (Crédito)</option>
                        <option value="expense">Gasto (Débito)</option>
                    </x-wire-native-select>
                </div>
                    <div>
                        <x-wire-native-select label="Categoría" name="category_id">
                            <option value="">-- Sin categoría --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </x-wire-native-select>
                    </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-wire-input label="Monto" name="amount" type="number" step="0.01" min="0.01" required />
                <x-wire-textarea label="Descripción" name="description" placeholder="Detalle" />
            </div>

            <div class="flex justify-end gap-4">
                <x-wire-button type="submit" icon="check" green>Guardar</x-wire-button>
                <x-wire-button href="{{ route('admin.bank-transactions.index') }}" gray>Cancelar</x-wire-button>
            </div>
        </form>
    </x-wire-card>

    <!-- Simplificado: ya no se maneja pago de venta aquí -->
</x-admin-layout>
