<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Paciente;
use App\Models\Doctores;
use App\Models\Cita;

class LegacyPacienteController extends Controller
{
    public function index()
    {
        $doctores = Doctores::all();
        return view('pacientes.contenedor-legacy', compact('doctores'));
    }

    public function buscar(Request $request)
    {
        $search = $request->query('search');
        
        $pacientes = Paciente::where('nombre', 'LIKE', "%{$search}%")
            ->orWhere('documento', 'LIKE', "%{$search}%")
            ->get(['id', 'documento', 'nombre']);

        return response()->json($pacientes);
    }

    public function agendar(Request $request)
    {
        $request->validate([
            'paciente_id' => 'required',
            'doctor_id' => 'required',
            'fecha' => 'required|date',
            'hora' => 'required',
        ]);

        try {
            Cita::create([
                'paciente_id' => $request->paciente_id,
                'doctor_id' => $request->doctor_id,
                'fecha' => $request->fecha,
                'hora' => $request->hora,
                'estado' => 'programada'
            ]);

            return response()->json(['mensaje' => 'Cita agendada correctamente']);
        } catch (\Exception $e) {
            return response()->json(['detalle' => $e->getMessage()], 500);
        }
    }
}