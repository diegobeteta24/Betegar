<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            if (!Schema::hasColumn('quotes','customer_address_id')) {
                $table->foreignId('customer_address_id')->nullable()->after('customer_id')
                      ->constrained('customer_addresses')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            if (Schema::hasColumn('quotes','customer_address_id')) {
                $table->dropConstrainedForeignId('customer_address_id');
            }
        });
    }
};
