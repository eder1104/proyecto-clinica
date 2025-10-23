<?php

namespace App\Http\Controllers;

use App\Models\HistoriaClinica;
use App\Models\Paciente;
use App\Models\Cita;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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

        if ($cita->tipo_cita_id == 1 || $cita->tipo_cita_id == 2) {
            $historia = HistoriaClinica::firstOrCreate([
                'paciente_id' => $cita->paciente_id,
            ]);

            return redirect()->route('historias.edit', ['historia' => $historia->id]);
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
            'motivo_consulta' => 'nullable|string',
            'antecedentes' => 'nullable|array',
            'signos_vitales' => 'nullable|array',
            'diagnostico' => 'nullable|string|max:255',
            'conducta' => 'nullable|string',
            'created_by' => 'nullable|integer|exists:users,id',
            'updated_by' => 'nullable|integer|exists:users,id',
        ]);

        $historia = HistoriaClinica::where('paciente_id', $cita->paciente_id)->first();

        if ($historia) {
            $historia->update($request->all());
        } else {
            HistoriaClinica::create([
                'paciente_id' => $cita->paciente_id,
            ] + $request->all());
        }

        return redirect()->route('historias.edit', ['historia' => $historia->id ?? HistoriaClinica::latest()->first()->id])
            ->with('success', 'Historia clínica actualizada correctamente.');
    }

    public function destroy(HistoriaClinica $historia)
    {
        $paciente_id = $historia->paciente_id;
        $historia->delete();

        return redirect()->route('historias.show', $paciente_id)
            ->with('success', 'Historia clínica eliminada.');
    }


    public function registrarDesdeCita(Cita $cita, User $user)
    {
        $historiaData = [
            'paciente_id'     => $cita->paciente_id,
            'motivo_consulta' => 'Atención en cita de optometría',
            'antecedentes'    => [],
            'signos_vitales'  => [],
            'diagnostico'     => 'En espera de diagnóstico del optómetra',
            'conducta'        => 'Se recomienda seguimiento',
            'created_by'      => $user->id,
            'updated_by'      => $user->id,
        ];

        HistoriaClinica::updateOrCreate(
            ['paciente_id' => $cita->paciente_id],
            $historiaData
        );

        return true;
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
