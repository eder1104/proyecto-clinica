<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Paciente;
use App\Models\doctores;
use App\Models\Cita;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class LegacyPacienteController extends Controller
{
    public function index()
    {
        $doctores = doctores::all();
        $convenios = DB::table('convenios')
            ->where('activo', true)
            ->get();

        return view('pacientes.contenedor-legacy', compact('doctores', 'convenios'));
    }

    public function getPlanes($convenioId)
    {
        $planes = DB::table('planes')
            ->where('convenio_id', $convenioId)
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

        $sexo = $request->input('cmb_sexo') == '1' ? 'F' : ($request->input('cmb_sexo') == '2' ? 'M' : null);

        $user = Auth::user();
        $creador = ($user->nombres ?? $user->name ?? 'Usuario') . ' ' . ($user->apellidos ?? '');

        // Corrección: Nombres de inputs coinciden con la vista (txt_estado_residencia, etc.)
        $parametros = [
            $request->input('cmb_tipo_documento'),
            $request->input('txt_numero_documento'),
            $nombres,
            $apellidos,
            $request->input('txt_fecha_nacimiento'),
            $sexo,
            $request->input('cmb_pais_nac'),
            $request->input('txt_telefono'),
            $request->input('cmb_pais_res'),
            $request->input('txt_estado_residencia'),
            $request->input('txt_municipio_residencia'),
            $request->input('cmb_zona_residencia'),
            $request->input('txt_direccion'),
            $request->input('txt_email'),
            $request->input('cmb_convenio'),
            $request->input('cmb_plan'),
            $request->input('cmb_rango'),
            $request->input('cmb_tipoUsuario'),
            $request->input('cmb_estado_aseguradora'),
            $request->input('cmb_exento_cuota'),
            $request->input('txt_observ_paciente'),
            trim($creador)
        ];

        DB::statement('CALL sp_crear_paciente_legacy(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', $parametros);

        return redirect()->route('legacy.index')->with('success', 'Paciente guardado correctamente.');
    }

    public function importarCSV(Request $request)
    {
        try {
            $request->validate([
                'fileISS' => 'required|file',
                'cmb_plan' => 'required'
            ]);

            $file = $request->file('fileISS');
            $handle = fopen($file->getRealPath(), 'r');

            fgetcsv($handle, 1000, ";");

            $primeraFilaData = fgetcsv($handle, 1000, ";");
            $nombreConvenio = isset($primeraFilaData[0]) ? trim($primeraFilaData[0]) : null;

            if (!$nombreConvenio) {
                fclose($handle);
                return response()->json(['res' => 0, 'error' => 'No se encontró el nombre del convenio'], 422);
            }

            $convenio = DB::table('convenios')
                ->where('nombre', 'LIKE', $nombreConvenio)
                ->first();

            if (!$convenio) {
                fclose($handle);
                return response()->json(['res' => 0, 'error' => 'El convenio no existe'], 422);
            }

            $planId = $request->input('cmb_plan');
            $user = Auth::user();
            $creador = ($user->nombres ?? $user->name ?? 'Sistema') . ' (Importación)';
            $insertados = 0;

            $procesarFila = function ($datos) use ($convenio, $planId, $creador, &$insertados) {
                if (empty($datos[2])) return;

                $exento = isset($datos[14]) && strtolower($datos[14]) == 'si' ? 1 : 0;

                $parametros = [
                    $datos[1],
                    $datos[2],
                    $datos[3],
                    $datos[4],
                    $datos[6] ?? '1900-01-01',
                    $datos[5] ?? null,
                    1,
                    $datos[7] ?? '0',
                    1,
                    $datos[10] ?? null,
                    $datos[11] ?? null,
                    $datos[12] ?? null,
                    $datos[8] ?? 'Sin dirección',
                    $datos[9] ?? 'sin@correo.com',
                    $convenio->id,
                    $planId,
                    0,
                    620,
                    $datos[13] ?? 'Activo',
                    $exento,
                    'Importado masivamente via CSV',
                    $creador
                ];

                DB::statement('CALL sp_crear_paciente_legacy(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', $parametros);
                $insertados++;
            };

            $procesarFila($primeraFilaData);

            while (($datos = fgetcsv($handle, 1000, ";")) !== FALSE) {
                $procesarFila($datos);
            }

            fclose($handle);

            return response()->json([
                'res' => 1,
                'mensaje' => "Importación exitosa. Total: {$insertados} pacientes registrados."
            ]);
        } catch (\Exception $e) {
            return response()->json(['res' => 0, 'error' => $e->getMessage()], 500);
        }
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
                'hora_inicio' => $request->hora,
                'estado' => 'programada'
            ]);

            return response()->json(['mensaje' => 'Cita agendada correctamente']);
        } catch (\Exception $e) {
            return response()->json(['detalle' => $e->getMessage()], 500);
        }
    }
}
