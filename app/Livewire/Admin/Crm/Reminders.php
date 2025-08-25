<?php

namespace App\Livewire\Admin\Crm;

use Livewire\Component;
use App\Models\Reminder;
use App\Models\Quote;
use Illuminate\Support\Facades\Auth;
use App\Notifications\ReminderCreated;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;

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
        $user = Auth::user();
        if($user){
            try {
                $hasVapid = (bool) config('webpush.vapid.public_key');
                $hasSubs  = method_exists($user, 'pushSubscriptions') ? $user->pushSubscriptions()->exists() : false;
                Log::info('[CRM][Reminder] Notif precheck', ['vapid'=>$hasVapid, 'subs'=>$hasSubs, 'user_id'=>$user->id]);
                if(! $hasVapid || ! $hasSubs){
                    $msg = ! $hasVapid ? 'Push no configurado (VAPID)' : 'Sin suscripción push activa';
                    Log::warning('[CRM][Reminder] Not sending push: '.$msg, ['user_id'=>$user->id]);
                    // Seguimos sin interrumpir la UX; el recordatorio se creó.
                    return;
                }

                if($rem->remind_at){
                    $gtz = config('app.tz_guatemala','America/Guatemala');
                    $when = $rem->remind_at->copy()->timezone($gtz);
                    if ($when->isPast()) {
                        Notification::sendNow($user, new ReminderCreated(
                            'Recordatorio',
                            ($rem->notes ?: 'Recordatorio programado para ').$when->format('d/m/Y H:i'),
                            $rem->id
                        ));
                        Log::info('[CRM][Reminder] Push sent immediately', ['reminder_id'=>$rem->id, 'user_id'=>$user->id]);
                    } else {
                        $user->notify((new ReminderCreated(
                            'Recordatorio',
                            ($rem->notes ?: 'Recordatorio programado para ').$when->format('d/m/Y H:i'),
                            $rem->id
                        ))->delay($when));
                        Log::info('[CRM][Reminder] Push scheduled', ['reminder_id'=>$rem->id, 'when'=>$when->toIsoString(), 'user_id'=>$user->id]);
                    }
                } else {
                    Notification::sendNow($user, new ReminderCreated(
                        'Recordatorio (sin fecha)',
                        $rem->notes ?: 'Recordatorio creado',
                        $rem->id
                    ));
                    Log::info('[CRM][Reminder] Push sent immediate (no date)', ['reminder_id'=>$rem->id, 'user_id'=>$user->id]);
                }
            } catch(\Throwable $e){
                Log::error('[CRM][Reminder] Push error: '.$e->getMessage(), ['trace'=>$e->getTraceAsString()]);
            }
        }
    }

    public function toggleCompleted(Reminder $r)
    {
        $r->completed = !$r->completed;
        $r->save();
    }
}
