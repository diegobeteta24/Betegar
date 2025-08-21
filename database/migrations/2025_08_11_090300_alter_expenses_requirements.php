<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            // Reglas: description NOT NULL ya está, amount NOT NULL ya está
            // Forzaremos que la fecha sea created_at (no editable) y que exista al menos un comprobante en images
            // A nivel DB polimórfico no aseguramos el comprobante, se hará a nivel de app.
            if (!Schema::hasColumn('expenses', 'has_voucher')) {
                $table->boolean('has_voucher')->default(false)->after('amount');
            }
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            if (Schema::hasColumn('expenses', 'has_voucher')) {
                $table->dropColumn('has_voucher');
            }
        });
    }
};
