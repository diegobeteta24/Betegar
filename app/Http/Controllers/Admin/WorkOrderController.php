<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\User;
use App\Models\WorkOrder;
use App\Notifications\WorkOrderCreated;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WorkOrderController extends Controller
{
    public function index()
    {
        $this->authorize('create', WorkOrder::class); // admin-only section
        return view('admin.work_orders.index');
    }

    public function create()
    {
        $this->authorize('create', WorkOrder::class);
        $customers = Customer::orderBy('name')->get(['id','name']);
        $technicians = User::whereHas('roles', fn($q) => $q->where('name','technician'))
            ->orderBy('name')->get(['id','name']);
        return view('admin.work_orders.create', compact('customers','technicians'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', WorkOrder::class);
        $data = $request->validate([
            'customer_id' => ['required','exists:customers,id'],
            'address'     => ['required','string','max:255'],
            'objective'   => ['required','string'],
            'status'      => ['nullable', Rule::in(['pending','in_progress','done','cancelled'])],
            'technicians' => ['required','array','min:1'],
            'technicians.*' => ['integer','exists:users,id'],
        ], [], [
            'customer_id' => 'Cliente',
            'address'     => 'Dirección',
            'objective'   => 'Objetivo',
            'status'      => 'Estado',
            'technicians' => 'Técnicos',
        ]);
        $data['status'] = $data['status'] ?? 'pending';

        // Persist work order; keep legacy user_id as first selected for compatibility
        $order = WorkOrder::create([
            'customer_id' => $data['customer_id'],
            'user_id'     => $data['technicians'][0],
            'address'     => $data['address'],
            'objective'   => $data['objective'],
            'status'      => $data['status'],
        ]);
        $order->technicians()->sync($data['technicians']);

        // Notificaciones push a admins y técnicos asignados
        try {
            $technicianUsers = \App\Models\User::whereIn('id', $data['technicians'])->get(['id','name']);
            $technicianNames = $technicianUsers->pluck('name')->all();
            $notification = new WorkOrderCreated($order->id, $order->customer->name ?? 'Cliente', $technicianNames);
            // técnicos asignados
            foreach($technicianUsers as $tech){
                try { $tech->notify($notification); } catch(\Throwable $e) { \Log::warning('Notif tech order create', ['tech'=>$tech->id,'err'=>$e->getMessage()]); }
            }
            // admins
            $admins = \App\Models\User::role('admin')->get();
            foreach($admins as $admin){
                try { $admin->notify($notification); } catch(\Throwable $e) { \Log::warning('Notif admin order create', ['admin'=>$admin->id,'err'=>$e->getMessage()]); }
            }
        } catch(\Throwable $e){ \Log::error('Error enviando notificaciones WorkOrderCreated', ['err'=>$e->getMessage()]); }

        return redirect()->route('admin.work-orders.index')->with('sweet-alert', [
            'icon' => 'success',
            'title' => 'Orden creada',
            'text' => 'La orden de trabajo fue creada y técnicos asignados.',
            'timer' => 2500,
            'showConfirmButton' => false,
        ]);
    }

    public function show(WorkOrder $workOrder)
    {
        $this->authorize('create', WorkOrder::class); // sección sólo admin
        $workOrder->load(['customer','technicians','entries.user','entries.images','entries.signature']);
        return view('admin.work_orders.show', compact('workOrder'));
    }

    /**
     * Formulario importación masiva de órdenes de trabajo.
     */
    public function import(Request $request)
    {
        $this->authorize('create', WorkOrder::class); // reuse create ability
        return view('admin.work_orders.import');
    }
}
