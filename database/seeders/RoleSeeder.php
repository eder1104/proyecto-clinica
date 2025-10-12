<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $permisoUsuarios = Permission::firstOrCreate(['name' => 'gestionar usuarios', 'guard_name' => 'web']);

        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->givePermissionTo([$permisoUsuarios,]);

        $callcenter = Role::firstOrCreate(['name' => 'callcenter', 'guard_name' => 'web']);

        $admisiones = Role::firstOrCreate(['name' => 'admisiones', 'guard_name' => 'web']);

        $paciente = Role::firstOrCreate(['name' => 'paciente', 'guard_name' => 'web']);
    }
}
