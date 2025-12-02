<?php

namespace App\Http\Controllers;

use App\Models\BitacoraAuditoria;
use Illuminate\Http\Request;

class BitacoraAuditoriaController extends Controller
{
    public function index()
    {
        $bitacoras = BitacoraAuditoria::with(['usuario', 'historialCambios'])
            ->orderByDesc('fecha_hora')
            ->paginate(20);

        return view('citas.bitacora', compact('bitacoras'));
    }
}