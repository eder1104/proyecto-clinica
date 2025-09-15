<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('citas', function (Blueprint $table) {
            $table->unsignedBigInteger('admisiones_id')->nullable()->after('paciente_id');
            $table->foreign('admisiones_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('citas', function (Blueprint $table) {
            $table->dropForeign(['admisiones_id']);
            $table->dropColumn('admisiones_id');
        });
    }
};
