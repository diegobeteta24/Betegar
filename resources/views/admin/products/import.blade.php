{{-- import excel --}}
<x-admin-layout
    title="Productos | Betegar"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Productos', 'href' => route('admin.products.index')],
        ['name' => 'Importar'],
    ]"
>

@livewire('admin.import-of-products')

</x-admin-layout>