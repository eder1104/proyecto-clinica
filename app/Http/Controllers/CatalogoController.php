<?php

namespace App\Http\Controllers;

use App\Models\Alergia;
use App\Models\DiagnosticoOftalmologico;
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

        $alergiasPrevias = collect();
        if ($paciente_id) {
            $paciente = Paciente::find($paciente_id);
            if ($paciente) {
                $alergiasPrevias = $paciente->alergias()->get();
            }
        }

        return view('citas.catalogos', compact(
            'tipo',
            'titulo',
            'nombre_input',
            'paciente_id',
            'historia_id',
            'alergiasPrevias'
        ))->with('ocultarMenu', true);
    }

    public function buscarDiagnosticos(Request $request)
    {
        $termino = $request->input('termino');
        if (!$termino) return response()->json([]);

        return DiagnosticoOftalmologico::where('nombre', 'like', "%$termino%")
            ->orWhere('codigo', 'like', "%$termino%")
            ->limit(20)
            ->get()
            ->map(fn($item) => [
                'id' => $item->id,
                'nombre' => $item->nombre,
                'codigo' => $item->codigo,
                'tipo' => 'diagnostico'
            ]);
    }

    public function buscarProcedimientos(Request $request)
    {
        $termino = $request->input('termino');
        if (!$termino) return response()->json([]);

        return ProcedimientoOftalmologico::where('nombre', 'like', "%$termino%")
            ->orWhere('codigo', 'like', "%$termino%")
            ->limit(20)
            ->get()
            ->map(fn($item) => [
                'id' => $item->id,
                'nombre' => $item->nombre,
                'codigo' => $item->codigo,
                'tipo' => 'procedimiento'
            ]);
    }

    public function buscarAlergias(Request $request)
    {
        $termino = $request->input('termino');
        if (!$termino) return response()->json([]);

        return Alergia::where('nombre', 'like', "%$termino%")
            ->limit(20)
            ->get()
            ->map(fn($item) => [
                'id' => $item->id,
                'nombre' => $item->nombre,
                'codigo' => null,
                'tipo' => 'alergia'
            ]);
    }

    public function guardarSeleccion(CatalogosRequest $request)
    {
        $pacienteId = $request->paciente_id;
        $historiaId = $request->historia_id;

        $ids = $request->items_ids ?? [];
        $tipos = $request->items_tipos ?? [];

        $alergiasIds = [];
        $procedimientosIds = [];
        $diagnosticoId = null;

        foreach ($tipos as $i => $tipo) {
            $id = $ids[$i] ?? null;
            if (!$id) continue;

            if ($tipo === 'diagnostico' && $diagnosticoId === null) {
                $diagnosticoId = $id;
            }

            if ($tipo === 'procedimiento') {
                $procedimientosIds[] = $id;
            }

            if ($tipo === 'alergia') {
                $alergiasIds[] = $id;
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
