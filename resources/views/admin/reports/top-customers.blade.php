<x-admin-layout
    title="Mejores clientes | Betegar"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Reportes', 'href' => route('admin.reports.top-customers')],
    ]"
>
@livewire('admin.datatables.top-customers-table')
</x-admin-layout>