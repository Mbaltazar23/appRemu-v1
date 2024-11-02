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
        Schema::table('schools', function (Blueprint $table) {
            // Agregar la columna para la relación
            $table->foreignId('sustainer_id')->nullable()->constrained()->onDelete('set null');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            // Eliminar la columna y la clave foránea
            $table->dropForeign(['sustainer_id']);
            $table->dropColumn('sustainer_id');
        });
    }
};
