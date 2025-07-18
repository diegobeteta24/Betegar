<?php

// database/migrations/2025_07_18_000001_create_bank_transactions_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('bank_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_account_id')
                  ->constrained()
                  ->onDelete('cascade');
            $table->morphs('transactionable'); 
              // permite relaciones con venta, compra o gasto manual
            $table->enum('type', ['credit','debit'])
                  ->comment('credit=entrada (+), debit=salida (–)');
            $table->timestamp('date')->useCurrent();
            $table->decimal('amount', 12, 2);
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('bank_transactions');
    }
};
