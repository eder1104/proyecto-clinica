<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Paciente;

class PacienteSeeder extends Seeder
{
    public function run(): void
    {
        $pacientes = [
            ['nombres' => 'Carlos', 'apellidos' => 'Pérez Gómez', 'documento' => '1002003001', 'telefono' => '3001234567', 'direccion' => 'Cra 10 #20-30', 'email' => 'carlos@example.com', 'fecha_nacimiento' => '1990-05-14', 'sexo' => 'M', 'estado' => 'Soltero', 'profesion' => 'Ingeniero', 'ciudad' => 'Bogotá'],
            ['nombres' => 'Ana', 'apellidos' => 'Rodríguez López', 'documento' => '1002003002', 'telefono' => '3109876543', 'direccion' => 'Cll 45 #12-34', 'email' => 'ana@example.com', 'fecha_nacimiento' => '1992-07-20', 'sexo' => 'F', 'estado' => 'Casada', 'profesion' => 'Abogada', 'ciudad' => 'Medellín'],
            ['nombres' => 'Julián', 'apellidos' => 'Martínez Torres', 'documento' => '1002003003', 'telefono' => '3201112233', 'direccion' => 'Av 30 #45-67', 'email' => 'julian@example.com', 'fecha_nacimiento' => '1988-02-05', 'sexo' => 'M', 'estado' => 'Soltero', 'profesion' => 'Profesor', 'ciudad' => 'Cali'],
            ['nombres' => 'Laura', 'apellidos' => 'Castro Ramírez', 'documento' => '1002003004', 'telefono' => '3015556677', 'direccion' => 'Cra 7 #65-12', 'email' => 'laura@example.com', 'fecha_nacimiento' => '1995-10-30', 'sexo' => 'F', 'estado' => 'Soltera', 'profesion' => 'Diseñadora', 'ciudad' => 'Cartagena'],
            ['nombres' => 'Andrés', 'apellidos' => 'García Peña', 'documento' => '1002003005', 'telefono' => '3027778899', 'direccion' => 'Cll 80 #25-60', 'email' => 'andres@example.com', 'fecha_nacimiento' => '1993-03-12', 'sexo' => 'M', 'estado' => 'Casado', 'profesion' => 'Arquitecto', 'ciudad' => 'Bucaramanga'],
            ['nombres' => 'Mariana', 'apellidos' => 'Fernández Soto', 'documento' => '1002003006', 'telefono' => '3123334444', 'direccion' => 'Cra 50 #20-15', 'email' => 'mariana@example.com', 'fecha_nacimiento' => '1991-08-09', 'sexo' => 'F', 'estado' => 'Unión libre', 'profesion' => 'Psicóloga', 'ciudad' => 'Manizales'],
            ['nombres' => 'Esteban', 'apellidos' => 'Ramírez Díaz', 'documento' => '1002003007', 'telefono' => '3004445555', 'direccion' => 'Cll 100 #15-45', 'email' => 'esteban@example.com', 'fecha_nacimiento' => '1989-12-22', 'sexo' => 'M', 'estado' => 'Casado', 'profesion' => 'Contador', 'ciudad' => 'Pereira'],
            ['nombres' => 'Paula', 'apellidos' => 'Moreno Jiménez', 'documento' => '1002003008', 'telefono' => '3018889999', 'direccion' => 'Cra 15 #50-20', 'email' => 'paula@example.com', 'fecha_nacimiento' => '1996-01-18', 'sexo' => 'F', 'estado' => 'Soltera', 'profesion' => 'Enfermera', 'ciudad' => 'Cúcuta'],
            ['nombres' => 'Felipe', 'apellidos' => 'Ortega Vargas', 'documento' => '1002003009', 'telefono' => '3122223333', 'direccion' => 'Cll 25 #35-10', 'email' => 'felipe@example.com', 'fecha_nacimiento' => '1994-06-27', 'sexo' => 'M', 'estado' => 'Soltero', 'profesion' => 'Médico', 'ciudad' => 'Santa Marta'],
            ['nombres' => 'Camila', 'apellidos' => 'Rojas Cárdenas', 'documento' => '1002003010', 'telefono' => '3205556666', 'direccion' => 'Av 68 #45-12', 'email' => 'camila@example.com', 'fecha_nacimiento' => '1997-09-03', 'sexo' => 'F', 'estado' => 'Soltera', 'profesion' => 'Estudiante', 'ciudad' => 'Neiva'],
        ];


        foreach ($pacientes as $data) {
            Paciente::updateOrCreate(
                ['email' => $data['email']],
                $data
            );
        }
    }
}
