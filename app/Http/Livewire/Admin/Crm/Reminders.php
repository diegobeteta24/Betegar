<?php

namespace App\Http\Livewire\Admin\Crm;

use Livewire\Component;
use App\Models\Reminder;
use App\Models\Quote;
use Illuminate\Support\Facades\Auth;

class Reminders extends Component
{
    public $notes = '';
    public $remind_at;
    public $type = 'follow_up';
    public $quote_id;

    protected $rules = [
        'notes' => 'nullable|string',
        'remind_at' => 'nullable|date',
        'type' => 'required|string',
        'quote_id' => 'nullable|exists:quotes,id',
    ];

    public function mount($quote = null)
    {
        $this->quote_id = $quote;
    }

    public function render()
    {
        $reminders = Reminder::with(['quote','user'])->orderBy('remind_at','desc')->get();
        $quotes = Quote::orderBy('id','desc')->limit(50)->get();
        return view('livewire.admin.crm.reminders', compact('reminders','quotes'));
    }

    public function store()
    {
        $this->validate();
        Reminder::create([
            'quote_id' => $this->quote_id,
            'user_id' => Auth::id(),
            'type' => $this->type,
            'notes' => $this->notes,
            'remind_at' => $this->remind_at,
        ]);

        $this->reset(['notes','remind_at','type','quote_id']);
        $this->dispatch('swal', ['title' => 'Recordatorio creado', 'icon' => 'success']);
    }

    public function toggleCompleted(Reminder $r)
    {
        $r->completed = !$r->completed;
        $r->save();
    }
}
