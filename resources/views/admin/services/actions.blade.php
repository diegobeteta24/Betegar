<div class="flex space-x-2">
    <a href="{{ route('admin.services.edit',$service) }}" class="text-blue-600 hover:underline text-sm">Editar</a>
    <form action="{{ route('admin.services.destroy',$service) }}" method="POST" onsubmit="return confirm('¿Eliminar servicio?')">
        @csrf
        @method('DELETE')
        <button class="text-red-600 hover:underline text-sm" type="submit">Eliminar</button>
    </form>
</div>
