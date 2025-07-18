<x-admin-layout
    title="Clientes | Betegar"
    :breadcrumbs="[
        ['name' => 'Dashboard',  'href' => route('admin.dashboard')],
        ['name' => 'Clientes',   'href' => route('admin.customers.index')],
        ['name' => 'Clientes'],
    ]"
>
    <x-slot name="action">
        <x-wire-button href="{{ route('admin.customers.create') }}" blue>
            Nuevo
        </x-wire-button>
    </x-slot>

    {{-- Aquí se mostrará la tabla livewire --}}
    @livewire('admin.datatables.customer-table')

</x-admin-layout>
