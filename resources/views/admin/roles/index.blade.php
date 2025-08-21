<x-admin-layout
    title="Roles | Betegar"
    :breadcrumbs="[
        ['name' => 'Dashboard',  'href' => route('admin.dashboard')],
        ['name' => 'Roles',   'href' => route('admin.roles.index')],
    ]"
>
    <x-slot name="action">
        <x-wire-button href="{{ route('admin.roles.create') }}" blue>
            Nuevo
        </x-wire-button>
    </x-slot>

    {{-- Aquí se mostrará la tabla livewire --}}
    @livewire('admin.datatables.role-table')

</x-admin-layout>
