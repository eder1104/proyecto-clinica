<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            Registro de Cita Médica
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8" x-data="{ open: true }">
        <div x-show="open" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div class="bg-white w-full max-w-3xl p-6 rounded-lg shadow-lg">
                
                <form action="{{ route('citas.guardarExamen', $cita->id) }}" method="POST">
                    @csrf

                    <h1 class="title">Examen General</h1>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700">Presión arterial</label>
                            <input type="text" name="tension_arterial" value="{{ old('tension_arterial', $cita->tension_arterial) }}" class="w-full border rounded-md p-2">
                        </div>

                        <div>
                            <label class="block text-gray-700">Frecuencia cardiaca</label>
                            <input type="text" name="frecuencia_cardiaca" value="{{ old('frecuencia_cardiaca', $cita->frecuencia_cardiaca) }}" class="w-full border rounded-md p-2">
                        </div>

                        <div>
                            <label class="block text-gray-700">Frecuencia respiratoria</label>
                            <input type="text" name="frecuencia_respiratoria" value="{{ old('frecuencia_respiratoria', $cita->frecuencia_respiratoria) }}" class="w-full border rounded-md p-2">
                        </div>

                        <div>
                            <label class="block text-gray-700">Temperatura</label>
                            <input type="text" name="temperatura" value="{{ old('temperatura', $cita->temperatura) }}" class="w-full border rounded-md p-2">
                        </div>

                        <div>
                            <label class="block text-gray-700">Saturación O₂</label>
                            <input type="text" name="saturacion" value="{{ old('saturacion', $cita->saturacion) }}" class="w-full border rounded-md p-2">
                        </div>

                        <div>
                            <label class="block text-gray-700">Peso</label>
                            <input type="text" name="peso" value="{{ old('peso', $cita->peso) }}" class="w-full border rounded-md p-2">
                        </div>

                        <div class="col-span-2">
                            <label class="block text-gray-700">Examen físico</label>
                            <textarea name="examen_fisico" class="w-full border rounded-md p-2">{{ old('examen_fisico', $cita->examen_fisico) }}</textarea>
                        </div>

                        <div class="col-span-2">
                            <label class="block text-gray-700">Diagnóstico</label>
                            <textarea name="diagnostico" class="w-full border rounded-md p-2">{{ old('diagnostico', $cita->diagnostico) }}</textarea>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-2 mt-6">
                        <a href="{{ route('citas.index') }}" class="px-4 py-2 rounded hover:bg-gray-400">Cancelar</a>
                        <button type="submit" class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

<style>
    .title {
        font-size: 3rem;
        font-weight: 600;
        color: #111827;
        margin-bottom: 1rem;
        width: 100%;
        text-align: center;
    }
</style>
