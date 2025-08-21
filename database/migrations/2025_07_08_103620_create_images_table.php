<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('images', function (Blueprint $table) {
            $table->id();

            // Ruta y tamaño
            $table->string('path')->unique();
            $table->integer('size')->default(0);

            // Tag para diferenciar (null = imagen normal; 'signature' = firma; otros tags si los necesitas)
            $table->string('tag')->nullable();

            // Polimórfica
            $table->morphs('imageable');

            $table->timestamps();

            // Índice único para que sólo haya UNA imagen con tag='signature' por cada modelo/id
            $table->unique(
                ['imageable_type', 'imageable_id', 'tag'],
                'images_imageable_tag_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('images');
    }
};
