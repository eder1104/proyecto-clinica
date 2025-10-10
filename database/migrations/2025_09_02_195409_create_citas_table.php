<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('citas', function (Blueprint $table) {
            $table->id();
            $table->string('numero_fuente')->nullable();
            $table->date('fecha');
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->text('motivo_consulta')->nullable();

            $table->string('tension_arterial')->nullable();
            $table->string('frecuencia_cardiaca')->nullable();
            $table->string('frecuencia_respiratoria')->nullable();
            $table->string('temperatura')->nullable();
            $table->string('saturacion')->nullable();
            $table->string('peso')->nullable();
            $table->text('examen_fisico')->nullable();
            $table->text('diagnostico')->nullable();

            $table->string('estado')->default('programada');
            $table->foreignId('paciente_id')->constrained('pacientes')->restrictOnDelete();
            
            $table->foreignId('admisiones_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('cancel_reason')->nullable();
            $table->string('pdf_path')->nullable();
            $table->softDeletes();
            $table->timestamps();
            
        });
    }

    public function down(): void {
        Schema::dropIfExists('citas');
    }
};
