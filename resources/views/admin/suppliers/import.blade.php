{{-- import excel suppliers --}}
<x-admin-layout
    title="Importar Proveedores | Betegar"
    :breadcrumbs="[
        ['name' => 'Dashboard',  'href' => route('admin.dashboard')],
        ['name' => 'Proveedores',   'href' => route('admin.suppliers.index')],
        ['name' => 'Importar'],
    ]"
>
    @livewire('admin.import-of-suppliers')
</x-admin-layout>
