<x-admin-layout
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Categorías', 'href' => route('admin.categories.index')],
        ['name' => 'Editar'],
    ]"
>
    

    <x-wire-card>
        <form
            action="{{ route('admin.categories.update', $category) }}"
            method="POST"
            class="space-y-4"
        >
            @csrf
            @method('PUT')

            <x-wire-input
                name="name"
                label="Nombre"
                placeholder="Nombre de la categoría"
                value="{{ old('name', $category->name) }}"
                required
            />

            <x-wire-textarea
                name="description"
                label="Descripción"
                placeholder="Descripción de la categoría"
                required
            >
                {{ old('description', $category->description) }}
            </x-wire-textarea>

           <div class="flex justify-end">
    <x-button type="submit">
        Actualizar
    </x-button>
</div>
        </form>
    </x-wire-card>
</x-admin-layout>
