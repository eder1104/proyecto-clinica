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

                    <select name="hora_inicio" id="hora_inicio"
                    class="mt-1 block w-full rounded-md shadow-sm border-gray-300 hora-select">
                    @php
                    $times = [];
                    for ($h = 8; $h < 18; $h++) {
                        for ($m=0; $m < 60; $m +=20) {
                        $value=sprintf('%02d:%02d', $h, $m);
                        $ampm=date('g:i A', strtotime($value));
                        $times[]=['value'=> $value, 'label' => "$value ($ampm)"];
                        }
                        }
                        @endphp

                        <option value="">-- Seleccione la hora --</option>

                        @foreach ($times as $t)
                        <option value="{{ $t['value'] }}">
                            {{ $t['label'] }}
                        </option>
                        @endforeach
                </select>


                <div class="mb-4" style="display:none;">
                    <label class="block text-gray-700">Hora de fin</label>
                    <input type="time" name="hora_fin" id="hora_fin" value="{{ old('hora_fin') }}" class="mt-1 block w-full rounded-md shadow-sm border-gray-300">
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
