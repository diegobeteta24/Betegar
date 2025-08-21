<?php

// database/migrations/xxxx_xx_xx_xxxxxx_create_technician_session_locations_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTechnicianSessionLocationsTable extends Migration
{
    public function up()
    {
        Schema::create('technician_session_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('technician_session_id')
                  ->constrained('technician_sessions')
                  ->onDelete('cascade');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->timestamp('logged_at')->useCurrent();
        });
    }

    public function down()
    {
        Schema::dropIfExists('technician_session_locations');
    }
}
