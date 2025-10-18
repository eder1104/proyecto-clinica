<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pre_examenes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cita_id')->constrained('citas')->onDelete('cascade');
            $table->string('vision_lejana_od')->nullable();
            $table->string('vision_lejana_oi')->nullable();
            $table->string('vision_cercana_od')->nullable();
            $table->string('vision_cercana_oi')->nullable();
            $table->string('test_color')->nullable();
            $table->string('test_profundidad')->nullable();
            $table->string('motilidad_ocular')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pre_examenes');
    }
};
