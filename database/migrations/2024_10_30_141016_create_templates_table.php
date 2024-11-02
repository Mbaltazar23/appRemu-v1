<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('templates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id'); // Cambiado a unsignedBigInteger
            $table->integer('type');
            $table->integer('position');
            $table->char('code', 3)->nullable();
            $table->string('tuition_id')->nullable();
            $table->boolean('ignore_zero')->nullable(); // Cambiado a boolean
            $table->boolean('parentheses')->nullable(); // Cambiado a boolean
            $table->timestamps();
            // Foreign key
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->foreign('tuition_id')->references('tuition_id')->on('tuitions')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('templates');
    }
};
