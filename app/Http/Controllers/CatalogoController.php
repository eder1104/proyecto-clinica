<?php

namespace App\Http\Controllers;

use App\Models\Alergia;
use App\Models\DiagnosticoOftalmologico;
use App\Models\Procedimiento;
use App\Models\HistoriaClinica;
use App\Models\Paciente;
use App\Models\ProcedimientoOftalmologico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CatalogoController extends Controller
{
    public function modal(Request $request)
    {
        $tipo = $request->query('tipo', 'diagnostico');
        $titulo = $request->query('titulo', null);
        $nombre_input = $request->query('nombre_input', 'catalogo_ids');

        return view('citas.catalogos', compact('tipo', 'titulo', 'nombre_input'))
            ->with('ocultarMenu', true);
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

    public function guardarSeleccion(Request $request)
    {
        $request->validate([
            'paciente_id' => 'required|exists:pacientes,id',
            'historia_id' => 'required|exists:historias_clinicas,id',
            'items_ids'   => 'array',
            'items_tipos' => 'array',
        ]);

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

            match ($tipo) {
                'alergia'       => $alergiasIds[] = $id,
                'procedimiento' => $procedimientosIds[] = $id,
                'diagnostico'   => $diagnosticoId = $id,
                default         => null,
            };
        }

        DB::transaction(function () use ($pacienteId, $historiaId, $alergiasIds, $procedimientosIds, $diagnosticoId) {

            if ($alergiasIds) {
                $paciente = Paciente::findOrFail($pacienteId);
                $paciente->alergias()->syncWithoutDetaching($alergiasIds);
            }

            $historia = HistoriaClinica::findOrFail($historiaId);

            if ($diagnosticoId) {
                $historia->diagnostico_principal_id = $diagnosticoId;
            }

            $historia->save();

            if ($procedimientosIds) {
                $historia->procedimientos()->syncWithoutDetaching($procedimientosIds);
            }
        });

        return back()->with('success', 'Información guardada correctamente.');
    }
}
