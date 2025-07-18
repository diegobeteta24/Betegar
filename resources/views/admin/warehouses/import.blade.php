{{-- import excel --}}
<x-admin-layout
    title="Almacenes | Betegar"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Almacenes', 'href' => route('admin.warehouses.index')],
        ['name' => 'Importar'],
    ]"
>   



@livewire('admin.import-of-warehouses')

</x-admin-layout>