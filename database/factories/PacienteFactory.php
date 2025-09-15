<?php

namespace Database\Factories;

use App\Models\Paciente;
use Illuminate\Database\Eloquent\Factories\Factory;

class PacienteFactory extends Factory
{
    protected $model = Paciente::class;

    public function definition(): array
    {
        return [
            'nombres'          => $this->faker->firstName,
            'apellidos'        => $this->faker->lastName,
            'documento'        => $this->faker->unique()->numerify('#########'),
            'telefono'         => $this->faker->phoneNumber,
            'direccion'        => $this->faker->address,
            'email'            => $this->faker->unique()->safeEmail,
            'fecha_nacimiento' => $this->faker->date('Y-m-d', '2005-01-01'),
            'sexo'             => $this->faker->randomElement(['M', 'F']),
            'created_by'       => 1,
            'updated_by'       => 1,
        ];
    }
}
