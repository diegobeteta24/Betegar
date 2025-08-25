<x-admin-layout
    title="Dashboard | Betegar"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
    ]"
>
    {{-- Definir la función dashboard() antes de que Alpine procese x-data para evitar "is not defined" --}}
    <script>
        window.dashboard = function dashboard(){
            return {
                kpis: [],
                recentPayments: [],
                accounts: [],
                // Inicializamos para evitar errores de acceso temprano
                receivables: { aging:{}, top_debtors:[], total:0, open_invoices:0, debtors:0 },
                updatedAt: '',
                charts: {},
                alive: true,
                chartsReady: false,
                _building: false,
                loaded:false,
                _chartsVisible: false,
                _visObs: null,
                // Debug flag from app config
                DEBUG: {{ config('app.debug') ? 'true' : 'false' }},
                // idle callback id
                _ricId: null,
                // requestIdleCallback helpers (wrapped to keep correct window binding)
                _ric(cb){
                    if (typeof window.requestIdleCallback === 'function') return window.requestIdleCallback(cb);
                    return setTimeout(() => cb({ didTimeout:false, timeRemaining:()=>0 }), 50);
                },
                _cRic(id){
                    if (!id) return;
                    if (typeof window.cancelIdleCallback === 'function') return window.cancelIdleCallback(id);
                    return clearTimeout(id);
                },
                init(){
                    // Evita doble fetch (load + readyState complete)
                    this._ensureVisibleObserver();
                    const start = () => { if(this.alive && !this.loaded) this.fetchData(); };
                    if (document.readyState === 'complete') start(); else window.addEventListener('load', start, { once:true });
                },
                destroy(){
                    this.alive = false;
                    if(this._ricId){ try{ this._cRic(this._ricId); }catch(_e){} this._ricId=null; }
                    // Cleanup charts explicitly
                    try{ Object.values(this.charts||{}).forEach(c=>{ try{ c.destroy(); }catch(_e){} }); }catch(_e){}
                    try{ if(this._visObs){ this._visObs.disconnect(); this._visObs=null; } }catch(_e){}
                },
                money(v){ return 'Q '+Number(v).toFixed(2); },
                fmtInt(v){ return new Intl.NumberFormat().format(v); },
                trendText(card){ return (card.trend>=0?'+':'')+card.trend+'% vs prev.'; },
                ensureCanvas(id){
                    const el = document.getElementById(id);
                    if(!el){ console.warn('[Dashboard] canvas',id,'no encontrado'); }
                    return el;
                },
                isValidCanvas(el){ return !!(el && el.nodeType===1 && el.tagName==='CANVAS' && el.ownerDocument); },
                buildCharts(payload){
                    if(this.chartsReady || this._building) return;
                    this._pendingPayload = payload;
                    this._attempts = 0;
                    this._building = true;
                    // Schedule building when browser is idle to reduce jank
                    this._ricId = this._ric(()=>{ if(this.alive) this._tryBuild(); });
                },
                _ensureVisibleObserver(){
                    try{
                        const target = document.getElementById('chartsRow');
                        if(!('IntersectionObserver' in window) || !target){ this._chartsVisible = true; return; }
                        this._visObs = new IntersectionObserver((entries)=>{
                            const e = entries.find(x=>x.target===target) || entries[0];
                            if(e && e.isIntersecting){
                                this._chartsVisible = true;
                                try{ this._visObs.disconnect(); }catch(_){ }
                                this._visObs = null;
                                if(this._pendingPayload && !this.chartsReady && !this._building){ this.buildCharts(this._pendingPayload); this.buildReceivableCharts(); }
                            }
                        }, { root:null, threshold:0.1 });
                        this._visObs.observe(target);
                    }catch(_){ this._chartsVisible = true; }
                },
                buildReceivableCharts(){
                    if(!this.receivables || typeof Chart==='undefined') return;
                    const agingEl=document.getElementById('receivablesAgingChart');
                    if(agingEl && agingEl.isConnected && this.receivables.aging && Object.keys(this.receivables.aging).length){
                        const labels=Object.keys(this.receivables.aging).map(k=>k.replace('_','-'));
                        const data=Object.values(this.receivables.aging).map(v=>Number(v.toFixed(2)));
                        try { if(this.charts.aging) this.charts.aging.destroy(); }catch(_){ }
                        const ctx = agingEl.getContext && agingEl.getContext('2d', { willReadFrequently: true });
                        if(ctx) this.charts.aging=new Chart(ctx,{type:'doughnut',data:{labels,datasets:[{data,backgroundColor:['#6366f1','#8b5cf6','#f59e0b','#dc2626']} ]},options:{events:[],plugins:{tooltip:{enabled:false},legend:{position:'bottom'}}}});
                    }
                    const debtEl=document.getElementById('topDebtorsChart');
                    if(debtEl && debtEl.isConnected && this.receivables.top_debtors && this.receivables.top_debtors.length){
                        const labels=this.receivables.top_debtors.map(d=>d.customer);
                        const data=this.receivables.top_debtors.map(d=>Number(d.due.toFixed(2)));
                        try { if(this.charts.debtors) this.charts.debtors.destroy(); }catch(_){ }
                        const ctx = debtEl.getContext && debtEl.getContext('2d', { willReadFrequently: true });
                        if(ctx) this.charts.debtors=new Chart(ctx,{type:'bar',data:{labels,datasets:[{label:'Saldo',data,backgroundColor:'#f59e0b'}]},options:{events:[],indexAxis:'y',plugins:{tooltip:{enabled:false},legend:{display:false}},scales:{x:{ticks:{callback:(v)=>'Q '+v}}}}});
                    }
                },
                _tryBuild(){
                    const payload = this._pendingPayload;
                    if(!payload || !this.alive || !this.$root.isConnected){ this._building=false; return; }
                    if(typeof Chart === 'undefined'){
                        if(this._attempts++ < 10) return setTimeout(()=>this._tryBuild(), 80);
                        this._building=false; return console.warn('[Dashboard] Chart.js no disponible');
                    }
                    // Global lightweight defaults
                    try{
                        Chart.defaults.animation = false;
                        Chart.defaults.responsive = false;
                        Chart.defaults.maintainAspectRatio = false;
                        Chart.defaults.devicePixelRatio = 1; // reduce HiDPI cost
                        Chart.defaults.events = [];
                        if(Chart.defaults.plugins && Chart.defaults.plugins.tooltip) Chart.defaults.plugins.tooltip.enabled = false;
                    }catch(_){}
                    const c1=this.$root.querySelector('#salesPurchasesChart');
                    const c2=this.$root.querySelector('#topProductsChart');
                    const c3=this.$root.querySelector('#expensesCategoryChart');
                    const canvases=[c1,c2,c3];
                    const invalid = canvases.some(c=>!c || !c.isConnected || !this.isValidCanvas(c));
                    if(invalid){
                        if(this._attempts++ < 10) return setTimeout(()=>this._tryBuild(), 100);
                        this._building=false; return console.warn('[Dashboard] Canvases inválidos tras reintentos');
                    }
                    const ctx1 = c1.getContext && c1.getContext('2d', { willReadFrequently: true });
                    const ctx2 = c2.getContext && c2.getContext('2d', { willReadFrequently: true });
                    const ctx3 = c3.getContext && c3.getContext('2d', { willReadFrequently: true });
                    if(!ctx1 || !ctx2 || !ctx3){
                        if(this._attempts++ < 25) return setTimeout(()=>this._tryBuild(), 100);
                        this._building=false; return console.error('[Dashboard] No se pudieron obtener contextos 2d');
                    }
                    if(this.DEBUG) console.debug('[Dashboard] Construyendo charts intento', this._attempts);
                    try {
                        const safeNew=(key,ctx,cfg)=>{ if(!ctx || !ctx.canvas || !ctx.canvas.isConnected) return; try{ if(this.charts[key]) this.charts[key].destroy(); }catch(_){ } this.charts[key]=new Chart(ctx,cfg); };
                        safeNew('sp', ctx1, {type:'line',data:{labels:payload.sales_purchases.labels,datasets:[
                            {label:'Ventas',data:payload.sales_purchases.sales,borderColor:'#2563eb',backgroundColor:'rgba(37,99,235,.15)',tension:.3,fill:true},
                            {label:'Compras',data:payload.sales_purchases.purchases,borderColor:'#dc2626',backgroundColor:'rgba(220,38,38,.15)',tension:.3,fill:true},
                        ]},options:{events:[],animation:false,responsive:false,maintainAspectRatio:false,plugins:{tooltip:{enabled:false}},scales:{y:{beginAtZero:true}}}});
                        safeNew('tp', ctx2, {type:'bar',data:{labels:payload.top_products.labels,datasets:[{label:'Ventas',data:payload.top_products.values,backgroundColor:'#10b981'}]},options:{events:[],animation:false,responsive:false,indexAxis:'y',plugins:{tooltip:{enabled:false},legend:{display:false}}}});
                        safeNew('ec', ctx3, {type:'doughnut',data:{labels:payload.expenses_cat.labels,datasets:[{data:payload.expenses_cat.values,backgroundColor:['#6366f1','#f59e0b','#ef4444','#10b981','#0ea5e9','#8b5cf6']}]},options:{events:[],animation:false,responsive:false,plugins:{tooltip:{enabled:false},legend:{position:'bottom'}}}});
                        const monitor = () => {
                            [ ['sp',ctx1.canvas], ['tp',ctx2.canvas], ['ec',ctx3.canvas] ].forEach(([k,canvas]) => {
                                if(this.charts[k] && canvas && !canvas.isConnected){
                                    try { this.charts[k].destroy(); } catch(_){ }
                                    delete this.charts[k];
                                }
                            });
                            if(this.alive && (this.charts.sp||this.charts.tp||this.charts.ec)) requestAnimationFrame(monitor);
                        };
                        requestAnimationFrame(monitor);
                        this.chartsReady = true;
                        this._building = false;
                    } catch(err){
                        if(this._attempts++ < 3){
                            if(this.DEBUG) console.warn('[Dashboard] Retry charts tras error', err);
                            return setTimeout(()=>this._tryBuild(), 100);
                        }
                        this._building=false; if(this.DEBUG) console.error('[Dashboard] Error creando charts (final)', err);
                    }
                },
                fetchData(){
                    if(this._loading || this.loaded) { return; }
                    this._loading=true;
                    if(this.DEBUG) console.log('[Dashboard] fetching metrics...');
                    fetch('{{ route('api.dashboard.metrics') }}', {headers:{'Accept':'application/json'}})
                        .then(r=>{ if(this.DEBUG) console.log('[Dashboard] fetch status', r.status); return r.json();})
                        .then(data=>{
                            if(this.DEBUG) console.log('[Dashboard] data received', data);
                            if(!this.alive) return;
                            this.kpis=data.kpis; this.recentPayments=data.recent_payments; this.accounts=data.accounts; this.updatedAt=data.generated_at; this.receivables=data.receivables;
                            if(this._chartsVisible){ this.buildCharts(data); this.buildReceivableCharts(); } else { this._pendingPayload=data; }
                            this.loaded=true;
                        }).catch(e=>{ if(this.DEBUG) console.warn('[Dashboard] fetch error', e) }).finally(()=>{ this._loading=false; });
                }
            }
        }
    </script>
    <div class="dashboard-wrapper h-[calc(100vh-6rem)] flex flex-col overflow-hidden" x-data="dashboard()" x-init="init()">
        <!-- KPI CARDS -->
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4 flex-shrink-0">
            <template x-for="card in kpis" :key="card.key">
                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400" x-text="card.label"></p>
                            <p class="mt-2 text-2xl font-semibold text-gray-800 dark:text-gray-100" x-text="card.format==='money'?money(card.value):fmtInt(card.value)"></p>
                            <p class="mt-1 text-xs" :class="card.trend>=0 ? 'text-green-600' : 'text-red-600'" x-text="trendText(card)"></p>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100 text-blue-600 dark:bg-blue-900/40 dark:text-blue-300">
                            <i :class="card.icon"></i>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- CHARTS ROW -->
    <div id="chartsRow" class="grid gap-4 lg:grid-cols-3 flex-shrink-0">
            <div class="col-span-2 rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-sm font-semibold tracking-wide text-gray-600 dark:text-gray-300">Ventas vs Compras (últimos 30 días)</h3>
                    <div class="text-xs text-gray-400" x-text="updatedAt"></div>
                </div>
                <div class="h-36"><canvas id="salesPurchasesChart" height="140" class="!w-full !h-full" wire:ignore x-ignore></canvas></div>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <h3 class="mb-4 text-sm font-semibold tracking-wide text-gray-600 dark:text-gray-300">Top Productos</h3>
                <div class="h-40"><canvas id="topProductsChart" height="160" class="!w-full !h-full" wire:ignore x-ignore></canvas></div>
            </div>
        </div>

        <!-- SECOND ROW (scrollable if needed) -->
    <div class="grid gap-4 xl:grid-cols-4 lg:grid-cols-3 overflow-y-auto pt-2 pb-4 flex-1">
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800 flex flex-col">
                <h3 class="mb-4 text-sm font-semibold tracking-wide text-gray-600 dark:text-gray-300">Pagos Recientes</h3>
                <ul class="divide-y divide-gray-100 dark:divide-gray-700 overflow-y-auto custom-thin-scroll pr-1" x-show="recentPayments.length">
                    <template x-for="p in recentPayments" :key="p.id">
                        <li class="py-2 flex items-center justify-between">
                            <div class="text-xs">
                                <p class="font-medium text-gray-700 dark:text-gray-200" x-text="'#'+p.sale_ref"></p>
                                <p class="text-[10px] text-gray-400" x-text="p.paid_at"></p>
                            </div>
                            <span class="text-xs font-semibold text-green-600 dark:text-green-400" x-text="money(p.amount)"></span>
                        </li>
                    </template>
                </ul>
                <p class="text-xs text-gray-400" x-show="!recentPayments.length">Sin pagos.</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800 flex flex-col">
                <h3 class="mb-4 text-sm font-semibold tracking-wide text-gray-600 dark:text-gray-300">Gastos por Categoría (30d)</h3>
                <div class="h-44"><canvas id="expensesCategoryChart" height="160" class="!w-full !h-full" wire:ignore x-ignore></canvas></div>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800 flex flex-col">
                <h3 class="mb-4 text-sm font-semibold tracking-wide text-gray-600 dark:text-gray-300">Balance Cuentas Bancarias</h3>
                <ul class="space-y-2" x-show="accounts.length">
                    <template x-for="a in accounts" :key="a.id">
                        <li class="flex items-center justify-between rounded-md bg-gray-50 px-3 py-2 text-xs dark:bg-gray-700/40">
                            <span class="font-medium" x-text="a.name"></span>
                            <span class="font-semibold" :class="a.balance>=0 ? 'text-green-600 dark:text-green-400':'text-red-600 dark:text-red-400'" x-text="money(a.balance)"></span>
                        </li>
                    </template>
                </ul>
                <p class="text-xs text-gray-400" x-show="!accounts.length">No hay cuentas.</p>
            </div>
            <!-- Aging cuentas por cobrar -->
            <div class="rounded-xl border border-purple-200 bg-white p-4 shadow-sm dark:border-purple-800 dark:bg-gray-800 flex flex-col" x-show="receivables && receivables.aging && Object.keys(receivables.aging).length">
                <h3 class="mb-3 text-sm font-semibold tracking-wide text-purple-700 dark:text-purple-300">Aging CxC</h3>
                <div class="h-40"><canvas id="receivablesAgingChart" height="160" class="!w-full !h-full" wire:ignore x-ignore></canvas></div>
                <ul class="mt-3 grid grid-cols-2 gap-2 text-[11px]">
                    <template x-for="(val,key) in receivables.aging" :key="key">
                        <li class="flex justify-between bg-purple-50 dark:bg-purple-900/30 px-2 py-1 rounded">
                            <span x-text="key.replace('_','-')"></span>
                            <span x-text="money(val)"></span>
                        </li>
                    </template>
                </ul>
            </div>
            <!-- Top Deudores -->
            <div class="rounded-xl border border-amber-200 bg-white p-4 shadow-sm dark:border-amber-800 dark:bg-gray-800 flex flex-col" x-show="receivables && receivables.top_debtors && receivables.top_debtors.length">
                <h3 class="mb-3 text-sm font-semibold tracking-wide text-amber-700 dark:text-amber-300">Top Deudores</h3>
                <div class="h-40"><canvas id="topDebtorsChart" height="160" class="!w-full !h-full" wire:ignore x-ignore></canvas></div>
                <ul class="mt-3 space-y-1 text-[11px]">
                    <template x-for="d in receivables.top_debtors" :key="d.customer_id">
                        <li class="flex justify-between bg-amber-50 dark:bg-amber-900/30 px-2 py-1 rounded">
                            <span class="truncate max-w-[60%]" x-text="d.customer"></span>
                            <span x-text="money(d.due)"></span>
                        </li>
                    </template>
                </ul>
            </div>
        </div>
    </div>

    @push('js')
        <script defer src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    @endpush
    @push('css')
        <style>
            .custom-thin-scroll{ scrollbar-width: thin; scrollbar-color: #9ca3af transparent; }
            .custom-thin-scroll::-webkit-scrollbar{ width:6px; }
            .custom-thin-scroll::-webkit-scrollbar-track{ background:transparent; }
            .custom-thin-scroll::-webkit-scrollbar-thumb{ background:#9ca3af; border-radius:3px; }
            @media (max-width: 1024px){
                .dashboard-wrapper{ height:auto; }
            }
            .dark .border-purple-200{border-color:rgba(168,85,247,.4)}
            .dark .border-amber-200{border-color:rgba(245,158,11,.4)}
        </style>
    @endpush
</x-admin-layout>
