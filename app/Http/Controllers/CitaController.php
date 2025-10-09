<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Paciente;
use App\Models\User;
use App\Models\Plantilla_Optometria;
use App\Models\TipoCita;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class CitaController extends Controller
{
    public function index(Request $request)
    {
        $query = Cita::with(['paciente', 'admisiones', 'createdBy', 'updatedBy', 'cancelledBy', 'TipoCita']);

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
        $optometria = Plantilla_Optometria::all();
        $tiposCitas = TipoCita::all();
        return view('citas.create', compact('pacientes', 'admisiones', 'optometria', 'tiposCitas'));
    }

    public function store(Request $request)
    {
        $consultaCompleta = $request->input('consulta_completa', 0);

        $rules = [
            'fecha'                   => 'required|date',
            'hora_inicio'             => 'required|date_format:H:i',
            'hora_fin'                => 'required|date_format:H:i|after:hora_inicio',
            'estado'                  => 'required|string',
            'paciente_id'             => 'required|exists:pacientes,id',
            'admisiones_id'           => 'required|exists:users,id',
            'motivo_consulta'         => 'nullable|string|max:1000',
            'tension_arterial'        => 'nullable|string|max:20',
            'frecuencia_cardiaca'     => 'nullable|string|max:20',
            'frecuencia_respiratoria' => 'nullable|string|max:20',
            'temperatura'             => 'nullable|string|max:20',
            'saturacion'              => 'nullable|string|max:20',
            'peso'                    => 'nullable|string|max:20',
            'examen_fisico'           => 'nullable|string',
            'diagnostico'             => 'nullable|string|max:2000',
            'plantilla_id'            => 'nullable|exists:plantilla_optometria,id',
            'tipo_cita_id'            => 'nullable|exists:tipos_citas,id',
        ];

        $validated = $consultaCompleta ? $request->all() : $request->validate($rules);

        $validated['consulta_completa'] = $consultaCompleta ? 1 : 0;
        $validated['created_by'] = Auth::id();

        $cita = Cita::create($validated);

        if ($cita->tipo_cita_id == 1) {
            Plantilla_Optometria::create([
                'cita_id' => $cita->id,
                'optometra' => Auth::user()->name ?? 'Desconocido',
            ]);
        }

        $this->generarPDF($cita);

        if ($cita->tipo_cita_id == 1) {
            return redirect()->route('optometria.show', ['cita_id' => $cita->id]);
        } elseif ($cita->tipo_cita_id == 2) {
            return redirect()->route('citas.examen', ['cita' => $cita->id]);
        }

        return redirect()->route('citas.index')->with('success', 'Cita creada correctamente.');
    }

    public function edit(Cita $cita)
    {
        $pacientes = Paciente::all();
        $admisiones = User::all();
        $optometria = Plantilla_Optometria::all();
        $tiposCitas = TipoCita::all();
        return view('citas.edit', compact('cita', 'pacientes', 'admisiones', 'optometria', 'tiposCitas'));
    }

    public function update(Request $request, Cita $cita)
    {
        if ($cita->estado === 'finalizada') {
            return redirect()->route('citas.index')->with('error', 'La cita ya fue finalizada y no se puede modificar.');
        }

        $consultaCompleta = $request->input('consulta_completa', 0);

        $rules = [
            'fecha'                   => 'required|date',
            'hora_inicio'             => 'required|date_format:H:i',
            'hora_fin'                => 'required|date_format:H:i|after:hora_inicio',
            'estado'                  => 'required|string|in:programada,cancelada,finalizada',
            'paciente_id'             => 'required|exists:pacientes,id',
            'admisiones_id'           => 'required|exists:users,id',
            'motivo_consulta'         => 'nullable|string|max:1000',
            'tension_arterial'        => 'nullable|string|max:20',
            'frecuencia_cardiaca'     => 'nullable|string|max:20',
            'observaciones'           => 'nullable|string|max:2000',
            'frecuencia_respiratoria' => 'nullable|string|max:20',
            'temperatura'             => 'nullable|string|max:20',
            'saturacion'              => 'nullable|string|max:20',
            'peso'                    => 'nullable|string|max:20',
            'examen_fisico'           => 'nullable|string',
            'diagnostico'             => 'nullable|string|max:2000',
            'plantilla_id'            => 'nullable|exists:plantilla_optometria,id',
            'tipo_cita_id'            => 'nullable|exists:tipos_citas,id',
        ];

        $validated = $consultaCompleta ? $request->all() : $request->validate($rules);

        $validated['consulta_completa'] = $consultaCompleta ? 1 : 0;
        $validated['updated_by'] = Auth::id();

        $cita->update([
            'estado'        => 'finalizada'
        ]);

        $this->generarPDF($cita);

        if ($cita->tipo_cita_id == 1) {
            return redirect()->route('optometria.show', ['cita_id' => $cita->id]);
        } elseif ($cita->tipo_cita_id == 2) {
            return redirect()->route('citas.examen', ['cita' => $cita->id]);
        }

        return redirect()->route('citas.index')->with('success', 'Cita creada correctamente.');
    }

    public function destroy(Cita $cita, Request $request)
    {
        $motivo = $request->input('delete_reason');

        if (!$motivo) {
            return redirect()->route('citas.index')->with('error', 'Debe ingresar una razón para cancelar la cita.');
        }

        $cita->update([
            'estado'        => 'cancelada',
            'cancelled_by'  => Auth::id(),
            'cancel_reason' => $motivo,
        ]);

        $this->generarPDF($cita);

        return redirect()->route('citas.index')->with('success', 'Cita cancelada correctamente.');
    }


    public function cancelar(Cita $cita)
    {
        $cita->update([
            'estado'       => 'cancelada',
            'cancelled_by' => Auth::id(),
        ]);

        $this->generarPDF($cita);

        return redirect()->route('citas.index')->with('success', 'Cita cancelada correctamente.');
    }

    public function atencion(Cita $cita)
    {
        $cita->load(['paciente', 'admisiones', 'TipoCita']);
        $plantillaView = $this->obtenerPlantillaPorTipo($cita->tipo_cita_id);
        $users = User::all();
        return view('citas.cita', compact('cita', 'plantillaView', 'users'));
    }

    public function pdf(Cita $cita)
    {
        if ($cita->pdf_path && Storage::disk('public')->exists($cita->pdf_path)) {
            return response()->download(storage_path('app/public/' . $cita->pdf_path));
        }

        $pdf = Pdf::loadView('citas.pdf', compact('cita'));
        return $pdf->download('cita_' . $cita->id . '.pdf');
    }

    private function generarPDF(Cita $cita)
    {
        $pdf = Pdf::loadView('citas.pdf', compact('cita'));

        $path = 'pdfs/citas/cita_' . $cita->id . '.pdf';
        Storage::disk('public')->put($path, $pdf->output());

        $cita->update([
            'pdf_path' => $path
        ]);
    }

    public function finalizar(Cita $cita)
    {
        $cita->update([
            'estado'     => 'finalizada',
            'updated_by' => Auth::id(),
        ]);

        $this->generarPDF($cita);

        return redirect()->route('citas.index')->with('success', 'Cita finalizada correctamente.');
    }

    public function descargarHistoriaPdf($id)
    {
        $paciente = Paciente::findOrFail($id);

        $historias = Cita::where('paciente_id', $id)
            ->where('estado', 'finalizada')
            ->get();

        if ($historias->isEmpty()) {
            return redirect()->back()->with('error', 'El paciente no tiene historias clínicas registradas.');
        }

        $pdf = Pdf::loadView('pdf.historia_paciente', compact('paciente', 'historias'));

        return $pdf->download('historia_clinica_' . $paciente->nombres . '_' . $paciente->apellidos . '.pdf');
    }

    public function ModalPaciente()
    {
        return view('citas.ModalPaciente');
    }

    public function guardarExamen(Request $request, $id)
    {
        $cita = Cita::findOrFail($id);

        $cita->tension_arterial        = $request->tension_arterial;
        $cita->frecuencia_cardiaca     = $request->frecuencia_cardiaca;
        $cita->frecuencia_respiratoria = $request->frecuencia_respiratoria;
        $cita->temperatura             = $request->temperatura;
        $cita->saturacion              = $request->saturacion;
        $cita->peso                    = $request->peso;
        $cita->examen_fisico           = $request->examen_fisico;
        $cita->diagnostico             = $request->diagnostico;

        if ($request->filled('tipo_cita_id')) {
            $cita->tipo_cita_id = $request->tipo_cita_id;
        }

        $cita->save();

        return redirect()->route('citas.atencion', ['cita' => $cita->id])
            ->with('success', 'Examen guardado correctamente.');
    }


    public function examen($id)
    {
        $cita = Cita::findOrFail($id);
        return view('citas.examen', compact('cita'));
    }

    public function obtenerPlantillaPorTipo($tipo_cita_id)
    {
        switch ($tipo_cita_id) {
            case 1:
                return 'plantillas.optometria';
            case 2:
                return 'plantillas.examenes';
            default:
                return null;
        }
    }
}
