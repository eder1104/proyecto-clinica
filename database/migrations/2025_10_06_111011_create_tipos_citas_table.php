<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipos_citas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->timestamps();
        });

        DB::table('tipos_citas')->insert([
            [
                'id' => 1,
                'nombre' => 'Optometría',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'nombre' => 'Exámenes',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        Schema::table('citas', function (Blueprint $table) {
            $table->foreignId('tipo_cita_id')
                ->nullable()
                ->constrained('tipos_citas')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('citas', function (Blueprint $table) {
            $table->dropForeign(['tipo_cita_id']);
            $table->dropColumn('tipo_cita_id');
        });

        Schema::dropIfExists('tipos_citas');
    }
};
