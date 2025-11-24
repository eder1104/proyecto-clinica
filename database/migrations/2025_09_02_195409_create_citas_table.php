<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tipos_citas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->timestamps();
        });

        DB::table('tipos_citas')->insert([
            ['id' => 1, 'nombre' => 'Optometría', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'nombre' => 'Exámenes',   'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'nombre' => 'Retina',      'created_at' => now(), 'updated_at' => now()],
        ]);

        Schema::create('citas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('doctor_id')
                ->nullable()
                ->constrained('doctores')
                ->nullOnDelete();

            $table->date('fecha');
            $table->time('hora_inicio');
            $table->time('hora_fin');

            $table->string('motivo_consulta')->nullable();
            $table->string('tipo_examen')->nullable();

            $table->string('estado')->default('programada');

            $table->foreignId('paciente_id')
                ->constrained('pacientes')
                ->restrictOnDelete();

            $table->foreignId('tipo_cita_id')
                ->nullable()
                ->constrained('tipos_citas')
                ->nullOnDelete();

            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('cancelled_by')->nullable();
            $table->text('cancel_reason')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('citas');
        Schema::dropIfExists('tipos_citas');
    }
};
