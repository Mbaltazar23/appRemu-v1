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
        Schema::create('tmp_liquidation', function (Blueprint $table) {
            $table->id(); // El campo 'id' es autoincrementable por defecto en Laravel
              // El campo 'tuition_id' será un string y se usará como campo adicional
              $table->string('tuition_id'); // El campo 'tuition_id' para asociar con tuición
              // El título y valor del cálculo
              $table->string('title');
              $table->float('value');
              // El campo que indica si está en liquidación o no
              $table->boolean('in_liquidation');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tmp_liquidation');
    }
};
