<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PlantillaConsentimiento;
use App\Models\ConsentimientoPaciente;
use App\Models\Paciente;
use App\Models\doctores;
use App\Models\Cita;
use App\Models\BloqueoAgenda;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ConsentimientoController extends Controller
{
    public function generar(Request $request)
    {
        $cita_id = $request->query('cita_id');
        if (!$cita_id) return back()->with('error', 'No se ha proporcionado una cita válida.');

        $cita = Cita::findOrFail($cita_id);

        $horaActual = Carbon::now()->format('H:i:s');
        $fechaActual = Carbon::now()->format('Y-m-d');

        $tieneBloqueo = BloqueoAgenda::where('creado_por', Auth::id())
            ->where('fecha', $fechaActual)
            ->where('hora_inicio', '<=', $horaActual)
            ->where('hora_fin', '>=', $horaActual)
            ->exists();

        if ($tieneBloqueo) {
            return redirect()->route('citas.index')
                ->with('error', 'No puedes realizar atenciones, tienes un bloqueo de agenda activo en este momento.');
        }

        if ($cita->tipo_cita_id == 1) {

            $alergiasPrevias = $cita->paciente->alergias()->get();

            $vista = 'Plantillas.Optometria';
            return view($vista, [
                'cita_id' => $cita->id,
                'paciente_id' => $cita->paciente_id,
                'plantilla_id' => null,
                'plantilla_consentimiento' => null,
                'cita' => $cita,
                'alergiasPrevias' => $alergiasPrevias
            ]);
        }


        $tipo = $cita->tipo_examen;

        $vistas = [
            'cirugia_retina'          => 'citas.PlantillasConsentimiento.ConsentimientoCirugiaRetina',
            'fotocoagulacion_laser'   => 'citas.PlantillasConsentimiento.ConsentimientoFotocoagulacionLaser',
            'inyeccion_intravitrea'   => 'citas.PlantillasConsentimiento.ConsentimientoInyeccionIntravitrea',
        ];

        if (!isset($vistas[$tipo]) || !View::exists($vistas[$tipo])) {
            return back()->with('error', 'No existe una plantilla para este tipo de examen.');
        }

        // Intento robusto de encontrar la plantilla para la vista
        $plantilla = PlantillaConsentimiento::where('tipo', $tipo)
            ->orWhere('tipo', str_replace('_', ' ', $tipo)) // Busca "fotocoagulacion laser"
            ->orWhere('tipo', str_replace(' ', '_', $tipo)) // Busca "fotocoagulacion_laser"
            ->first();

        return view($vistas[$tipo], [
            'cita_id' => $cita->id,
            'paciente_id' => $cita->paciente_id,
            'plantilla_id' => $plantilla->id ?? null,
            'plantilla_consentimiento' => $plantilla,
            'cita' => $cita
        ]);
    }


    public function create(Request $request)
    {
        $cita_id = $request->query('cita_id') ?? $request->query('cita');

        if ($cita_id) {
            $cita = Cita::findOrFail($cita_id);

            $horaActual = Carbon::now()->format('H:i:s');
            $fechaActual = Carbon::now()->format('Y-m-d');

            $tieneBloqueo = BloqueoAgenda::where('creado_por', Auth::id())
                ->where('fecha', $fechaActual)
                ->where('hora_inicio', '<=', $horaActual)
                ->where('hora_fin', '>=', $horaActual)
                ->exists();

            if ($tieneBloqueo) {
                return redirect()->route('citas.index')->with('error', 'No puedes realizar atenciones, tienes un bloqueo de agenda activo en este momento.');
            }
        }

        $plantillas = PlantillaConsentimiento::where('activo', 1)->get();
        $paciente_id = $request->query('paciente_id');

        if (!$paciente_id) abort(400, 'No se ha proporcionado el ID del paciente.');

        return view('citas.consentimiento', compact('plantillas', 'paciente_id', 'cita_id'));
    }

    public function store(Request $request)
    {
        $tipoFirmante = $request->input('tipo_firmante');
        $validatedData = [];

        if ($tipoFirmante === 'paciente') {
            $validatedData = $request->validate([
                'fecha_firma'  => 'required',
                'imagen_firma' => 'required|image|mimes:jpeg,png|max:2048',
                'paciente_id'  => 'required|integer',
                'cita_id'      => 'nullable|integer',
            ]);
        } elseif ($tipoFirmante === 'acompanante') {
            $validatedData = $request->validate([
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
        $nombrePaciente = $paciente
            ? trim($paciente->nombres . ' ' . $paciente->apellidos)
            : 'Paciente sin nombre';

        $doctorRecord = doctores::with('user')->where('user_id', Auth::id())->first();
        $nombreDoctor = $doctorRecord && $doctorRecord->user
            ? trim($doctorRecord->user->nombres . ' ' . $doctorRecord->user->apellidos)
            : (Auth::user()->nombre_completo ?? 'Doctor sin nombre');

        $nombreFirmante = $tipoFirmante === 'paciente'
            ? $nombrePaciente
            : trim($request->nombre_acompanante . ' ' . $request->apellido_acompanante);

        $fechaFormateada = Carbon::createFromFormat('d/m/Y', $request->fecha_firma)->format('Y-m-d');

        // Lógica corregida para recuperar el ID de la plantilla
        $plantillaId = $request->input('plantilla_id');

        // Si no viene en el request, lo buscamos usando la cita
        if (!$plantillaId && $request->cita_id) {
            $cita = Cita::find($request->cita_id);
            if ($cita && $cita->tipo_examen) {
                // Buscamos coincidencia exacta o variantes (con guion bajo o espacios)
                $plantillaEncontrada = PlantillaConsentimiento::where(function($query) use ($cita) {
                    $query->where('tipo', $cita->tipo_examen)
                          ->orWhere('tipo', str_replace(' ', '_', $cita->tipo_examen))
                          ->orWhere('tipo', str_replace('_', ' ', $cita->tipo_examen));
                })->first();

                if ($plantillaEncontrada) {
                    $plantillaId = $plantillaEncontrada->id;
                }
            }
        }

        $consentimiento = ConsentimientoPaciente::create([
            'plantilla_id'    => $plantillaId,
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