<?php

namespace App\Http\Controllers;

use App\Models\Alergia;
use App\Models\DiagnosticoOftalmologico;
use App\Models\ProcedimientoOftalmologico;
use Illuminate\Http\Request;

class CatalogoController extends Controller
{
    public function modal(Request $request)
    {
        $tipo = $request->query('tipo', 'diagnostico');
        $titulo = $request->query('titulo', null);
        $nombre_input = $request->query('nombre_input', 'catalogo_ids');

        return view('citas.BusquedaCatalogoModal', compact('tipo', 'titulo', 'nombre_input'))
            ->with('ocultarMenu', true);
    }

    public function buscar(Request $request)
    {
        $termino = $request->input('termino');
        $resultados = collect();

        if (empty($termino)) {
            return response()->json($resultados);
        }

        $diagnosticos = DiagnosticoOftalmologico::where('nombre', 'like', '%' . $termino . '%')
            ->select('id', 'nombre', 'codigo')
            ->get()
            ->map(function ($item) {
                $item->tipo = 'Diagnóstico';
                return $item;
            });

        $procedimientos = ProcedimientoOftalmologico::where('nombre', 'like', '%' . $termino . '%')
            ->select('id', 'nombre', 'codigo')
            ->get()
            ->map(function ($item) {
                $item->tipo = 'Procedimiento';
                return $item;
            });

        $alergias = Alergia::where('nombre', 'like', '%' . $termino . '%')
            ->select('id', 'nombre')
            ->get()
            ->map(function ($item) {
                $item->tipo = 'Alergia';
                $item->codigo = null;
                return $item;
            });

        $resultados = $diagnosticos
            ->concat($procedimientos)
            ->concat($alergias)
            ->values();

        return response()->json($resultados);
    }
}
