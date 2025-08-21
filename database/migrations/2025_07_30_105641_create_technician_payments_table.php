// database/migrations/xxxx_xx_xx_xxxxxx_create_technician_payments_table.php
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTechnicianPaymentsTable extends Migration
{
    public function up()
    {
        Schema::create('technician_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->constrained()
                  ->onDelete('cascade');
            $table->decimal('amount', 12, 2);
            $table->string('description')->nullable(); // Concepto o detalle del envío de dinero
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('technician_payments');
    }
}
