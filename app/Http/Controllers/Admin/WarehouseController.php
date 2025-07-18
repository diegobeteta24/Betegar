<?php
// app/Http/Controllers/Admin/WarehouseController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    /**
     * Display a listing of the warehouses.
     */
    public function index()
    {
        return view('admin.warehouses.index');
    }

    /**
     * Show the form for creating a new warehouse.
     */
    public function create()
    {
        return view('admin.warehouses.create');
    }

    /**
     * Store a newly created warehouse in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255', 'unique:warehouses,name'],
            'location' => ['nullable', 'string', 'max:255'],
        ]);

        $warehouse = Warehouse::create($data);

        return redirect()
            ->route('admin.warehouses.edit', $warehouse)
            ->with('sweet-alert', [
                'icon'              => 'success',
                'title'             => '¡Almacén creado!',
                'text'              => 'Ahora puedes editar los detalles.',
                'timer'             => 3000,
                'showConfirmButton' => false,
            ]);
    }

    /**
     * Show the form for editing the specified warehouse.
     */
    public function edit(Warehouse $warehouse)
    {
        return view('admin.warehouses.edit', compact('warehouse'));
    }

    /**
     * Update the specified warehouse in storage.
     */
    public function update(Request $request, Warehouse $warehouse)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255', "unique:warehouses,name,{$warehouse->id}"],
            'location' => ['nullable', 'string', 'max:255'],
        ]);

        $warehouse->update($data);

        return redirect()
            ->route('admin.warehouses.edit', $warehouse)
            ->with('sweet-alert', [
                'icon'              => 'success',
                'title'             => '¡Almacén actualizado!',
                'text'              => 'Los cambios se guardaron correctamente.',
                'timer'             => 3000,
                'showConfirmButton' => false,
            ]);
    }

    /**
     * Remove the specified warehouse from storage.
     */
 public function destroy(Warehouse $warehouse)
{
    // 1. Validar que no tenga inventario asociado
    if ($warehouse->inventories()->exists()) {
        return redirect()
            ->route('admin.warehouses.index')
            ->with('sweet-alert', [
                'icon'              => 'error',
                'title'             => '¡No permitido!',
                'text'              => 'El almacén tiene inventario asociado y no puede eliminarse.',
                'timer'             => 4000,
                'showConfirmButton' => true,
            ]);
    }

    // 2. Si no tiene inventario, eliminar
    $warehouse->delete();

    return redirect()
        ->route('admin.warehouses.index')
        ->with('sweet-alert', [
            'icon'              => 'success',
            'title'             => '¡Almacén eliminado!',
            'text'              => 'El almacén ha sido eliminado correctamente.',
            'timer'             => 3000,
            'showConfirmButton' => false,
        ]);
}

public function import(Request $request)
    {
       return view('admin.warehouses.import');
    }
}
