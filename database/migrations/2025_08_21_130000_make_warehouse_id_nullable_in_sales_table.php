<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            try { $table->dropForeign(['warehouse_id']); } catch (\Throwable $e) {}
        });
        Schema::table('sales', function (Blueprint $table) {
            $table->foreignId('warehouse_id')->nullable()->change();
        });
        Schema::table('sales', function (Blueprint $table) {
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            try { $table->dropForeign(['warehouse_id']); } catch (\Throwable $e) {}
        });
        Schema::table('sales', function (Blueprint $table) {
            $table->foreignId('warehouse_id')->nullable(false)->change();
        });
        Schema::table('sales', function (Blueprint $table) {
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->cascadeOnDelete();
        });
    }
};
