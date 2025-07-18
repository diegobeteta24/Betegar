{{-- resources/views/admin/quotes/index.blade.php --}}
<x-admin-layout
    title="Cotizaciones | Betegar"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Cotizaciones', 'href' => route('admin.quotes.index')],
    ]"
>
    <x-slot name="action">
        <x-wire-button href="{{ route('admin.quotes.create') }}" blue>
            Nuevo
        </x-wire-button>
    </x-slot>

    @livewire('admin.datatables.quote-table')
</x-admin-layout>
