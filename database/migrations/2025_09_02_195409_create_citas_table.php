<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('citas', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('pacientes_id')
                  ->constrained('pacientes')
                  ->restrictOnDelete();
            
            $table->dateTime('fecha_cita');
            $table->text('motivo')->nullable();
            
            $table->foreignId('created_by')
                  ->constrained('users')
                  ->restrictOnDelete();
            
            $table->foreignId('updated_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('citas');
    }
};
