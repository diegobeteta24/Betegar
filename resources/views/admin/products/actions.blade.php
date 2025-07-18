<div class="flex items-center space-x-2">
    <x-wire-button
        href="{{ route('admin.products.kardex', $product) }}"
        green xs
    >
        <i class="fas fa-boxes-stacked"></i>
    </x-wire-button>
    
    <x-wire-button
        href="{{ route('admin.products.edit', $product) }}"
        blue xs
    >
        <i class="fas fa-edit"></i>
    </x-wire-button>

    <form
        action="{{ route('admin.products.destroy', $product) }}" 
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
            <i class="fas fa-trash-alt"></i>
        </x-wire-button>
    </form>
</div>
