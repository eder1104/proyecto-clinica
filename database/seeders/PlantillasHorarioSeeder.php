<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlantillasHorarioSeeder extends Seeder
{
    public function run()
    {
        $doctores = DB::table('doctores')->pluck('id');

        foreach ($doctores as $doctorId) {
            DB::table('plantillas_horario')->updateOrInsert(
                [
                    'doctor_id' => $doctorId
                ],
                [
                    'fecha' => null,
                    'hora_inicio' => '08:00:00',
                    'hora_fin' => '18:00:00',
                    'activo' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
