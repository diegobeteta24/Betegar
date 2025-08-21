<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('bank_transactions', function (Blueprint $table) {
            if (Schema::hasColumn('bank_transactions','transactionable_type')) {
                $table->string('transactionable_type')->nullable()->change();
            }
            if (Schema::hasColumn('bank_transactions','transactionable_id')) {
                $table->unsignedBigInteger('transactionable_id')->nullable()->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('bank_transactions', function (Blueprint $table) {
            if (Schema::hasColumn('bank_transactions','transactionable_type')) {
                $table->string('transactionable_type')->nullable(false)->change();
            }
            if (Schema::hasColumn('bank_transactions','transactionable_id')) {
                $table->unsignedBigInteger('transactionable_id')->nullable(false)->change();
            }
        });
    }
};
