<div class="flex items-center space-x-2">
    <x-wire-button
        href="{{ route('admin.suppliers.edit', $supplier) }}"
        blue xs
    >
        Editar
    </x-wire-button>

    <form
        action="{{ route('admin.suppliers.destroy', $supplier) }}"
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
