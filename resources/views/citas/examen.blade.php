<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            Registro de Cita Médica
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8" x-data="{ open: true }">
        <div x-show="open" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div class="bg-white w-full max-w-3xl p-6 rounded-2xl shadow-2xl">

                <form action="{{ route('preexamen.store', $cita->id) }}" method="POST" class="space-y-6">
                    @csrf

                    <h1 class="title">Examen General</h1>

                    @if (session('error'))
                        <div class="mb-4 text-red-600 font-semibold text-center bg-red-100 p-2 rounded-lg">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="grid grid-cols-2 gap-5">
                        <div>
                            <label class="block text-gray-700 font-medium mb-1">Presión arterial</label>
                            <input type="text" name="tension_arterial"
                                   value="{{ old('tension_arterial', $cita->tension_arterial ?? '') }}"
                                   class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 p-2.5 bg-gray-50 shadow-sm">
                        </div>

                        <div>
                            <label class="block text-gray-700 font-medium mb-1">Frecuencia cardiaca</label>
                            <input type="number" name="frecuencia_cardiaca"
                                   value="{{ old('frecuencia_cardiaca', $cita->frecuencia_cardiaca ?? '') }}"
                                   class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 p-2.5 bg-gray-50 shadow-sm">
                        </div>

                        <div>
                            <label class="block text-gray-700 font-medium mb-1">Frecuencia respiratoria</label>
                            <input type="number" name="frecuencia_respiratoria"
                                   value="{{ old('frecuencia_respiratoria', $cita->frecuencia_respiratoria ?? '') }}"
                                   class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 p-2.5 bg-gray-50 shadow-sm">
                        </div>

                        <div>
                            <label class="block text-gray-700 font-medium mb-1">Temperatura (°C)</label>
                            <input type="number" step="0.1" name="temperatura"
                                   value="{{ old('temperatura', $cita->temperatura ?? '') }}"
                                   class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 p-2.5 bg-gray-50 shadow-sm">
                        </div>

                        <div>
                            <label class="block text-gray-700 font-medium mb-1">Saturación O₂ (%)</label>
                            <input type="number" step="0.1" name="saturacion"
                                   value="{{ old('saturacion', $cita->saturacion ?? '') }}"
                                   class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 p-2.5 bg-gray-50 shadow-sm">
                        </div>

                        <div>
                            <label class="block text-gray-700 font-medium mb-1">Peso (kg)</label>
                            <input type="number" step="0.1" name="peso"
                                   value="{{ old('peso', $cita->peso ?? '') }}"
                                   class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 p-2.5 bg-gray-50 shadow-sm">
                        </div>

                        <div class="col-span-2">
                            <label class="block text-gray-700 font-medium mb-1">Examen físico</label>
                            <textarea name="examen_fisico" rows="3"
                                      class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 p-2.5 bg-gray-50 shadow-sm resize-none">{{ old('examen_fisico', $cita->examen_fisico ?? '') }}</textarea>
                        </div>

                        <div class="col-span-2">
                            <label class="block text-gray-700 font-medium mb-1">Diagnóstico</label>
                            <textarea name="diagnostico" rows="3"
                                      class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 p-2.5 bg-gray-50 shadow-sm resize-none">{{ old('diagnostico', $cita->diagnostico ?? '') }}</textarea>
                        </div>

                        <div class="col-span-2">
                            <label class="block text-gray-700 font-semibold mb-1">Tipo de Cita</label>
                            <select name="tipo_cita_id" id="TipoCita"
                                    class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 p-2.5 bg-gray-50 shadow-sm"
                                    required>
                                <option value="">Seleccione un tipo de cita</option>
                                <option value="1" {{ old('tipo_cita_id', $cita->tipo_cita_id) == 1 ? 'selected' : '' }}>Optometría</option>
                                <option value="2" {{ old('tipo_cita_id', $cita->tipo_cita_id) == 2 ? 'selected' : '' }}>Exámenes</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 mt-6">
                        <a href="{{ route('citas.index') }}"
                           class="px-5 py-2.5 rounded-xl bg-gray-200 hover:bg-gray-300 transition font-medium shadow-sm">
                            Cancelar
                        </a>
                        <button type="submit"
                                class="px-5 py-2.5 rounded-xl bg-blue-600 text-white hover:bg-blue-700 transition font-semibold shadow-sm">
                            Guardar
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>

<style>
    .title {
        font-size: 2rem;
        font-weight: 700;
        color: #111827;
        margin-bottom: 1.5rem;
        text-align: center;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
</style>
