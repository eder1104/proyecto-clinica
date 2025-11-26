<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DoctorParcialidad;
use App\Models\Doctores;
use App\Http\Controllers\BitacoraAuditoriaController;
use App\Http\Requests\ParcialRequest;
use Illuminate\Support\Facades\Auth;

class CitasParcialController extends Controller
{
    public function index($doctorId, $fecha)
    {
        $user_id = $doctorId;

        $doctorProfile = Doctores::where('user_id', $user_id)->first();
        $doctor_table_id = $doctorProfile ? $doctorProfile->id : null;

        $horas = [];
        for ($i = 0; $i < 24; $i++) {
            $hora = str_pad($i, 2, '0', STR_PAD_LEFT) . ':00';
            $horas[] = [
                'hora' => $hora,
                'estado' => 'activo',
            ];
        }

        $parcialidades_guardadas = DoctorParcialidad::where('doctor_id', $doctor_table_id)
            ->where('fecha', $fecha)
            ->get();

        return view('citas.parcial', [
            'horas' => $horas,
            'dia' => $fecha,
            'doctorId' => $user_id,
            'parcialidades_guardadas' => $parcialidades_guardadas
        ]);
    }

    public function store(ParcialRequest $request)
    {
        $validated = $request->validated();

        $user_id = $validated['doctor_id'];

        $doctorProfile = Doctores::where('user_id', $user_id)->firstOrFail();

        $datosParaGuardar = $validated;
        $datosParaGuardar['doctor_id'] = $doctorProfile->id;

        $parcialidad = new DoctorParcialidad($datosParaGuardar);
        $parcialidad->save();

        $observacion = "Creación de rango parcial para la fecha {$validated['fecha']} de {$validated['hora_inicio']} a {$validated['hora_fin']}.";
        $datosBitacora = array_merge($validated, ['observacion' => $observacion]);

        BitacoraAuditoriaController::registrar(
            Auth::id(),
            'parcialidad',
            'crear',
            $parcialidad->id,
            $datosBitacora
        );

        return redirect()->route('citas.parcial', [
            'doctorId' => $validated['doctor_id'],
            'fecha' => $validated['fecha']
        ])->with('success', 'Rango de disponibilidad añadido correctamente.');
    }

    public function destroy(DoctorParcialidad $doctorParcialidad)
    {
        $datosEliminados = [
            'fecha' => $doctorParcialidad->fecha,
            'hora_inicio' => $doctorParcialidad->hora_inicio,
            'hora_fin' => $doctorParcialidad->hora_fin,
        ];

        $observacion = "Eliminación de rango parcial para la fecha {$doctorParcialidad->fecha} de {$doctorParcialidad->hora_inicio} a {$doctorParcialidad->hora_fin}.";
        $datosBitacora = array_merge($datosEliminados, ['observacion' => $observacion]);

        $idEliminado = $doctorParcialidad->id;
        $doctorParcialidad->delete();

        BitacoraAuditoriaController::registrar(
            Auth::id(),
            'parcialidad',
            'eliminar',
            $idEliminado,
            $datosBitacora
        );

        return back()->with('success', 'Rango eliminado.');
    }
}