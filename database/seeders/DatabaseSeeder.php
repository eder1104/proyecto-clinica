<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Paciente;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $permisoUsuarios = Permission::firstOrCreate(['name' => 'gestionar usuarios', 'guard_name' => 'web']);
        $permisoReportes = Permission::firstOrCreate(['name' => 'ver reportes', 'guard_name' => 'web']);

        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->givePermissionTo([$permisoUsuarios, $permisoReportes]);

        $instructor = Role::firstOrCreate(['name' => 'instructor', 'guard_name' => 'web']);
        $instructor->givePermissionTo([$permisoReportes]);

        $pacienteRole = Role::firstOrCreate(['name' => 'paciente', 'guard_name' => 'web']);
        $admisionesRole = Role::firstOrCreate(['name' => 'admisiones', 'guard_name' => 'web']);

        $user = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'nombres' => 'Administrador',
                'apellidos' => 'Sistema',
                'password' => bcrypt('password123'),
                'role' => 'admin',
            ]
        );
        $user->syncRoles(['admin']);

        $usuarioAdmision = User::updateOrCreate(
            ['email' => 'admisiones@example.com'],
            [
                'nombres' => 'Usuario',
                'apellidos' => 'Admisiones',
                'password' => bcrypt('password123'),
                'role' => 'admisiones', 
            ]
        );
        $usuarioAdmision->syncRoles(['admisiones']);

      
        $pacientes = [
            ['nombres' => 'Carlos', 'apellidos' => 'Pérez', 'email' => 'carlos@example.com'],
            ['nombres' => 'Ana', 'apellidos' => 'Rodríguez', 'email' => 'ana@example.com'],
            ['nombres' => 'Julián', 'apellidos' => 'Martínez', 'email' => 'julian@example.com'],
            ['nombres' => 'Laura', 'apellidos' => 'Castro', 'email' => 'laura@example.com'],
            ['nombres' => 'Andrés', 'apellidos' => 'García', 'email' => 'andres@example.com'],
            ['nombres' => 'Mariana', 'apellidos' => 'Fernández', 'email' => 'mariana@example.com'],
            ['nombres' => 'Esteban', 'apellidos' => 'Ramírez', 'email' => 'esteban@example.com'],
            ['nombres' => 'Paula', 'apellidos' => 'Moreno', 'email' => 'paula@example.com'],
            ['nombres' => 'Felipe', 'apellidos' => 'Ortega', 'email' => 'felipe@example.com'],
            ['nombres' => 'Camila', 'apellidos' => 'Rojas', 'email' => 'camila@example.com'],
        ];

        foreach ($pacientes as $p) {
            $nuevoUser = User::updateOrCreate(
                ['email' => $p['email']],
                [
                    'nombres' => $p['nombres'],
                    'apellidos' => $p['apellidos'],
                    'password' => bcrypt('password123'),
                    'role' => 'paciente', 
                ]
            );
            $nuevoUser->syncRoles(['paciente']);

            Paciente::updateOrCreate(
                ['email' => $p['email']],
                [
                    'nombres' => $p['nombres'],
                    'apellidos' => $p['apellidos'],
                    'documento' => rand(10000000, 99999999),
                    'telefono' => '300' . rand(1000000, 9999999),
                    'direccion' => 'Calle ' . rand(1, 50) . ' # ' . rand(1, 20) . '-' . rand(1, 50),
                    'fecha_nacimiento' => now()->subYears(rand(18, 60))->format('Y-m-d'),
                    'sexo' => rand(0, 1) ? 'M' : 'F',
                    'created_by' => $user->id,
                ]
            );
        }
    }
}
