<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize('category.view', Category::class);
        return view('admin.categories.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('category.create', Category::class);
        return view('admin.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
     public function store(Request $request)
    {
        Gate::authorize('category.create', Category::class);
        // 1. Validar datos
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255', 'unique:categories,name'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        // 2. Crear la categoría
      $category= Category::create($data);

        // 3. Redirigir con mensaje de éxito
        return redirect()
            ->route('admin.categories.edit', $category)
             ->with('sweet-alert', [
            'icon'    => 'success',
            'title'   => '¡Categoría creada!',
            'text'    => 'Ahora puedes editar los detalles.',
            'timer'   => 3000,    // milisegundos (opcional)
            'showConfirmButton' => false,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        Gate::authorize('category.update', $category);
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
   public function update(Request $request, Category $category)
    {
        Gate::authorize('category.update', $category);
        // 1. Validar datos
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255', "unique:categories,name,{$category->id}"],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        // 2. Actualizar la categoría
        $category->update($data);

        // 3. Redirigir con SweetAlert
        return redirect()
            ->route('admin.categories.edit', $category)
            ->with('sweet-alert', [
                'icon'    => 'success',
                'title'   => '¡Categoría actualizada!',
                'text'    => 'Los cambios se guardaron correctamente.',
                'timer'   => 3000,
                'showConfirmButton' => false,
            ]);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
{
        Gate::authorize('category.delete', $category);
    // 1. Verificar si tiene productos asociados
    if ($category->products()->exists()) {
        return redirect()
            ->route('admin.categories.index')
            ->with('sweet-alert', [
                'icon'    => 'error',
                'title'   => '¡No permitido!',
                'text'    => 'La categoría tiene productos asociados y no puede eliminarse.',
                'timer'   => 4000,
                'showConfirmButton' => true,
            ]);
    }

    // 2. Si no tiene productos, eliminar
    $category->delete();

    // 3. Redirigir con SweetAlert de éxito
    return redirect()
        ->route('admin.categories.index')
        ->with('sweet-alert', [
            'icon'              => 'success',
            'title'             => '¡Categoría eliminada!',
            'text'              => 'La categoría ha sido eliminada correctamente.',
            'timer'             => 3000,
            'showConfirmButton' => false,
        ]);
}
 public function import(Request $request)
    {
        Gate::authorize('category.create', Category::class);

       return view('admin.categories.import');
    }
}
