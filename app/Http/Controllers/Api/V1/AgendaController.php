<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cita;
use Carbon\Carbon;

class AgendaController extends Controller
{
    public function disponibilidad(Request $request)
    {
        return response()->json(['mensaje' => 'Endpoint de disponibilidad listo']);
    }

    public function index(Request $request)
    {
        return response()->json(['mensaje' => 'Endpoint de listado listo']);
    }

    public function store(Request $request)
    {
        return response()->json(['mensaje' => 'Endpoint de creación listo']);
    }

    public function cancelar($id, Request $request)
    {
        return response()->json(['mensaje' => 'Endpoint de cancelación listo']);
    }
}