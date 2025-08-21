<?php

namespace App\Http\Controllers;

use App\Models\WorkOrder;
use App\Models\WorkOrderEntry;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use App\Notifications\TechnicianEvent;
use App\Models\User;

class WorkOrderEntryController extends Controller
{
    public function index(WorkOrder $workOrder)
    {
        $this->authorize('view', $workOrder);
        return $workOrder->entries()->with(['user', 'images', 'signature'])->orderBy('work_date', 'desc')->get();
    }

    public function store(Request $request, WorkOrder $workOrder)
    {
        $this->authorize('update', $workOrder);

        if ($workOrder->status === 'done') {
            return response()->json(['message' => 'La orden está finalizada y no permite nuevas entradas.'], 422);
        }

        $data = $request->validate([
            'progress' => ['required','string'],
            'requests' => ['nullable','string'],
            'images.*' => ['nullable','image','max:8192'],
            'signature' => ['nullable','image','max:4096'],
        ]);

        $todayGt = Carbon::now('America/Guatemala')->toDateString();
        $entry = WorkOrderEntry::create([
            'work_order_id' => $workOrder->id,
            'user_id' => $request->user()->id,
            'work_date' => $todayGt,
            'progress' => $data['progress'],
            'requests' => $data['requests'] ?? null,
        ]);

        // Guardar imágenes
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('work-entries', 'public');
                $entry->images()->create([
                    'path' => $path,
                    'size' => $file->getSize(),
                ]);
            }
        }

        // Guardar firma (única por entrada)
        if ($request->hasFile('signature')) {
            $file = $request->file('signature');
            $path = $file->store('work-entries', 'public');
            $entry->signature()->create([
                'path' => $path,
                'size' => $file->getSize(),
                'tag'  => 'signature',
            ]);
            $entry->update([
                'signed_at' => now('UTC'),
                'signature_by' => $request->user()->name,
            ]);
        }

        // If technician marked it done, allow passing status=done
        $statusBefore = $workOrder->status;
        $markedDone = false;
        if ($request->boolean('mark_done')) {
            $workOrder->update(['status' => 'done']);
            $markedDone = true;
        } else {
            if ($workOrder->status === 'pending') {
                $workOrder->update(['status' => 'in_progress']);
            }
        }

        // Push notification: nueva entrada
        User::role('admin')->each(function($admin) use ($workOrder, $entry, $request){
            $admin->notify(new TechnicianEvent(
                'Nueva entrada O.T. #'.$workOrder->id,
                $request->user()->name.' registró avance',
                [
                    'type' => 'workorder_entry',
                    'work_order_id' => $workOrder->id,
                    'entry_id' => $entry->id,
                    'status' => $workOrder->status,
                    'url' => route('admin.work-orders.show', $workOrder),
                    'tag' => 'wo-'.$workOrder->id,
                    'ts' => now()->timestamp,
                ]
            ));
        });

        // Push adicional si se marcó como finalizada (transición a done)
        if($markedDone && $statusBefore !== 'done'){
            User::role('admin')->each(function($admin) use ($workOrder, $request){
                $admin->notify(new TechnicianEvent(
                    'Orden finalizada #'.$workOrder->id,
                    $request->user()->name.' marcó la orden como finalizada',
                    [
                        'type' => 'workorder_done',
                        'work_order_id' => $workOrder->id,
                        'status' => 'done',
                        'url' => route('admin.work-orders.show', $workOrder),
                        'tag' => 'wo-'.$workOrder->id,
                        'ts' => now()->timestamp,
                    ]
                ));
            });
        }

        return $entry->load(['images', 'signature']);
    }
}
