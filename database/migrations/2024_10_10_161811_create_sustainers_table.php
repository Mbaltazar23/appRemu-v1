<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * -- Tabla: sustainers (sostenedor)
     */
    public function up(): void
    {
        Schema::create('sustainers', function (Blueprint $table) {
            $table->id();
            $table->string('rut', 15)->nullable();
            $table->string('business_name', 50)->nullable();
            $table->string('address')->nullable();
            $table->string('commune', 30)->nullable();
            $table->string('region', 40)->nullable();
            $table->string('legal_nature', 50)->nullable();
            $table->string('legal_representative', 50)->nullable();
            $table->string('rut_legal_representative', 15)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sustainers');
    }
};
