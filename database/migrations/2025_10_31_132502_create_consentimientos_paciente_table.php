<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('consentimientos_paciente', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cita_id')->nullable()->constrained('citas')->onDelete('set null');
            $table->foreignId('paciente_id')->constrained('pacientes')->onDelete('cascade');
            $table->foreignId('plantilla_id')->constrained('plantillas_consentimiento')->onDelete('cascade');

            $table->string('nombre_firmante');
            $table->date('fecha_firma')->default(now());

            $table->string('firma')->nullable();

            $table->unsignedBigInteger('doctor_id')->nullable();

            $table->boolean('activo')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consentimientos_paciente');
    }
};
