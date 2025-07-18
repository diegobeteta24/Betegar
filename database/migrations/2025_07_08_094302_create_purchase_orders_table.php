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
        Schema::create('purchase_orders', function (Blueprint $table) {
    $table->id();
    // Cambiado de integer a string
    $table->string('voucher_type', 50);
    $table->string('serie');
    $table->integer('correlative');
    $table->timestamp('date')
    ->useCurrent();
    $table->foreignId('supplier_id')
          ->constrained()
          ->onDelete('cascade');
    $table->decimal('total', 10, 2)->default(0);
    $table->string('observation')->nullable();
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
