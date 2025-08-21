<x-admin-layout
    title="Nueva Categoría de Gasto | Betegar"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Categorías de gasto', 'href' => route('admin.expense-categories.index')],
        ['name' => 'Nueva'],
    ]"
>
    <x-wire-card class="max-w-md mx-auto">
        <form method="POST" action="{{ route('admin.expense-categories.store') }}" class="space-y-4">
            @csrf
            <x-wire-input label="Nombre" name="name" required autofocus />
            <div class="flex justify-end gap-2">
                <x-wire-button href="{{ route('admin.expense-categories.index') }}" gray>Cancelar</x-wire-button>
                <x-wire-button type="submit" icon="check" primary>Guardar</x-wire-button>
            </div>
        </form>
    </x-wire-card>
</x-admin-layout>
