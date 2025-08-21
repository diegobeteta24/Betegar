<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('technician_sessions', function (Blueprint $table) {
            // Columna de fecha (según TZ de Guatemala) para aplicar la regla: 1 check-in inicial por día por técnico
            if (!Schema::hasColumn('technician_sessions', 'started_on_date')) {
                $table->date('started_on_date')->after('started_at');
                $table->index(['user_id', 'started_on_date'], 'technician_sessions_user_date_idx');
                // Unicidad por usuario+fecha
                $table->unique(['user_id', 'started_on_date'], 'technician_sessions_user_date_unique');
            }

            // Índices útiles
            $table->index(['user_id', 'ended_at'], 'technician_sessions_user_end_idx');
        });
    }

    public function down(): void
    {
        Schema::table('technician_sessions', function (Blueprint $table) {
            if (Schema::hasColumn('technician_sessions', 'started_on_date')) {
                $table->dropUnique('technician_sessions_user_date_unique');
                $table->dropIndex('technician_sessions_user_date_idx');
                $table->dropColumn('started_on_date');
            }
            $table->dropIndex('technician_sessions_user_end_idx');
        });
    }
};
