<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        return view('admin.users.index');
    }

    public function create()
    {
        // Obtiene todos los roles (clave = nombre, valor = nombre)
        $roles = Role::pluck('name','name');
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        // 1. Validar datos incluyendo el rol
        $data = $request->validate([
            'name'     => ['required','string','max:255','unique:users,name'],
            'email'    => ['required','email','max:255','unique:users,email'],
            'password' => ['required','string','min:8','confirmed'],
            'role'     => ['required','exists:roles,name'],
        ]);

        // 2. Hashear contraseña
        $data['password'] = bcrypt($data['password']);

        // 3. Crear usuario
        $user = User::create($data);

        // 4. Asignar rol
        $user->assignRole($data['role']);

        // 5. Redirigir con mensaje
        return redirect()
            ->route('admin.users.index')
            ->with('sweet-alert', [
                'icon'    => 'success',
                'title'   => '¡Usuario creado!',
                'text'    => 'Ahora puedes editar los detalles.',
                'timer'   => 3000,
                'showConfirmButton' => false,
            ]);
    }

    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        // Roles para el select
        $roles = Role::pluck('name','name');
        // Rol actual (para marcarlo en el form)
        $currentRole = $user->roles->pluck('name')->first();
        return view('admin.users.edit', compact('user','roles','currentRole'));
    }

    public function update(Request $request, User $user)
    {
        // 1. Validar datos + rol
        $data = $request->validate([
            'name'     => ['required','string','max:255',"unique:users,name,{$user->id}"],
            'email'    => ['required','email','max:255',"unique:users,email,{$user->id}"],
            'password' => ['nullable','string','min:8','confirmed'],
            'role'     => ['required','exists:roles,name'],
        ]);

        // 2. Hashear o descartar password
        if (! empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        // 3. Actualizar usuario
        $user->update($data);

        // 4. Sincronizar rol
        $user->syncRoles($data['role']);

        // 5. Redirigir con mensaje
        return redirect()
            ->route('admin.users.index')
            ->with('sweet-alert', [
                'icon'             => 'success',
                'title'            => '¡Usuario actualizado!',
                'text'             => 'Los datos se guardaron correctamente.',
                'timer'            => 3000,
                'showConfirmButton'=> false,
            ]);
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('sweet-alert', [
                'icon'             => 'success',
                'title'            => '¡Usuario eliminado!',
                'text'             => 'El usuario ha sido eliminado correctamente.',
                'timer'            => 3000,
                'showConfirmButton'=> false,
            ]);
    }
}
