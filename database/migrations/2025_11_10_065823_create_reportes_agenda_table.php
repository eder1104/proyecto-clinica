<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reportes_agenda', function (Blueprint $table) {
            $table->id();
            $table->date('fecha'); // Día del reporte
            $table->integer('total_horarios')->default(0);
            $table->integer('horarios_ocupados')->default(0);
            $table->integer('horarios_bloqueados')->default(0);
            $table->integer('citas_programadas')->default(0);
            $table->integer('citas_canceladas')->default(0);
            $table->integer('citas_atendidas')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reportes_agenda');
    }
};
