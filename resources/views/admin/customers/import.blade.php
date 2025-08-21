{{-- import excel customers --}}
<x-admin-layout
    title="Importar Clientes | Betegar"
    :breadcrumbs="[
        ['name' => 'Dashboard',  'href' => route('admin.dashboard')],
        ['name' => 'Clientes',   'href' => route('admin.customers.index')],
        ['name' => 'Importar'],
    ]"
>
    @livewire('admin.import-of-customers')
</x-admin-layout>
