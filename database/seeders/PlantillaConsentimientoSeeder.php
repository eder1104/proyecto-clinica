<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PlantillaConsentimiento;

class PlantillaConsentimientoSeeder extends Seeder
{
    public function run(): void
    {
        PlantillaConsentimiento::insert([
            [
                'version' => 1,
                'titulo' => 'Inyección Intravítrea',
                'texto' => 'Medicamento aplicado dentro del ojo para tratar la retina.',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'version' => 1,
                'titulo' => 'Fotocoagulación Láser',
                'texto' => 'Láser usado para sellar vasos en la retina.',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'version' => 1,
                'titulo' => 'Cirugía de Retina',
                'texto' => 'Repara daños o desprendimientos en la retina.',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'version' => 1,
                'titulo' => 'Cirugía de Catarata',
                'texto' => 'Se reemplaza el cristalino opaco con un lente.',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'version' => 1,
                'titulo' => 'Topografía Corneal',
                'texto' => 'Análisis de la forma y curvatura de la córnea.',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
