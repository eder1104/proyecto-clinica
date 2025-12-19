<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConveniosSeeder extends Seeder
{
    public function run(): void
    {
        $convenios = [
            ['nombre' => 'NUEVA EPS', 'codigo' => 'EPS037', 'tipo' => 'EPS'],
            ['nombre' => 'EPS SANITAS', 'codigo' => 'EPS005', 'tipo' => 'EPS'],
            ['nombre' => 'SURA EPS', 'codigo' => 'EPS010', 'tipo' => 'EPS'],
            ['nombre' => 'SALUD TOTAL', 'codigo' => 'EPS002', 'tipo' => 'EPS'],
            ['nombre' => 'COOSALUD', 'codigo' => 'EPS025', 'tipo' => 'EPS'],
            ['nombre' => 'MUTUAL SER', 'codigo' => 'EPS030', 'tipo' => 'EPS'],
            ['nombre' => 'FAMISANAR', 'codigo' => 'EPS017', 'tipo' => 'EPS'],
            ['nombre' => 'COMPENSAR', 'codigo' => 'EPS008', 'tipo' => 'EPS'],
            ['nombre' => 'SERVICIO OCCIDENTAL DE SALUD', 'codigo' => 'EPS018', 'tipo' => 'EPS'],
            ['nombre' => 'EPS FAMILIAR DE COLOMBIA', 'codigo' => 'EPS040', 'tipo' => 'EPS'],
            ['nombre' => 'CAPITAL SALUD', 'codigo' => 'EPS041', 'tipo' => 'EPS'],
            ['nombre' => 'SAVIA SALUD', 'codigo' => 'EPS042', 'tipo' => 'EPS'],
            ['nombre' => 'ASMET SALUD', 'codigo' => 'EPS043', 'tipo' => 'EPS'],
            ['nombre' => 'EMSSANAR', 'codigo' => 'EPS044', 'tipo' => 'EPS'],
            ['nombre' => 'CAJACOPI', 'codigo' => 'EPS045', 'tipo' => 'EPS'],
            ['nombre' => 'COMFACHOCO', 'codigo' => 'EPS046', 'tipo' => 'EPS'],
            ['nombre' => 'CAPRESOCA', 'codigo' => 'EPS047', 'tipo' => 'EPS'],
            ['nombre' => 'ANAS WAYUU', 'codigo' => 'EPS048', 'tipo' => 'EPS'],
            ['nombre' => 'MALLAMAS', 'codigo' => 'EPS049', 'tipo' => 'EPS'],
            ['nombre' => 'PIJAOS SALUD', 'codigo' => 'EPS050', 'tipo' => 'EPS'],
            ['nombre' => 'SALUD MIA', 'codigo' => 'EPS051', 'tipo' => 'EPS'],
            ['nombre' => 'ALIANSALUD', 'codigo' => 'EPS001', 'tipo' => 'EPS'],
            ['nombre' => 'FERROCARRILES NACIONALES', 'codigo' => 'ESP006', 'tipo' => 'ESPECIAL'],
            ['nombre' => 'ECOPETROL', 'codigo' => 'ESP001', 'tipo' => 'ESPECIAL'],
            ['nombre' => 'FUERZAS MILITARES', 'codigo' => 'ESP002', 'tipo' => 'ESPECIAL'],
            ['nombre' => 'POLICIA NACIONAL', 'codigo' => 'ESP003', 'tipo' => 'ESPECIAL'],
            ['nombre' => 'MAGISTERIO', 'codigo' => 'ESP004', 'tipo' => 'ESPECIAL'],
            ['nombre' => 'UNIVERSIDAD NACIONAL', 'codigo' => 'ESP005', 'tipo' => 'ESPECIAL'],
            ['nombre' => 'ALLIANZ SEGUROS', 'codigo' => 'SEG001', 'tipo' => 'SEGURO'],
            ['nombre' => 'AXA COLPATRIA', 'codigo' => 'SEG002', 'tipo' => 'SEGURO'],
            ['nombre' => 'SEGUROS BOLIVAR', 'codigo' => 'SEG003', 'tipo' => 'SEGURO'],
            ['nombre' => 'LIBERTY SEGUROS', 'codigo' => 'SEG004', 'tipo' => 'SEGURO'],
            ['nombre' => 'MAPFRE SEGUROS', 'codigo' => 'SEG005', 'tipo' => 'SEGURO'],
            ['nombre' => 'SEGUROS DEL ESTADO', 'codigo' => 'SEG006', 'tipo' => 'SEGURO'],
            ['nombre' => 'LA PREVISORA SEGUROS', 'codigo' => 'SEG007', 'tipo' => 'SEGURO'],
            ['nombre' => 'COLMEDICA MEDICINA PREPAGADA', 'codigo' => 'PRE001', 'tipo' => 'PREPAGADA'],
            ['nombre' => 'COOMEVA MEDICINA PREPAGADA', 'codigo' => 'PRE002', 'tipo' => 'PREPAGADA'],
            ['nombre' => 'MEDIPLUS', 'codigo' => 'PRE003', 'tipo' => 'PREPAGADA'],
            ['nombre' => 'ARL SURA', 'codigo' => 'ARL001', 'tipo' => 'ARL'],
            ['nombre' => 'PARTICULAR', 'codigo' => 'PAR001', 'tipo' => 'PARTICULAR'],
        ];

        foreach ($convenios as $data) {
            $convenioId = DB::table('convenios')->insertGetId([
                'nombre' => $data['nombre'],
                'codigo_externo' => $data['codigo'],
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $planes = match ($data['tipo']) {
                'EPS' => ['CONTRIBUTIVO', 'SUBSIDIADO', 'PAC'],
                'ESPECIAL' => ['COTIZANTE', 'BENEFICIARIO', 'PENSIONADO'],
                'SEGURO' => ['POLIZA DE SALUD', 'SOAT', 'ACCIDENTES PERSONALES'],
                'PREPAGADA' => ['PLAN ORO', 'PLAN PLATA', 'PLAN FAMILIAR'],
                'ARL' => ['RIESGO I', 'RIESGO II', 'RIESGO III'],
                'PARTICULAR' => ['TARIFA PLENA', 'TARIFA PREFERENCIAL'],
                default => ['GENERAL'],
            };

            foreach ($planes as $planNombre) {
                DB::table('planes')->insert([
                    'convenio_id' => $convenioId,
                    'nombre' => $planNombre,
                    'codigo' => strtoupper(substr($planNombre, 0, 3)),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}