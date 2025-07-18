<div class="flex items-center space-x-2">
    <x-wire-button
        href="{{ route('admin.customers.edit', $customer) }}"
        blue xs
    >
        Editar
    </x-wire-button>

    <form
        action="{{ route('admin.customers.destroy', $customer) }}"
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
