@extends('huellacarbono::layouts.master')

@section('content')
<!-- Banner título (igual que página principal: imagen de fondo + overlay) -->
<div class="relative overflow-hidden h-[320px] min-h-[320px]">
    <div class="absolute inset-0 z-0">
        <img src="https://images.pexels.com/photos/221012/pexels-photo-221012.jpeg?auto=compress&cs=tinysrgb&w=1600" alt="" class="w-full h-full object-cover" loading="lazy">
        <div class="absolute inset-0 bg-gradient-to-r from-green-900/80 to-emerald-900/80"></div>
    </div>
    <div class="absolute inset-0 z-10 flex items-center justify-center max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 left-0 right-0">
        <div class="text-center text-white">
            <i class="fas fa-calculator text-7xl mb-6 drop-shadow-lg"></i>
            <h1 class="text-4xl md:text-5xl font-extrabold mb-4 drop-shadow-lg">Calculadora Personal</h1>
            <p class="text-xl opacity-90 drop-shadow-md">Descubre cuánto CO₂ generas con tus actividades diarias</p>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="lg:flex lg:gap-8">
        <!-- Formulario -->
        <div class="lg:flex-1">
            <div class="bg-white rounded-2xl shadow-xl p-8">
                <div class="flex items-center mb-6">
                    <div class="bg-blue-100 w-12 h-12 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-edit text-2xl text-blue-600"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900">Ingresa tus datos</h3>
                </div>
                
                <form id="carbonCalculatorForm">
                    @csrf
                    
                    <!-- Datos personales -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nombre (opcional)</label>
                            <input type="text" name="name" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                   placeholder="Tu nombre">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email (opcional)</label>
                            <input type="email" name="email" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                   placeholder="tu@email.com">
                        </div>
                    </div>

                    <!-- Período -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Período de cálculo <span class="text-slate-500">*</span>
                        </label>
                        <select name="period" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            <option value="daily">Diario</option>
                            <option value="weekly">Semanal</option>
                            <option value="monthly" selected>Mensual</option>
                            <option value="yearly">Anual</option>
                        </select>
                    </div>

                    <hr class="my-6">
                    
                    <div class="flex items-center mb-6">
                        <i class="fas fa-leaf text-2xl text-green-600 mr-3"></i>
                        <h5 class="text-xl font-semibold text-gray-900">Consumos y Actividades</h5>
                    </div>

                    <div class="space-y-4">
                        @foreach($emissionFactors as $factor)
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <label class="block text-sm font-medium text-gray-900 mb-2">
                                {{ $factor->name }}
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 ml-2">
                                    {{ $factor->unit }}
                                </span>
                                <span class="text-xs text-gray-500 ml-2">(Factor: {{ $factor->factor }})</span>
                            </label>
                            <input type="number" 
                                   name="{{ strtolower($factor->code) }}_consumption" 
                                   id="{{ $factor->code }}"
                                   data-factor="{{ $factor->factor }}"
                                   data-requires-percentage="{{ $factor->requires_percentage ? 'true' : 'false' }}"
                                   step="0.001" 
                                   min="0" 
                                   value="0"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                   placeholder="Ingrese cantidad">
                            
                            @if($factor->requires_percentage)
                            <input type="number" 
                                   name="fertilizer_nitrogen_percentage" 
                                   step="0.01" 
                                   min="0" 
                                   max="100"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent mt-2"
                                   placeholder="% de Nitrógeno (0-100)">
                            @endif
                        </div>
                        @endforeach
                    </div>

                    <button type="submit" 
                            class="w-full mt-8 px-6 py-4 bg-green-600 hover:bg-green-700 text-white font-bold text-lg rounded-xl transition shadow-lg transform hover:scale-105">
                        <i class="fas fa-calculator mr-2"></i> Calcular Mi Huella de Carbono
                    </button>
                </form>
            </div>
        </div>

        <!-- Resultado -->
        <div class="lg:w-96 lg:flex-shrink-0 mt-8 lg:mt-0">
            <div class="lg:sticky lg:top-24 space-y-6">
                <div class="bg-white rounded-2xl shadow-xl p-8">
                <div class="flex items-center mb-6">
                    <div class="bg-green-100 w-12 h-12 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-chart-pie text-2xl text-green-600"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900">Resultado</h3>
                </div>
                
                <div id="resultCard" class="text-center">
                    <i class="fas fa-leaf text-7xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500">Complete el formulario y presione calcular para ver su huella de carbono</p>
                </div>
                </div>

                <!-- Información -->
                <div class="bg-blue-50 rounded-2xl p-6 border border-blue-200">
                <div class="flex items-center mb-4">
                    <i class="fas fa-info-circle text-2xl text-blue-600 mr-3"></i>
                    <h5 class="text-lg font-semibold text-gray-900">¿Cómo interpretar?</h5>
                </div>
                <p class="text-sm text-gray-700 mb-4">
                    El resultado muestra los kilogramos de CO₂ equivalente generados en el período seleccionado.
                </p>
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded">
                    <p class="text-sm text-gray-800">
                        <strong>Tip:</strong> Un árbol absorbe aproximadamente 22 kg de CO₂ al año.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Botón Volver -->
    <div class="mt-12 text-center">
        <a href="{{ route('cefa.huellacarbono.index') }}" 
           class="inline-flex items-center px-8 py-4 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-xl transition shadow-lg">
            <i class="fas fa-arrow-left mr-2"></i> Volver al Inicio
        </a>
    </div>
</div>
@endsection

@section('script')
<script>
$(document).ready(function() {
    $('#carbonCalculatorForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = $(this).serialize();
        
        $.ajax({
            url: '{{ route("cefa.huellacarbono.calculate_personal") }}',
            method: 'POST',
            data: formData,
            success: function(response) {
                if(response.success) {
                    const totalCO2 = response.total_co2;
                    const trees = (totalCO2 / 22).toFixed(2);
                    
                    $('#resultCard').html(`
                        <div class="animate-bounce mb-4">
                            <i class="fas fa-check-circle text-7xl text-green-500"></i>
                        </div>
                        <h2 class="text-4xl font-bold text-green-600 mb-2">${totalCO2} kg CO₂</h2>
                        <p class="text-lg text-gray-700 mb-4">Tu huella de carbono</p>
                        <hr class="my-4">
                        <div class="bg-green-50 rounded-lg p-4">
                            <i class="fas fa-tree text-3xl text-green-600 mb-2"></i>
                            <p class="text-gray-800">Equivale a <strong class="text-2xl text-green-600">${trees}</strong> árboles por año</p>
                        </div>
                        <div class="mt-4 bg-blue-50 border-l-4 border-blue-400 p-4 rounded text-left">
                            <p class="text-sm text-gray-800">
                                <strong>¡Tip!</strong> Reduce tu consumo de energía y combustibles para disminuir tu impacto ambiental.
                            </p>
                        </div>
                    `);
                    
                    showToast('success', response.message);
                }
            },
            error: function(xhr) {
                showToast('error', 'Error al calcular la huella de carbono');
                console.error(xhr);
            }
        });
    });
});
</script>
@endsection
