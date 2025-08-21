<?php

namespace App\Livewire\Technician;

use App\Models\WorkOrder;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class WorkOrdersList extends Component
{
    public $orders = [];

    public function mount(): void
    {
        $user = Auth::user();
        $this->orders = WorkOrder::pending()->forTechnician($user->id)->with('customer')->orderBy('id','desc')->get();
    }

    public function render()
    {
        return view('livewire.technician.work-orders-list');
    }
}
