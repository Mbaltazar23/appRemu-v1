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
        Schema::create('license_details', function (Blueprint $table) {
            $table->id(); // ID de la relación de horas (auto-incrementable)
            $table->unsignedBigInteger('license_id'); // ID de la licencia (relación con 'licenses')
            $table->integer('day')->default(0); // Día
            $table->integer('month')->default(0); // Mes
            $table->integer('year')->default(0); // Año
            $table->integer('hours')->default(0); // Horas de la licencia
            $table->boolean('exists')->nullable(); // Campo que indica si el día está activo (1 = sí, 0 = no)
            $table->foreign('license_id')->references('id')->on('licenses')->onDelete('cascade'); 
            $table->timestamps(); // timestamps() agrega created_at y updated_at
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('licenses_details');
    }
};
