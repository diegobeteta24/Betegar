{{-- resources/views/admin/transfers/index.blade.php --}}
<x-admin-layout
    title="Transferencias | Betegar"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Transferencias', 'href' => route('admin.transfers.index')],
    ]"
>
    <x-slot name="action">
        <x-wire-button href="{{ route('admin.transfers.create') }}" blue>
            Nuevo
        </x-wire-button>
    </x-slot>

    @livewire('admin.datatables.transfer-table')
</x-admin-layout>
