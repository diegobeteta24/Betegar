<?php

namespace App\Http\Controllers;

use App\Models\WorkOrder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Notifications\TechnicianEvent;
use App\Models\User;

class WorkOrderController extends Controller
{
    public function indexForTechnician(Request $request)
    {
        $user = $request->user();
        $orders = WorkOrder::query()
            ->pending()
            ->forTechnician($user->id)
            ->with(['customer'])
            ->orderBy('id', 'desc')
            ->get();
        return response()->json($orders);
    }

    public function store(Request $request)
    {
        $this->authorize('create', WorkOrder::class);
        $data = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'user_id' => ['required', 'exists:users,id'],
            'address' => ['required', 'string', 'max:255'],
            'objective' => ['required', 'string'],
            'status' => ['nullable', Rule::in(['pending','in_progress','done','cancelled'])],
        ]);
        $data['status'] = $data['status'] ?? 'pending';
        return WorkOrder::create($data);
    }

    public function show(WorkOrder $workOrder, Request $request)
    {
        $this->authorize('view', $workOrder);
        return $workOrder->load(['customer', 'technician']);
    }

    public function update(WorkOrder $workOrder, Request $request)
    {
        $this->authorize('update', $workOrder);
        $data = $request->validate([
            'address' => ['sometimes','string','max:255'],
            'objective' => ['sometimes','string'],
            'status' => ['sometimes', Rule::in(['pending','in_progress','done','cancelled'])],
        ]);
        $originalStatus = $workOrder->status;
        $workOrder->update($data);
        if(array_key_exists('status',$data) && $data['status'] !== $originalStatus){
            // Notificar admins del cambio de estado
            User::role('admin')->each(function($admin) use ($workOrder, $data){
                $admin->notify(new TechnicianEvent(
                    'Orden actualizada',
                    'Orden #'.$workOrder->id.' ahora está '.$data['status'],
                    ['type'=>'workorder_status','work_order_id'=>$workOrder->id,'status'=>$data['status']]
                ));
            });
        }
        return $workOrder->fresh();
    }

    // Web page to view a work order with entries UI
    public function showPage(Request $request, WorkOrder $workOrder)
    {
        $this->authorize('view', $workOrder);
        return view('work-orders.show', ['workOrder' => $workOrder]);
    }
}
