<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alergia_paciente', function (Blueprint $table) {
            $table->foreignId('paciente_id')->constrained('pacientes')->onDelete('cascade');
            $table->foreignId('alergia_id')->constrained('alergias')->onDelete('cascade');

            $table->primary(['paciente_id', 'alergia_id']);
            $table->text('detalles')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alergia_paciente');
    }
};
