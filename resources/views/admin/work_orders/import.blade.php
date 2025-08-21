<x-admin-layout
    title="Importar Órdenes de Trabajo | Betegar"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Órdenes de Trabajo', 'href' => route('admin.work-orders.index')],
        ['name' => 'Importar'],
    ]"
>
    @livewire('admin.import-of-work-orders')
</x-admin-layout>
