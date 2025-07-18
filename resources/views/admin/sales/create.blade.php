{{-- resources/views/admin/sales/create.blade.php --}}
<x-admin-layout
    title="Ventas | Betegar"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Ventas', 'href' => route('admin.sales.index')],
        ['name' => 'Nuevo'],
    ]"
>
    

    @livewire('admin.sale-create')

    

</x-admin-layout>