<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared("
            DROP PROCEDURE IF EXISTS sp_buscar_pacientes;
            CREATE PROCEDURE sp_buscar_pacientes (IN termino VARCHAR(100))
            BEGIN
                SELECT * FROM pacientes
                WHERE nombre LIKE CONCAT('%', termino, '%');
            END
        ");
    }

    public function down(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_buscar_pacientes");
    }
};