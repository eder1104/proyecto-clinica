<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pacientes', function (Blueprint $table) {
            $table->id();


            $table->string('nombres');
            $table->string('apellidos');
            $table->string('tipo_documento');
            $table->string('documento')->unique();
            $table->string('telefono');
            $table->string('direccion');
            $table->string('email')->unique();
            $table->date('fecha_nacimiento');
            $table->enum('sexo', ['M', 'F']);

            $table->string('created_by', 100)->nullable();
            $table->string('updated_by', 100)->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }
    public function down(): void
    {

        Schema::dropIfExists('pacientes');
    }
};
