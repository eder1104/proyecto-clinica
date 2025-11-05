<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consentimientos_paciente', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained('pacientes')->onDelete('cascade');
            $table->foreignId('doctor_id')->constrained('doctores')->onDelete('cascade');
            $table->foreignId('plantilla_id')->constrained('plantillas_consentimiento')->onDelete('cascade');
            $table->string('nombre_firmante')->nullable();
            $table->string('firma')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consentimientos_paciente');
    }
};
