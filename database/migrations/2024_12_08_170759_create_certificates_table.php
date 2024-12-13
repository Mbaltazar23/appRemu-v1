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
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('worker_id');
            $table->unsignedBigInteger('school_id'); // Aquí agregamos el campo school_id
            $table->integer('year');
            $table->binary('description')->nullable(); // Si el campo description es opcional
            $table->timestamps();
            // Establecer la clave foránea que referencia a la tabla 'workers'
            $table->foreign('worker_id')->references('id')->on('workers')->onDelete('cascade');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade'); // Relación con la tabla schools
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
