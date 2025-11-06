<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class DoctorAgendaController extends Controller
{
    public function index()
    {
        $doctores = User::where('role', 'doctor')->get();
        return view('citas.DoctorAgenda', compact('doctores'));
    }
}
