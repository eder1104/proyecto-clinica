<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class DoctorAgendaController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $doctores = User::where('role', 'doctor')
            ->with('doctor')
            ->when($search, function ($query) use ($search) {
                $query->whereHas('doctor', function ($q) use ($search) {
                    $q->where('documento', 'like', "%{$search}%");
                });
            })
            ->paginate(10)
            ->appends($request->query());

        return view('citas.DoctorAgenda', compact('doctores', 'search'));
    }
}
