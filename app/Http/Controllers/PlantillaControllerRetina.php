<?php

namespace App\Http\Controllers;

use App\Models\Retina;
use Illuminate\Http\Request;
use App\Models\Cita;
use App\Models\User;
use Illuminate\Support\Facades\Storage;


class PlantillaControllerRetina extends Controller
{
    public function index(Cita $cita)
    {
        $retinas = Retina::all();
        $citas = Cita::with('paciente')->orderBy('fecha', 'desc')->get();
        return view('plantillas.retina', compact('retinas', 'citas', 'cita'));
    }


    public function store(Request $request, Cita $cita)
    {
        $data = $request->validate([
            'diagnostico' => 'nullable|string',
            'tratamiento' => 'nullable|string',
            'observaciones' => 'nullable|string',
            'imagen_ojo_izquierdo' => 'nullable|image',
            'imagen_ojo_derecho' => 'nullable|image',
            'imagen_editada_izq' => 'nullable|string',
            'imagen_editada_der' => 'nullable|string',
        ]);

        $rutaIzq = $request->file('imagen_ojo_izquierdo')?->store('retina_originales', 'public');
        $rutaDer = $request->file('imagen_ojo_derecho')?->store('retina_originales', 'public');

        $editadaIzq = null;
        $editadaDer = null;

        if ($request->imagen_editada_izq) {
            $editadaIzq = 'retina_editadas/izq_' . time() . '.png';
            Storage::disk('public')->put($editadaIzq, base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $request->imagen_editada_izq)));
        }

        if ($request->imagen_editada_der) {
            $editadaDer = 'retina_editadas/der_' . time() . '.png';
            Storage::disk('public')->put($editadaDer, base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $request->imagen_editada_der)));
        }

        Retina::create([
            'cita_id' => $cita->id,
            'diagnostico' => $data['diagnostico'] ?? '',
            'tratamiento' => $data['tratamiento'] ?? '',
            'observaciones' => $data['observaciones'] ?? '',
            'imagen_ojo_izquierdo' => $rutaIzq,
            'imagen_ojo_derecho' => $rutaDer,
            'imagen_editada_izq' => $editadaIzq,
            'imagen_editada_der' => $editadaDer,
        ]);

        return redirect()->route('citas.index')->with('success', 'Consulta de retina guardada correctamente.');
    }

    public function edit(Cita $cita)
    {
        $cita->load(['paciente']);
        $users = User::all();

        if ($cita->tipo_cita_id != 3) {
            return redirect()->back()->with('error', 'Esta cita no corresponde a retina.');
        }

        $plantilla = Retina::where('cita_id', $cita->id)->first();

        return view('historias.retina_edit', compact('plantilla', 'cita', 'users'));
    }

    public function update(Request $request, Cita $cita)
    {
        $request->validate([
            'diagnostico' => 'nullable|string|max:255',
            'tratamiento' => 'nullable|string|max:255',
            'observaciones' => 'nullable|string',
            'imagen_ojo_izquierdo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'imagen_ojo_derecho' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $plantilla = Retina::where('cita_id', $cita->id)->first();

        $imagenIzquierda = $plantilla->imagen_ojo_izquierdo ?? null;
        $imagenDerecha = $plantilla->imagen_ojo_derecho ?? null;

        if ($request->hasFile('imagen_ojo_izquierdo')) {
            $imagenIzquierda = $request->file('imagen_ojo_izquierdo')->store('imagenes_retina', 'public');
        }
        if ($request->hasFile('imagen_ojo_derecho')) {
            $imagenDerecha = $request->file('imagen_ojo_derecho')->store('imagenes_retina', 'public');
        }

        $data = $request->all();
        $data['imagen_ojo_izquierdo'] = $imagenIzquierda;
        $data['imagen_ojo_derecho'] = $imagenDerecha;

        if ($plantilla) {
            $plantilla->update($data);
        } else {
            Retina::create(array_merge(
                $data,
                ['cita_id' => $cita->id]
            ));
        }

        return redirect()->route('historias.index', $cita->id)
            ->with('success', 'Registro de retina actualizado correctamente.');
    }

    public function destroy($id)
    {
        $retina = Retina::findOrFail($id);
        $retina->delete();
        return response()->json(['message' => 'Registro eliminado correctamente']);
    }
}
