<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HistoriaClinica;
use App\Models\PreExamen;
use App\Models\Cita;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class PreExamenController extends Controller
{
    public function create($cita_id)
    {
        $cita = Cita::with('paciente')->findOrFail($cita_id);
        $users = User::where('role', 'admisiones')->get();
        $paciente = $cita->paciente;

        return view('citas.examen', compact('cita', 'users', 'paciente'));
    }

    public function store(Request $request, $cita_id)
    {
        $cita = Cita::findOrFail($cita_id);

        $request->validate([
            'tipo_cita_id' => 'nullable|in:1,2',
            'vision_lejana_od' => 'nullable|numeric|min:0|max:10',
            'vision_lejana_oi' => 'nullable|numeric|min:0|max:10',
            'vision_cercana_od' => 'nullable|numeric|min:0|max:10',
            'vision_cercana_oi' => 'nullable|numeric|min:0|max:10',
            'test_color' => 'nullable|numeric|min:0|max:100',
            'test_profundidad' => 'nullable|numeric|min:0|max:100',
            'motilidad_ocular' => 'nullable|string|max:100',
            'observaciones' => 'nullable|string|max:500',
            'tension_arterial' => 'nullable|string|max:10',
            'frecuencia_cardiaca' => 'nullable|string|max:10',
            'frecuencia_respiratoria' => 'nullable|string|max:10',
            'temperatura' => 'nullable|string|max:10',
            'saturacion' => 'nullable|string|max:10',
            'peso' => 'nullable|string|max:10',
            'examen_fisico' => 'nullable|string|max:500',
            'diagnostico' => 'nullable|string|max:500',
            'antecedentes' => 'nullable|string|max:500',
            'redirect_to_url' => 'required|string',
        ]);

        $usuarioNombre = Auth::user()->nombres . ' ' . Auth::user()->apellidos;

        PreExamen::create([
            'cita_id' => $cita->id,
            'vision_lejana_od' => $request->vision_lejana_od,
            'vision_lejana_oi' => $request->vision_lejana_oi,
            'vision_cercana_od' => $request->vision_cercana_od,
            'vision_cercana_oi' => $request->vision_cercana_oi,
            'test_color' => $request->test_color,
            'test_profundidad' => $request->test_profundidad,
            'motilidad_ocular' => $request->motilidad_ocular,
            'observaciones' => $request->observaciones,
        ]);

        $usuario = Auth::user();

        HistoriaClinica::updateOrCreate(
            ['paciente_id' => $cita->paciente_id],
            [
                'motivo_consulta' => 'Atención en cita médica',
                'antecedentes' => $request->antecedentes,
                'signos_vitales' => [
                    'tension_arterial' => $request->tension_arterial,
                    'frecuencia_cardiaca' => $request->frecuencia_cardiaca,
                    'frecuencia_respiratoria' => $request->frecuencia_respiratoria,
                    'temperatura' => $request->temperatura,
                    'saturacion' => $request->saturacion,
                    'peso' => $request->peso,
                ],
                'diagnostico' => $request->diagnostico ?? 'Pendiente por evaluación',
                'conducta' => 'Por definir',
                'created_by' => $usuario->nombres . ' ' . $usuario->apellidos,
                'updated_by' => $usuario->nombres . ' ' . $usuario->apellidos,
            ]
        );

        if ($request->tipo_cita_id && !$cita->tipo_cita_id) {
            $cita->tipo_cita_id = $request->tipo_cita_id;
        }

        $cita->estado = 'asistida';
        $cita->updated_by = $usuarioNombre;
        $cita->save();
        
        return view('citas.examen', compact('cita'));
    }

    public function examen($id)
    {
        $cita = Cita::findOrFail($id);
        $cita->update([
            'estado' => 'asistida',
            'updated_by' => Auth::user()->nombres . ' ' . Auth::user()->apellidos,
        ]);

        return view('citas.examen', compact('cita'));
    }
}
