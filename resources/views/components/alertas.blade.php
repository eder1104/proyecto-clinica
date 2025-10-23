@if ($errors->any() || session('success') || session('error'))
<div 
    x-data="{ show: true }" 
    x-show="show"
    x-transition
    class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
>
    <div class="bg-white w-full max-w-lg rounded-lg shadow-lg relative p-6">
        <!-- Botón de cerrar -->
        <button 
            @click="show = false" 
            class="absolute top-3 right-3 text-gray-400 hover:text-gray-700 text-2xl font-bold"
        >&times;</button>

        @if ($errors->any())
            <h2 class="text-xl font-semibold text-red-600 mb-3">Se detectaron errores:</h2>
            <ul class="list-disc pl-5 space-y-1 text-gray-700">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif

        @if (session('success'))
            <h2 class="text-xl font-semibold text-green-600 mb-3">Éxito:</h2>
            <p class="text-gray-700">{{ session('success') }}</p>
        @endif

        @if (session('error'))
            <h2 class="text-xl font-semibold text-red-600 mb-3">Error:</h2>
            <p class="text-gray-700">{{ session('error') }}</p>
        @endif

        @if (session('show_cancel_modal'))
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Cancelar cita</h2>
            <p class="text-gray-600 mb-3">Por favor, ingrese la razón de la cancelación:</p>

            <form method="POST" action="{{ route('citas.destroy', session('cita_id')) }}">
                @csrf
                @method('DELETE')
                <textarea 
                    name="delete_reason" 
                    class="w-full border border-gray-300 rounded-md p-2 focus:ring focus:ring-blue-200" 
                    rows="3" 
                    required
                ></textarea>

                <div class="mt-5 flex justify-end gap-3">
                    <button 
                        type="button"
                        @click="show = false"
                        class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 transition"
                    >Cerrar</button>

                    <button 
                        type="submit"
                        class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition"
                    >Confirmar</button>
                </div>
            </form>
        @endif

        <div class="mt-5 flex justify-end">
            <button 
                @click="show = false"
                class="bg-gray-800 text-white px-4 py-2 rounded-md hover:bg-gray-900 transition"
            >
                Cerrar
            </button>
        </div>
    </div>
</div>
@endif
