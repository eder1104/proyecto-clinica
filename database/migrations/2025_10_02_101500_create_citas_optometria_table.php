<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('optometria', function (Blueprint $table) {
            $table->id();

            $table->string('optometra')->nullable();
            $table->boolean('consulta_completa')->default(false);

            $table->text('anamnesis')->nullable();
            $table->string('alternativa_deseada')->nullable();
            $table->string('dominancia_ocular')->nullable();

            $table->string('av_lejos_od')->nullable();
            $table->string('av_intermedia_od')->nullable();
            $table->string('av_cerca_od')->nullable();
            $table->string('av_lejos_oi')->nullable();
            $table->string('av_intermedia_oi')->nullable();
            $table->string('av_cerca_oi')->nullable();

            $table->text('observaciones_internas')->nullable();
            $table->text('observaciones_optometria')->nullable();

            $table->string('tipo_lente')->nullable();
            $table->text('especificaciones_lente')->nullable();
            $table->string('vigencia_formula')->nullable();
            $table->string('filtro')->nullable();
            $table->string('tiempo_formulacion')->nullable();
            $table->string('distancia_pupilar')->nullable();
            $table->unsignedInteger('cantidad')->nullable();

            $table->string('diagnostico_principal')->nullable();
            $table->text('otros_diagnosticos')->nullable();
            $table->text('datos_adicionales')->nullable();
            $table->string('finalidad_consulta')->nullable();
            $table->string('causa_motivo_atencion')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('citas_optometria');
    }
};
