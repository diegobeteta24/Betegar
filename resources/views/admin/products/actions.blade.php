<div class="flex items-center space-x-2">
    @if(!$product->trashed())
        @if($product->type === 'product')
        <x-wire-button href="{{ route('admin.products.kardex', $product) }}" green xs title="Kardex">
            <i class="fas fa-boxes-stacked"></i>
        </x-wire-button>
        @endif

        <x-wire-button href="{{ route('admin.products.edit', $product) }}" blue xs title="Editar">
            <i class="fas fa-edit"></i>
        </x-wire-button>

        <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="delete-form" title="Enviar a papelera">
            @csrf
            @method('DELETE')
            <x-wire-button type="button" red xs class="delete-button">
                <i class="fas fa-trash-alt"></i>
            </x-wire-button>
        </form>
    @else
        <form action="{{ route('admin.products.restore', $product->id) }}" method="POST">
            @csrf
            <x-wire-button type="submit" emerald xs title="Restaurar">
                <i class="fas fa-undo"></i>
            </x-wire-button>
        </form>

        <form action="{{ route('admin.products.force-delete', $product->id) }}" method="POST" class="force-delete-form" title="Eliminar definitivamente">
            @csrf
            @method('DELETE')
            <x-wire-button type="button" negative xs class="force-delete-button">
                <i class="fas fa-skull-crossbones"></i>
            </x-wire-button>
        </form>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Confirmación para envío a papelera
    document.querySelectorAll('.delete-form .delete-button').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const form = e.currentTarget.closest('form');
            if (confirm('¿Enviar el producto a la papelera?')) {
                form.submit();
            }
        });
    });
    // Confirmación para eliminación definitiva
    document.querySelectorAll('.force-delete-form .force-delete-button').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const form = e.currentTarget.closest('form');
            if (confirm('Esta acción eliminará el producto definitivamente. ¿Continuar?')) {
                form.submit();
            }
        });
    });
});
</script>
@endpush
