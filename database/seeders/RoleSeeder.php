<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Permisos
        $permisoUsuarios = Permission::firstOrCreate(['name' => 'gestionar usuarios', 'guard_name' => 'web']);
        $permisoReportes = Permission::firstOrCreate(['name' => 'ver reportes', 'guard_name' => 'web']);

        // Roles
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->givePermissionTo([$permisoUsuarios, $permisoReportes]);

        $callcenter = Role::firstOrCreate(['name' => 'callcenter', 'guard_name' => 'web']);
        $callcenter->givePermissionTo([$permisoReportes]);

        $admisiones = Role::firstOrCreate(['name' => 'admisiones', 'guard_name' => 'web']);
        $admisiones->givePermissionTo([$permisoReportes]);

        $paciente = Role::firstOrCreate(['name' => 'paciente', 'guard_name' => 'web']);
    }
}
