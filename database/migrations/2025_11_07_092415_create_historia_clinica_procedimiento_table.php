<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historia_clinica_procedimiento', function (Blueprint $table) {

            $table->foreignId('historia_clinica_id')->constrained('historias_clinicas')->onDelete('cascade');
            $table->foreignId('procedimiento_id')->constrained('procedimientos_oftalmologicos')->onDelete('cascade');
            $table->foreignId('cita_id')->constrained('citas')->onDelete('cascade');

            $table->primary(['historia_clinica_id', 'procedimiento_id', 'cita_id']);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historia_clinica_procedimiento');
    }
};
