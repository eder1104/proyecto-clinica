<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('citas', function (Blueprint $table) {
            $table->id();
            $table->string('numero_fuente')->nullable();
            $table->date('fecha');
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->text('mensaje')->nullable();
            $table->enum('estado', [
                'programada',
                'modificada',
                'cancelada',
                'asistida',
                'no_asistida'
            ])->default('programada');

            // Relaciones
            $table->foreignId('paciente_id')
                  ->constrained('pacientes')
                  ->cascadeOnDelete();

            $table->foreignId('admisiones_id') // 👈 aquí está
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->foreignId('created_by')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->foreignId('updated_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->foreignId('cancelled_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->text('cancel_reason')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};
