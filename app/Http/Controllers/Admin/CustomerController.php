<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Models\Identity;
use App\Models\CustomerAddress;
use Illuminate\Support\Facades\Gate;


class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize('customer.view', Customer::class);
        
        return view('admin.customers.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('customer.create', Customer::class);
        $identities = Identity::all();
        return view('admin.customers.create', compact('identities'));
    }

    /**
     * Store a newly created resource in storage.
     */
  public function store(Request $request)
    {
        Gate::authorize('customer.create', Customer::class);
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
            'address'         => 'nullable|string|max:255', // legacy quick field
            'email'           => 'nullable|email|max:255',
            'phone'           => 'nullable|string|max:50',
            'addresses'                   => 'array',
            'addresses.*.id'              => 'nullable|integer',
            'addresses.*.label'           => 'nullable|string|max:50',
            'addresses.*.address'         => 'required_with:addresses|string|max:255',
            'addresses.*.is_primary'      => 'nullable|boolean',
        ]);

        // Addresses come separately; avoid mass-assigning 'addresses'
        $addressesInput = $request->input('addresses', []);
        unset($data['addresses']);

        $customer = Customer::create($data);

        $this->syncAddresses($customer, $addressesInput, $data['address'] ?? null);

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
        Gate::authorize('customer.update', $customer);
        $identities = Identity::orderBy('name')->get();
        return view('admin.customers.edit', compact('customer', 'identities'));
    }

    /**
     * Update the specified resource in storage.
     */
        public function update(Request $request, Customer $customer)
    {
        Gate::authorize('customer.update', $customer);
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
            'addresses'                   => 'array',
            'addresses.*.id'              => 'nullable|integer',
            'addresses.*.label'           => 'nullable|string|max:50',
            'addresses.*.address'         => 'required_with:addresses|string|max:255',
            'addresses.*.is_primary'      => 'nullable|boolean',
        ]);

        $addressesInput = $request->input('addresses', []);
        unset($data['addresses']);

        $customer->update($data);
        $this->syncAddresses($customer, $addressesInput, $data['address'] ?? null);

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
        Gate::authorize('customer.delete', $customer);
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

    /**
     * Sincroniza direcciones enviadas desde el formulario create/edit.
     * - Crea nuevas (sin id)
     * - Actualiza existentes (con id)
     * - Elimina las que no vienen
     * - Garantiza una principal (usa la marcada; si ninguna, toma primera; si legacy address existe y no hay ninguna, crea principal)
     */
    protected function syncAddresses(Customer $customer, array $addressesInput, ?string $legacyAddress = null): void
    {
        // Normalizar array (filtrar vacías)
        $addresses = collect($addressesInput)
            ->filter(fn($a) => isset($a['address']) && trim($a['address']) !== '')
            ->values();

        if($addresses->isEmpty()) {
            // Si no hay array y hay legacy y no existen direcciones previas: crear una
            if($legacyAddress && !$customer->addresses()->exists()) {
                $customer->addresses()->create([
                    'label' => 'Principal',
                    'address' => $legacyAddress,
                    'is_primary' => true,
                ]);
                $customer->address = $legacyAddress; // mantener campo legacy sincronizado
                $customer->save();
            }
            return;
        }

        $incomingIds = $addresses->pluck('id')->filter()->map(fn($v)=>(int)$v)->all();
        $existing = $customer->addresses()->get();
        $existingIds = $existing->pluck('id')->all();

        // Eliminar los que ya no vienen
        $toDelete = array_diff($existingIds, $incomingIds);
        if(count($toDelete)) {
            $customer->addresses()->whereIn('id', $toDelete)->delete();
        }

        $markedPrimaryId = null;

        foreach($addresses as $row) {
            $isPrimary = !empty($row['is_primary']);
            if(!empty($row['id'])) {
                $addr = $customer->addresses()->whereKey($row['id'])->first();
                if($addr) {
                    $addr->update([
                        'label' => $row['label'] ?? null,
                        'address' => $row['address'],
                        // is_primary se setea luego globalmente
                    ]);
                    if($isPrimary) { $markedPrimaryId = $addr->id; }
                }
            } else {
                $addr = $customer->addresses()->create([
                    'label' => $row['label'] ?? null,
                    'address' => $row['address'],
                    'is_primary' => false,
                ]);
                if($isPrimary) { $markedPrimaryId = $addr->id; }
            }
        }

        // Resolver principal
        if(!$markedPrimaryId) {
            // Tomar primera existente
            $first = $customer->addresses()->orderBy('id')->first();
            $markedPrimaryId = $first?->id;
        }

        // Reset y marcar primaria
        $customer->addresses()->update(['is_primary' => false]);
        if($markedPrimaryId) {
            $customer->addresses()->whereKey($markedPrimaryId)->update(['is_primary' => true]);
            $primary = $customer->addresses()->whereKey($markedPrimaryId)->first();
            if($primary) {
                $customer->address = $primary->address; // sincroniza campo legacy
                $customer->save();
            }
        }
    }

    /**
     * Formulario de importación masiva vía Excel.
     */
    public function import(Request $request)
    {
    Gate::authorize('customer.import', Customer::class);
        return view('admin.customers.import');
    }
}
