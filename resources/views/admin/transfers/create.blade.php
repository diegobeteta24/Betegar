{{-- resources/views/admin/transfers/create.blade.php --}}
<x-admin-layout
    title="Transferencia | Betegar"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Transferencias', 'href' => route('admin.transfers.index')],
        ['name' => 'Nuevo'],
    ]"
>
    

    @livewire('admin.transfer-create')

    

</x-admin-layout>