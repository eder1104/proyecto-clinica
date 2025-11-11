<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plantilla_Optometria;
use App\Models\Cita;
use App\Models\doctores;
use App\Http\Requests\PlantillaOptometriaRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class PlantillaControllerOptometria extends Controller
{
    public function index($cita_id)
    {
        $cita = Cita::with('paciente')->findOrFail($cita_id);

        $plantilla = Plantilla_Optometria::where('cita_id', $cita->id)->first();
        $citas = Cita::with('paciente')->orderBy('fecha', 'desc')->get();
        $doctores = User::where('role', 'doctor')->get();
        $id = $plantilla ? $plantilla->id : null;

        return view('plantillas.optometria', compact('id', 'plantilla', 'citas', 'cita', 'doctores'));
    }

    public function edit(Cita $cita)
    {
        $cita->load(['paciente',]);

        $plantilla = Plantilla_Optometria::where('cita_id', $cita->id)->first();
        $doctores = User::where('role', 'doctor')->get();


        return view('historias.optometria_edit', compact('plantilla', 'cita', 'doctores',));
    }

   public function store(PlantillaOptometriaRequest $request, $cita_id)
{
    $cita = Cita::findOrFail($cita_id);
    $user = Auth::user();

    if ($user->role !== 'doctor') {
        return back()->withErrors(['optometra' => 'El usuario logueado no tiene el rol de doctor.'])->withInput();
    }

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
        'diagnostico_principal' => $request->diagnostico_principal,
        'otros_diagnosticos' => $request->otros_diagnosticos,
        'datos_adicionales' => $request->datos_adicionales,
        'finalidad_consulta' => $request->finalidad_consulta,
        'causa_motivo_atencion' => $request->causa_motivo_atencion,
    ];

    Plantilla_Optometria::create($data);

    $cita->update(['estado' => 'finalizada']);

    return redirect()
        ->route('historias.cita', ['paciente' => $cita->paciente_id])
        ->with('success', 'Cita finalizada y plantilla registrada correctamente.');
}


    public function update(PlantillaOptometriaRequest $request, Cita $cita)
    {
        $user = Auth::user();

        $idOptometraLogeado = $user->id;

        if ($user->role !== 'doctor') {
            return back()->withErrors(['optometra' => 'El usuario logueado no tiene el rol de doctor.'])->withInput();
        }

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

        return redirect()->route('historias.index', $cita->id)
            ->with('success', 'Plantilla de optometría actualizada correctamente.');
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
        return view('plantillas.optometria_show', compact('plantilla'));
    }

    public function destroy($id)
    {
        $plantilla = Plantilla_Optometria::findOrFail($id);
        $plantilla->delete();
        return redirect()->back()->with('success', 'Plantilla eliminada correctamente.');
    }
}