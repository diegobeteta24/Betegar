<x-admin-layout
    title="Usuarios | Betegar"
    :breadcrumbs="[
        ['name' => 'Dashboard',  'href' => route('admin.dashboard')],
        ['name' => 'Usuarios',   'href' => route('admin.users.index')],
       
    ]"
>
    <x-slot name="action">
        <x-wire-button href="{{ route('admin.users.create') }}" blue>
            Nuevo
        </x-wire-button>
    </x-slot>

    {{-- Aquí se mostrará la tabla livewire --}}
    @livewire('admin.datatables.user-table')

</x-admin-layout>
