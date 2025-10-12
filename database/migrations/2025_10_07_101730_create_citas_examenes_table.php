<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('examenes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cita_id');
            $table->string('profesional');
            $table->string('tipoExamen');
            $table->enum('ojo', ['Ojo Derecho', 'Ojo Izquierdo']);
            $table->string('archivo')->nullable();
            $table->text('observaciones')->nullable();
            $table->string('codigoCiex')->nullable();
            $table->text('diagnostico')->nullable();
            $table->enum('ojoDiag', ['Ojo Derecho', 'Ojo Izquierdo'])->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('examenes');
    }
};
