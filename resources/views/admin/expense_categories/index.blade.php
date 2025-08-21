<x-admin-layout
    title="Categorías de Gasto | Betegar"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Categorías de gasto'],
    ]"
>
    <div class="flex justify-end mb-4 gap-2">
        <x-wire-button href="{{ route('admin.expense-categories.import') }}" icon="arrow-down-tray" gray>Importar</x-wire-button>
        <x-wire-button href="{{ route('admin.expense-categories.create') }}" icon="plus" primary>Nuevo</x-wire-button>
    </div>
    <x-wire-card>
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b bg-gray-50">
                    <th class="text-left p-2">ID</th>
                    <th class="text-left p-2">Nombre</th>
                    <th class="text-left p-2">Creado</th>
                    <th class="p-2"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $c)
                    <tr class="border-b">
                        <td class="p-2">{{ $c->id }}</td>
                        <td class="p-2">{{ $c->name }}</td>
                        <td class="p-2">{{ $c->created_at->format('Y-m-d') }}</td>
                        <td class="p-2 text-right space-x-2">
                            <x-wire-mini-button href="{{ route('admin.expense-categories.edit', $c) }}" icon="pencil" />
                            <form method="POST" action="{{ route('admin.expense-categories.destroy', $c) }}" class="inline" onsubmit="return confirm('¿Eliminar categoría?')">
                                @csrf
                                @method('DELETE')
                                <x-wire-mini-button type="submit" icon="trash" red />
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-4 text-center text-gray-500">Sin registros</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-4">{{ $categories->links() }}</div>
    </x-wire-card>
</x-admin-layout>
