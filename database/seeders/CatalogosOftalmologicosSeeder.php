<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Alergia;
use App\Models\DiagnosticoOftalmologico;
use App\Models\ProcedimientoOftalmologico;

class CatalogosOftalmologicosSeeder extends Seeder
{
    public function run(): void
    {
        $diagnosticos = [
            ['nombre' => 'Degeneración Macular Asociada a la Edad (DMAE)', 'codigo' => 'H35.3'],
            ['nombre' => 'Retinopatía Diabética', 'codigo' => 'H36.0'],
            ['nombre' => 'Glaucoma Primario de Ángulo Abierto', 'codigo' => 'H40.1'],
            ['nombre' => 'Miopía Magna', 'codigo' => 'H44.2'],
            ['nombre' => 'Edema Macular', 'codigo' => 'H35.0'],
        ];

        foreach ($diagnosticos as $diagnostico) {
            DiagnosticoOftalmologico::firstOrCreate($diagnostico);
        }

        $procedimientos = [
            ['nombre' => 'OCT macular', 'codigo' => '92025'],
            ['nombre' => 'Angiografía fluoresceínica', 'codigo' => '92235'],
            ['nombre' => 'Inyección intravítrea', 'codigo' => '67028'],
            ['nombre' => 'Fotocoagulación láser', 'codigo' => '67145'],
        ];

        foreach ($procedimientos as $procedimiento) {
            ProcedimientoOftalmologico::firstOrCreate($procedimiento);
        }

        $alergias = [
            ['nombre' => 'Lágrimas artificiales', 'tipo' => 'Medicamento'],
            ['nombre' => 'Anestésico tópico', 'tipo' => 'Medicamento'],
            ['nombre' => 'Yodo povidona', 'tipo' => 'Sustancia'],
        ];

        foreach ($alergias as $alergia) {
            Alergia::firstOrCreate($alergia);
        }
    }
}
