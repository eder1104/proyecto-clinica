<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PlantillaConsentimiento;
use App\Models\ConsentimientoPaciente;
use App\Models\Paciente;
use App\Models\Cita;
use Illuminate\Support\Facades\Auth;

class ConsentimientoController extends Controller
{
    public function index()
    {
        $pacientes = Paciente::select('id', 'nombres', 'apellidos')
            ->orderBy('apellidos', 'asc')
            ->get();

        $plantillas = PlantillaConsentimiento::select('id', 'titulo')
            ->where('activo', true)
            ->orderBy('titulo', 'asc')
            ->get();

        $consentimientos = ConsentimientoPaciente::with(['paciente', 'plantilla'])
            ->orderBy('fecha_firma', 'desc')
            ->get();

        return view('citas.index', compact('pacientes', 'plantillas', 'consentimientos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cita_id' => 'required|exists:citas,id',
            'paciente_id' => 'required|exists:pacientes,id',
            'plantilla_id' => 'required|exists:plantillas_consentimiento,id',
            'nombre_firmante' => 'required|string|max:255',
            'fecha_firma' => 'required|date',
            'imagen_firma' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'redirect_to_url' => 'nullable|string',
        ]);

        $imagenPath = $request->file('imagen_firma')->store('firmas', 'public');

        ConsentimientoPaciente::create([
            'cita_id' => $request->cita_id,
            'paciente_id' => $request->paciente_id,
            'plantilla_id' => $request->plantilla_id,
            'nombre_firmante' => $request->nombre_firmante,
            'fecha_firma' => $request->fecha_firma,
            'imagen_firma' => $imagenPath,
            'creado_por' => Auth::id(),
        ]);

        return redirect()->route('citas.examen', ['cita' => $request->cita_id])
            ->with('success', 'Consentimiento guardado correctamente.');
    }
}
