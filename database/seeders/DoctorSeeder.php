<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\doctores;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;

class DoctorSeeder extends Seeder
{
    public function run(): void
    {
        $doctorRole = Role::where('name', 'doctor')->first();

        $doctores = [
            [
                'nombres'        => 'Laura',
                'apellidos'      => 'González Pérez',
                'documento'      => '1012456789',
                'telefono'       => '3004567890',
                'email'          => 'laura.gonzalez@example.com',
                'especializacion'=> 'Optometría',
            ],
            [
                'nombres'        => 'Carlos',
                'apellidos'      => 'Ramírez Soto',
                'documento'      => '1012456790',
                'telefono'       => '3009876543',
                'email'          => 'carlos.ramirez@example.com',
                'especializacion'=> 'Oftalmología',
            ],
            [
                'nombres'        => 'Ana',
                'apellidos'      => 'Martínez López',
                'documento'      => '1012456791',
                'telefono'       => '3124567890',
                'email'          => 'ana.martinez@example.com',
                'especializacion'=> 'Optometría Pediátrica',
            ],
            [
                'nombres'        => 'Jorge',
                'apellidos'      => 'Castro Ruiz',
                'documento'      => '1012456792',
                'telefono'       => '3102345678',
                'email'          => 'jorge.castro@example.com',
                'especializacion'=> 'Cirugía Ocular',
            ],
            [
                'nombres'        => 'Diana',
                'apellidos'      => 'Fernández Mejía',
                'documento'      => '1012456793',
                'telefono'       => '3019876543',
                'email'          => 'diana.fernandez@example.com',
                'especializacion'=> 'Baja Visión',
            ],
        ];

        foreach ($doctores as $doctorData) {
            $usuario = User::updateOrCreate(
                ['email' => $doctorData['email']],
                [
                    'nombres'    => $doctorData['nombres'],
                    'apellidos'  => $doctorData['apellidos'],
                    'password'   => Hash::make('password123'),
                    'role'       => 'doctor',
                    'status'     => 'activo',
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                ]
            );

            $usuario->syncRoles([$doctorRole]);

            doctores::updateOrCreate(
                ['user_id' => $usuario->id],
                [
                    'documento'       => $doctorData['documento'],
                    'telefono'        => $doctorData['telefono'],
                    'especializacion' => $doctorData['especializacion'],
                    'estado'          => 'activo',
                ]
            );
        }
    }
}
