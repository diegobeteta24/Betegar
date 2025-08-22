<?php

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
// Registrar/actualizar suscripción WebPush (protegido)
Route::middleware('auth:sanctum')
    ->post('/push/subscribe', function(\Illuminate\Http\Request $request){
    $request->validate([
        'endpoint' => 'required|string',
        'keys.auth' => 'required|string',
        'keys.p256dh' => 'required|string',
    ]);
    $user = $request->user();
    $user->updatePushSubscription(
        $request->input('endpoint'),
        $request->input('keys.p256dh'),
        $request->input('keys.auth'),
        $request->input('encoding', null)
    );
    return response()->json(['ok'=>true]);
    })
    ->withoutMiddleware([Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class]);
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Warehouse;
use App\Models\Customer;
use App\Models\Quote;
use App\Models\Reason;
use App\Models\Expense;
use App\Models\Image;
use App\Http\Controllers\TechnicianSessionController;
use App\Http\Controllers\WorkOrderController;
use App\Http\Controllers\WorkOrderEntryController;
use App\Http\Controllers\ExpenseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;


Route::middleware(['auth:sanctum','throttle:60,1'])->group(function(){
Route::match(['GET','POST'],'/warehouses', function(Request $request) {
    return Warehouse::select('id','name','location as description')
        // 1) Excluir el origen primero
        ->when($request->exclude, fn($q,$e) => $q->where('id','!=',$e))

        // 2) Agrupar el search en su propio WHERE (...)
        ->when($request->search, function($q,$s){
            $q->where(function($q2) use($s){
                $q2->where('name','like',   "%{$s}%")
                   ->orWhere('location','like', "%{$s}%");
            });
        })

        // 3) Devolver los seleccionados o los primeros 10
        ->when(
            $request->filled('selected'),
            fn($q) => $q->whereIn('id', (array) $request->input('selected')),
            fn($q) => $q->limit(10)
        )

        ->get();
})->name('api.warehouses.index');



Route::match(['GET','POST'],'/suppliers', function(Request $request) {
    return Supplier::select('id','name')
        ->when($request->search, function($query,$search) {
            $query->where('name','like',"%{$search}%")
              ->orWhere('document_number','like',"%{$search}%");
        })
        ->when($request->exists('selected'),
            fn($query) => $query->whereIn('id',$request->input('selected', [])),
            fn($query) => $query->limit(10)
        )
        ->get();
})->name('api.suppliers.index');

Route::match(['GET','POST'],'/customers', function(Request $request) {
    return Customer::select('id','name')
        ->when($request->search, function($query,$search) {
            $query->where('name','like',"%{$search}%")
              ->orWhere('document_number','like',"%{$search}%");
        })
        ->when($request->exists('selected'),
            fn($query) => $query->whereIn('id',$request->input('selected', [])),
            fn($query) => $query->limit(10)
        )
        ->get();
})->name('api.customers.index');

// Direcciones de un cliente
Route::get('/customers/{customer}/addresses', function(Customer $customer){
    return $customer->addresses()->orderByDesc('is_primary')->orderBy('id')
        ->get(['id','label','address','is_primary'])
        ->map(fn($a)=>[
            'id'=>$a->id,
            'label'=>$a->label ?: 'Dirección',
            'address'=>$a->address,
            'is_primary'=>$a->is_primary,
        ]);
})->name('api.customers.addresses');

Route::match(['GET','POST'],'/products', function(Request $request) {

    return Product::select('id','name','type','price')
        ->when($request->search, function($query,$search) {
            $query->where('name','like',"%{$search}%")
              ->orWhere('sku','like',"%{$search}%");
              
        })
        ->when($request->exists('selected'),
            fn($query) => $query->whereIn('id',$request->input('selected', [])),
            fn($query) => $query->limit(10)
        )
        ->get()
        ->map(function($p){
            return [
                'id'=>$p->id,
                'name'=>$p->name,
                'description'=>$p->type === 'service' ? 'Servicio' : 'Producto',
            ];
        });
})->name('api.products.index');

Route::match(['GET','POST'],'/purchase-orders', function(Request $request) {

    $purchaseOrders = PurchaseOrder::when($request->search, function($query,$search) {

        $parts = explode('-', $search);

        if(count($parts) == 1) {

            $query->whereHas('supplier', function($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('document_number', 'like', "%{$search}%");
            });
            return;
        }

        if (count($parts) == 2) {

        $serie = $parts[0];
        $correlative = ltrim($parts[1], '0');

        $query->where('serie',$serie)
              ->where('correlative', 'LIKE', "%{$correlative}%");
               return;  
        }

    })
        ->when($request->exists('selected'),
            fn($query) => $query->whereIn('id',$request->input('selected', [])),
            fn($query) => $query->limit(10)
        )
        ->with(['supplier'])
        ->orderBy('created_at', 'desc')
        ->get();

        return $purchaseOrders->map(function($purchaseOrder) {

            return [
                'id' => $purchaseOrder->id,
                'name' => $purchaseOrder->serie . '-' . $purchaseOrder->correlative,
                'description' => $purchaseOrder->supplier->name . ' - ' . $purchaseOrder->supplier->document_number,

            ];
        });
           

   
})->name('api.purchase-orders.index');

Route::match(['GET','POST'],'/quotes', function(Request $request) {
    $query = Quote::query()
        ->with('customer')
        // Excluir las que ya tienen una venta asociada
        ->whereDoesntHave('sale');

    if($search = $request->search){
        $parts = explode('-', $search);
        if(count($parts) === 2){
            $serie = $parts[0];
            $correlative = ltrim($parts[1], '0');
            $query->where('serie', $serie)
                  ->where('correlative','LIKE',"%{$correlative}%");
        } else {
            $query->where(function($q) use ($search){
                $q->whereHas('customer', function($qc) use ($search){
                    $qc->where('name','like',"%{$search}%")
                       ->orWhere('document_number','like',"%{$search}%");
                })
                ->orWhere('serie','like',"%{$search}%")
                ->orWhere('correlative','like',"%{$search}%");
            });
        }
    }

    if($request->exists('selected')){
        $query->whereIn('id', (array)$request->input('selected', []));
    } else {
        $query->limit(10);
    }

    $quotes = $query->orderByDesc('created_at')->get();

    return $quotes->map(fn($quote)=>[
        'id' => $quote->id,
        'name' => $quote->serie . '-' . $quote->correlative,
        'description' => $quote->customer?->name . ' - ' . $quote->customer?->document_number,
    ]);
})->name('api.sales.index');

//Reason api
Route::match(['GET','POST'],'/reasons', function(Request $request) {
    return Reason::select('id','name','type')
        ->when($request->search, function($query,$search) {
            $query->where('name','like',"%{$search}%");
        })
        ->when($request->exists('selected'),
            fn($query) => $query->whereIn('id',$request->input('selected', [])),
            fn($query) => $query->limit(10)
        )
        ->when($request->filled('type'), function($q) use ($request){
            $q->where('type', (int) $request->input('type'));
        })
        ->orderBy('name')
        ->get();
})->name('api.reasons.index');
}); // end sanctum+throttle group

// Admin expenses listing (with voucher) - requires admin role
Route::middleware('auth:sanctum')->get('/admin/expenses', function(Request $request){
    $user = $request->user();
    if(!$user || !$user->hasRole('admin')){ abort(403); }
    $q = Expense::with(['technician:id,name','images'=>fn($img)=>$img->where('tag','voucher')])
        ->orderByDesc('id');
    if($search = $request->get('search')){
        $q->where(function($qb) use($search){
            $qb->where('description','like',"%{$search}%")
               ->orWhereHas('technician', function($qt) use ($search){
                    $qt->where('name','like',"%{$search}%");
               });
        });
    }
    $expenses = $q->limit(200)->get();
    return $expenses->map(function($e){
        $img = $e->images->first();
        return [
            'id'=>$e->id,
            'description'=>$e->description,
            'amount'=>$e->amount,
            'created_at'=>$e->created_at,
            'created_at_format'=>$e->created_at?->format('d/m/Y H:i'),
            'technician'=> $e->technician ? ['id'=>$e->technician->id,'name'=>$e->technician->name] : null,
            'voucher_url'=> $img ? Storage::disk('public')->url($img->path) : null,
        ];
    });
});

// Protected API routes for technician flows and work orders
Route::middleware('auth:sanctum')->group(function () {
    // Sesiones de técnico (check-in, pings, checkout)
    Route::post('/technician/checkin', [TechnicianSessionController::class, 'checkin']);
    Route::post('/technician/ping', [TechnicianSessionController::class, 'ping']);
    Route::post('/technician/checkout', [TechnicianSessionController::class, 'checkout']);

    // Overview para el técnico autenticado: saldo, últimos gastos y fondos recibidos
    Route::get('/technician/overview', function(\Illuminate\Http\Request $request){
        $user = $request->user();
        if (! $user->hasRole('technician')) {
            abort(403,'Solo técnicos');
        }
        $tz = config('app.tz_guatemala','America/Guatemala');
        $balance = $user->technician_balance; // accessor existente
        $lastExpenses = $user->expenses()->orderByDesc('id')->limit(10)->get(['id','amount','description','created_at'])
            ->map(fn($e)=>[
                'id'=>$e->id,
                'amount'=>number_format($e->amount,2,'.',''),
                'description'=>$e->description,
                'created_at'=>optional($e->created_at)->setTimezone($tz)->format('d/m/Y H:i'),
            ]);
        $transfers = $user->receivedFundTransfers()->with('admin:id,name,email')
            ->orderByDesc('sent_at')->limit(10)->get(['id','amount','currency','note','sent_at','admin_id'])
            ->map(fn($t)=>[
                'id'=>$t->id,
                'amount'=>number_format($t->amount,2,'.',''),
                'currency'=>$t->currency,
                'note'=>$t->note,
                'sent_at'=>optional($t->sent_at)->setTimezone($tz)->format('d/m/Y H:i'),
                'admin'=>[
                    'id'=>$t->admin?->id,
                    'name'=>$t->admin?->name,
                    'email'=>$t->admin?->email,
                ],
            ]);
        return response()->json([
            'balance'=>$balance,
            'expenses'=>$lastExpenses,
            'fund_transfers'=>$transfers,
        ]);
    })->name('api.technician.overview');

    // Órdenes de trabajo para técnico
    Route::get('/technician/work-orders', [WorkOrderController::class, 'indexForTechnician']);
    
        // Dashboard metrics (admin)
        Route::get('/dashboard/metrics', function(){
            $cacheKey = 'dashboard_metrics_v1';
            $data = Cache::remember($cacheKey, 60, function(){
            // Aggregate last 30 days sales & purchases
            $from = now()->subDays(29)->startOfDay();
            $dates = collect(range(0,29))->map(fn($i)=>$from->clone()->addDays($i)->format('Y-m-d'));

            $salesRaw = \App\Models\Sale::where('date','>=',$from)->selectRaw('DATE(date) d, SUM(total) s')->groupBy('d')->pluck('s','d');
            $purchRaw = \App\Models\Purchase::where('date','>=',$from)->selectRaw('DATE(date) d, SUM(total) s')->groupBy('d')->pluck('s','d');
            $salesSeries = $dates->map(fn($d)=>(float)($salesRaw[$d] ?? 0));
            $purchSeries = $dates->map(fn($d)=>(float)($purchRaw[$d] ?? 0));

            // Cuentas por cobrar (ventas con saldo pendiente)
            $salesDueRows = \App\Models\Sale::query()
                ->leftJoin('sale_payments','sales.id','=','sale_payments.sale_id')
                ->whereNull('sales.deleted_at')
                ->groupBy('sales.id','sales.customer_id','sales.date','sales.total')
                ->selectRaw('sales.id, sales.customer_id, sales.date, sales.total, COALESCE(SUM(sale_payments.amount),0) paid, (sales.total-COALESCE(SUM(sale_payments.amount),0)) due')
                ->having('due','>',0)
                ->get();
            $receivables = (float)$salesDueRows->sum('due');
            $openInvoices = $salesDueRows->count();
            $debtorCustomers = $salesDueRows->pluck('customer_id')->unique()->count();
            // Aging buckets
            $aging = ['0_15'=>0.0,'16_30'=>0.0,'31_60'=>0.0,'61_plus'=>0.0];
            $now = now();
            foreach($salesDueRows as $r){
                $days = $r->date ? $now->diffInDays($r->date) : 0;
                if($days <= 15) $aging['0_15'] += $r->due;
                elseif($days <= 30) $aging['16_30'] += $r->due;
                elseif($days <= 60) $aging['31_60'] += $r->due;
                else $aging['61_plus'] += $r->due;
            }
            // Top deudores (clientes con mayor saldo)
            $topDebtors = $salesDueRows->groupBy('customer_id')->map(fn($rows)=>[
                'customer_id'=>$rows->first()->customer_id,
                'due'=> (float)$rows->sum('due'),
            ])->sortByDesc('due')->take(5)->values();
            $customerModels = \App\Models\Customer::whereIn('id',$topDebtors->pluck('customer_id'))
                ->get()->keyBy('id');
            $topDebtors = $topDebtors->map(function($d) use ($customerModels){
                $c = $customerModels[$d['customer_id']] ?? null;
                return [
                    'customer_id' => $d['customer_id'],
                    'customer' => $c?->name ?? ('Cliente '.$d['customer_id']),
                    'due' => $d['due'],
                ];
            });

            // KPIs
            $sales30 = $salesSeries->sum();
            $purch30 = $purchSeries->sum();
            $payments30 = \App\Models\SalePayment::where('paid_at','>=',$from)->sum('amount');
            // Combine manual expenses + bank debits as expenses
            $manualExpenses30 = \App\Models\Expense::where('created_at','>=',$from)->sum('amount');
            $bankDebits30 = \App\Models\BankTransaction::where('date','>=',$from)->where('type','debit')->sum('amount');
            $expenses30 = (float)$manualExpenses30 + (float)$bankDebits30;

            $kpis = [
                ['key'=>'sales','label'=>'Ventas 30d','value'=>$sales30,'trend'=>0,'icon'=>'fa-solid fa-cart-shopping','format'=>'money'],
                ['key'=>'purchases','label'=>'Compras 30d','value'=>$purch30,'trend'=>0,'icon'=>'fa-solid fa-truck','format'=>'money'],
                ['key'=>'payments','label'=>'Cobrado 30d','value'=>$payments30,'trend'=>0,'icon'=>'fa-solid fa-hand-holding-dollar','format'=>'money'],
                ['key'=>'expenses','label'=>'Gastos 30d','value'=>$expenses30,'trend'=>0,'icon'=>'fa-solid fa-receipt','format'=>'money'],
                ['key'=>'receivables','label'=>'Ctas por cobrar','value'=>$receivables,'trend'=>0,'icon'=>'fa-solid fa-file-invoice-dollar','format'=>'money'],
                ['key'=>'open_invoices','label'=>'Facturas abiertas','value'=>$openInvoices,'trend'=>0,'icon'=>'fa-solid fa-file-circle-exclamation','format'=>'int'],
            ];

            // Top products by quantity sold last 30
            $topProducts = \DB::table('productables')
                ->join('sales','productables.productable_id','=','sales.id')
                ->where('productable_type', \App\Models\Sale::class)
                ->where('sales.date','>=',$from)
                ->selectRaw('product_id, SUM(quantity) q')
                ->groupBy('product_id')
                ->orderByDesc('q')
                ->limit(5)
                ->get();
            $prodModels = \App\Models\Product::whereIn('id',$topProducts->pluck('product_id'))->get()->keyBy('id');
            $tpLabels = $topProducts->map(fn($r)=>$prodModels[$r->product_id]->name ?? ('Prod '.$r->product_id));
            $tpValues = $topProducts->pluck('q')->map(fn($v)=>(int)$v);

            // Expenses by category: merge technician expenses + bank transaction debits
            $expCatManual = \App\Models\Expense::where('created_at','>=',$from)
                ->selectRaw('expense_category_id cid, SUM(amount) s')
                ->groupBy('cid')->get();
            $expCatBank = \App\Models\BankTransaction::where('date','>=',$from)
                ->where('type','debit')
                ->selectRaw('category_id cid, SUM(amount) s')
                ->groupBy('cid')->get();

            $combinedByCat = collect();
            foreach([$expCatManual, $expCatBank] as $set){
                foreach($set as $row){
                    $cid = $row->cid; $sum = (float)$row->s;
                    $combinedByCat[$cid] = ($combinedByCat[$cid] ?? 0) + $sum;
                }
            }
            $catIds = $combinedByCat->keys()->filter(fn($id)=>!is_null($id))->values();
            $catModels = \App\Models\ExpenseCategory::whereIn('id',$catIds)->get()->keyBy('id');
            $ecLabels = [];
            $ecValues = [];
            foreach($combinedByCat as $cid=>$sum){
                $label = $cid ? ($catModels[$cid]->name ?? 'Otros') : 'Otros';
                $ecLabels[] = $label; $ecValues[] = (float)$sum;
            }

            // Recent payments
            $recentPayments = \App\Models\SalePayment::with('sale')
                ->orderByDesc('paid_at')->limit(7)->get()
                ->map(fn($p)=>[
                    'id'=>$p->id,
                    'amount'=>$p->amount,
                    'paid_at'=>$p->paid_at?->format('d/m H:i'),
                    'sale_ref'=>($p->sale?->serie ?? '—').'-'.str_pad((string)($p->sale?->correlative ?? $p->sale?->id),4,'0',STR_PAD_LEFT),
                ]);

            // Accounts balances
            $accounts = \App\Models\BankAccount::with('transactions')->get()->map(fn($a)=>[
                'id'=>$a->id,
                'name'=>$a->name,
                'balance'=>$a->current_balance,
            ]);

            return [
                'generated_at'=>now()->format('H:i:s'),
                'kpis'=>$kpis,
                'sales_purchases'=>[
                    'labels'=>$dates->map(fn($d)=>\Carbon\Carbon::parse($d)->format('d'))->toArray(),
                    'sales'=>$salesSeries,
                    'purchases'=>$purchSeries,
                ],
                'top_products'=>['labels'=>$tpLabels,'values'=>$tpValues],
                'expenses_cat'=>['labels'=>$ecLabels,'values'=>$ecValues],
                'recent_payments'=>$recentPayments,
                'accounts'=>$accounts,
                'receivables'=>[
                    'total'=>$receivables,
                    'aging'=>$aging,
                    'top_debtors'=>$topDebtors,
                    'open_invoices'=>$openInvoices,
                    'debtors'=>$debtorCustomers,
                ],
            ];
            });
            return response()->json($data);
        })->name('api.dashboard.metrics');

    // Entradas de órdenes
    Route::get('/work-orders/{workOrder}/entries', [WorkOrderEntryController::class, 'index']);
    Route::post('/work-orders/{workOrder}/entries', [WorkOrderEntryController::class, 'store']);

    // Gastos
    Route::get('/expenses', [ExpenseController::class, 'index']);
    Route::post('/expenses', [ExpenseController::class, 'store']);

    // Admin: fondos, tracking de sesiones y ubicaciones (Spatie middleware)
    Route::middleware(['role:admin'])->group(function() {
        // Endpoint para mapa de técnicos (solo admin)
        Route::get('/admin/technicians/checkins', function() {
            $tz = config('app.tz_guatemala', 'America/Guatemala');
            $today = now($tz)->toDateString();

            $techs = \App\Models\User::role('technician')
                ->with(['technicianSessions' => function($q) use ($today) {
                    $q->where('started_on_date', $today)->orderByDesc('started_at');
                }])
                ->get();

            $data = $techs->map(function($t) use ($tz) {
                $sessions = $t->technicianSessions->map(function($s) use ($tz) {
                    // Última ubicación registrada para la sesión
                    $lastLoc = $s->locations()->orderByDesc('logged_at')->first();
                    return [
                        'id' => $s->id,
                        // Fechas con manejo null-safe (evita llamar format() sobre null)
                        'started_at' => $s->started_at?->copy()->setTimezone($tz)->format('d/m/Y H:i'),
                        'ended_at' => $s->ended_at?->copy()->setTimezone($tz)->format('d/m/Y H:i'),
                        'start' => [
                            'lat' => $s->start_latitude,
                            'lng' => $s->start_longitude,
                        ],
                        'last' => $lastLoc ? [
                            'lat' => $lastLoc->latitude,
                            'lng' => $lastLoc->longitude,
                            'logged_at' => $lastLoc->logged_at
                                ? $lastLoc->logged_at->copy()->setTimezone($tz)->format('d/m/Y H:i')
                                : null,
                        ] : null,
                        'end' => ($s->ended_at && $s->end_latitude && $s->end_longitude) ? [
                            'lat' => $s->end_latitude,
                            'lng' => $s->end_longitude,
                        ] : null,
                    ];
                });
                return [
                    'id' => $t->id,
                    'name' => $t->name,
                    'email' => $t->email,
                    'sessions' => $sessions,
                ];
            })->values();

            return response()->json($data);
        });
        // Dashboard de técnicos: balance, últimas sesiones y gastos (endpoint separado)
        Route::get('/admin/technicians/overview', function() {
            $tz = config('app.tz_guatemala', 'America/Guatemala');
            $today = now($tz)->toDateString();
            $techs = \App\Models\User::role('technician')
                ->withCount(['expenses as expenses_total_amount' => function($q){ $q->select(DB::raw('COALESCE(SUM(amount),0)')); }])
                ->get();

            $data = $techs->map(function($t) use ($today, $tz){
                $lastSessions = $t->technicianSessions()->where('started_on_date',$today)
                    ->orderByDesc('started_at')->take(3)->get(['id','started_at','ended_at']);
                $lastExpenses = $t->expenses()->orderByDesc('id')->take(5)->get(['id','amount','description','created_at']);
                return [
                    'id' => $t->id,
                    'name' => $t->name,
                    'email' => $t->email,
                    'balance' => $t->technician_balance,
                    'sessions_today' => $lastSessions->map(fn($s) => [
                        'id' => $s->id,
                        'started_at' => optional($s->started_at)->setTimezone($tz)->format('H:i'),
                        'ended_at' => $s->ended_at?->setTimezone($tz)->format('H:i'),
                    ]),
                    'recent_expenses' => $lastExpenses->map(fn($e) => [
                        'id' => $e->id,
                        'amount' => number_format($e->amount,2,'.',''),
                        'description' => $e->description,
                        'created_at' => optional($e->created_at)->setTimezone($tz)->format('d/m H:i'),
                    ]),
                ];
            });
            return response()->json($data);
        });
        Route::get('/admin/fund-transfers', [\App\Http\Controllers\Admin\FundTransferController::class, 'index']);
        Route::post('/admin/fund-transfers', [\App\Http\Controllers\Admin\FundTransferController::class, 'store']);
    Route::delete('/admin/fund-transfers/{fundTransfer}', [\App\Http\Controllers\Admin\FundTransferController::class, 'destroy']);

        // Listado de cuentas bancarias para selects (solo admin)
        Route::match(['GET','POST'],'/bank-accounts', function(){
            return \App\Models\BankAccount::orderBy('name')->get()->map(fn($a)=>[
                'id' => $a->id,
                'name' => $a->name,
                'balance' => number_format($a->current_balance,2,'.',''),
            ]);
        })->name('api.bank-accounts.index');

        // Admin: tracking de sesiones y ubicaciones
        Route::get('/admin/technicians/{technician}/sessions', [\App\Http\Controllers\Admin\TechnicianTrackingController::class, 'sessions']);
        Route::get('/admin/technician-sessions/{session}/locations', [\App\Http\Controllers\Admin\TechnicianTrackingController::class, 'locations']);
    });
});


