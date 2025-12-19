<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Paciente;
use App\Models\Doctores;
use App\Models\Cita;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class LegacyPacienteController extends Controller
{
    public function index()
    {
        $doctores = Doctores::all();
        $convenios = DB::table('convenios')
            ->where('activo', true)
            ->orderBy('nombre', 'asc')
            ->get();

        return view('pacientes.contenedor-legacy', compact('doctores', 'convenios'));
    }

    public function getPlanes($convenioId)
    {
        $planes = DB::table('planes')
            ->where('convenio_id', $convenioId)
            ->orderBy('nombre', 'asc')
            ->get();

        return response()->json($planes);
    }

    public function buscar(Request $request)
    {
        $search = $request->query('search');

        if (!$search) {
            return response()->json([]);
        }

        $pacientes = Paciente::where('documento', 'LIKE', "%{$search}%")
            ->orWhere('nombres', 'LIKE', "%{$search}%")
            ->orWhere('apellidos', 'LIKE', "%{$search}%")
            ->orWhere('telefono', 'LIKE', "%{$search}%")
            ->get();

        return response()->json($pacientes);
    }

   public function store(Request $request)
    {
        $request->validate([
            'txt_numero_documento' => 'required|unique:pacientes,documento',
            'txt_nombre_1' => 'required',
            'txt_apellido_1' => 'required',
            'cmb_tipo_documento' => 'required',
        ]);

        $nombres = trim($request->input('txt_nombre_1') . ' ' . $request->input('txt_nombre_2'));
        $apellidos = trim($request->input('txt_apellido_1') . ' ' . $request->input('txt_apellido_2'));
        
        $sexo = null;
        if($request->input('cmb_sexo') == '1') $sexo = 'F';
        if($request->input('cmb_sexo') == '2') $sexo = 'M';

        $fechaNacimiento = $request->input('txt_fecha_nacimiento') ?: null;
        $convenioId = $request->input('cmb_convenio') ?: null;
        $planId = $request->input('cmb_plan') ?: null;
        $paisNac = $request->input('cmb_pais_nac') ?: null;
        $paisRes = $request->input('cmb_pais_res') ?: null;

        $user = Auth::user();
        $creador = ($user->nombres ?? $user->name ?? 'Usuario') . ' ' . ($user->apellidos ?? '');

        $parametros = [
            $request->input('cmb_tipo_documento'),
            $request->input('txt_numero_documento'),
            $nombres,
            $apellidos,
            $fechaNacimiento,
            $sexo,
            $paisNac,
            $request->input('txt_telefono'),
            $paisRes,
            $request->input('txt_direccion'),
            $request->input('txt_email'),
            $convenioId,
            $planId,
            $request->input('cmb_rango'),
            $request->input('cmb_tipoUsuario'),
            $request->input('txt_observ_paciente'),
            trim($creador)
        ];

        DB::statement('CALL sp_crear_paciente_legacy(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', $parametros);

        return redirect()->route('legacy.index')->with('success', 'Paciente guardado correctamente.');
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
