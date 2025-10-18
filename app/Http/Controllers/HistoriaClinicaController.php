<?php

namespace App\Http\Controllers;

use App\Models\HistoriaClinica;
use App\Models\Paciente;
use App\Models\Cita;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Plantilla_Optometria;
use App\Models\Plantilla_Examenes;

class HistoriaClinicaController extends Controller
{
    public function index()
    {
        $pacientes = Paciente::all();
        return view('historias.index', compact('pacientes'));
    }

    public function cita($paciente_id)
    {
        $paciente = Paciente::findOrFail($paciente_id);

        $citas = Cita::where('paciente_id', $paciente_id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('historias.historia', compact('paciente', 'citas'));
    }

    public function show($id)
    {
        $cita = Cita::with(['paciente', 'TipoCita'])->findOrFail($id);

        if ($cita->tipo_cita_id == 1) {
            return redirect()->route('optometria.edit', ['cita' => $cita->id]);
        }

        if ($cita->tipo_cita_id == 2) {
            return redirect()->route('examenes.edit', ['cita' => $cita->id]);
        }

        return redirect()->route('historias.index')
            ->with('error', 'No existe una plantilla asociada a este tipo de cita.');
    }


    public function edit(HistoriaClinica $historia)
    {
        $historia->load('paciente');
        return view('historias.edit', compact('historia'));
    }

    public function update(Request $request, Cita $cita)
    {
        $request->validate([
            'optometra' => 'required|integer|exists:users,id',
            'consulta_completa' => 'nullable|boolean',
            'anamnesis' => 'nullable|string',
            'alternativa_deseada' => 'nullable|string|max:255',
            'dominancia_ocular' => 'nullable|string|max:50',
            'av_lejos_od' => 'nullable|string|max:20',
            'av_intermedia_od' => 'nullable|string|max:20',
            'av_cerca_od' => 'nullable|string|max:20',
            'av_lejos_oi' => 'nullable|string|max:20',
            'av_intermedia_oi' => 'nullable|string|max:20',
            'av_cerca_oi' => 'nullable|string|max:20',
            'observaciones_internas' => 'nullable|string',
            'observaciones_optometria' => 'nullable|string',
            'observaciones_formula' => 'nullable|string',
            'tipo_lente' => 'nullable|string|max:50',
            'especificaciones_lente' => 'nullable|string',
            'vigencia_formula' => 'nullable|string|max:50',
            'filtro' => 'nullable|string|max:50',
            'tiempo_formulacion' => 'nullable|string|max:50',
            'distancia_pupilar' => 'nullable|string|max:10',
            'cantidad' => 'nullable|integer',
            'diagnostico_principal' => 'nullable|string|max:255',
            'otros_diagnosticos' => 'nullable|string',
            'datos_adicionales' => 'nullable|string',
            'finalidad_consulta' => 'nullable|string|max:255',
            'causa_motivo_atencion' => 'nullable|string|max:255',
        ]);

        $plantilla = Plantilla_Optometria::where('cita_id', $cita->id)->first();

        if ($plantilla) {
            $plantilla->update($request->all());
        } else {
            Plantilla_Optometria::create([
                'cita_id' => $cita->id,
                'paciente_id' => $cita->paciente_id,
            ] + $request->all());
        }

        return redirect()->route('optometria.edit', $cita->id)
            ->with('success', 'Plantilla de optometría actualizada correctamente.');
    }


    public function destroy(HistoriaClinica $historia)
    {
        $paciente_id = $historia->paciente_id;
        $historia->delete();

        return redirect()->route('historias.show', $paciente_id)
            ->with('success', 'Historia clínica eliminada.');
    }

    public function verPdf(Cita $cita)
    {
        if ($cita->pdf_path && Storage::disk('public')->exists($cita->pdf_path)) {
            return response()->file(storage_path('app/public/' . $cita->pdf_path));
        }

        return back()->with('error', 'El PDF no existe o no fue generado.');
    }

    public function descargarPdf(Cita $cita)
    {
        if ($cita->pdf_path && Storage::disk('public')->exists($cita->pdf_path)) {
            return response()->download(storage_path('app/public/' . $cita->pdf_path));
        }

        return back()->with('error', 'El PDF no existe o no fue generado.');
    }
}
