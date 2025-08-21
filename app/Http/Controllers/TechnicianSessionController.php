<?php

namespace App\Http\Controllers;

use App\Models\TechnicianSession;
use App\Models\TechnicianSessionLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Notifications\TechnicianEvent;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route as RouteFacade;

class TechnicianSessionController extends Controller
{
    public function checkin(Request $request)
    {
        $user = $request->user();
        if (!$user->hasRole('technician')) {
            abort(403, 'Solo técnicos');
        }

        $data = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        $tz = config('app.tz_guatemala', 'America/Guatemala');
        $nowGt = Carbon::now($tz);
        $cutoffStr = config('app.checkin_cutoff', '09:10:00');
        [$h,$m,$s] = array_map('intval', explode(':', $cutoffStr));
        $cutoff = $nowGt->copy()->setTime($h, $m, $s);
        if ($nowGt->greaterThan($cutoff)) {
            return response()->json(['message' => 'El check-in debe hacerse antes de las '.Carbon::createFromTime($h,$m,$s,$tz)->isoFormat('h:mm a')], 422);
        }

        $sessionDate = $nowGt->toDateString();

        $exists = TechnicianSession::where('user_id', $user->id)
            ->where('started_on_date', $sessionDate)
            ->exists();
        if ($exists) {
            return response()->json(['message' => 'Ya realizó el check-in de hoy'], 422);
        }

        // Guardamos hora local (America/Guatemala) directamente para evitar interpretación doble
        $session = TechnicianSession::create([
            'user_id' => $user->id,
            'start_latitude' => $data['latitude'],
            'start_longitude' => $data['longitude'],
            'started_at' => $nowGt->copy(),
            'started_on_date' => $sessionDate,
        ]);

        TechnicianSessionLocation::create([
            'technician_session_id' => $session->id,
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'logged_at' => $nowGt->copy(),
        ]);

        // Notificar a admins (incluye coords y link al mapa con focus del técnico)
        // Nota: la ruta está nombrada como 'admin.technicians.map' (prefijo admin.)
        $mapRouteName = 'admin.technicians.map';
        $mapUrl = RouteFacade::has($mapRouteName)
            ? route($mapRouteName, ['focus' => $user->id])
            : url('/admin/technicians/map?focus='.$user->id);
        $payload = [
            'type' => 'checkin',
            'technician_id' => $user->id,
            'lat' => $data['latitude'],
            'lng' => $data['longitude'],
            'tag' => 'tech-'.$user->id,
            'url' => $mapUrl,
            'ts' => now()->timestamp,
        ];
    Log::info('[TECH][CHECKIN] Dispatching TechnicianEvent push', $payload);
        User::role('admin')->each(function($admin) use ($user, $payload){
            $admin->notify(new TechnicianEvent(
                'Check-in técnico',
                $user->name.' realizó check-in',
                $payload
            ));
        });
    Log::info('[TECH][CHECKIN] Notification loop completed');

        return response()->json($session->toArray(), 201);
    }

    public function ping(Request $request)
    {
        $user = $request->user();
        if (!$user->hasRole('technician')) {
            abort(403, 'Solo técnicos');
        }

        $data = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        $open = TechnicianSession::where('user_id', $user->id)->whereNull('ended_at')->latest('id')->first();
        if (!$open) {
            return response()->json(['message' => 'No hay sesión abierta. Realice check-in.'], 422);
        }

        // Guardamos también en hora local para consistencia (evitar mezclar UTC y local)
        $loc = TechnicianSessionLocation::create([
            'technician_session_id' => $open->id,
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'logged_at' => Carbon::now(config('app.tz_guatemala','America/Guatemala')),
        ]);

        return response()->json(['ok' => true, 'id' => $loc->id]);
    }

    public function checkout(Request $request)
    {
        $user = $request->user();
        if (!$user->hasRole('technician')) {
            abort(403, 'Solo técnicos');
        }

        $data = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        $open = TechnicianSession::where('user_id', $user->id)->whereNull('ended_at')->latest('id')->first();
        if (!$open) {
            return response()->json(['message' => 'No hay sesión abierta.'], 422);
        }

        $open->update([
            'end_latitude' => $data['latitude'],
            'end_longitude' => $data['longitude'],
            'ended_at' => Carbon::now(config('app.tz_guatemala','America/Guatemala')),
        ]);

        TechnicianSessionLocation::create([
            'technician_session_id' => $open->id,
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'logged_at' => Carbon::now(config('app.tz_guatemala','America/Guatemala')),
        ]);

        // Notificar a admins (coords finales)
        $mapRouteName = 'admin.technicians.map';
        $mapUrl = RouteFacade::has($mapRouteName)
            ? route($mapRouteName, ['focus' => $user->id])
            : url('/admin/technicians/map?focus='.$user->id);
        $payload = [
            'type' => 'checkout',
            'technician_id' => $user->id,
            'lat' => $data['latitude'],
            'lng' => $data['longitude'],
            'tag' => 'tech-'.$user->id,
            'url' => $mapUrl,
            'ts' => now()->timestamp,
        ];
    Log::info('[TECH][CHECKOUT] Dispatching TechnicianEvent push', $payload);
        User::role('admin')->each(function($admin) use ($user, $payload){
            $admin->notify(new TechnicianEvent(
                'Checkout técnico',
                $user->name.' realizó checkout',
                $payload
            ));
        });
    Log::info('[TECH][CHECKOUT] Notification loop completed');

        return response()->json(['ok' => true]);
    }
}
