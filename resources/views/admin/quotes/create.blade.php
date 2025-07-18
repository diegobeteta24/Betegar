{{-- resources/views/admin/quotes/create.blade.php --}}
<x-admin-layout
    title="Cotizaciones | Betegar"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Cotizaciones', 'href' => route('admin.quotes.index')],
        ['name' => 'Nuevo'],
    ]"
>
    

    @livewire('admin.quote-create')

    

</x-admin-layout>