<x-admin-layout
    title="Usuarios | Betegar"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Usuarios',  'href' => route('admin.users.index')],
        ['name' => 'Detalle'],
    ]"
>
    <x-slot name="action">
        <x-wire-button href="{{ route('admin.users.index') }}" gray>
            Volver
        </x-wire-button>
    </x-slot>
</x-admin-layout>