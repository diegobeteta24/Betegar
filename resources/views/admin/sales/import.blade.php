{{-- import excel sales --}}
<x-admin-layout
    title="Importar Ventas | Betegar"
    :breadcrumbs="[
        ['name' => 'Dashboard',  'href' => route('admin.dashboard')],
        ['name' => 'Ventas',   'href' => route('admin.sales.index')],
        ['name' => 'Importar'],
    ]"
>
    @livewire('admin.import-of-sales')
</x-admin-layout>
