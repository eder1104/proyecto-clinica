<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlantillasHorarioTable extends Migration
{
    public function up()
    {
        Schema::create('plantillas_horario', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('dia_semana')->comment('0=Dom,1=Lun,...6=Sáb');
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->unique(['dia_semana', 'hora_inicio', 'hora_fin']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('plantillas_horario');
    }
}
