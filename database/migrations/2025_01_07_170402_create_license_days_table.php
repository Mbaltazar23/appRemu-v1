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
        Schema::create('license_days', function (Blueprint $table) {
            $table->unsignedBigInteger('license_id');
            $table->integer('day')->default(0);
            $table->integer('month')->default(0);
            $table->integer('year')->default(0);
            $table->boolean('exists')->nullable();  // Puede ser NULL o 0/1

            $table->foreign('license_id')->references('id')->on('licenses')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('license_days');
    }
};
