<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->decimal('subtotal', 10,2)->default(0)->after('customer_id');
            $table->decimal('discount_percent',5,2)->default(0)->after('subtotal');
            $table->decimal('discount_amount',10,2)->default(0)->after('discount_percent');
            $table->uuid('public_token')->nullable()->unique()->after('discount_amount');
        });
        Schema::table('sales', function (Blueprint $table) {
            $table->decimal('subtotal', 10,2)->default(0)->after('warehouse_id');
            $table->decimal('discount_percent',5,2)->default(0)->after('subtotal');
            $table->decimal('discount_amount',10,2)->default(0)->after('discount_percent');
        });
    }

    public function down(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->dropColumn(['subtotal','discount_percent','discount_amount','public_token']);
        });
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['subtotal','discount_percent','discount_amount']);
        });
    }
};
