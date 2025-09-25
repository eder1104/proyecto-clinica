<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $roles = ['paciente', 'admisiones'];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name'=>$role,'guard_name'=>'web']);
        }

        $this->call([
            UserSeeder::class,
            PacienteSeeder::class,
        ]);
    }
}
