{{-- resources/views/admin/warehouses/index.blade.php --}}
<x-admin-layout
    title="Almacenes | Betegar"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Almacenes', 'href' => route('admin.warehouses.index')],
    ]"
>
    <x-slot name="action">
        <div class="flex flex-col sm:flex-row sm:space-x-2 space-y-2 sm:space-y-0 w-full mb-4">
            <x-wire-button
                href="{{ route('admin.warehouses.import') }}"
                green
                class="w-full sm:w-auto"
            >
                <i class="fas fa-file-import"></i>
                Importar
            </x-wire-button>

            <x-wire-button
                href="{{ route('admin.warehouses.create') }}"
                blue
                class="w-full sm:w-auto"
            >
                <i class="fas fa-plus"></i>
                Nuevo
            </x-wire-button>
        </div>
    </x-slot>

    <livewire:admin.datatables.warehouse-table />
</x-admin-layout>
