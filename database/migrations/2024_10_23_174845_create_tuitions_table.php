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
        Schema::create('tuitions', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('tuition_id')->nullable(); // Asegúrate de que sea único
            $table->char('type', 1)->nullable();
            $table->string('description')->nullable();
            $table->tinyInteger('in_liquidation')->nullable();
            $table->tinyInteger('editable')->nullable();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });   
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tuitions');
    }
};
