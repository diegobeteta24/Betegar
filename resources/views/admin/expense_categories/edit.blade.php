<x-admin-layout
    title="Editar Categoría de Gasto | Betegar"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Categorías de gasto', 'href' => route('admin.expense-categories.index')],
        ['name' => 'Editar'],
    ]"
>
    <x-wire-card class="max-w-md mx-auto">
        <form method="POST" action="{{ route('admin.expense-categories.update', $category) }}" class="space-y-4">
            @csrf
            @method('PUT')
            <x-wire-input label="Nombre" name="name" value="{{ old('name', $category->name) }}" required />
            <div class="flex justify-end gap-2">
                <x-wire-button href="{{ route('admin.expense-categories.index') }}" gray>Cancelar</x-wire-button>
                <x-wire-button type="submit" icon="check" primary>Actualizar</x-wire-button>
            </div>
        </form>
    </x-wire-card>
</x-admin-layout>
