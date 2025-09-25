<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $permisoUsuarios = Permission::firstOrCreate([
            'name' => 'gestionar usuarios',
            'guard_name' => 'web'
        ]);

        $permisoReportes = Permission::firstOrCreate([
            'name' => 'ver reportes',
            'guard_name' => 'web'
        ]);

        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $callcenterRole = Role::firstOrCreate(['name' => 'callcenter', 'guard_name' => 'web']);
        $admisionesRole = Role::firstOrCreate(['name' => 'admisiones', 'guard_name' => 'web']);

        $adminRole->givePermissionTo([$permisoUsuarios, $permisoReportes]);
        $callcenterRole->givePermissionTo([$permisoReportes]);
        $admisionesRole->givePermissionTo([$permisoReportes]);

        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'nombres' => 'Administrador',
                'apellidos' => 'Sistema',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'status' => 'activo',
            ]
        );
        $admin->syncRoles([$adminRole]);

        $callcenter = User::updateOrCreate(
            ['email' => 'callcenter@example.com'],
            [
                'nombres' => 'Agente',
                'apellidos' => 'Callcenter',
                'password' => Hash::make('password123'),
                'role' => 'callcenter',
                'status' => 'activo',
            ]
        );
        $callcenter->syncRoles([$callcenterRole]);

        $admisiones = User::updateOrCreate(
            ['email' => 'admisiones@example.com'],
            [
                'nombres' => 'Agente',
                'apellidos' => 'Admisiones',
                'password' => Hash::make('password123'),
                'role' => 'admisiones',
                'status' => 'activo',
            ]
        );
        $admisiones->syncRoles([$admisionesRole]);
    }
}
