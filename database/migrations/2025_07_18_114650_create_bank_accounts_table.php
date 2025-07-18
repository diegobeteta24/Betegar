<?php

// database/migrations/2025_07_18_000000_create_bank_accounts_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->comment('Nombre de la cuenta (p.ej. Caja, Banco G&T)');
            $table->decimal('initial_balance', 12, 2)->default(0)->comment('Saldo inicial en GTQ');
            $table->string('currency', 3)->default('GTQ');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('bank_accounts');
    }
};
