<?php

namespace App\Livewire\Technician;

use App\Models\WorkOrder;
use App\Models\WorkOrderEntry;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Carbon\Carbon;
use App\Notifications\TechnicianEvent;
use App\Models\User;

class WorkOrderShow extends Component
{
    use WithFileUploads;

    public WorkOrder $workOrder;
    public string $progress = '';
    public ?string $requests = null;
    public array $images = [];
    public $signature = null; // temporary uploaded file
    public ?string $signatureData = null; // base64 PNG from SignaturePad
    public string $resultStatus = 'pending'; // 'pending' or 'done'

    public function mount(WorkOrder $workOrder)
    {
        $this->workOrder = $workOrder->load(['customer','technician']);
        Gate::authorize('view', $this->workOrder);
    }

    public function saveEntry()
    {
        Gate::authorize('update', $this->workOrder);

        if ($this->workOrder->status === 'done') {
            $this->addError('progress', 'La orden está finalizada y no permite nuevas entradas.');
            return;
        }

        $data = $this->validate([
            'progress' => ['required','string'],
            'requests' => ['nullable','string'],
            'images.*' => ['nullable','image','max:8192'],
            'signature' => ['nullable','image','max:4096'],
        ]);

        // Create entry
        $today = Carbon::now(config('app.tz_guatemala','America/Guatemala'))->toDateString();
        $entry = WorkOrderEntry::create([
            'work_order_id' => $this->workOrder->id,
            'user_id' => Auth::id(),
            'work_date' => $today,
            'progress' => $data['progress'],
            'requests' => $data['requests'] ?? null,
        ]);

        // Save photos
        foreach ($this->images as $file) {
            $path = $file->store('work-entries', 'public');
            $entry->images()->create([
                'path' => $path,
                'size' => $file->getSize(),
            ]);
        }

        // Save signature (one)
        if ($this->signature) {
            $path = $this->signature->store('work-entries', 'public');
            $entry->signature()->create([
                'path' => $path,
                'size' => $this->signature->getSize(),
                'tag'  => 'signature',
            ]);
            $entry->update([
                'signed_at' => now('UTC'),
                'signature_by' => Auth::user()->name,
            ]);
        }

        // Save signature from SignaturePad (base64 PNG) if provided and no file uploaded
        if (!$this->signature && $this->signatureData) {
            $raw = preg_replace('#^data:image/\w+;base64,#i', '', $this->signatureData);
            $binary = base64_decode($raw);
            $fileName = 'work-entries/signature_'.$entry->id.'_'.time().'.png';
            Storage::disk('public')->put($fileName, $binary);
            $entry->signature()->create([
                'path' => $fileName,
                'size' => strlen($binary),
                'tag'  => 'signature',
            ]);
            $entry->update([
                'signed_at' => now('UTC'),
                'signature_by' => Auth::user()->name,
            ]);
        }

        // Update work order status according to technician selection
        $statusBefore = $this->workOrder->status;
        $markDone = ($this->resultStatus === 'done');
        if ($markDone) {
            $this->workOrder->update(['status' => 'done']);
        } else {
            if ($this->workOrder->status === 'pending') {
                $this->workOrder->update(['status' => 'in_progress']);
            }
        }

        // Push: nueva entrada
        User::role('admin')->each(function($admin){
            $admin->notify(new TechnicianEvent(
                'Nueva entrada O.T. #'.$this->workOrder->id,
                Auth::user()->name.' registró avance',
                [
                    'type' => 'workorder_entry',
                    'work_order_id' => $this->workOrder->id,
                    'status' => $this->workOrder->status,
                    'url' => route('admin.work-orders.show', $this->workOrder),
                    'tag' => 'wo-'.$this->workOrder->id,
                    'ts' => now()->timestamp,
                ]
            ));
        });

        // Push: finalizada
        if($markDone && $statusBefore !== 'done'){
            User::role('admin')->each(function($admin){
                $admin->notify(new TechnicianEvent(
                    'Orden finalizada #'.$this->workOrder->id,
                    Auth::user()->name.' marcó la orden como finalizada',
                    [
                        'type' => 'workorder_done',
                        'work_order_id' => $this->workOrder->id,
                        'status' => 'done',
                        'url' => route('admin.work-orders.show', $this->workOrder),
                        'tag' => 'wo-'.$this->workOrder->id,
                        'ts' => now()->timestamp,
                    ]
                ));
            });
        }

        // Reset form and refresh order entries via event
    $this->reset(['progress','requests','images','signature','signatureData']);
    $this->workOrder = $this->workOrder->fresh();
        $this->dispatch('entry-created');
        session()->flash('saved', 'Entrada registrada.');
    }

    public function render()
    {
        $entries = $this->workOrder->entries()->with(['user','images','signature'])
            ->orderByDesc('work_date')->get();
        return view('livewire.technician.work-order-show', [
            'entries' => $entries,
        ]);
    }
}
