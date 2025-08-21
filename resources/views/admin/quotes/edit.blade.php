{{-- resources/views/admin/quotes/edit.blade.php --}}
<x-admin-layout
    title="Editar Cotización | Betegar"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Cotizaciones', 'href' => route('admin.quotes.index')],
        ['name' => 'Editar'],
    ]"
>
    @livewire('admin.quote-edit', ['quote' => $quote])
</x-admin-layout>
