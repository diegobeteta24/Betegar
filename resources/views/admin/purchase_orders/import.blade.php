<x-admin-layout
    title="Importar Órdenes de Compra | Betegar"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Órdenes de Compra', 'href' => route('admin.purchase-orders.index')],
        ['name' => 'Importar'],
    ]"
>
    @livewire('admin.import-of-purchase-orders')
</x-admin-layout>
