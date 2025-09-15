<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Paciente;

class PacienteSeeder extends Seeder
{
    public function run(): void
    {
        $usuarios = [
            [
                'nombres' => 'Administrador',
                'apellidos' => 'Sistema',
                'documento' => '1000000001',
                'telefono' => '3000000001',
                'direccion' => 'Cra 1 #1-1',
                'email' => 'admin@example.com',
                'fecha_nacimiento' => '1980-01-01',
                'sexo' => 'M',
            ],
            [
                'nombres' => 'Usuario',
                'apellidos' => 'Admisiones',
                'documento' => '1000000002',
                'telefono' => '3000000002',
                'direccion' => 'Cra 2 #2-2',
                'email' => 'admisiones@example.com',
                'fecha_nacimiento' => '1985-02-02',
                'sexo' => 'F',
            ],
            [
                'nombres' => 'Instructor',
                'apellidos' => 'Formación',
                'documento' => '1000000003',
                'telefono' => '3000000003',
                'direccion' => 'Cra 3 #3-3',
                'email' => 'instructor@example.com',
                'fecha_nacimiento' => '1975-03-03',
                'sexo' => 'M',
            ],
            [
                'nombres' => 'Carlos',
                'apellidos' => 'Pérez Gómez',
                'documento' => '1002003001',
                'telefono' => '3001234567',
                'direccion' => 'Cra 10 #20-30',
                'email' => 'carlos@example.com',
                'fecha_nacimiento' => '1990-05-14',
                'sexo' => 'M',
            ],
            [
                'nombres' => 'Ana',
                'apellidos' => 'Rodríguez López',
                'documento' => '1002003002',
                'telefono' => '3109876543',
                'direccion' => 'Cll 45 #12-34',
                'email' => 'ana@example.com',
                'fecha_nacimiento' => '1992-07-20',
                'sexo' => 'F',
            ],
            [
                'nombres' => 'Julián',
                'apellidos' => 'Martínez Torres',
                'documento' => '1002003003',
                'telefono' => '3201112233',
                'direccion' => 'Av 30 #45-67',
                'email' => 'julian@example.com',
                'fecha_nacimiento' => '1988-02-05',
                'sexo' => 'M',
            ],
            [
                'nombres' => 'Laura',
                'apellidos' => 'Castro Ramírez',
                'documento' => '1002003004',
                'telefono' => '3015556677',
                'direccion' => 'Cra 7 #65-12',
                'email' => 'laura@example.com',
                'fecha_nacimiento' => '1995-10-30',
                'sexo' => 'F',
            ],
            [
                'nombres' => 'Andrés',
                'apellidos' => 'García Peña',
                'documento' => '1002003005',
                'telefono' => '3027778899',
                'direccion' => 'Cll 80 #25-60',
                'email' => 'andres@example.com',
                'fecha_nacimiento' => '1993-03-12',
                'sexo' => 'M',
            ],
            [
                'nombres' => 'Mariana',
                'apellidos' => 'Fernández Soto',
                'documento' => '1002003006',
                'telefono' => '3123334444',
                'direccion' => 'Cra 50 #20-15',
                'email' => 'mariana@example.com',
                'fecha_nacimiento' => '1991-08-09',
                'sexo' => 'F',
            ],
            [
                'nombres' => 'Esteban',
                'apellidos' => 'Ramírez Díaz',
                'documento' => '1002003007',
                'telefono' => '3004445555',
                'direccion' => 'Cll 100 #15-45',
                'email' => 'esteban@example.com',
                'fecha_nacimiento' => '1989-12-22',
                'sexo' => 'M',
            ],
            [
                'nombres' => 'Paula',
                'apellidos' => 'Moreno Jiménez',
                'documento' => '1002003008',
                'telefono' => '3018889999',
                'direccion' => 'Cra 15 #50-20',
                'email' => 'paula@example.com',
                'fecha_nacimiento' => '1996-01-18',
                'sexo' => 'F',
            ],
            [
                'nombres' => 'Felipe',
                'apellidos' => 'Ortega Vargas',
                'documento' => '1002003009',
                'telefono' => '3122223333',
                'direccion' => 'Cll 25 #35-10',
                'email' => 'felipe@example.com',
                'fecha_nacimiento' => '1994-06-27',
                'sexo' => 'M',
            ],
            [
                'nombres' => 'Camila',
                'apellidos' => 'Rojas Cárdenas',
                'documento' => '1002003010',
                'telefono' => '3205556666',
                'direccion' => 'Av 68 #45-12',
                'email' => 'camila@example.com',
                'fecha_nacimiento' => '1997-09-03',
                'sexo' => 'F',
            ],
        ];

        foreach ($usuarios as $data) {
            Paciente::firstOrCreate(
                ['email' => $data['email']],
                $data
            );
        }
    }
}
