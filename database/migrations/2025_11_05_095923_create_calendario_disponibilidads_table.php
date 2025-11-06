<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calendario_disponibilidad', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained('doctores')->onDelete('cascade');
            $table->date('fecha');
            $table->enum('estado', ['Disponible', 'Parcial', 'Bloqueado']);
            $table->timestamps();

            $table->unique(['doctor_id', 'fecha']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calendario_disponibilidad');
    }
};
