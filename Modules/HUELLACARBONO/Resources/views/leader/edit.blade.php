@extends('huellacarbono::layouts.master')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">
                <i class="fas fa-edit text-blue-600"></i> Editar Registro
            </h1>
            <p class="text-gray-600">Modificar datos del consumo registrado</p>
        </div>

        <!-- Formulario -->
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <form id="editConsumptionForm">
                @csrf
                @method('PUT')
                
                <!-- Info no editable -->
                <div class="bg-gray-50 rounded-lg p-6 mb-6">
                    <h3 class="font-semibold text-gray-900 mb-4">Información del Registro</h3>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-600">Fecha:</p>
                            <p class="font-bold text-gray-900">{{ $consumption->consumption_date->format('d/m/Y') }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Variable:</p>
                            <p class="font-bold text-gray-900">{{ $consumption->emissionFactor->name }}</p>
                        </div>
                    </div>
                </div>

                <!-- Cantidad (editable) -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Cantidad <span class="text-slate-500">*</span>
                        <span class="text-green-600 font-semibold">({{ $consumption->emissionFactor->unit }})</span>
                    </label>
                    <input type="number" name="quantity" id="quantity" 
                           value="{{ $consumption->quantity }}"
                           step="0.001" min="0" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                </div>

                <!-- Porcentaje de Nitrógeno (si aplica) -->
                @if($consumption->emissionFactor->requires_percentage)
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Porcentaje de Nitrógeno (%) <span class="text-slate-500">*</span>
                    </label>
                    <input type="number" name="nitrogen_percentage" id="nitrogen_percentage" 
                           value="{{ $consumption->nitrogen_percentage }}"
                           step="0.01" min="0" max="100" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                </div>
                @endif

                <!-- Observaciones -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Observaciones</label>
                    <textarea name="observations" rows="3"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">{{ $consumption->observations }}</textarea>
                </div>

                <!-- Botones -->
                <div class="flex space-x-4">
                    <button type="submit" 
                            class="flex-1 bg-green-600 hover:bg-green-700 text-white px-6 py-4 rounded-xl font-bold text-lg transition shadow-lg">
                        <i class="fas fa-save mr-2"></i> Guardar Cambios
                    </button>
                    <a href="{{ route('cefa.huellacarbono.leader.history') }}" 
                       class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-4 rounded-xl font-bold text-lg transition text-center">
                        <i class="fas fa-times mr-2"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$('#editConsumptionForm').on('submit', function(e) {
    e.preventDefault();
    
    const formData = $(this).serialize();
    
    $.ajax({
        url: '{{ route("cefa.huellacarbono.leader.update_consumption", $consumption->id) }}',
        method: 'PUT',
        data: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        success: function(response) {
            if(response.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Actualizado!',
                    text: response.message,
                    confirmButtonColor: '#10b981',
                }).then(() => {
                    window.location.href = '{{ route("cefa.huellacarbono.leader.history") }}';
                });
            }
        },
        error: function(xhr) {
            showToast('error', 'Error al actualizar el registro');
        }
    });
});
</script>
@endsection

