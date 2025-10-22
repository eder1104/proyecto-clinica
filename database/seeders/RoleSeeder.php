<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Permisos base
        $permisoUsuarios = Permission::firstOrCreate([
            'name' => 'gestionar usuarios',
            'guard_name' => 'web'
        ]);

        $permisoCitas = Permission::firstOrCreate([
            'name' => 'gestionar citas',
            'guard_name' => 'web'
        ]);

        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->givePermissionTo([$permisoUsuarios, $permisoCitas]);

        $callcenter = Role::firstOrCreate(['name' => 'callcenter', 'guard_name' => 'web']);
        $callcenter->givePermissionTo([$permisoCitas]);

        $admisiones = Role::firstOrCreate(['name' => 'admisiones', 'guard_name' => 'web']);
        $admisiones->givePermissionTo([$permisoCitas]);

        $doctor = Role::firstOrCreate(['name' => 'doctor', 'guard_name' => 'web']);
        $doctor->givePermissionTo([$permisoCitas]);

        $paciente = Role::firstOrCreate(['name' => 'paciente', 'guard_name' => 'web']);
    }
}
