<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $procedure = "
            DROP PROCEDURE IF EXISTS sp_crear_paciente_legacy;
            
            CREATE PROCEDURE sp_crear_paciente_legacy(
                IN p_tipo_documento VARCHAR(20),
                IN p_documento VARCHAR(20),
                IN p_nombres VARCHAR(100),
                IN p_apellidos VARCHAR(100),
                IN p_fecha_nacimiento DATE,
                IN p_sexo CHAR(1),
                IN p_pais_nacimiento_cod VARCHAR(10),
                IN p_telefono VARCHAR(20),
                IN p_pais_residencia_cod VARCHAR(10),
                IN p_depto_residencia_cod VARCHAR(100),
                IN p_municipio_residencia_cod VARCHAR(100),
                IN p_zona_cod VARCHAR(20),
                IN p_direccion VARCHAR(255),
                IN p_email VARCHAR(100),
                IN p_convenio_id BIGINT,
                IN p_plan_id BIGINT,
                IN p_rango VARCHAR(10),
                IN p_tipo_usuario VARCHAR(20),
                IN p_estado_afiliacion VARCHAR(20),
                IN p_exento_cuota TINYINT,
                IN p_observaciones TEXT,
                IN p_created_by VARCHAR(100)
            )
            BEGIN
                INSERT INTO pacientes (
                    tipo_documento, documento, nombres, apellidos, fecha_nacimiento, sexo,
                    pais_nacimiento_cod, telefono, pais_residencia_cod, 
                    depto_residencia_cod, municipio_residencia_cod, zona_cod,
                    direccion, email,
                    convenio_id, plan_id, rango, tipo_usuario, 
                    estado_afiliacion, exento_cuota,
                    observaciones, created_by,
                    created_at, updated_at
                ) VALUES (
                    p_tipo_documento, p_documento, p_nombres, p_apellidos, p_fecha_nacimiento, p_sexo,
                    p_pais_nacimiento_cod, p_telefono, p_pais_residencia_cod, 
                    p_depto_residencia_cod, p_municipio_residencia_cod, p_zona_cod,
                    p_direccion, p_email,
                    p_convenio_id, p_plan_id, p_rango, p_tipo_usuario, 
                    p_estado_afiliacion, p_exento_cuota,
                    p_observaciones, p_created_by,
                    NOW(), NOW()
                );
            END;
        ";
        DB::unprepared($procedure);
    }

    public function down(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_crear_paciente_legacy");
    }
};