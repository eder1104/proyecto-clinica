<?php

namespace App\Http\Controllers;

use App\Models\Alergia;
use App\Models\DiagnosticoOftalmologico;
use App\Models\Procedimiento;
use App\Models\HistoriaClinica;
use App\Models\Paciente;
use App\Models\ProcedimientoOftalmologico;
use Illuminate\Http\Request;
use App\Http\Requests\CatalogosRequest;
use Illuminate\Support\Facades\DB;

class CatalogoController extends Controller
{
    public function modal(Request $request)
    {
        $tipo = $request->query('tipo', 'diagnostico');
        $titulo = $request->query('titulo', null);
        $nombre_input = $request->query('nombre_input', 'catalogo_ids');

        $paciente_id = $request->query('paciente_id');
        $historia_id = $request->query('historia_id');

        return view('citas.catalogos', compact(
            'tipo',
            'titulo',
            'nombre_input',
            'paciente_id',
            'historia_id'
        ))->with('ocultarMenu', true);
    }


    public function buscar(Request $request)
    {
        $termino = $request->input('termino');

        if (empty($termino)) {
            return response()->json([]);
        }

        $diagnosticos = DiagnosticoOftalmologico::where('nombre', 'like', "%$termino%")
            ->orWhere('codigo', 'like', "%$termino%")
            ->limit(10)->get()
            ->map(fn($item) => [
                'id'   => $item->id,
                'nombre' => $item->nombre,
                'codigo' => $item->codigo,
                'tipo'   => 'diagnostico',
                'label'  => 'Diagnóstico'
            ]);

        $procedimientos = ProcedimientoOftalmologico::where('nombre', 'like', "%$termino%")
            ->orWhere('codigo', 'like', "%$termino%")
            ->limit(10)->get()
            ->map(fn($item) => [
                'id'   => $item->id,
                'nombre' => $item->nombre,
                'codigo' => $item->codigo,
                'tipo'   => 'procedimiento',
                'label'  => 'Procedimiento'
            ]);

        $alergias = Alergia::where('nombre', 'like', "%$termino%")
            ->limit(10)->get()
            ->map(fn($item) => [
                'id'   => $item->id,
                'nombre' => $item->nombre,
                'codigo' => null,
                'tipo'   => 'alergia',
                'label'  => 'Alergia'
            ]);

        return response()->json(
            $diagnosticos->concat($procedimientos)->concat($alergias)->values()
        );
    }

    public function guardarSeleccion(CatalogosRequest $request)
    {
        $pacienteId = $request->paciente_id;
        $historiaId = $request->historia_id;

        $ids   = $request->items_ids ?? [];
        $tipos = $request->items_tipos ?? [];

        $alergiasIds = [];
        $procedimientosIds = [];
        $diagnosticoId = null;

        foreach ($tipos as $i => $tipo) {
            $id = $ids[$i] ?? null;
            if (!$id) continue;

            switch ($tipo) {
                case 'diagnostico':
                    if ($diagnosticoId === null) {
                        $diagnosticoId = $id;
                    }
                    break;

                case 'procedimiento':
                    $procedimientosIds[] = $id;
                    break;

                case 'alergia':
                    $alergiasIds[] = $id;
                    break;
            }
        }

        DB::transaction(function () use ($pacienteId, $historiaId, $alergiasIds, $procedimientosIds, $diagnosticoId) {

            if (!empty($alergiasIds)) {
                $paciente = Paciente::findOrFail($pacienteId);
                $paciente->alergias()->syncWithoutDetaching($alergiasIds);
            }

            $historia = HistoriaClinica::findOrFail($historiaId);

            if (!empty($diagnosticoId)) {
                $historia->diagnostico_principal_id = $diagnosticoId;
            }

            $historia->save();

            if (!empty($procedimientosIds)) {
                $historia->procedimientos()->syncWithoutDetaching($procedimientosIds);
            }
        });

        return back()->with('success', 'Información guardada correctamente.');
    }
}
