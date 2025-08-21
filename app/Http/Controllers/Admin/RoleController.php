<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Logic to retrieve and return a list of roles
        return view('admin.roles.index');
    }

    /**
     * Show the form for creating a new resource.
     */
   public function create()
    {
        // Cargamos todos los permisos aquí
        $permissions = Permission::all();

        return view('admin.roles.create', compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => ['required', 'string', 'max:255', 'unique:roles,name'],
            'permissions'   => ['nullable', 'array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ]);

        $role = Role::create(['name' => $data['name']]);

        // sync by permission names
        $names = Permission::whereIn('id', $data['permissions'] ?? [])->pluck('name');
        $role->syncPermissions($names);

        return redirect()
            ->route('admin.roles.index')
            ->with('sweet-alert', [
                'icon'              => 'success',
                'title'             => '¡Rol creado!',
                'text'              => 'El rol se ha creado correctamente.',
                'timer'             => 3000,
                'showConfirmButton' => false,
            ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        // Logic to display a specific role
        return view('admin.roles.show', compact('role'));
    }

    /**
     * Show the form for editing the specified resource.
     */
   public function edit(Role $role)
    {
        $permissions = Permission::all();

        return view('admin.roles.edit', compact('role', 'permissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        $data = $request->validate([
            'name'          => ['required', 'string', 'max:255', "unique:roles,name,{$role->id}"],
            'permissions'   => ['nullable', 'array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ]);

        $role->update(['name' => $data['name']]);

        $names = Permission::whereIn('id', $data['permissions'] ?? [])->pluck('name');
        $role->syncPermissions($names);

        return redirect()
            ->route('admin.roles.index')
            ->with('sweet-alert', [
                'icon'              => 'success',
                'title'             => '¡Rol actualizado!',
                'text'              => 'Los cambios se han guardado correctamente.',
                'timer'             => 3000,
                'showConfirmButton' => false,
            ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        // 1. Delete
        $role->delete();

        // 2. Redirect with success
        return redirect()
            ->route('admin.roles.index')
            ->with('sweet-alert', [
                'icon'              => 'success',
                'title'             => '¡Rol eliminado!',
                'text'              => 'El rol ha sido eliminado.',
                'timer'             => 3000,
                'showConfirmButton' => false,
            ]);
    }
}
