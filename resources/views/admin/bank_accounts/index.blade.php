{{-- resources/views/admin/sales/index.blade.php --}}
<x-admin-layout
    title="Ventas | Betegar"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Banco', 'href' => route('admin.bank-accounts.index')],
    ]"
>
    <x-slot name="action">
        <x-wire-button href="{{ route('admin.bank-accounts.create') }}" blue>
            Nuevo
        </x-wire-button>
    </x-slot>

    @livewire('admin.datatables.sale-table')
</x-admin-layout>
