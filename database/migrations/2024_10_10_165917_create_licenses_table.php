<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Tabla: licenses (licencia)
     */
    public function up(): void
    {
        Schema::create('licenses', function (Blueprint $table) {
            $table->id(); // ID de la licencia (auto-incrementable)
            $table->unsignedBigInteger('worker_id')->nullable(); // ID del trabajador
            $table->date('issue_date')->nullable(); // Fecha de la licencia
            $table->string('reason', 50)->nullable(); // Motivo de la licencia
            $table->integer('days')->nullable(); // Días de la licencia
            $table->string('institution')->nullable(); // Institución que emite la licencia
            $table->string('receipt_number', 10)->nullable(); // Folio o número de recibo
            $table->string('receipt_date', 11)->nullable(); // Fecha de recepción de la licencia
            $table->string('processing_date', 11)->nullable(); // Fecha de tramitación
            $table->string('responsible_person', 50)->nullable(); // Persona responsable de la tramitación
            $table->foreign('worker_id')->references('id')->on('workers')->onDelete('cascade'); // Relación con la tabla workers
            $table->timestamps(); // timestamps() agrega created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('licenses');
    }
};
