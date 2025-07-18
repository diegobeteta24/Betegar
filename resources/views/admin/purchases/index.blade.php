{{-- resources/views/admin/purchases/index.blade.php --}}
<x-admin-layout
    title="Compras | Betegar"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Compras', 'href' => route('admin.purchases.index')],
    ]"
>
    <x-slot name="action">
        <x-wire-button href="{{ route('admin.purchases.create') }}" blue>
            Nuevo
        </x-wire-button>
    </x-slot>

    @livewire('admin.datatables.purchase-table')
</x-admin-layout>
