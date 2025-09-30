<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Paciente;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class CitaController extends Controller
{
    public function index(Request $request)
    {
        $query = Cita::with(['paciente', 'admisiones', 'createdBy', 'updatedBy', 'cancelledBy']);

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('fecha')) {
            $query->whereDate('fecha', $request->fecha);
        }

        $citas = $query->get();

        return view('citas.index', compact('citas'));
    }

    public function create()
    {
        $pacientes = Paciente::all();
        $admisiones = User::all();
        return view('citas.create', compact('pacientes', 'admisiones'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'numero_fuente'   => 'nullable|string|max:255',
            'fecha'           => 'required|date',
            'hora_inicio'     => 'required|date_format:H:i',
            'hora_fin'        => 'required|date_format:H:i|after:hora_inicio',
            'mensaje'         => 'nullable|string',
            'estado'          => 'required|string',
            'paciente_id'     => 'required|exists:pacientes,id',
            'admisiones_id'   => 'required|exists:users,id',
            'motivo_consulta' => 'nullable|string|max:1000',
        ]);

        $validated['created_by'] = Auth::id();

        $cita = Cita::create($validated);

        $this->generarPDF($cita);

        return redirect()->route('citas.index')->with('success', 'Cita creada correctamente.');
    }

    public function edit(Cita $cita)
    {
        $pacientes = Paciente::all();
        $admisiones = User::all();
        return view('citas.edit', compact('cita', 'pacientes', 'admisiones'));
    }

    public function update(Request $request, Cita $cita)
    {
        $validated = $request->validate([
            'numero_fuente'   => 'nullable|string|max:255',
            'fecha'           => 'required|date',
            'hora_inicio'     => 'sometimes|required',
            'hora_fin'        => 'sometimes|required|after:hora_inicio',
            'mensaje'         => 'nullable|string',
            'estado'          => 'required|string',
            'paciente_id'     => 'required|exists:pacientes,id',
            'admisiones_id'   => 'required|exists:users,id',
            'motivo_consulta' => 'nullable|string|max:1000',
        ]);

        $validated['updated_by'] = Auth::id();

        $cita->update($validated);

        $this->generarPDF($cita);

        return redirect()->route('citas.index')->with('success', 'Cita actualizada correctamente.');
    }

    public function updateMotivo(Request $request, Cita $cita)
    {
        $validated = $request->validate([
            'motivo_consulta' => 'nullable|string|max:1000',
        ]);

        $validated['updated_by'] = Auth::id();

        $cita->update($validated);

        $this->generarPDF($cita);

        return back()->with('success', 'Motivo de consulta guardado correctamente.');
    }

    public function destroy(Request $request, Cita $cita)
    {
        $request->validate([
            'delete_reason' => 'required|string|max:500',
        ]);

        $cita->update([
            'estado'        => 'cancelada',
            'cancel_reason' => $request->delete_reason,
            'cancelled_by'  => Auth::id(),
        ]);

        $this->generarPDF($cita);

        return redirect()->route('citas.index')->with('success', 'Cita cancelada correctamente.');
    }

    public function cancelar(Request $request, Cita $cita)
    {
        $validated = $request->validate([
            'cancel_reason' => 'required|string|max:500',
        ]);

        $cita->update([
            'estado'        => 'cancelada',
            'cancel_reason' => $validated['cancel_reason'],
            'cancelled_by'  => Auth::id(),
        ]);

        $this->generarPDF($cita);

        return redirect()->route('citas.index')->with('success', 'Cita cancelada correctamente.');
    }

    public function atencion(Cita $cita)
    {
        $cita->load(['paciente', 'admisiones']);
        return view('citas.cita', compact('cita'));
    }

    private function generarPDF(Cita $cita)
    {
        $pdf = Pdf::loadView('citas.pdf', compact('cita'));
        $fileName = 'citas/cita_' . $cita->id . '.pdf';

        Storage::disk('public')->put($fileName, $pdf->output());

        $cita->update([
            'pdf_path' => $fileName
        ]);
    }
}
