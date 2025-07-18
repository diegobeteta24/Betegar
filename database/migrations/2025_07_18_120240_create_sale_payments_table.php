<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
    Schema::create('sale_payments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('sale_id')
          ->constrained()
          ->onDelete('cascade');
    $table->foreignId('bank_account_id')
          ->constrained()
          ->onDelete('cascade');
    $table->decimal('amount', 12, 2);
    $table->enum('method', ['cash','transfer','card','other']);
    $table->string('reference')->nullable();
    $table->timestamp('paid_at')->useCurrent();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_payments');
    }
};
