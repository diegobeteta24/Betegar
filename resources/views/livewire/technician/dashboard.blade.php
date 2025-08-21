<div>
@if($adminMode ?? false)
    @include('livewire.technician.dashboard-admin', ['techniciansData' => $techniciansData])
@else
<div class="space-y-6" x-data="technicianOverview()" x-init="load()">
    <div class="grid gap-6 md:grid-cols-3">
        <div class="bg-white border rounded-lg p-4 flex flex-col justify-between">
            <div>
                <h3 class="text-lg font-semibold mb-2">Saldo disponible</h3>
                <div class="text-3xl font-bold text-emerald-600" x-text="balanceFmt()"></div>
            </div>
            <div class="text-xs text-gray-500 mt-2" x-text="loadedAt ? 'Actualizado '+loadedAt : 'Cargando...' "></div>
        </div>
        <div class="bg-white border rounded-lg p-4 md:col-span-2">
            <h3 class="text-lg font-semibold mb-2">Órdenes pendientes</h3>
            @if(count($pendingOrders)===0)
                <div class="text-gray-500">No tienes órdenes pendientes.</div>
            @else
                <ul class="divide-y max-h-60 overflow-auto pr-1">
                    @foreach($pendingOrders as $order)
                        <li class="py-2 flex items-center justify-between">
                            <div>
                                <div class="font-medium">#{{ $order->id }} - {{ $order->customer->name }}</div>
                                <div class="text-sm text-gray-500">{{ $order->address }}</div>
                            </div>
                            <a href="{{ url('/work-orders/'.$order->id) }}" class="text-indigo-600 hover:underline">Ver</a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
    <div class="grid gap-6 md:grid-cols-2">
        <div class="bg-white border rounded-lg p-4">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-lg font-semibold">Gastos recientes</h3>
                <button type="button" class="text-xs text-indigo-600" @click="load()" x-text="loading ? '...' : 'Refrescar'"></button>
            </div>
            <template x-if="expenses.length===0 && !loading">
                <div class="text-gray-500 text-sm">No hay gastos registrados.</div>
            </template>
            <ul class="divide-y max-h-72 overflow-auto pr-1" x-show="expenses.length>0">
                <template x-for="e in expenses" :key="e.id">
                    <li class="py-2 text-sm flex justify-between">
                        <div>
                            <div class="font-medium" x-text="'Q '+e.amount"></div>
                            <div class="text-gray-500" x-text="e.description"></div>
                        </div>
                        <div class="text-xs text-gray-400" x-text="e.created_at"></div>
                    </li>
                </template>
            </ul>
        </div>
        <div class="bg-white border rounded-lg p-4">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-lg font-semibold">Fondos recibidos</h3>
                <button type="button" class="text-xs text-indigo-600" @click="load()" x-text="loading ? '...' : 'Refrescar'"></button>
            </div>
            <template x-if="funds.length===0 && !loading">
                <div class="text-gray-500 text-sm">No tienes fondos registrados.</div>
            </template>
            <ul class="divide-y max-h-72 overflow-auto pr-1" x-show="funds.length>0">
                <template x-for="f in funds" :key="f.id">
                    <li class="py-2 text-sm">
                        <div class="flex justify-between">
                            <div class="font-medium" x-text="'Q '+f.amount"></div>
                            <div class="text-xs text-gray-400" x-text="f.sent_at"></div>
                        </div>
                        <div class="text-gray-500 flex justify-between items-center">
                            <span x-text="f.note || 'Sin nota'"></span>
                            <span class="text-xs" x-text="f.admin.name"></span>
                        </div>
                    </li>
                </template>
            </ul>
        </div>
    </div>
    <div class="bg-white border rounded-lg p-4">
        <h3 class="text-lg font-semibold mb-2">Solicitudes recientes</h3>
        @if(count($recentRequests)===0)
            <div class="text-gray-500">No hay solicitudes recientes.</div>
        @else
            <ul class="divide-y">
                @foreach($recentRequests as $req)
                    <li class="py-2">
                        <div class="text-sm text-gray-700">{{ $req->requests }}</div>
                        <div class="text-xs text-gray-500">{{ $req->created_at->timezone(config('app.tz_guatemala','America/Guatemala'))->format('d/m/Y H:i') }}</div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    <script>
        function technicianOverview(){
            return {
                loading:false,
                balance:'0.00',
                expenses:[],
                funds:[],
                loadedAt:null,
                balanceFmt(){ return 'Q '+this.balance; },
                async load(){
                    this.loading=true;
                    try {
                        const res = await fetch('/api/technician/overview', { credentials:'same-origin' });
                        if(!res.ok) throw new Error('HTTP '+res.status);
                        const json = await res.json();
                        this.balance = json.balance;
                        this.expenses = json.expenses || [];
                        this.funds = json.fund_transfers || [];
                        const now = new Date();
                        this.loadedAt = now.toLocaleTimeString('es-GT',{hour:'2-digit',minute:'2-digit'});
                    } catch(e){ console.warn('Overview load error', e); }
                    finally { this.loading=false; }
                }
            }
        }
    </script>
</div>
@endif
</div>
