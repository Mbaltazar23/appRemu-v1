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
        Schema::create('operations', function (Blueprint $table) {
            $table->id();
            $table->string('tuition_id')->nullable();
            $table->foreignId('school_id')->nullable()->constrained('schools')->onDelete('set null');
            $table->integer('worker_type')->nullable();
            $table->binary('operation')->nullable(); 
            $table->string('limit_unit')->nullable();
            $table->double('min_limit')->nullable();
            $table->double('max_limit')->nullable();
            $table->double('max_value')->default(0);
            $table->string('application', 12)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operations');
    }
};
