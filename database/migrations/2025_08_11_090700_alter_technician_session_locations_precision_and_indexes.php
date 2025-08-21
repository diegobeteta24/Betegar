<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('technician_session_locations', function (Blueprint $table) {
            // Asegurar precisión y performance
            $table->decimal('latitude', 10, 7)->change();
            $table->decimal('longitude', 10, 7)->change();
            $table->index(['technician_session_id', 'logged_at'], 'tsl_session_logged_idx');
        });
    }

    public function down(): void
    {
        Schema::table('technician_session_locations', function (Blueprint $table) {
            $table->dropIndex('tsl_session_logged_idx');
        });
    }
};
