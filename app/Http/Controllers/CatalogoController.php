<?php

namespace App\Http\Controllers;

use App\Models\Alergia;
use App\Models\DiagnosticoOftalmologico;
use App\Models\ProcedimientoOftalmologico;
use Illuminate\Http\Request;

class CatalogoController extends Controller
{
    public function buscar(Request $request)
    {
        $tipo = $request->input('tipo');
        $termino = $request->input('termino');
        $resultados = collect();

        if (empty($termino)) {
            return response()->json($resultados);
        }

        switch ($tipo) {
            case 'diagnostico':
                $modelo = DiagnosticoOftalmologico::class;
                break;
            case 'procedimiento':
                $modelo = ProcedimientoOftalmologico::class;
                break;
            case 'alergia':
                $modelo = Alergia::class;
                break;
            default:
                return response()->json($resultados);
        }

        $resultados = $modelo::where('nombre', 'like', '%' . $termino . '%')
            ->orWhere('codigo', 'like', '%' . $termino . '%')
            ->select('id', 'nombre', 'codigo')
            ->limit(15) 
            ->get();

        return response()->json($resultados);
    }
 }
