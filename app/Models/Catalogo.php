<?php

namespace App\Models;

class Catalogo
{
    public static function buscar($termino)
    {
        $diagnosticos = DiagnosticoOftalmologico::where('nombre', 'like', "%$termino%")
            ->orWhere('codigo', 'like', "%$termino%")
            ->take(10)->get();

        $procedimientos = ProcedimientoOftalmologico::where('nombre', 'like', "%$termino%")
            ->orWhere('codigo', 'like', "%$termino%")
            ->take(10)->get();

        $alergias = Alergia::where('nombre', 'like', "%$termino%")
            ->take(10)->get();

        return [
            'diagnosticos' => $diagnosticos,
            'procedimientos' => $procedimientos,
            'alergias' => $alergias,
        ];
    }
}
