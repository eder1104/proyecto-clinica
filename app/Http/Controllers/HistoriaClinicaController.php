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

    public function update(Request $request, HistoriaClinica $historia)
    {
        $request->validate([
            'motivo_consulta' => 'required|string|max:255',
            'antecedentes'    => 'nullable|string',
            'signos_vitales'  => 'nullable|string',
            'diagnostico'     => 'required|string',
            'conducta'        => 'required|string',
        ]);

        $historia->update([
            'motivo_consulta' => $request->motivo_consulta,
            'antecedentes'    => $request->antecedentes,
            'signos_vitales'  => $request->signos_vitales,
            'diagnostico'     => $request->diagnostico,
            'conducta'        => $request->conducta,
        ]);

        return redirect()->route('historias.show', $historia->paciente_id)
            ->with('success', 'Historia clínica actualizada.');
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
