<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $tables = [
            'products','categories','customers','suppliers','warehouses','purchase_orders','purchases','quotes','sales','movements','transfers','work_orders','work_order_entries','images','reminders','bank_accounts','bank_transactions','expense_categories','expenses','fund_transfers','sale_payments','inventories','reasons','technician_sessions','technician_session_locations'
        ];
        foreach ($tables as $table) {
            if (Schema::hasTable($table) && !Schema::hasColumn($table,'deleted_at')) {
                Schema::table($table, function(Blueprint $bp){
                    $bp->softDeletes();
                });
            }
        }
    }
    public function down(): void
    {
        $tables = [
            'products','categories','customers','suppliers','warehouses','purchase_orders','purchases','quotes','sales','movements','transfers','work_orders','work_order_entries','images','reminders','bank_accounts','bank_transactions','expense_categories','expenses','fund_transfers','sale_payments','inventories','reasons','technician_sessions','technician_session_locations'
        ];
        foreach ($tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table,'deleted_at')) {
                Schema::table($table, function(Blueprint $bp){
                    $bp->dropSoftDeletes();
                });
            }
        }
    }
};
