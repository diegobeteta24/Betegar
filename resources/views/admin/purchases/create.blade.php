{{-- resources/views/admin/purchases/create.blade.php --}}
<x-admin-layout
    title="Compras | Betegar"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Compras', 'href' => route('admin.purchases.index')],
        ['name' => 'Nuevo'],
    ]"
>
    

    @livewire('admin.purchase-create')

    

</x-admin-layout>