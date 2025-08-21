<x-admin-layout title="Técnicos - Overview" :breadcrumbs="[['name'=>'Técnicos Overview']]">
    <div class="py-4 space-y-6" x-data="techOverview()" x-init="load()">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold">Resumen de Técnicos</h2>
            <div class="flex items-center gap-2">
                <input x-model="filters.search" @input.debounce.400ms="applyFilters" type="text" placeholder="Buscar técnico" class="border rounded px-2 py-1 text-sm" />
                <button @click="load" class="px-3 py-1.5 bg-indigo-600 text-white text-sm rounded">Refrescar</button>
            </div>
        </div>

        <div class="bg-white rounded shadow divide-y" x-show="loading">
            <div class="p-4 text-gray-500">Cargando...</div>
        </div>

        <template x-for="t in filtered" :key="t.id">
            <div class="bg-white rounded shadow p-4 grid md:grid-cols-7 gap-4 items-start">
                <div class="md:col-span-2">
                    <div class="font-semibold" x-text="t.name"></div>
                    <div class="text-xs text-gray-500" x-text="t.email"></div>
                    <div class="mt-2 text-sm">Saldo: <span class="font-mono" :class="Number(t.balance) < 0 ? 'text-red-600':'text-emerald-600'" x-text="t.balance"></span> GTQ</div>
                </div>
                <div class="md:col-span-2">
                    <h4 class="text-sm font-semibold mb-1">Sesiones de hoy</h4>
                    <template x-if="t.sessions_today.length === 0">
                        <div class="text-xs text-gray-400">Sin sesiones</div>
                    </template>
                    <ul class="text-xs space-y-1 max-h-28 overflow-auto pr-1">
                        <template x-for="s in t.sessions_today" :key="s.id">
                            <li class="flex items-center gap-2">
                                <span class="inline-block bg-emerald-100 text-emerald-700 px-1 rounded" x-text="s.started_at"></span>
                                <span class="text-gray-400">→</span>
                                <span class="inline-block bg-blue-100 text-blue-700 px-1 rounded" x-text="s.ended_at || '...' "></span>
                            </li>
                        </template>
                    </ul>
                </div>
                <div class="md:col-span-2">
                    <h4 class="text-sm font-semibold mb-1">Gastos recientes</h4>
                    <template x-if="t.recent_expenses.length === 0">
                        <div class="text-xs text-gray-400">Sin gastos</div>
                    </template>
                    <ul class="text-xs space-y-1 max-h-28 overflow-auto pr-1">
                        <template x-for="e in t.recent_expenses" :key="e.id">
                            <li class="flex justify-between gap-2">
                                <span class="truncate" x-text="e.description"></span>
                                <span class="font-mono" x-text="e.amount"></span>
                            </li>
                        </template>
                    </ul>
                </div>
                <div class="md:col-span-1 flex flex-col gap-2">
                    <button @click="openFunds(t)" class="px-2 py-1 bg-indigo-600 text-white text-xs rounded">Enviar fondos</button>
                    <button @click="openMore(t)" class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded">Detalle</button>
                </div>
            </div>
        </template>

        <div class="text-xs text-gray-400" x-show="!loading && filtered.length === 0">Sin resultados.</div>

        <!-- Modal enviar fondos -->
        <div x-show="fundsModal" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
            <div class="bg-white rounded shadow max-w-md w-full p-5" @click.outside="fundsModal=false">
                <h3 class="text-lg font-semibold mb-4">Enviar fondos a <span x-text="current?.name"></span></h3>
                <form @submit.prevent="submitFunds" class="space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600">Monto (GTQ)</label>
                        <input x-model.number="fundForm.amount" type="number" step="0.01" min="0.01" required class="mt-1 w-full border rounded px-2 py-1" />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600">Cuenta bancaria origen</label>
                        <select x-model="fundForm.bank_account_id" required class="mt-1 w-full border rounded px-2 py-1 text-xs">
                            <option value="" disabled selected>Seleccione cuenta</option>
                            <template x-for="a in bankAccounts" :key="a.id">
                                <option :value="a.id" x-text="a.name + ' ('+a.balance+')'"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600">Nota</label>
                        <input x-model="fundForm.note" type="text" maxlength="255" class="mt-1 w-full border rounded px-2 py-1" placeholder="Opcional" />
                    </div>
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="fundsModal=false" class="px-3 py-1 text-xs bg-gray-100 rounded">Cancelar</button>
                        <button type="submit" class="px-3 py-1 text-xs bg-indigo-600 text-white rounded" :disabled="sendingFunds" x-text="sendingFunds? 'Enviando...' : 'Enviar'"></button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal detalle técnico -->
        <div x-cloak x-show="detailModal" x-transition.opacity.scale class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4" @keydown.escape.window="detailModal=false" @click="detailModal=false">
            <div class="bg-white rounded shadow max-w-2xl w-full p-5 space-y-4" @click.stop @click.outside="detailModal=false">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-lg font-semibold" x-text="detail.name"></h3>
                    <p class="text-xs text-gray-500" x-text="detail.email"></p>
                    <p class="mt-1 text-sm">Saldo: <span class="font-mono" x-text="detail.balance"></span> GTQ</p>
                </div>
                <button type="button" class="text-xs px-2 py-1 bg-gray-100 rounded" @click="detailModal=false">Cerrar</button>
            </div>
            <div class="grid md:grid-cols-3 gap-4">
                <div class="md:col-span-1">
                    <h4 class="text-xs font-semibold mb-2">Sesiones hoy</h4>
                    <ul class="text-xs space-y-1 max-h-40 overflow-auto pr-1">
                        <template x-for="s in (detail.sessions_today||[])" :key="s.id">
                            <li><span x-text="s.started_at"></span> → <span x-text="s.ended_at||'...' "></span></li>
                        </template>
                        <li x-show="!(detail.sessions_today||[]).length" class="text-gray-400">Sin sesiones</li>
                    </ul>
                </div>
                <div class="md:col-span-1">
                    <h4 class="text-xs font-semibold mb-2">Gastos recientes</h4>
                    <ul class="text-xs space-y-1 max-h-40 overflow-auto pr-1">
                        <template x-for="e in (detail.recent_expenses||[])" :key="e.id">
                            <li class="flex justify-between"><span class="truncate" x-text="e.description"></span><span class="font-mono" x-text="e.amount"></span></li>
                        </template>
                        <li x-show="!(detail.recent_expenses||[]).length" class="text-gray-400">Sin gastos</li>
                    </ul>
                </div>
                <div class="md:col-span-1">
                    <h4 class="text-xs font-semibold mb-2">Envíos de fondos</h4>
                    <ul class="text-xs space-y-1 max-h-40 overflow-auto pr-1">
                        <template x-for="tr in (detail.transfers||[])" :key="tr.id">
                            <li class="flex justify-between items-center gap-2">
                                <span><span class="font-mono" x-text="tr.amount"></span> <span class="text-gray-400" x-text="tr.currency"></span></span>
                                <button class="text-red-600 hover:underline" @click="deleteTransfer(tr)">Eliminar</button>
                            </li>
                        </template>
                        <li x-show="!(detail.transfers||[]).length" class="text-gray-400">Sin envíos</li>
                    </ul>
                </div>
            </div>
            </div>
        </div>
    </div>

    <script>
        function techOverview(){
            return {
                loading: false,
                list: [],
                filtered: [],
                filters: { search: '' },
                fundsModal: false,
                current: null,
                fundForm: { amount: '', note: '', bank_account_id: '' },
                bankAccounts: [],
                sendingFunds: false,
                load(){
                    this.loading = true;
                    Promise.all([
                        fetch('/api/admin/technicians/overview', { credentials: 'same-origin'}).then(r=>r.json()),
                        fetch('/api/bank-accounts', { credentials: 'same-origin'}).then(r=> r.ok ? r.json() : [])
                    ])
                        .then(([techs, accounts]) => { this.list = techs; this.bankAccounts = accounts; this.applyFilters(); })
                        .catch(e => console.error(e))
                        .finally(()=> this.loading=false);
                },
                applyFilters(){
                    const s = this.filters.search.toLowerCase();
                    this.filtered = this.list.filter(t => !s || t.name.toLowerCase().includes(s) || t.email.toLowerCase().includes(s));
                },
                openFunds(t){
                    this.current = t; this.fundForm={amount:'',note:'', bank_account_id:''}; this.fundsModal=true; setTimeout(()=> document.querySelector('[x-model="fundForm.amount"]').focus(),50);
                },
                openMore(t){
                    this.loadDetail(t.id);
                },
                detailModal:false,
                detail:{},
                loadDetail(id){
                    fetch('/api/admin/technicians/overview') // reuse overview list for summary
                        .then(r=>r.json())
                        .then(list=>{ this.detail = list.find(u=>u.id===id) || {}; return fetch('/api/admin/fund-transfers?technician_id='+id); })
                        .then(r=> r.ok ? r.json(): [])
                        .then(transfers=>{ this.detail.transfers = transfers.filter(tr=>tr.technician_id===id); this.detailModal=true; })
                        .catch(e=>alert('Error cargando detalle'));                    
                },
                deleteTransfer(tr){
                    if(!confirm('¿Eliminar este envío y revertir saldo?')) return;
                    fetch('/api/admin/fund-transfers/'+tr.id, {method:'DELETE', credentials:'same-origin', headers:{'X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content,'Accept':'application/json'}})
                        .then(r=> { if(!r.ok) throw new Error('Error'); return r.json(); })
                        .then(()=>{ this.detail.transfers = this.detail.transfers.filter(x=>x.id!==tr.id); this.load(); })
                        .catch(()=>alert('No se pudo eliminar'));
                },
                submitFunds(){
                    if(!this.current) return;
                    this.sendingFunds = true;
            fetch('/api/admin/fund-transfers', {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                            'Accept': 'application/json'
                        },
                        body: new URLSearchParams({
                            technician_id: this.current.id,
                            amount: this.fundForm.amount,
                note: this.fundForm.note || '',
                bank_account_id: this.fundForm.bank_account_id
                        })
                    }).then(r => {
                        if(!r.ok) return r.text().then(t=>{throw new Error(t)});
                        return r.json();
                    }).then(()=>{
                        this.fundsModal=false; this.refreshBalance(this.current);
                    }).catch(e=> alert('Error: '+e.message)).finally(()=> this.sendingFunds=false);
                },
                refreshBalance(t){
                    // Reconsultar un técnico puntual (simple: recargar todo para mantener consistencia)
                    this.load();
                }
            }
        }
    </script>
</x-admin-layout>

    @push('css')
    <style>[x-cloak]{display:none!important;}</style>
    @endpush
