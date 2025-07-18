<x-admin-layout
    title="Productos más vendidos | Betegar"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Reporte', 'href' => route('admin.reports.top-products')],
    ]"
>
@livewire('admin.datatables.top-products-table')
</x-admin-layout>