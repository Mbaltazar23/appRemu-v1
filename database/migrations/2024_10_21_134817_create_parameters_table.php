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
        Schema::create('parameters', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('school_id')->nullable()->constrained('schools')->onDelete('cascade');
            $table->unsignedBigInteger('worker_id')->nullable(); // Puede ser null y no tiene restricción
            $table->string('description')->nullable();
            $table->string('unit', 10)->nullable();
            $table->string('start_date', 4)->nullable();
            $table->string('end_date', 4)->nullable();
            $table->double('value')->nullable();
//            $table->foreign('worker_id')->references('id')->on('workers')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parameters');
    }
};
