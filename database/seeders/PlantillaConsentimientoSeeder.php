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
                'titulo' => 'Plantilla Consentimiento — Inyección Intravítrea',
                'texto' => 'Procedimiento donde se aplica un medicamento directamente dentro del ojo, en el espacio vítreo, con el fin de tratar enfermedades de la retina como edema macular o degeneración macular.',
                'activo' => true,
                'tipo' => 'inyeccion_intravitrea',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'version' => 1,
                'titulo' => 'Plantilla Consentimiento — Fotocoagulación Láser',
                'texto' => 'Tratamiento con láser que permite sellar vasos sanguíneos anormales o reducir inflamación en la retina, usado comúnmente en retinopatía diabética o desgarros retinianos.',
                'activo' => true,
                'tipo' => 'fotocoagulacion_laser',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'version' => 1,
                'titulo' => 'Plantilla Consentimiento — Cirugía de Retina',
                'texto' => 'Procedimiento quirúrgico que permite reparar daños en la retina, tratar desprendimientos o remover tejido fibrovascular mediante técnicas como vitrectomía.',
                'activo' => true,
                'tipo' => 'cirugia_retina',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
