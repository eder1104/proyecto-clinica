<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('consentimientos_paciente', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cita_id')
                ->constrained('citas')
                ->onDelete('cascade');

            $table->foreignId('paciente_id')
                ->constrained('pacientes')
                ->onDelete('cascade');

            $table->string('nombre_paciente')->nullable();

            $table->unsignedBigInteger('doctor_id')->nullable();
            $table->string('nombre_doctor')->nullable();

            $table->foreignId('plantilla_id')
                ->nullable()
                ->constrained('plantillas_consentimiento')
                ->onDelete('set null');

            $table->string('nombre_firmante')->nullable();
            $table->date('fecha_firma')->default(now());
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
