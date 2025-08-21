// database/migrations/xxxx_xx_xx_xxxxxx_create_work_orders_table.php
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('work_orders', function (Blueprint $table) {
            $table->id();
            // Cliente al que pertenece la orden (suponiendo que usas la tabla customers)
            $table->foreignId('customer_id')
                  ->constrained()
                  ->onDelete('cascade');
            // Técnico asignado
            $table->foreignId('user_id')
                  ->constrained()
                  ->onDelete('cascade');
            $table->string('address');
            $table->text('objective');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('work_orders');
    }
}
