<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historias_clinicas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('diagnostico_principal_id')
                ->nullable()
                ->constrained('diagnosticos_oftalmologicos')
                ->onDelete('set null');

            $table->foreignId('paciente_id')
                ->constrained('pacientes')
                ->onDelete('cascade');

            $table->text('motivo_consulta')->nullable();
            $table->json('antecedentes')->nullable();
            $table->json('signos_vitales')->nullable();

            $table->text('conducta')->nullable();

            $table->string('created_by');
            $table->string('updated_by')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historias_clinicas');
    }
};
