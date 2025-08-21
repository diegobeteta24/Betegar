<x-admin-layout>
    <x-slot name="title">Gasto #{{ $expense->id }}</x-slot>
    <div class="mb-4">
        <a href="{{ route('expenses.index') }}" class="text-primary-600 hover:underline">&larr; Volver</a>
    </div>
    <div class="grid md:grid-cols-3 gap-6">
        <div class="md:col-span-2 space-y-4">
            <div class="bg-white dark:bg-gray-800 p-4 rounded shadow">
                <h2 class="text-lg font-semibold mb-2">Información</h2>
                <dl class="text-sm grid grid-cols-2 gap-x-4 gap-y-2">
                    <dt class="font-medium text-gray-600 dark:text-gray-300">ID</dt><dd>{{ $expense->id }}</dd>
                    <dt class="font-medium text-gray-600 dark:text-gray-300">Fecha</dt><dd>{{ $expense->created_at?->format('d/m/Y H:i') }}</dd>
                    <dt class="font-medium text-gray-600 dark:text-gray-300">Técnico</dt><dd>{{ $expense->technician?->name }}</dd>
                    <dt class="font-medium text-gray-600 dark:text-gray-300">Descripción</dt><dd>{{ $expense->description }}</dd>
                    <dt class="font-medium text-gray-600 dark:text-gray-300">Monto</dt><dd>Q {{ number_format($expense->amount,2) }}</dd>
                    <dt class="font-medium text-gray-600 dark:text-gray-300">Comprobante</dt>
                    <dd>
                        @php $voucher = $expense->images()->where('tag','voucher')->first(); @endphp
                        @if($voucher)
                            <a href="{{ Storage::disk('public')->url($voucher->path) }}" target="_blank" class="text-primary-600 hover:underline">Ver comprobante</a>
                        @else
                            <span class="text-gray-400">No adjunto</span>
                        @endif
                    </dd>
                </dl>
            </div>
        </div>
        <div class="space-y-4">
            <div class="bg-white dark:bg-gray-800 p-4 rounded shadow">
                <h3 class="font-semibold mb-2">Acciones</h3>
                <p class="text-sm text-gray-500">(Futuras acciones aquí)</p>
            </div>
        </div>
    </div>
</x-admin-layout>
