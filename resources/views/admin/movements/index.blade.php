{{-- resources/views/admin/movements/index.blade.php --}}
<x-admin-layout
    title="Movimientos | Betegar"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Movimientos', 'href' => route('admin.movements.index')],
    ]"
>
    <x-slot name="action">
        <x-wire-button href="{{ route('admin.movements.create') }}" blue>
            Nuevo
        </x-wire-button>
    </x-slot>

    @livewire('admin.datatables.movement-table')
</x-admin-layout>
