{{-- resources/views/admin/sales/index.blade.php --}}
<x-admin-layout
    title="Ventas | Betegar"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Ventas', 'href' => route('admin.sales.index')],
    ]"
>
    <x-slot name="action">
        <div class="flex gap-2">
            <x-wire-button href="{{ route('admin.sales.import') }}" gray>
                <i class="fas fa-file-import"></i>
                Importar
            </x-wire-button>
            <x-wire-button href="{{ route('admin.sales.create') }}" blue>
                Nuevo
            </x-wire-button>
        </div>
    </x-slot>

    @livewire('admin.datatables.sale-table')
</x-admin-layout>
