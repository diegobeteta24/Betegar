<x-admin-layout
    title="Clientes | Betegar"
    :breadcrumbs="[
        ['name' => 'Dashboard',  'href' => route('admin.dashboard')],
        ['name' => 'Proveedores',   'href' => route('admin.suppliers.index')],
       
    ]"
>
    <x-slot name="action">
        <div class="flex gap-2">
            <x-wire-button href="{{ route('admin.suppliers.import') }}" gray>
                <i class="fas fa-file-import"></i>
                Importar
            </x-wire-button>
            <x-wire-button href="{{ route('admin.suppliers.create') }}" blue>
                Nuevo
            </x-wire-button>
        </div>
    </x-slot>

    {{-- Aquí se mostrará la tabla livewire --}}
    @livewire('admin.datatables.supplier-table')

</x-admin-layout>
