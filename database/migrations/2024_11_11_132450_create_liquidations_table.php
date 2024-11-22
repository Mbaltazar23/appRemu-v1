<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('liquidations', function (Blueprint $table) {
            $table->id(); // Esto crea el campo 'id' como auto-incremental
            $table->foreignId('worker_id')->constrained('workers')->onDelete('cascade'); // Relación con la tabla 'workers'
            $table->integer('month');
            $table->integer('year');
            $table->double('values'); // Usamos double para el campo 'values'
            $table->json('details')->nullable(); // Campo JSON que puede ser NULL
            $table->binary('glosa')->nullable(); // Cambiar 'text' a 'binary' para almacenar BLOB
            $table->timestamps(); // Campos created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('liquidations');
    }
};
