<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        $procedure = "
        DROP PROCEDURE IF EXISTS sp_registrar_cita;
        
        CREATE PROCEDURE sp_registrar_cita(
            IN p_paciente_id BIGINT,
            IN p_doctor_id BIGINT,
            IN p_fecha DATE,
            IN p_hora_inicio TIME
        )
        BEGIN
            DECLARE v_medico_ocupado INT;
            DECLARE v_paciente_ocupado INT;

            DECLARE EXIT HANDLER FOR SQLEXCEPTION
            BEGIN
                ROLLBACK;
                RESIGNAL;
            END;

            START TRANSACTION;

            -- 1. Verificar si el médico está ocupado
            -- Usamos 'fecha' y 'hora_inicio' según tu Modelo
            SELECT COUNT(*) INTO v_medico_ocupado
            FROM citas
            WHERE doctor_id = p_doctor_id 
            AND fecha = p_fecha
            AND hora_inicio = p_hora_inicio
            AND estado != 'cancelada';

            IF v_medico_ocupado > 0 THEN
                SIGNAL SQLSTATE '45000' 
                SET MESSAGE_TEXT = 'El médico no está disponible en ese horario';
            END IF;

            -- 2. Verificar si el paciente ya tiene cita
            SELECT COUNT(*) INTO v_paciente_ocupado
            FROM citas
            WHERE paciente_id = p_paciente_id 
            AND fecha = p_fecha
            AND hora_inicio = p_hora_inicio
            AND estado != 'cancelada';

            IF v_paciente_ocupado > 0 THEN
                SIGNAL SQLSTATE '45000' 
                SET MESSAGE_TEXT = 'El paciente ya tiene una cita en ese horario';
            END IF;

            -- 3. Insertar la cita
            -- Asignamos 'hora_inicio' y calculamos 'hora_fin' (ej: +30 min) o la dejamos igual si es null
            INSERT INTO citas (
                paciente_id, 
                doctor_id, 
                fecha, 
                hora_inicio, 
                hora_fin, 
                estado, 
                created_at, 
                updated_at
            )
            VALUES (
                p_paciente_id, 
                p_doctor_id, 
                p_fecha, 
                p_hora_inicio, 
                ADDTIME(p_hora_inicio, '00:30:00'), -- Calculo automático de fin
                'programada', 
                NOW(), 
                NOW()
            );

            COMMIT;
        END;
        ";

        DB::unprepared($procedure);
    }

    public function down()
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_registrar_cita");
    }
};