<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fund_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('technician_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('GTQ');
            $table->string('note')->nullable();
            $table->timestamp('sent_at')->useCurrent();
            $table->timestamps();

            $table->index(['technician_id', 'sent_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fund_transfers');
    }
};
