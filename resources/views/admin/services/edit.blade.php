<x-admin-layout
    title="Servicios | Betegar"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Servicios', 'href' => route('admin.services.index')],
        ['name' => 'Editar'],
    ]"
>
    <x-wire-card>
        <form action="{{ route('admin.services.update',$service) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <x-wire-input name="name" label="Nombre" placeholder="Nombre del servicio" value="{{ old('name',$service->name) }}" />
            <x-wire-textarea name="description" label="Descripción" placeholder="Descripción del servicio">{{ old('description',$service->description) }}</x-wire-textarea>
            <x-wire-input name="price" type="number" step="0.01" label="Precio" placeholder="Precio" value="{{ old('price',$service->price) }}" />
            <x-wire-native-select label="Categoría" name="category_id">
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('category_id',$service->category_id)==$cat->id?'selected':'' }}>{{ $cat->name }}</option>
                @endforeach
            </x-wire-native-select>
            @error('category_id')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            <div class="flex justify-end"><x-button type="submit">Actualizar</x-button></div>
        </form>
    </x-wire-card>
</x-admin-layout>
