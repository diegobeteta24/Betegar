<?php

namespace App\Livewire\Technician;

use App\Models\TechnicianSession;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CheckinPanel extends Component
{
    public bool $hasCheckedInToday = false;
    public bool $hasOpenSession = false;
    public ?int $sessionId = null;

    public function mount(): void
    {
        $user = Auth::user();
    $tz = config('app.tz_guatemala', 'America/Guatemala');
    $today = Carbon::now($tz)->toDateString();
        $session = TechnicianSession::where('user_id', $user->id)
            ->where('started_on_date', $today)
            ->latest('id')
            ->first();
        $this->hasCheckedInToday = (bool) $session;
        $this->hasOpenSession = $session && is_null($session->ended_at);
        $this->sessionId = $session?->id;
    }

    public function render()
    {
        return view('livewire.technician.checkin-panel');
    }
}
