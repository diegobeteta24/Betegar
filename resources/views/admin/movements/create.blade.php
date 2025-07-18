{{-- resources/views/admin/movements/create.blade.php --}}
<x-admin-layout
    title="Movimientos | Betegar"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Movimientos', 'href' => route('admin.movements.index')],
        ['name' => 'Nuevo'],
    ]"
>
    

    @livewire('admin.movement-create')

    

</x-admin-layout>