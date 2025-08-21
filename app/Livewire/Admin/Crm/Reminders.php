<?php

namespace App\Livewire\Admin\Crm;

use Livewire\Component;
use App\Models\Reminder;
use App\Models\Quote;
use Illuminate\Support\Facades\Auth;
use App\Notifications\ReminderCreated;
use Illuminate\Support\Carbon;

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
        $rem = Reminder::create([
            'quote_id' => $this->quote_id,
            'user_id' => Auth::id(),
            'type' => $this->type,
            'notes' => $this->notes,
            'remind_at' => $this->remind_at,
        ]);

        $this->reset(['notes','remind_at','type','quote_id']);
        $this->dispatch('swal', ['title' => 'Recordatorio creado', 'icon' => 'success']);

        // Programar notificación para la fecha/hora indicada (zona Guatemala)
        if(Auth::user()){
            if($rem->remind_at){
                $gtz = config('app.tz_guatemala','America/Guatemala');
                $when = $rem->remind_at->copy()->timezone($gtz);
                // Si la hora está en el pasado, manda inmediatamente
                $delay = $when->isPast() ? now() : $when;
                Auth::user()->notify((new ReminderCreated(
                    'Recordatorio',
                    ($rem->notes ?: 'Recordatorio programado para ').$when->format('d/m/Y H:i'),
                    $rem->id
                ))->delay($delay));
            } else {
                // Sin fecha -> inmediato
                Auth::user()->notify(new ReminderCreated(
                    'Recordatorio (sin fecha)',
                    $rem->notes ?: 'Recordatorio creado',
                    $rem->id
                ));
            }
        }
    }

    public function toggleCompleted(Reminder $r)
    {
        $r->completed = !$r->completed;
        $r->save();
    }
}
