<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{Product, Customer, Supplier, Warehouse, User, Purchase, PurchaseOrder, Sale, BankAccount, BankTransaction, ExpenseCategory, Expense, WorkOrder};
use Spatie\Permission\Models\Role;

class DemoFullSeeder extends Seeder
{
    public function run(): void
    {
        $min = 5; $max = 10;

        // Ensure base role data seeded
        if (!Role::where('name','admin')->exists()) {
            $this->call(RoleSeeder::class);
        }

        // Warehouses
        if (Warehouse::count() < $min) {
            \App\Models\Warehouse::factory()->count($min - Warehouse::count())->create();
        }

        // Suppliers & Customers
        if (Supplier::count() < $min) Supplier::factory()->count($min - Supplier::count())->create();
        if (Customer::count() < $min) Customer::factory()->count($min - Customer::count())->create();

        // Products
        if (Product::count() < $max) Product::factory()->count($max - Product::count())->create();

        // Technicians
        $needTech = $min - User::role('technician')->count();
        if ($needTech > 0) {
            User::factory()->count($needTech)->create()->each(fn($u) => $u->assignRole('technician'));
        }

        // Expense Categories
        foreach (['Combustible','Viáticos','Herramientas','Papelería','Servicios'] as $ec) {
            ExpenseCategory::firstOrCreate(['name' => $ec]);
        }

        // Bank Accounts
        if (BankAccount::count() < 3) {
            BankAccount::factory()->count(3 - BankAccount::count())->create();
        }

        $accounts   = BankAccount::all();
        $warehouses = Warehouse::all();
        $suppliers  = Supplier::all();
        $customers  = Customer::all();
        $products   = Product::inRandomOrder()->take(20)->get();
        $techs      = User::role('technician')->get();

        // Purchase Orders + Purchases
        for ($i=0;$i<$min;$i++) {
            $supplier = $suppliers->random();
            $po = PurchaseOrder::create([
                'supplier_id'  => $supplier->id,
                'voucher_type' => 1,
                'serie'        => 'PO'.str_pad($i+1,3,'0',STR_PAD_LEFT),
                'correlative'  => rand(1000,9999),
                'date'         => now()->subDays(rand(5,20)),
                'observation'  => 'OC demo '.($i+1),
            ]);
            $total = 0;
            $poProducts = $products->random(rand(3,5));
            foreach ($poProducts as $p) {
                $qty = rand(1,8); $price = rand(50,300)/10; $sub = $qty*$price; $total += $sub;
                $po->products()->attach($p->id, [
                    'quantity'=>$qty,
                    'price'=>$price,
                    'subtotal'=>$sub,
                ]);
            }
            $po->update(['total'=>$total]);

            $purchase = Purchase::create([
                'voucher_type'      => 1,
                'serie'             => 'FA'.str_pad($i+1,3,'0',STR_PAD_LEFT),
                'correlative'       => rand(1000,9999),
                'date'              => now()->subDays(rand(1,10)),
                'purchase_order_id' => $po->id,
                'supplier_id'       => $supplier->id,
                'warehouse_id'      => $warehouses->random()->id,
                'bank_account_id'   => $accounts->random()->id,
                'total'             => $total,
                'observation'       => 'Compra demo '.$i,
            ]);
            foreach ($poProducts as $p) {
                $pivot = $po->products()->where('product_id',$p->id)->first()->pivot;
                $purchase->products()->attach($p->id, [
                    'quantity'=>$pivot->quantity,
                    'price'=>$pivot->price,
                    'subtotal'=>$pivot->subtotal,
                ]);
            }
        }

        // Sales
        for ($i=0;$i<$min;$i++) {
            $customer = $customers->random();
            $saleTotal = 0; $items = $products->random(rand(2,4));
            $sale = Sale::create([
                'customer_id' => $customer->id,
                'warehouse_id'=> $warehouses->random()->id,
                'voucher_type'=> 1,
                'serie'       => 'V'.str_pad($i+1,3,'0',STR_PAD_LEFT),
                'correlative' => rand(2000,9000),
                'date'        => now()->subDays(rand(0,8)),
                'total'       => 0,
                'observation' => 'Venta demo '.$i,
            ]);
            foreach ($items as $p) {
                $qty = rand(1,5); $price = rand(80,500)/10; $sub = $qty*$price; $saleTotal += $sub;
                $sale->products()->attach($p->id, [
                    'quantity'=>$qty,
                    'price'=>$price,
                    'subtotal'=>$sub,
                ]);
            }
            $sale->update(['total'=>$saleTotal]);
        }

        // Expenses
        $expenseCategories = ExpenseCategory::all();
        foreach ($techs as $t) {
            for ($i=0;$i<rand(2,4);$i++) {
                Expense::create([
                    'technician_id'       => $t->id,
                    'expense_category_id' => $expenseCategories->random()->id,
                    'description'         => 'Gasto demo '.$i.' de '.$t->name,
                    'amount'              => rand(50,400),
                    'has_voucher'         => true,
                ]);
            }
        }

        // Work Orders
        if (WorkOrder::count() < $min) {
            for ($i=0;$i<$min;$i++) {
                $wo = WorkOrder::create([
                    'customer_id' => $customers->random()->id,
                    'user_id'     => $techs->random()->id,
                    'address'     => 'Dirección demo '.($i+1),
                    'objective'   => 'Objetivo demo '.($i+1),
                    'status'      => 'pending',
                ]);
                $wo->technicians()->sync($techs->random(rand(1, min(3, $techs->count())))->pluck('id')->toArray());
            }
        }

        // Manual bank transactions
        foreach ($accounts as $acc) {
            for ($i=0;$i<2;$i++) {
                BankTransaction::create([
                    'bank_account_id' => $acc->id,
                    'type'            => 'credit',
                    'date'            => now()->subDays(rand(1,7)),
                    'amount'          => rand(500,1500),
                    'description'     => 'Ingreso inicial '.$i,
                ]);
                BankTransaction::create([
                    'bank_account_id' => $acc->id,
                    'type'            => 'debit',
                    'date'            => now()->subDays(rand(1,7)),
                    'amount'          => rand(100,400),
                    'description'     => 'Gasto bancario '.$i,
                ]);
            }
        }
    }
}
