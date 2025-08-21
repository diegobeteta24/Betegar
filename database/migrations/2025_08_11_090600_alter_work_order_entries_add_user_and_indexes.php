<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('work_order_entries', function (Blueprint $table) {
            if (!Schema::hasColumn('work_order_entries', 'user_id')) {
                $table->foreignId('user_id')->after('work_order_id')->constrained('users')->cascadeOnDelete();
            }
            $table->index(['work_order_id', 'work_date']);
        });
    }

    public function down(): void
    {
        Schema::table('work_order_entries', function (Blueprint $table) {
            $table->dropIndex(['work_order_id', 'work_date']);
            if (Schema::hasColumn('work_order_entries', 'user_id')) {
                $table->dropConstrainedForeignId('user_id');
            }
        });
    }
};
