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
        // ===  ===
        $diagnosticos = [
            ['nombre' => 'Degeneración Macular Asociada a la Edad (DMAE)', 'codigo' => 'H35.3'],
            ['nombre' => 'Retinopatía Diabética', 'codigo' => 'H36.0'],
            ['nombre' => 'Glaucoma Primario de Ángulo Abierto', 'codigo' => 'H40.1'],
            ['nombre' => 'Miopía Magna', 'codigo' => 'H44.2'],
            ['nombre' => 'Edema Macular', 'codigo' => 'H35.0'],
        ];

        $contador = 1;
        foreach ($diagnosticos as $diagnostico) {
            DiagnosticoOftalmologico::firstOrCreate([
                'nombre' => $diagnostico['nombre'],
                'codigo' => $diagnostico['codigo'],
                'serial' => 'DIA' . str_pad($contador++, 3, '0', STR_PAD_LEFT),
            ]);
        }

        $procedimientos = [
            ['nombre' => 'OCT macular', 'codigo' => '92025'],
            ['nombre' => 'Angiografía fluoresceínica', 'codigo' => '92235'],
            ['nombre' => 'Inyección intravítrea', 'codigo' => '67028'],
            ['nombre' => 'Fotocoagulación láser', 'codigo' => '67145'],
        ];

        $contador = 1;
        foreach ($procedimientos as $procedimiento) {
            ProcedimientoOftalmologico::firstOrCreate([
                'nombre' => $procedimiento['nombre'],
                'codigo' => $procedimiento['codigo'],
                'serial' => 'PRO' . str_pad($contador++, 3, '0', STR_PAD_LEFT),
            ]);
        }

        $alergias = [
            ['nombre' => 'Lágrimas artificiales', 'tipo' => 'Medicamento'],
            ['nombre' => 'Anestésico tópico', 'tipo' => 'Medicamento'],
            ['nombre' => 'Yodo povidona', 'tipo' => 'Sustancia'],
        ];

        $contador = 1;
        foreach ($alergias as $alergia) {
            Alergia::firstOrCreate([
                'nombre' => $alergia['nombre'],
                'tipo' => $alergia['tipo'],
                'serial' => 'ALE' . str_pad($contador++, 3, '0', STR_PAD_LEFT),
            ]);
        }
    }
}
