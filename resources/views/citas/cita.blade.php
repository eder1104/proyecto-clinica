<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detalle de Cita Médica
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200">
                <div class="px-8 py-6 bg-gradient-to-r from-blue-600 to-blue-500 text-white">
                    <h1 class="text-2xl font-bold">Cita Médica General</h1>
                    <p class="text-sm">Registro detallado de atención médica</p>
                </div>

                <div class="p-8 space-y-8">
                    
                    <div>
                        <h2 class="text-lg font-semibold text-gray-700 border-b pb-2">Datos del Paciente</h2>
                        <div class="grid grid-cols-2 gap-6 mt-4 text-sm text-gray-600">
                            <p><span class="font-medium">Nombre:</span> Juan Pérez Gómez</p>
                            <p><span class="font-medium">Documento:</span> 1023456789</p>
                            <p><span class="font-medium">Edad:</span> 32 años</p>
                            <p><span class="font-medium">Sexo:</span> Masculino</p>
                            <p><span class="font-medium">Teléfono:</span> 3204567890</p>
                            <p><span class="font-medium">Dirección:</span> Calle 123 #45-67</p>
                        </div>
                    </div>

                    <div>
                        <h2 class="text-lg font-semibold text-gray-700 border-b pb-2">Datos de la Cita</h2>
                        <div class="grid grid-cols-2 gap-6 mt-4 text-sm text-gray-600">
                            <p><span class="font-medium">Fecha:</span> 29/09/2025</p>
                            <p><span class="font-medium">Hora:</span> 10:30 AM</p>
                            <p><span class="font-medium">Estado:</span> Asistida</p>
                            <p><span class="font-medium">Atendido por:</span> Dr. Carlos Ramírez – Médico General</p>
                        </div>
                    </div>

                    <div>
                        <h2 class="text-lg font-semibold text-gray-700 border-b pb-2">Motivo de Consulta</h2>
                        <p class="mt-3 text-sm text-gray-700 leading-relaxed">
                            Paciente refiere malestar general desde hace 3 días, acompañado de fiebre intermitente, cefalea leve y sensación de cansancio. Manifiesta preocupación por posible infección respiratoria.
                        </p>
                    </div>

                    <div>
                        <h2 class="text-lg font-semibold text-gray-700 border-b pb-2">Antecedentes</h2>
                        <ul class="list-disc pl-6 mt-3 text-sm text-gray-700 space-y-1">
                            <li>No antecedentes patológicos de importancia.</li>
                            <li>No alergias medicamentosas conocidas.</li>
                            <li>Vacunación completa según esquema nacional.</li>
                            <li>Sin cirugías previas.</li>
                        </ul>
                    </div>

                    <div>
                        <h2 class="text-lg font-semibold text-gray-700 border-b pb-2">Signos Vitales</h2>
                        <div class="grid grid-cols-3 gap-6 mt-4 text-sm text-gray-600">
                            <p><span class="font-medium">Tensión arterial:</span> 120/80 mmHg</p>
                            <p><span class="font-medium">Frecuencia cardiaca:</span> 78 lpm</p>
                            <p><span class="font-medium">Frecuencia respiratoria:</span> 18 rpm</p>
                            <p><span class="font-medium">Temperatura:</span> 37.8 °C</p>
                            <p><span class="font-medium">Saturación O₂:</span> 97%</p>
                            <p><span class="font-medium">Peso:</span> 72 kg</p>
                        </div>
                    </div>

                    <div>
                        <h2 class="text-lg font-semibold text-gray-700 border-b pb-2">Examen Físico</h2>
                        <p class="mt-3 text-sm text-gray-700 leading-relaxed">
                            Paciente consciente, orientado en las tres esferas, facies levemente febril. Aparato respiratorio con murmullo vesicular conservado, sin ruidos agregados. Abdomen blando, depresible, sin dolor a la palpación. Extremidades sin edemas ni alteraciones visibles.
                        </p>
                    </div>

                    <div>
                        <h2 class="text-lg font-semibold text-gray-700 border-b pb-2">Diagnóstico</h2>
                        <p class="mt-3 text-sm text-gray-700 leading-relaxed">
                            Cuadro sugestivo de infección respiratoria de vías altas (IRVA) de probable origen viral.
                        </p>
                    </div>

                    <div>
                        <h2 class="text-lg font-semibold text-gray-700 border-b pb-2">Conducta / Plan</h2>
                        <ul class="list-disc pl-6 mt-3 text-sm text-gray-700 space-y-1">
                            <li>Reposo relativo por 3 días.</li>
                            <li>Hidratación abundante (mínimo 2 litros de agua diarios).</li>
                            <li>Acetaminofén 500 mg VO cada 8 horas por 3 días en caso de fiebre o dolor.</li>
                            <li>Revisión médica en 5 días o antes si presenta dificultad respiratoria.</li>
                            <li>Se explican signos de alarma y cuidados generales en casa.</li>
                        </ul>
                    </div>

                    <div class="pt-8 border-t">
                        <p class="text-sm text-gray-500">Fecha de registro: 29/09/2025 – 11:15 AM</p>
                        <p class="mt-6 font-medium text-gray-700">_____________________________</p>
                        <p class="text-sm text-gray-600">Dr. Carlos Ramírez <br>Médico General</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
