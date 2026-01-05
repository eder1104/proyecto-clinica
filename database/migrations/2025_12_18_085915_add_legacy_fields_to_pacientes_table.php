<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pacientes', function (Blueprint $table) {
            $table->string('pais_nacimiento_cod')->nullable();
            $table->string('pais_residencia_cod')->default('1');
            $table->string('depto_residencia_cod')->nullable();
            $table->string('municipio_residencia_cod')->nullable();
            $table->string('zona_cod')->nullable();
            $table->foreignId('convenio_id')->nullable()->constrained('convenios');
            $table->foreignId('plan_id')->nullable()->constrained('planes');
            $table->string('rango')->nullable();
            $table->string('tipo_usuario')->nullable();
            $table->string('estado_afiliacion')->nullable();
            $table->boolean('exento_cuota')->default(false);
            $table->text('observaciones')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('pacientes', function (Blueprint $table) {
            $table->dropForeign(['convenio_id']);
            $table->dropForeign(['plan_id']);
            $table->dropColumn([
                'pais_nacimiento_cod',
                'pais_residencia_cod',
                'depto_residencia_cod',
                'municipio_residencia_cod',
                'zona_cod',
                'convenio_id',
                'plan_id',
                'rango',
                'tipo_usuario',
                'estado_afiliacion',
                'exento_cuota',
                'observaciones'
            ]);
        });
    }
};