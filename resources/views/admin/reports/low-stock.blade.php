<x-admin-layout
    title="Poco Stock | Betegar"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Reportes', 'href' => route('admin.reports.low-stock')],
    ]"
>
@livewire('admin.datatables.low-stock-table')

</x-admin-layout>