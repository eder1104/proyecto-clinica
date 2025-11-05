<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bitacora_auditoria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('users')->onDelete('cascade');
            $table->string('modulo');
            $table->string('accion');
            $table->unsignedBigInteger('registro_afectado')->nullable();
            $table->timestamp('fecha_hora')->useCurrent();
            $table->json('observacion')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bitacora_auditoria');
    }
};
