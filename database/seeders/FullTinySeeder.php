<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use App\Models\{User, Product, Warehouse, Supplier, Customer, Reason, Movement, Transfer, PurchaseOrder, Purchase, Sale, BankAccount, BankTransaction, ExpenseCategory, Expense, FundTransfer, WorkOrder, TechnicianSession, SalePayment};

class FullTinySeeder extends Seeder
{
    public function run(): void
    {
        $min=5; $max=10;

        if (!Reason::count()) $this->call(ReasonSeeder::class);
        if (!ExpenseCategory::count()) {
            foreach (['Combustible','Viáticos','Herramientas','Papelería','Servicios'] as $c) ExpenseCategory::firstOrCreate(['name'=>$c]);
        }

        if (Warehouse::count()<$min) \App\Models\Warehouse::factory()->count($min - Warehouse::count())->create();
        if (Supplier::count()<$min) \App\Models\Supplier::factory()->count($min - Supplier::count())->create();
        if (Customer::count()<$min) \App\Models\Customer::factory()->count($min - Customer::count())->create();
        if (Product::count()<$max) \App\Models\Product::factory()->count($max - Product::count())->create();

        if (BankAccount::count()<3) {
            for($i=0;$i<3 - BankAccount::count();$i++) {
                BankAccount::create([
                    'name' => 'Cuenta Demo '.($i+1),
                    'initial_balance' => rand(500,2000),
                    'currency' => 'GTQ',
                    'description' => 'Cuenta generada por seeder',
                ]);
            }
        }

        $accounts = BankAccount::all();
        $warehouses = Warehouse::all();
        $suppliers  = Supplier::all();
        $customers  = Customer::all();
        $products   = Product::inRandomOrder()->take(25)->get();

        $admin = User::role('admin')->first() ?? User::first();
        $techCount = User::role('technician')->count();
        if ($techCount < 2) {
            \App\Models\User::factory()->count(2 - $techCount)->create()->each(fn($u)=>$u->assignRole('technician'));
        }
        $technicians = User::role('technician')->get();

        for($i=0;$i<$min;$i++) {
            $wo = WorkOrder::create([
                'customer_id' => $customers->random()->id,
                'user_id'     => $technicians->random()->id,
                'address'     => 'Dirección '.$i,
                'objective'   => 'Objetivo '.$i,
                'status'      => Arr::random(['pending','in_progress','done']),
            ]);
            $wo->technicians()->sync($technicians->random(rand(1,min(3,$technicians->count())))->pluck('id')->toArray());
        }

        foreach ($technicians as $t) {
            $days = collect(range(1,7))->shuffle()->take(2); // pick 2 distinct days
            foreach ($days as $d) {
                $start = now()->subDays($d)->setTime(rand(7,9),0);
                $end   = (clone $start)->addHours(rand(6,9));
                if (\App\Models\TechnicianSession::where('user_id',$t->id)->whereDate('started_at',$start->toDateString())->exists()) continue;
                TechnicianSession::create([
                    'user_id'        => $t->id,
                    'start_latitude' => 14.6 + mt_rand()/mt_getrandmax()/100,
                    'start_longitude'=> -90.5 + mt_rand()/mt_getrandmax()/100,
                    'started_at'     => $start,
                    'started_on_date'=> $start->toDateString(),
                    'end_latitude'   => 14.6 + mt_rand()/mt_getrandmax()/100,
                    'end_longitude'  => -90.5 + mt_rand()/mt_getrandmax()/100,
                    'ended_at'       => $end,
                ]);
            }
        }

        for($i=0;$i<$min;$i++) {
            $supplier=$suppliers->random();
            $po = PurchaseOrder::create([
                'supplier_id'=>$supplier->id,
                'voucher_type'=>1,
                'serie'=>'PO'.str_pad($i+1,3,'0',STR_PAD_LEFT),
                'correlative'=>rand(1000,9999),
                'date'=>now()->subDays(rand(10,30)),
                'observation'=>'OC auto '.$i,
            ]);
            $total=0; $poItems=$products->random(rand(3,6));
            foreach($poItems as $p){$q=rand(1,5);$pr=rand(50,300)/10;$sub=$q*$pr;$po->products()->attach($p->id,['quantity'=>$q,'price'=>$pr,'subtotal'=>$sub]);$total+=$sub;}
            $po->update(['total'=>$total]);
            $purchase = Purchase::create([
                'voucher_type'=>1,'serie'=>'FA'.str_pad($i+1,3,'0',STR_PAD_LEFT),'correlative'=>rand(1000,9999),'date'=>now()->subDays(rand(1,9)),'purchase_order_id'=>$po->id,'supplier_id'=>$supplier->id,'warehouse_id'=>$warehouses->random()->id,'bank_account_id'=>$accounts->random()->id,'total'=>$total,'observation'=>'Compra auto '.$i,
            ]);
            foreach($poItems as $p){$pv=$po->products()->where('product_id',$p->id)->first()->pivot; $purchase->products()->attach($p->id,['quantity'=>$pv->quantity,'price'=>$pv->price,'subtotal'=>$pv->subtotal]);}
        }

        $reasons = Reason::all();
        for($i=0;$i<$min;$i++) {
            $type = Arr::random([1,2]); // 1=in,2=out
            $mov = Movement::create([
                'type'=>$type,'serie'=>'MV'.str_pad($i+1,3,'0',STR_PAD_LEFT),'correlative'=>rand(1000,9999),'date'=>now()->subDays(rand(1,15)),'warehouse_id'=>$warehouses->random()->id,'total'=>0,'observation'=>'Movimiento '.$i,'reason_id'=>$reasons->random()->id,
            ]);
            $sum=0; $items=$products->random(rand(2,4));
            foreach($items as $p){$q=rand(1,4);$pr=rand(40,200)/10;$sub=$q*$pr;$mov->products()->attach($p->id,['quantity'=>$q,'price'=>$pr,'subtotal'=>$sub]);$sum+=$sub;}
            $mov->update(['total'=>$sum]);
        }

        for($i=0;$i<$min;$i++) {
            $orig=$warehouses->random(); $dest=$warehouses->where('id','!=',$orig->id)->random();
            $tr = Transfer::create([
                'serie'=>'TR'.str_pad($i+1,3,'0',STR_PAD_LEFT),'correlative'=>rand(1000,9999),'date'=>now()->subDays(rand(1,12)),'total'=>0,'observation'=>'Transfer '.$i,'origin_warehouse_id'=>$orig->id,'destination_warehouse_id'=>$dest->id
            ]);
            $sum=0;$items=$products->random(rand(2,4));
            foreach($items as $p){$q=rand(1,3);$pr=rand(50,250)/10;$sub=$q*$pr;$tr->products()->attach($p->id,['quantity'=>$q,'price'=>$pr,'subtotal'=>$sub]);$sum+=$sub;}
            $tr->update(['total'=>$sum]);
        }

        for($i=0;$i<$min;$i++) {
            $cust=$customers->random();
            $sale=Sale::create(['voucher_type'=>1,'serie'=>'V'.str_pad($i+1,3,'0',STR_PAD_LEFT),'correlative'=>rand(2000,9000),'date'=>now()->subDays(rand(0,6)),'customer_id'=>$cust->id,'warehouse_id'=>$warehouses->random()->id,'total'=>0,'observation'=>'Venta auto '.$i]);
            $sum=0;$items=$products->random(rand(2,5));
            foreach($items as $p){$q=rand(1,5);$pr=rand(80,500)/10;$sub=$q*$pr;$sale->products()->attach($p->id,['quantity'=>$q,'price'=>$pr,'subtotal'=>$sub]);$sum+=$sub;}
            $sale->update(['total'=>$sum]);
            $payAmount = round($sum * Arr::random([0.4,0.6,1]),2);
            $payment = SalePayment::create(['sale_id'=>$sale->id,'bank_account_id'=>$accounts->random()->id,'amount'=>$payAmount,'method'=>'transfer','reference'=>'PAY'.rand(1000,9999),'paid_at'=>now()->subDays(rand(0,3))]);
            $payment->transaction()->create(['bank_account_id'=>$payment->bank_account_id,'type'=>'credit','date'=>$payment->paid_at,'amount'=>$payment->amount,'description'=>'Pago venta #'.$sale->id]);
        }

        $expCats = ExpenseCategory::all();
        foreach($technicians as $t){
            for($i=0;$i<rand(2,4);$i++) {
                Expense::create(['technician_id'=>$t->id,'expense_category_id'=>$expCats->random()->id,'description'=>'Gasto '.$i.' tech '.$t->id,'amount'=>rand(50,300),'has_voucher'=>true]);
            }
        }

        foreach($technicians as $t){
            FundTransfer::create(['admin_id'=>$admin?->id,'technician_id'=>$t->id,'amount'=>rand(200,600),'currency'=>'GTQ','note'=>'Fondo inicial','sent_at'=>now()->subDays(rand(1,5))]);
        }

        foreach($accounts as $acc){
            BankTransaction::create(['bank_account_id'=>$acc->id,'type'=>'credit','date'=>now()->subDays(rand(1,5)),'amount'=>rand(400,1200),'description'=>'Ingreso manual demo']);
            BankTransaction::create(['bank_account_id'=>$acc->id,'type'=>'debit','date'=>now()->subDays(rand(1,5)),'amount'=>rand(100,300),'description'=>'Gasto bancario demo']);
        }
    }
}
