<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nombres');
            $table->string('apellidos');
            $table->string('email')->unique();
            $table->string('password');

            $table->string('role', 50)->default('callcenter');

            $table->enum('status', ['activo', 'inactivo'])
                ->default('activo');

            $table->rememberToken();
            $table->timestamps();

            $table->string('created_by', 255)->nullable();
            $table->string('updated_by', 255)->nullable();
            $table->string('cancelled_by', 255)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
