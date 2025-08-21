// database/migrations/xxxx_xx_xx_xxxxxx_create_work_order_entries_table.php
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkOrderEntriesTable extends Migration
{
    public function up()
    {
        Schema::create('work_order_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_order_id')
                  ->constrained()
                  ->onDelete('cascade');
            $table->text('progress');              // Avances del técnico
            $table->text('requests')->nullable();  // Solicitudes de materiales, etc.
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('work_order_entries');
    }
}
