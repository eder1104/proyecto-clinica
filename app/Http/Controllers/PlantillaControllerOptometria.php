<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plantilla_Optometria;
use App\Models\Cita;
use App\Models\HistoriaClinica;
use App\Models\User;
use App\Http\Requests\PlantillaOptometriaRequest;
use App\Models\ProcedimientoOftalmologico;
use App\Models\DiagnosticoOftalmologico;
use Illuminate\Support\Facades\Auth;
use App\Models\Alergia;
use Illuminate\Support\Facades\DB;

class PlantillaControllerOptometria extends Controller
{
public function index($cita_id)
{
    $cita = Cita::with('paciente')->findOrFail($cita_id);

    $plantilla = Plantilla_Optometria::where('cita_id', $cita->id)->first();
    $citas = Cita::with('paciente')->orderBy('fecha', 'desc')->get();
    $doctores = User::where('role', 'doctor')->get();
    $id = $plantilla ? $plantilla->id : null;

    $historia = HistoriaClinica::where('paciente_id', $cita->paciente_id)
        ->with(['diagnostico', 'procedimientos'])
        ->first();

    $alergiasPrevias = $cita->paciente->alergias()->get();

    return view('plantillas.optometria', compact(
        'id',
        'plantilla',
        'citas',
        'cita',
        'doctores',
        'historia',
        'alergiasPrevias'
    ));
}

    public function edit(Cita $cita)
    {
        $cita->load(['paciente']);

        $plantilla = Plantilla_Optometria::where('cita_id', $cita->id)->first();
        $doctores = User::where('role', 'doctor')->get();

        $historia = HistoriaClinica::where('paciente_id', $cita->paciente_id)
            ->with(['diagnostico', 'procedimientos'])
            ->first();

        $items = collect();

        if ($historia && $historia->diagnostico_principal_id) {
            $diag = DiagnosticoOftalmologico::find($historia->diagnostico_principal_id);
            if ($diag) {
                $diag->tipo_catalogo = 'diagnostico';
                $items->push($diag);
            }
        }

        if ($historia) {
            foreach ($historia->procedimientos as $proc) {
                $proc->tipo_catalogo = 'procedimiento';
                $items->push($proc);
            }
        }

        if ($cita->paciente) {
            foreach ($cita->paciente->alergias as $alergia) {
                $alergia->tipo_catalogo = 'alergia';
                $items->push($alergia);
            }
        }

        if ($plantilla) {
            $plantilla->itemsCatalogo = $items;
        } else {
            $plantilla = new Plantilla_Optometria();
            $plantilla->itemsCatalogo = $items;
        }

        return view('historias.optometria_edit', compact('plantilla', 'cita', 'doctores', 'historia'));
    }



    public function store(PlantillaOptometriaRequest $request, $cita_id)
    {
        $request->validated();

        $cita = Cita::findOrFail($cita_id);
        $user = Auth::user();

        $merged = $request->all();
        $merged['paciente_id'] = $merged['paciente_id'] ?? $cita->paciente_id;

        if (empty($merged['historia_id'])) {

            $historia = HistoriaClinica::firstOrCreate(
                ['paciente_id' => $cita->paciente_id],
                ['created_by' => Auth::id()]
            );

            $merged['historia_id'] = $historia->id;
        }


        $catalogoRequest = app(\App\Http\Requests\CatalogosRequest::class);
        $catalogoRequest->setContainer(app());
        $catalogoRequest->setRedirector(app('redirect'));
        $catalogoRequest->merge($merged);
        $catalogoRequest->validateResolved();

        if ($user->role !== 'doctor') {
            return back()->withErrors(['optometra' => 'El usuario logueado no tiene el rol de doctor.'])->withInput();
        }

        return DB::transaction(function () use ($request, $cita, $user) {

            $data = [
                'paciente_id' => $cita->paciente_id,
                'cita_id' => $cita->id,
                'optometra' => $user->id,
                'consulta_completa' => $request->has('consulta_completa') ? 1 : 0,
                'anamnesis' => $request->anamnesis,
                'alternativa_deseada' => $request->alternativa_deseada,
                'dominancia_ocular' => $request->dominancia_ocular,
                'av_lejos_od' => $request->av_lejos_od,
                'av_intermedia_od' => $request->av_intermedia_od,
                'av_cerca_od' => $request->av_cerca_od,
                'av_lejos_oi' => $request->av_lejos_oi,
                'av_intermedia_oi' => $request->av_intermedia_oi,
                'av_cerca_oi' => $request->av_cerca_oi,
                'observaciones_optometria' => $request->observaciones_optometria,
                'tipo_lente' => $request->tipo_lente,
                'especificaciones_lente' => $request->especificaciones_lente,
                'vigencia_formula' => $request->vigencia_formula,
                'filtro' => $request->filtro,
                'tiempo_formulacion' => $request->tiempo_formulacion,
                'distancia_pupilar' => $request->distancia_pupilar,
                'cantidad' => $request->cantidad,
                'medicamento_principal' => $request->medicamento_principal,
                'otros_medicamentos' => $request->otros_medicamentos,
                'notas_medicamento' => $request->notas_medicamento,
                'finalidad_consulta' => $request->finalidad_consulta,
                'causa_motivo_atencion' => $request->causa_motivo_atencion,
            ];

            Plantilla_Optometria::create($data);

            $this->procesarCatalogos($request, $cita);

            $cita->update(['estado' => 'finalizada']);

            return redirect()
                ->route('historias.cita', ['paciente' => $cita->paciente_id])
                ->with('success', 'Cita finalizada y plantilla registrada correctamente.');
        });
    }


    public function update(PlantillaOptometriaRequest $request, Cita $cita)
    {
        $user = Auth::user();
        $idOptometraLogeado = $user->id;

        if ($user->role !== 'doctor') {
            return back()->withErrors(['optometra' => 'El usuario logueado no tiene el rol de doctor.'])->withInput();
        }

        return DB::transaction(function () use ($request, $cita, $idOptometraLogeado) {

            $plantilla = Plantilla_Optometria::where('cita_id', $cita->id)->first();

            $data = $request->all();
            $data['consulta_completa'] = $request->has('consulta_completa') ? 1 : 0;
            $data['optometra'] = $idOptometraLogeado;

            if ($plantilla) {
                $plantilla->update($data);
            } else {
                Plantilla_Optometria::create(array_merge(
                    $data,
                    [
                        'cita_id' => $cita->id,
                        'paciente_id' => $cita->paciente_id
                    ]
                ));
            }

            $this->procesarCatalogos($request, $cita);

            return redirect()->route('historias.index', $cita->id)
                ->with('success', 'Plantilla de optometría actualizada correctamente.');
        });
    }

    private function procesarCatalogos(Request $request, Cita $cita)
    {
        $ids = $request->input('items_ids', []);
        $tipos = $request->input('items_tipos', []);

        $historia = HistoriaClinica::firstOrCreate(
            ['paciente_id' => $cita->paciente_id],
            ['created_by' => Auth::id()]
        );

        $diagnosticoId = null;
        $procedimientosIds = [];
        $alergiasIds = [];

        foreach ($tipos as $index => $tipo) {
            $id = $ids[$index] ?? null;
            if (!$id) continue;

            if ($tipo === 'diagnostico') {
                $diagnosticoId = $id;
            } elseif ($tipo === 'procedimiento') {
                $procedimientosIds[] = $id;
            } elseif ($tipo === 'alergia') {
                $alergiasIds[] = $id;
            }
        }

        if ($diagnosticoId) {
            $historia->diagnostico_principal_id = $diagnosticoId;
            $historia->save();
        }

        if (!empty($procedimientosIds)) {

            $syncData = [];
            foreach ($procedimientosIds as $pid) {
                $syncData[$pid] = ['cita_id' => $cita->id];
            }

            $historia->procedimientos()->sync($syncData);
        }

        if (!empty($alergiasIds) && $cita->paciente) {

            $syncAlergias = [];
            foreach ($alergiasIds as $aid) {
                $syncAlergias[$aid] = ['cita_id' => $cita->id];
            }

            $cita->paciente->alergias()->syncWithoutDetaching($syncAlergias);
        }
    }


    public function atender(Cita $cita)
    {
        $cita->load(['paciente']);
        $historia = $cita->paciente->historiaClinica ?? null;

        return view('optometria.historia', compact('cita', 'historia'));
    }

    public function show($id)
    {
        $plantilla = Plantilla_Optometria::with(['cita.paciente'])->findOrFail($id);

        $historia = HistoriaClinica::where('paciente_id', $plantilla->paciente_id)
            ->with(['diagnostico', 'procedimientos'])
            ->first();

        return view('plantillas.optometria_show', compact('plantilla', 'historia'));
    }

    public function destroy($id)
    {
        $plantilla = Plantilla_Optometria::findOrFail($id);
        $plantilla->delete();
        return redirect()->back()->with('success', 'Plantilla eliminada correctamente.');
    }
}
