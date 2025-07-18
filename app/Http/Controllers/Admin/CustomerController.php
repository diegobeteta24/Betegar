<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Models\Identity;


class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
        return view('admin.customers.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $identities = Identity::all();
        return view('admin.customers.create', compact('identities'));
    }

    /**
     * Store a newly created resource in storage.
     */
  public function store(Request $request)
    {
        $data = $request->validate([
            'identity_id'     => 'required|exists:identities,id',
            'document_number' => [
                'required',
                function ($attribute, $value, $fail) use ($request) {
                    $id = (int) $request->input('identity_id');

                    // 1) Sin documento → sólo CF
                    if ($id === 1 && strtoupper($value) !== 'CF') {
                        return $fail("Cuando la identidad es 'Sin documento', el número debe ser 'CF'.");
                    }

                    // 2) No sin documento → no permitir CF
                    if ($id !== 1 && strtoupper($value) === 'CF') {
                        return $fail("'CF' sólo está permitido si no posee documento.");
                    }

                    // 3) DPI (id=2) → único
                    if ($id === 2 && Customer::where('document_number', $value)->exists()) {
                        return $fail("Ya existe un cliente con este DPI.");
                    }

                    // 4) NIT (id=3) → permitimos duplicados
                },
            ],
            'name'            => 'required|string|max:255',
            'address'         => 'nullable|string|max:255',
            'email'           => 'nullable|email|max:255',
            'phone'           => 'nullable|string|max:50',
        ]);

        $customer = Customer::create($data);

        return redirect()
            ->route('admin.customers.index', $customer)
            ->with('sweet-alert', [
                'icon'              => 'success',
                'title'             => '¡Cliente creado!',
                'text'              => 'Ahora puedes editar los detalles.',
                'timer'             => 3000,
                'showConfirmButton' => false,
            ]);
    }



   

    /**
     * Show the form for editing the specified resource.
     */
     public function edit(Customer $customer)
    {
        $identities = Identity::orderBy('name')->get();
        return view('admin.customers.edit', compact('customer', 'identities'));
    }

    /**
     * Update the specified resource in storage.
     */
        public function update(Request $request, Customer $customer)
    {
        $data = $request->validate([
            'identity_id'     => 'required|exists:identities,id',
            'document_number' => [
                'required',
                function ($attribute, $value, $fail) use ($request, $customer) {
                    $id = (int) $request->input('identity_id');

                    // 1) Sin documento → sólo CF
                    if ($id === 1 && strtoupper($value) !== 'CF') {
                        return $fail("Cuando la identidad es 'Sin documento', el número debe ser 'CF'.");
                    }

                    // 2) No sin documento → no permitir CF
                    if ($id !== 1 && strtoupper($value) === 'CF') {
                        return $fail("'CF' sólo está permitido si no posee documento.");
                    }

                    // 3) DPI (id=2) → único, excepto este cliente
                    if ($id === 2 && Customer::where('document_number', $value)
                                             ->where('id', '!=', $customer->id)
                                             ->exists()) {
                        return $fail("Ya existe un cliente con este DPI.");
                    }

                    // 4) NIT (id=3) → permitimos duplicados
                },
            ],
            'name'            => 'required|string|max:255',
            'address'         => 'nullable|string|max:255',
            'email'           => 'nullable|email|max:255',
            'phone'           => 'nullable|string|max:50',
        ]);

        $customer->update($data);

        return redirect()
            ->route('admin.customers.edit', $customer)
            ->with('sweet-alert', [
                'icon'              => 'success',
                'title'             => '¡Cliente actualizado!',
                'text'              => 'Los cambios se guardaron correctamente.',
                'timer'             => 3000,
                'showConfirmButton' => false,
            ]);
    }

    /**
     * Remove the specified resource from storage.
     */
public function destroy(Customer $customer)
{
    // Si tiene cotizaciones o ventas, no permitir borrar
    if ($customer->quotes()->exists() || $customer->sales()->exists()) {
        return redirect()
            ->route('admin.customers.index')
            ->with('sweet-alert', [
                'icon'              => 'error',
                'title'             => '¡No permitido!',
                'text'              => 'El cliente tiene cotizaciones o ventas asociadas y no puede eliminarse.',
                'timer'             => 4000,
                'showConfirmButton' => true,
            ]);
    }

    // Si no tiene, eliminar normalmente
    $customer->delete();

    return redirect()
        ->route('admin.customers.index')
        ->with('sweet-alert', [
            'icon'              => 'success',
            'title'             => '¡Cliente eliminado!',
            'text'              => 'El cliente ha sido eliminado correctamente.',
            'timer'             => 3000,
            'showConfirmButton' => false,
        ]);
}
}
