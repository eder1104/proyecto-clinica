<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
            'tipo_cita_id'       => 'required|in:1,2',
            'vision_lejana_od'   => 'nullable|numeric|min:0|max:10',
            'vision_lejana_oi'   => 'nullable|numeric|min:0|max:10',
            'vision_cercana_od'  => 'nullable|numeric|min:0|max:10',
            'vision_cercana_oi'  => 'nullable|numeric|min:0|max:10',
            'test_color'         => 'nullable|numeric|min:0|max:100',
            'test_profundidad'   => 'nullable|numeric|min:0|max:100',
            'motilidad_ocular'   => 'nullable|string|max:100',
            'observaciones'      => 'nullable|string|max:500',
        ], [
            'tipo_cita_id.required' => 'Debe seleccionar un tipo de cita.',
            'tipo_cita_id.in'       => 'Tipo de cita no válido.',
            'vision_lejana_od.required' => 'Debe ingresar la visión lejana del ojo derecho.',
            'vision_lejana_od.numeric'  => 'El valor debe ser numérico.',
            'vision_lejana_od.max'      => 'El valor máximo permitido es 10.',
            'vision_lejana_oi.max'      => 'El valor máximo permitido es 10.',
            'vision_cercana_od.max'     => 'El valor máximo permitido es 10.',
            'vision_cercana_oi.max'     => 'El valor máximo permitido es 10.',
            'test_color.max'            => 'El test de color no debe superar 100.',
            'test_profundidad.max'      => 'El test de profundidad no debe superar 100.',
        ]);

        PreExamen::create([
            'cita_id'            => $cita->id,
            'vision_lejana_od'   => $request->vision_lejana_od,
            'vision_lejana_oi'   => $request->vision_lejana_oi,
            'vision_cercana_od'  => $request->vision_cercana_od,
            'vision_cercana_oi'  => $request->vision_cercana_oi,
            'test_color'         => $request->test_color,
            'test_profundidad'   => $request->test_profundidad,
            'motilidad_ocular'   => $request->motilidad_ocular,
            'observaciones'      => $request->observaciones,
        ]);

        $cita->update([
            'tipo_cita_id' => $request->tipo_cita_id,
            'estado'       => 'asistida',
            'updated_by'   => Auth::id(),
        ]);

        if ($cita->tipo_cita_id == 1) {
            return redirect()->route('plantillas.optometria', ['cita' => $cita->id]);
        } elseif ($cita->tipo_cita_id == 2) {
            return redirect()->route('examenes.edit', ['cita' => $cita->id]);
        }

        return redirect()->back()->with('error', 'Tipo de cita no válido.');
    }

    public function examen($id)
    {
        $cita = Cita::findOrFail($id);

        $cita->update([
            'estado'     => 'asistida',
            'updated_by' => Auth::id(),
        ]);

        return view('citas.examen', compact('cita'));
    }
}
