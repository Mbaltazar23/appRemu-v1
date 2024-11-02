<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Tabla: workers (trabajadores con tipo_trabajador, carga horaria, tipo_trabajador_funcion y dependencia)
     */
    public function up(): void
    {
        Schema::create('workers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('insurance_AFP')->nullable();
            $table->unsignedBigInteger('insurance_ISAPRE')->nullable();
            $table->unsignedBigInteger('school_id');
            $table->string('rut', 15);
            $table->string('name', 50);
            $table->string('last_name', 50);
            $table->date('birth_date');
            $table->string('address', 100);
            $table->string('commune', 30);
            $table->string('region', 20);
            $table->string('phone', 20);
            $table->string('marital_status', 20);
            $table->string('nationality');
            $table->unsignedInteger('worker_type');
            $table->text('load_hourly_work')->nullable();
            $table->unsignedBigInteger('worker_titular')->nullable(); // Campo añadido
            $table->unsignedInteger('function_worker');
            $table->date('settlement_date')->nullable();
            $table->foreign('insurance_AFP')->references('id')->on('insurances')->onDelete('set null');
            $table->foreign('insurance_ISAPRE')->references('id')->on('insurances')->onDelete('set null');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workers');
    }
};
