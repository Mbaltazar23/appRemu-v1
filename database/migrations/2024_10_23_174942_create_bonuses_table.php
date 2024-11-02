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
        Schema::create('bonuses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('tuition_id')->unique(); // Asegúrate de que sea único
            $table->foreignId('school_id')->nullable()->constrained('schools')->onDelete('set null');
            $table->foreignId('worker_id')->nullable()->constrained('workers')->onDelete('set null');
            $table->char('taxable', 1)->nullable();
            $table->char('is_bonus', 1)->nullable();
            $table->char('application', 1)->nullable();
            $table->integer('type')->nullable();
            $table->double('factor')->nullable();
            $table->integer('imputable')->nullable();
            $table->timestamps();
        }); 
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bonuses');
    }
};
