<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Citas Disponibles') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    {{-- Mensaje Flash de Login --}}
                    @if (session('status'))
                        <div id="alert-login" class="flex items-center justify-between bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                            <span>{{ session('status') }}</span>
                            <button onclick="document.getElementById('alert-login').remove()" class="ml-2 text-lg font-bold text-green-700 hover:text-green-900">
                                &times;
                            </button>
                        </div>
                    @endif

                    <p>Bienvenido al Dashboard</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
