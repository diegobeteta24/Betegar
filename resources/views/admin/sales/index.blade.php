{{-- resources/views/admin/sales/index.blade.php --}}
<x-admin-layout
    title="Ventas | Betegar"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Ventas', 'href' => route('admin.sales.index')],
    ]"
>
    <x-slot name="action">
        <x-wire-button href="{{ route('admin.sales.create') }}" blue>
            Nuevo
        </x-wire-button>
    </x-slot>

    @livewire('admin.datatables.sale-table')
</x-admin-layout>
