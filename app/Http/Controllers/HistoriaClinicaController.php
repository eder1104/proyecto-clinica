<?php

namespace App\Http\Controllers;

use App\Models\HistoriaClinica;
use App\Models\Paciente;
use App\Models\Cita;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class HistoriaClinicaController extends Controller
{
    public function index()
    {
        $pacientes = Paciente::all();
        return view('historias.index', compact('pacientes'));
    }

    public function create(Paciente $paciente)
    {
        return view('historias.create', compact('paciente'));
    }

    public function store(Request $request, Paciente $paciente)
    {
        $request->validate([
            'motivo_consulta' => 'required|string|max:255',
            'antecedentes'    => 'nullable|string',
            'signos_vitales'  => 'nullable|string',
            'diagnostico'     => 'required|string',
            'conducta'        => 'required|string',
        ]);

        HistoriaClinica::create([
            'paciente_id'     => $paciente->id,
            'motivo_consulta' => $request->motivo_consulta,
            'antecedentes'    => $request->antecedentes,
            'signos_vitales'  => $request->signos_vitales,
            'diagnostico'     => $request->diagnostico,
            'conducta'        => $request->conducta,
        ]);

        return redirect()->route('historias.show', $paciente->id)
            ->with('success', 'Historia clínica registrada.');
    }

    public function show($paciente_id)
    {
        $paciente = Paciente::findOrFail($paciente_id);

        $historias = HistoriaClinica::where('paciente_id', $paciente_id)->get();

        $pdfs = Cita::where('paciente_id', $paciente_id)
            ->whereNotNull('pdf_path')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('historias.show', compact('paciente', 'historias', 'pdfs'));
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
        //al fin sirve este desgraciado
        if ($cita->pdf_path && Storage::disk('public')->exists($cita->pdf_path)) {
            return response()->download(storage_path('app/public/' . $cita->pdf_path));
        }

        return back()->with('error', 'El PDF no existe o no fue generado.');
    }
}
