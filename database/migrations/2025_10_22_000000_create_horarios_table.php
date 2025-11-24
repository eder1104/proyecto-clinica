<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHorariosTable extends Migration
{
    public function up()
    {
        Schema::create('plantillas_horario', function (Blueprint $table) {
            $table->id();

            $table->date('fecha')->nullable();

            $table->time('hora_inicio');
            $table->time('hora_fin');

            $table->boolean('activo')->default(true);

            $table->foreignId('doctor_id')
                ->nullable()
                ->constrained('doctores')
                ->onDelete('cascade');

            $table->timestamps();

            $table->index(['doctor_id', 'fecha']);
            $table->unique(['doctor_id', 'hora_inicio', 'hora_fin']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('horarios');
    }
}
