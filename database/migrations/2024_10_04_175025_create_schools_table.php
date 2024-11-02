<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Table schools (colegios con subvencion y dependencia)
     */
    public function up(): void
    {
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->string('rut', 15)->nullable();
            $table->string('name', 50)->nullable();
            $table->string('rbd', 15)->nullable();
            $table->string('address')->nullable();
            $table->string('commune', 30)->nullable();
            $table->string('region', 30)->nullable();
            $table->string('director', 50)->nullable();
            $table->string('rut_director', 15)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->integer('dependency')->nullable();
            $table->integer('grantt')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schools');
    }
};
