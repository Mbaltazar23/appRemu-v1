<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Tabla: licenses (licencia)
     */
    public function up(): void
    {
        Schema::create('licenses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('worker_id')->nullable();
            $table->date('issue_date')->nullable();
            $table->string('reason', 50)->nullable();
            $table->integer('days')->nullable();
            $table->string('institution', 30)->nullable();
            $table->string('receipt_number', 10)->nullable();
            $table->date('receipt_date')->nullable();
            $table->date('processing_date')->nullable();
            $table->string('responsible_person', 50)->nullable();
            $table->text('dayslicense')->nullable();
            $table->foreign('worker_id')->references('id')->on('workers');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('licenses');
    }
};
