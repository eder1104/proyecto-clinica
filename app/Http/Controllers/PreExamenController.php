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
            'tipo_cita_id' => 'required|in:1,2',
        ], [
            'tipo_cita_id.required' => 'Debe seleccionar un tipo de cita.',
            'tipo_cita_id.in'       => 'Tipo de cita no válido.',
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
