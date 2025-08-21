<?php

namespace App\Livewire\Technician;

use App\Models\User;
use App\Models\TechnicianSession;
use App\Models\WorkOrder;
use App\Models\WorkOrderEntry;
use App\Models\Expense;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    public bool $adminMode = false;

    public function render()
    {
        if ($this->adminMode && Auth::user()->hasRole('admin')) {
            // Admin: ver todos los técnicos
            $technicians = User::role('technician')->get();
            $since = Carbon::now()->subDays(30);
            $data = $technicians->map(function($tech) use ($since) {
                $lastSession = TechnicianSession::where('user_id', $tech->id)->latest('started_at')->first();
                $pendingOrders = WorkOrder::forTechnician($tech->id)->whereIn('status', ['pending','in_progress'])->with('customer')->get();
                $recentRequests = WorkOrderEntry::where('user_id', $tech->id)
                    ->whereNotNull('requests')
                    ->orderByDesc('created_at')
                    ->limit(5)
                    ->get();
                // Estadísticas 30 días
                $expenses30 = Expense::where('technician_id', $tech->id)->where('created_at','>=',$since)->sum('amount');
                $completedOrders30 = WorkOrder::where('user_id',$tech->id)->where('status','completed')->where('updated_at','>=',$since)->count();
                // sesiones iniciadas últimos 30 días agrupadas por fecha
                $sessionsCount = TechnicianSession::where('user_id',$tech->id)->where('started_at','>=',$since)->count();
                $avgSessionsPerDay = $sessionsCount / 30; // simple promedio
                return [
                    'technician' => $tech,
                    'lastSession' => $lastSession,
                    'pendingOrders' => $pendingOrders,
                    'recentRequests' => $recentRequests,
                    'stats' => [
                        'expenses_30d' => (float) $expenses30,
                        'completed_orders_30d' => $completedOrders30,
                        'avg_sessions_day_30d' => round($avgSessionsPerDay,2),
                    ],
                ];
            });
            return view('livewire.technician.dashboard', [
                'adminMode' => true,
                'techniciansData' => $data,
            ]);
        } else {
            // Técnico: solo su info
            $user = Auth::user();
            $lastSession = TechnicianSession::where('user_id', $user->id)->latest('started_at')->first();
            $pendingOrders = WorkOrder::forTechnician($user->id)->whereIn('status', ['pending','in_progress'])->with('customer')->get();
            $recentRequests = WorkOrderEntry::where('user_id', $user->id)
                ->whereNotNull('requests')
                ->orderByDesc('created_at')
                ->limit(5)
                ->get();
            return view('livewire.technician.dashboard', [
                'adminMode' => false,
                'lastSession' => $lastSession,
                'pendingOrders' => $pendingOrders,
                'recentRequests' => $recentRequests,
            ]);
        }
    }
}
