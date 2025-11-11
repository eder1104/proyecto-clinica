<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PlantillaConsentimiento;
use App\Models\ConsentimientoPaciente;
use Illuminate\Support\Facades\Auth;

class ConsentimientoController extends Controller
{
    public function create(Request $request)
    {
        $plantillas = PlantillaConsentimiento::where('activo', 1)->get();

        $paciente_id = $request->query('paciente_id');
        $cita_id = $request->query('cita_id') ?? $request->query('cita');

        if (!$paciente_id) {
            abort(400, 'No se ha proporcionado el ID del paciente.');
        }

        return view('citas.consentimiento', compact('plantillas', 'paciente_id', 'cita_id'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'plantilla_id'    => 'required|exists:plantillas_consentimiento,id',
            'nombre_firmante' => 'required|string|max:255',
            'fecha_firma'     => 'required|date',
            'imagen_firma'    => 'required|image|mimes:jpeg,png|max:2048',
            'paciente_id'     => 'required|integer',
            'cita_id'         => 'nullable|integer',
        ]);

        $rutaFirma = $request->file('imagen_firma')->store('firmas_consents', 'public');

        ConsentimientoPaciente::create([
            'plantilla_id'    => $request->plantilla_id,
            'nombre_firmante' => $request->nombre_firmante,
            'fecha_firma'     => $request->fecha_firma,
            'firma'           => $rutaFirma,
            'paciente_id'     => $request->paciente_id,
            'cita_id'         => $request->cita_id,
            'doctor_id'       => Auth::id(),
            'activo'          => true,
        ]);

        return redirect()
            ->route('citas.atencion', ['cita' => $request->cita_id])
            ->with('success', 'Consentimiento registrado correctamente.');
    }
}
