<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('procedimientos_clinicos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->timestamps();
        });

        Schema::create('diagnostico_procedimiento', function (Blueprint $table) {
            $table->id();

            $table->foreignId('diagnostico_id')
                ->constrained('diagnosticos_oftalmologicos')
                ->onDelete('cascade');

            $table->foreignId('procedimiento_id')
                ->constrained('procedimientos_clinicos')
                ->onDelete('cascade');

            $table->timestamps();

            $table->unique(['diagnostico_id', 'procedimiento_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diagnostico_procedimiento');
        Schema::dropIfExists('procedimientos_clinicos');
    }
};
