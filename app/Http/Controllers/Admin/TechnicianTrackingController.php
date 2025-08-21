<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TechnicianSession;
use App\Models\TechnicianSessionLocation;
use App\Models\User;
use Illuminate\Http\Request;

class TechnicianTrackingController extends Controller
{
    public function sessions(Request $request, User $technician)
    {
        $this->authorize('viewAny', TechnicianSession::class);
        return TechnicianSession::where('user_id', $technician->id)
            ->orderBy('started_at','desc')
            ->get();
    }

    public function locations(Request $request, TechnicianSession $session)
    {
        $this->authorize('viewAny', TechnicianSessionLocation::class);
        return $session->locations()->orderBy('logged_at')->get();
    }
}
