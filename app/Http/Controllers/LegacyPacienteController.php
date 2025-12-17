<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Doctores;
use Exception;

class LegacyPacienteController extends Controller
{
    public function index()
    {
        $doctores = Doctores::all();
        return view('pacientes.contenedor-legacy', compact('doctores'));
    }

    public function buscar(Request $request)
    {
        $termino = $request->input('search');
        $pacientes = DB::select('CALL sp_buscar_pacientes(?)', [$termino]);
        return response()->json($pacientes);
    }

    public function agendar(Request $request)
    {
        $request->validate([
            'paciente_id' => 'required',
            'doctor_id'   => 'required',
            'fecha'       => 'required|date',
            'hora'        => 'required',
        ]);

        try {
            DB::statement('CALL sp_registrar_cita(?, ?, ?, ?)', [
                $request->paciente_id,
                $request->doctor_id,
                $request->fecha,
                $request->hora
            ]);

            return response()->json([
                'mensaje' => 'Cita registrada correctamente.',
                'datos' => $request->all()
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'error' => 'No se pudo registrar la cita',
                'detalle' => $e->getMessage()
            ], 400);
        }
    }
}