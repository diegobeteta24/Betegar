{{-- resources/views/admin/purchase_orders/index.blade.php --}}
<x-admin-layout
    title="Órdenes de Compra | Betegar"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Órdenes de Compra', 'href' => route('admin.purchase-orders.index')],
        ['name' => 'Nuevo'],
    ]"
>
    

    @livewire('admin.purchase-order-create')

    

</x-admin-layout>