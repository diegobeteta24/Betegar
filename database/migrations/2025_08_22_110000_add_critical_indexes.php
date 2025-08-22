<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Sales, Purchases dates for ranges
        if (Schema::hasTable('sales')) {
            Schema::table('sales', function (Blueprint $table) {
                $table->index('date', 'sales_date_idx');
                $table->index('customer_id', 'sales_customer_idx');
            });
        }
        if (Schema::hasTable('purchases')) {
            Schema::table('purchases', function (Blueprint $table) {
                $table->index('date', 'purchases_date_idx');
                $table->index('supplier_id', 'purchases_supplier_idx');
            });
        }
        // Bank transactions by date and account
        if (Schema::hasTable('bank_transactions')) {
            Schema::table('bank_transactions', function (Blueprint $table) {
                $table->index(['bank_account_id','date'], 'bt_account_date_idx');
                $table->index('type', 'bt_type_idx');
            });
        }
        // Sale payments by paid_at and sale
        if (Schema::hasTable('sale_payments')) {
            Schema::table('sale_payments', function (Blueprint $table) {
                $table->index('paid_at', 'sp_paid_at_idx');
                $table->index('sale_id', 'sp_sale_idx');
            });
        }
        // Expenses by created_at and technician_id
        if (Schema::hasTable('expenses')) {
            Schema::table('expenses', function (Blueprint $table) {
                $table->index('created_at', 'exp_created_idx');
                $table->index('technician_id', 'exp_tech_idx');
                if (Schema::hasColumn('expenses','expense_category_id')) {
                    $table->index('expense_category_id', 'exp_cat_idx');
                }
            });
        }
        // Productables polymorphic indices
        if (Schema::hasTable('productables')) {
            Schema::table('productables', function (Blueprint $table) {
                $table->index(['productable_type','productable_id'], 'productables_morph_idx');
                $table->index('product_id', 'productables_product_idx');
            });
        }
    }

    public function down(): void
    {
        $drop = function($table, $indexes){
            foreach($indexes as $idx){ if(\Illuminate\Support\Facades\DB::getSchemaBuilder()->hasTable($table)) {
                try { Schema::table($table, fn(Blueprint $t)=>$t->dropIndex($idx)); } catch(\Throwable $e) { /* ignore */ }
            }}
        };
        $drop('sales', ['sales_date_idx','sales_customer_idx']);
        $drop('purchases', ['purchases_date_idx','purchases_supplier_idx']);
        $drop('bank_transactions', ['bt_account_date_idx','bt_type_idx']);
        $drop('sale_payments', ['sp_paid_at_idx','sp_sale_idx']);
        $drop('expenses', ['exp_created_idx','exp_tech_idx','exp_cat_idx']);
        $drop('productables', ['productables_morph_idx','productables_product_idx']);
    }
};
