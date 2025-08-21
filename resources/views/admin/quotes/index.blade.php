{{-- resources/views/admin/quotes/index.blade.php --}}
<x-admin-layout
    title="Cotizaciones | Betegar"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Cotizaciones', 'href' => route('admin.quotes.index')],
    ]"
>
    <x-slot name="action">
        <div class="flex gap-2">
            <x-wire-button href="{{ route('admin.quotes.import') }}" gray>
                <i class="fas fa-file-import"></i>
                Importar
            </x-wire-button>
            <x-wire-button href="{{ route('admin.quotes.create') }}" blue>
                Nuevo
            </x-wire-button>
        </div>
    </x-slot>

    @livewire('admin.datatables.quote-table')
</x-admin-layout>
