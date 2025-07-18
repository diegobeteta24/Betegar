<div class="flex items-center space-x-2">
    <x-wire-button
        href="{{ route('admin.categories.edit', $category) }}"
        blue xs
    >
        Editar
    </x-wire-button>

    <form
        action="{{ route('admin.categories.destroy', $category) }}" 
        method="POST"
        class="delete-form"
    >
        @csrf
        @method('DELETE')
        <x-wire-button
            type="button"
            red xs
            class="delete-button"
        >
            Eliminar
        </x-wire-button>
    </form>
</div>
