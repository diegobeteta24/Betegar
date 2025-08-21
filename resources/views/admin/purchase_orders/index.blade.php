{{-- resources/views/admin/purchase_orders/index.blade.php --}}
<x-admin-layout
    title="Órdenes de Compra | Betegar"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Órdenes de Compra', 'href' => route('admin.purchase-orders.index')],
       
    ]"
>
    <x-slot name="action">
        <x-wire-button href="{{ route('admin.purchase-orders.create') }}" blue>
            Nuevo
        </x-wire-button>
        <x-wire-button href="{{ route('admin.purchase-orders.import') }}" purple>
            Importar
        </x-wire-button>
    </x-slot>

    @livewire('admin.datatables.purchase-order-table')
</x-admin-layout>
