<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Regla a nivel de datos: solo una firma por entrada usando images.tag = 'signature'
        // No podemos crear un constraint referencial directo por ser polimórfico,
        // pero reforzamos por índice parcial si el motor lo soporta (MySQL no soporta índices parciales),
        // así que dejamos validación en modelo y opcionalmente trigger.
        // Aquí documentamos la regla para consistencia.
        DB::unprepared(<<<SQL
        -- Regla: sólo una imagen con tag='signature' por imageable (en images ya hay unique (imageable_type, imageable_id, tag))
        -- Para usar con firmas de entradas: imageable_type = 'App\\Models\\WorkOrderEntry' y tag='signature'
        SQL);
    }

    public function down(): void
    {
        // Nada que revertir al ser comentario/nota.
    }
};
