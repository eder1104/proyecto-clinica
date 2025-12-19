<?php

namespace Database\Seeders;

use App\Models\PlantillaConsentimiento;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $roles = ['admin', 'admisiones', 'callcenter', 'doctor', 'paciente'];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        $this->call([
            UserSeeder::class,
            ConveniosSeeder::class,
            DoctorSeeder::class,
            PacienteSeeder::class,
            PlantillaConsentimientoSeeder::class,
            CatalogosOftalmologicosSeeder::class,
            PlantillasHorarioSeeder::class
        ]);
    }
}
