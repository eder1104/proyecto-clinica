<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar Cita
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow rounded-lg">

                @if(session('success'))
                    <div class="mb-4 p-3 rounded bg-green-500 text-white">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 p-3 rounded bg-red-500 text-white">
                        {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('citas.update', $cita->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="block font-medium">Fecha</label>
                        <input
                            type="date"
                            name="fecha"
                            value="{{ old('fecha', \Carbon\Carbon::parse($cita->fecha)->format('Y-m-d')) }}"
                            class="w-full border-gray-300 rounded-md shadow-sm"
                            @if($cita->estado === 'finalizada') disabled @endif
                        >
                        @error('fecha')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium">Hora inicio</label>
                        <input
                            type="time"
                            name="hora_inicio"
                            value="{{ old('hora_inicio', \Carbon\Carbon::parse($cita->hora_inicio)->format('H:i')) }}"
                            class="w-full border-gray-300 rounded-md shadow-sm"
                            @if($cita->estado === 'finalizada') disabled @endif
                        >
                        @error('hora_inicio')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium">Hora fin</label>
                        <input
                            type="time"
                            name="hora_fin"
                            value="{{ old('hora_fin', \Carbon\Carbon::parse($cita->hora_fin)->format('H:i')) }}"
                            class="w-full border-gray-300 rounded-md shadow-sm"
                            @if($cita->estado === 'finalizada') disabled @endif
                        >
                        @error('hora_fin')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium">Paciente</label>
                        <select
                            name="paciente_id"
                            class="w-full border-gray-300 rounded-md shadow-sm"
                            @if($cita->estado === 'finalizada') disabled @endif
                        >
                            @foreach($pacientes as $p)
                                <option value="{{ $p->id }}" @selected(old('paciente_id', $cita->paciente_id) == $p->id)>
                                    {{ $p->name ?? ($p->nombres . ' ' . $p->apellidos) }}
                                </option>
                            @endforeach
                        </select>
                        @error('paciente_id')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium">Admisiones</label>
                        <select
                            name="admisiones_id"
                            class="w-full border-gray-300 rounded-md shadow-sm"
                            @if($cita->estado === 'finalizada') disabled @endif
                        >
                            @foreach($admisiones as $a)
                                <option value="{{ $a->id }}" @selected(old('admisiones_id', $cita->admisiones_id) == $a->id)>
                                    {{ $a->name ?? ($a->nombres . ' ' . $a->apellidos) }}
                                </option>
                            @endforeach
                        </select>
                        @error('admisiones_id')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium">Estado</label>
                        <select
                            name="estado"
                            class="w-full border-gray-300 rounded-md shadow-sm"
                            @if($cita->estado === 'finalizada') disabled @endif
                        >
                            <option value="programada" {{ request('estado') == 'programada' ? 'selected' : '' }}>programada</option>
                            <option value="modificada" {{ request('estado') == 'modificada' ? 'selected' : '' }}>modificada</option>
                            <option value="cancelada" {{ request('estado') == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                            <option value="no_asistida" {{ request('estado') == 'no_asistida' ? 'selected' : '' }}>no_asistida</option>
                            <option value="asistida" {{ request('estado') == 'asistida' ? 'selected' : '' }}>asistida</option>
                        </select>
                        @error('estado')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex justify-end items-center gap-2">
                        <a href="{{ route('citas.index') }}" class="px-4 py-2 bg-gray-300 rounded-md">Volver</a>

                        @if($cita->estado !== 'finalizada')
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md">Guardar</button>
                        @endif

                        <a href="{{ route('citas.pdf', $cita) }}" target="_blank" class="px-4 py-2 bg-green-600 text-white rounded-md">Ver PDF</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
