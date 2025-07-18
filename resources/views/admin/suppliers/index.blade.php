<x-admin-layout
    title="Clientes | Betegar"
    :breadcrumbs="[
        ['name' => 'Dashboard',  'href' => route('admin.dashboard')],
        ['name' => 'Proveedores',   'href' => route('admin.suppliers.index')],
       
    ]"
>
    <x-slot name="action">
        <x-wire-button href="{{ route('admin.suppliers.create') }}" blue>
            Nuevo
        </x-wire-button>
    </x-slot>

    {{-- Aquí se mostrará la tabla livewire --}}
    @livewire('admin.datatables.supplier-table')

</x-admin-layout>
