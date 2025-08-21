<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            if (!Schema::hasColumn('expenses','expense_category_id')) {
                $table->foreignId('expense_category_id')->nullable()->after('technician_id')
                      ->constrained('expense_categories')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            if (Schema::hasColumn('expenses','expense_category_id')) {
                $table->dropConstrainedForeignId('expense_category_id');
            }
        });
    }
};
