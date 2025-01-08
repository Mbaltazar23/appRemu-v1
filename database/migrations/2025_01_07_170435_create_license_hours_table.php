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
        Schema::create('license_hours', function (Blueprint $table) {
            $table->unsignedBigInteger('license_id');
            $table->integer('day')->default(0);
            $table->integer('month')->default(0);
            $table->integer('year')->default(0);
            $table->integer('hours')->nullable();
            $table->foreign('license_id')->references('id')->on('licenses')->onDelete('cascade');
            $table->timestamps(); // Laravel manejará los timestamps automáticamente
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('license_hours');
    }
};
