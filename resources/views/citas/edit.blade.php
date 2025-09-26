<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar Cita
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow rounded-lg">
                <form action="{{ route('citas.update', $cita->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- Fecha --}}
                    <div class="mb-4">
                        <label class="block font-medium">Fecha</label>
                        <input type="date" name="fecha" 
                               value="{{ old('fecha', $cita->fecha) }}"
                               class="w-full border-gray-300 rounded-md shadow-sm">
                        @error('fecha') 
                            <span class="text-red-600 text-sm">{{ $message }}</span> 
                        @enderror
                    </div>

                    {{-- Hora inicio --}}
                    <div class="mb-4">
                        <label class="block font-medium">Hora inicio</label>
                        <input type="time" name="hora_inicio" 
                               value="{{ old('hora_inicio', $cita->hora_inicio) }}"
                               class="w-full border-gray-300 rounded-md shadow-sm">
                        @error('hora_inicio') 
                            <span class="text-red-600 text-sm">{{ $message }}</span> 
                        @enderror
                    </div>

                    {{-- Hora fin --}}
                    <div class="mb-4">
                        <label class="block font-medium">Hora fin</label>
                        <input type="time" name="hora_fin" 
                               value="{{ old('hora_fin', $cita->hora_fin) }}"
                               class="w-full border-gray-300 rounded-md shadow-sm">
                        @error('hora_fin') 
                            <span class="text-red-600 text-sm">{{ $message }}</span> 
                        @enderror
                    </div>

                    {{-- Paciente --}}
                    <div class="mb-4">
                        <label class="block font-medium">Paciente</label>
                        <select name="paciente_id" class="w-full border-gray-300 rounded-md shadow-sm">
                            @foreach($pacientes as $p)
                                <option value="{{ $p->id }}" 
                                        @selected($p->id == $cita->paciente_id)>
                                    {{ $p->name ?? ($p->nombres . ' ' . $p->apellidos) }}
                                </option>
                            @endforeach
                        </select>
                        @error('paciente_id') 
                            <span class="text-red-600 text-sm">{{ $message }}</span> 
                        @enderror
                    </div>

                    {{-- Admisiones --}}
                    <div class="mb-4">
                        <label class="block font-medium">Admisiones</label>
                        <select name="admisiones_id" class="w-full border-gray-300 rounded-md shadow-sm">
                            @foreach($admisiones as $a)
                                <option value="{{ $a->id }}" 
                                        @selected($a->id == $cita->admisiones_id)>
                                    {{ $a->name ?? ($a->nombres . ' ' . $a->apellidos) }}
                                </option>
                            @endforeach
                        </select>
                        @error('admisiones_id') 
                            <span class="text-red-600 text-sm">{{ $message }}</span> 
                        @enderror
                    </div>

                    {{-- Estado --}}
                    <div class="mb-4">
                        <label class="block font-medium">Estado</label>
                        <select name="estado" class="w-full border-gray-300 rounded-md shadow-sm">
                            <option value="programada" @selected($cita->estado == 'programada')>Programada</option>
                            <option value="cancelada" @selected($cita->estado == 'cancelada')>Cancelada</option>
                            <option value="finalizada" @selected($cita->estado == 'finalizada')>Finalizada</option>
                        </select>
                        @error('estado') 
                            <span class="text-red-600 text-sm">{{ $message }}</span> 
                        @enderror
                    </div>

                    {{-- Botones --}}
                    <div class="flex justify-end">
                        <a href="{{ route('citas.index') }}" 
                           class="px-4 py-2 bg-gray-300 rounded-md mr-2">Cancelar</a>
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-md">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
