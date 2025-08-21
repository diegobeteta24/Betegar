<?php

// database/migrations/xxxx_xx_xx_xxxxxx_create_technician_sessions_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTechnicianSessionsTable extends Migration
{
    public function up()
    {
        Schema::create('technician_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->constrained()
                  ->onDelete('cascade');
            // Datos del check‑in inicial
            $table->decimal('start_latitude', 10, 7);
            $table->decimal('start_longitude', 10, 7);
            $table->timestamp('started_at')->useCurrent();
            // Datos del check‑out (pueden quedar nulos hasta el checkout)
            $table->decimal('end_latitude', 10, 7)->nullable();
            $table->decimal('end_longitude', 10, 7)->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('technician_sessions');
    }
}
