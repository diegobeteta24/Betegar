<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Models\Identity;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    /**
     * Display a listing of the suppliers.
     */
    public function index()
    {
        $suppliers = Supplier::with('identity')->paginate(15);
        return view('admin.suppliers.index', compact('suppliers'));
    }

    /**
     * Show the form for creating a new supplier.
     */
    public function create()
    {
        $identities = Identity::orderBy('name')->get();
        return view('admin.suppliers.create', compact('identities'));
    }

    /**
     * Store a newly created supplier in storage.
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
                    if ($id === 2 && Supplier::where('document_number', $value)->exists()) {
                        return $fail("Ya existe un proveedor con este DPI.");
                    }

                    // 4) NIT (id=3) → permitimos duplicados
                },
            ],
            'name'            => 'required|string|max:255',
            'address'         => 'nullable|string|max:255',
            'email'           => 'nullable|email|max:255',
            'phone'           => 'nullable|string|max:50',
        ]);

        $supplier = Supplier::create($data);

        return redirect()
            ->route('admin.suppliers.edit', $supplier)
            ->with('sweet-alert', [
                'icon'              => 'success',
                'title'             => '¡Proveedor creado!',
                'text'              => 'Ahora puedes editar los detalles.',
                'timer'             => 3000,
                'showConfirmButton' => false,
            ]);
    }

    /**
     * Show the form for editing the specified supplier.
     */
    public function edit(Supplier $supplier)
    {
        $identities = Identity::orderBy('name')->get();
        return view('admin.suppliers.edit', compact('supplier', 'identities'));
    }

    /**
     * Update the specified supplier in storage.
     */
    public function update(Request $request, Supplier $supplier)
    {
        $data = $request->validate([
            'identity_id'     => 'required|exists:identities,id',
            'document_number' => [
                'required',
                function ($attribute, $value, $fail) use ($request, $supplier) {
                    $id = (int) $request->input('identity_id');

                    // 1) Sin documento → sólo CF
                    if ($id === 1 && strtoupper($value) !== 'CF') {
                        return $fail("Cuando la identidad es 'Sin documento', el número debe ser 'CF'.");
                    }

                    // 2) No sin documento → no permitir CF
                    if ($id !== 1 && strtoupper($value) === 'CF') {
                        return $fail("'CF' sólo está permitido si no posee documento.");
                    }

                    // 3) DPI (id=2) → único excepto este proveedor
                    if ($id === 2 && Supplier::where('document_number', $value)
                                             ->where('id', '!=', $supplier->id)
                                             ->exists()) {
                        return $fail("Ya existe un proveedor con este DPI.");
                    }

                    // 4) NIT (id=3) → permitimos duplicados
                },
            ],
            'name'            => 'required|string|max:255',
            'address'         => 'nullable|string|max:255',
            'email'           => 'nullable|email|max:255',
            'phone'           => 'nullable|string|max:50',
        ]);

        $supplier->update($data);

        return redirect()
            ->route('admin.suppliers.edit', $supplier)
            ->with('sweet-alert', [
                'icon'              => 'success',
                'title'             => '¡Proveedor actualizado!',
                'text'              => 'Los cambios se guardaron correctamente.',
                'timer'             => 3000,
                'showConfirmButton' => false,
            ]);
    }

    /**
     * Remove the specified supplier from storage.
     */
    public function destroy(Supplier $supplier)
    {
        if ($supplier->purchaseOrders()->exists() || $supplier->purchases()->exists()) {
            return redirect()
                ->route('admin.suppliers.index')
                ->with('sweet-alert', [
                    'icon'              => 'error',
                    'title'             => '¡No permitido!',
                    'text'              => 'El proveedor tiene órdenes de compra o compras asociadas y no puede eliminarse.',
                    'timer'             => 4000,
                    'showConfirmButton' => true,
                ]);
        }

        $supplier->delete();

        return redirect()
            ->route('admin.suppliers.index')
            ->with('sweet-alert', [
                'icon'              => 'success',
                'title'             => '¡Proveedor eliminado!',
                'text'              => 'El proveedor ha sido eliminado correctamente.',
                'timer'             => 3000,
                'showConfirmButton' => false,
            ]);
    }
}
