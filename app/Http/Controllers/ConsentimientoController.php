<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PlantillaConsentimiento;
use App\Models\ConsentimientoPaciente;
use App\Models\Paciente;
use App\Models\doctores;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

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
        $tipoFirmante = $request->input('tipo_firmante');

        if ($tipoFirmante === 'paciente') {
            $request->validate([
                'fecha_firma'  => 'required',
                'imagen_firma' => 'required|image|mimes:jpeg,png|max:2048',
                'paciente_id'  => 'required|integer',
                'cita_id'      => 'nullable|integer',
            ]);
        } elseif ($tipoFirmante === 'acompanante') {
            $request->validate([
                'nombre_acompanante'   => 'required|string|max:255',
                'apellido_acompanante' => 'required|string|max:255',
                'cedula_acompanante'   => 'required|string|max:20',
                'fecha_firma'          => 'required',
                'imagen_firma'         => 'required|image|mimes:jpeg,png|max:2048',
                'paciente_id'          => 'required|integer',
                'cita_id'              => 'nullable|integer',
            ]);
        } else {
            return back()->withErrors(['tipo_firmante' => 'Debe seleccionar paciente o acompañante.']);
        }

        $rutaFirma = $request->file('imagen_firma')->store('firmas_consents', 'public');

        $paciente = Paciente::find($request->paciente_id);
        $nombrePaciente = $paciente ? trim($paciente->nombres . ' ' . $paciente->apellidos) : 'Paciente sin nombre';

        $doctorRecord = doctores::with('user')->where('user_id', Auth::id())->first();
        $nombreDoctor = $doctorRecord && $doctorRecord->user
            ? trim($doctorRecord->user->nombres . ' ' . $doctorRecord->user->apellidos)
            : (Auth::user()->nombre_completo ?? 'Doctor sin nombre');

        if ($tipoFirmante === 'paciente') {
            $nombreFirmante = $nombrePaciente;
        } else {
            $nombreFirmante = trim($request->nombre_acompanante . ' ' . $request->apellido_acompanante);
        }

        $fechaFormateada = Carbon::createFromFormat('d/m/Y', $request->fecha_firma)->format('Y-m-d');

        ConsentimientoPaciente::create([
            'plantilla_id'    => $request->plantilla_id,
            'nombre_firmante' => $nombreFirmante,
            'fecha_firma'     => $fechaFormateada,
            'firma'           => $rutaFirma,
            'paciente_id'     => $request->paciente_id,
            'nombre_paciente' => $nombrePaciente,
            'cita_id'         => $request->cita_id,
            'doctor_id'       => Auth::id(),
            'nombre_doctor'   => $nombreDoctor,
            'activo'          => true,
        ]);

        return redirect()
            ->route('citas.atencion', ['cita' => $request->cita_id])
            ->with('success', 'Consentimiento registrado correctamente.');
    }
}
