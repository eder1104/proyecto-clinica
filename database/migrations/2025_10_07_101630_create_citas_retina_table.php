<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('citas_retina', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cita_id')->constrained('citas')->cascadeOnDelete();
            $table->string('diagnostico')->nullable();
            $table->string('tratamiento')->nullable();
            $table->text('observaciones')->nullable();
            $table->string('imagen_ojo_izquierdo')->nullable();
            $table->string('imagen_ojo_derecho')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('citas_retina');
    }
};
