@extends('huellacarbono::layouts.master')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">
                <i class="fas fa-clipboard-list text-green-600"></i> Todos los Consumos
            </h1>
            <p class="text-gray-600">Visualización y edición de todos los registros del sistema</p>
        </div>

        <!-- Filtros -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-filter text-blue-600 mr-2"></i>
                Filtros de Búsqueda
            </h3>
            
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Unidad Productiva</label>
                    <select name="unit_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                        <option value="">Todas</option>
                        @foreach($units as $unit)
                        <option value="{{ $unit->id }}" {{ request('unit_id') == $unit->id ? 'selected' : '' }}>
                            {{ $unit->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Inicio</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Fin</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                </div>
                
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-semibold transition">
                        <i class="fas fa-search mr-2"></i>Filtrar
                    </button>
                </div>
            </form>
        </div>

        <!-- Tabla de Consumos -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-green-600 to-emerald-700 px-6 py-4 flex items-center justify-between">
                <h3 class="text-xl font-bold text-white">
                    <i class="fas fa-database mr-2"></i> Registros de Consumo
                </h3>
                <span class="bg-white/20 px-4 py-2 rounded-lg text-white font-semibold">
                    {{ $consumptions->total() }} registros
                </span>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Fecha</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Unidad</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Factor</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase">Cantidad</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase">CO₂ (kg)</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Registrado por</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($consumptions as $consumption)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                                <div class="flex flex-col gap-1">
                                    <span>{{ $consumption->consumption_date->format('d/m/Y') }}</span>
                                    @if($consumption->isDelayFromAdminApproval())
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-800" title="Registro agregado en fecha distinta con permiso del Admin">
                                        <i class="fas fa-clock mr-1"></i> Retraso
                                    </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $consumption->productiveUnit->name ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                {{ $consumption->emissionFactor->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-right text-gray-900">
                                {{ $consumption->quantity }} {{ $consumption->emissionFactor->unit ?? '' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-right">
                                <span class="font-bold text-green-600">
                                    {{ number_format($consumption->co2_generated, 3) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <i class="fas fa-user-circle text-gray-400 mr-1"></i>
                                {{ $consumption->registeredBy->nickname ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center space-x-2">
                                    <button onclick="openEditConsumptionModal({{ $consumption->id }})" 
                                            class="text-blue-600 hover:text-blue-800 transition"
                                            title="Editar">
                                        <i class="fas fa-edit text-lg"></i>
                                    </button>
                                    <button onclick="deleteConsumption({{ $consumption->id }})" 
                                            class="text-slate-600 hover:text-slate-800 transition"
                                            title="Eliminar">
                                        <i class="fas fa-trash text-lg"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
                                <p class="text-gray-500 text-lg">No hay registros con los filtros aplicados</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Paginación -->
            <div class="px-6 py-4 bg-gray-50">
                {{ $consumptions->links() }}
            </div>
        </div>

        <!-- Botón Volver -->
        <div class="mt-8">
            <a href="{{ route('cefa.huellacarbono.admin.dashboard') }}" 
               class="inline-flex items-center px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-xl transition">
                <i class="fas fa-arrow-left mr-2"></i> Volver al Dashboard
            </a>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
function deleteConsumption(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: 'Esta acción no se puede deshacer',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/huellacarbono/admin/consumos/${id}/eliminar`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    showToast('success', 'Registro eliminado');
                    setTimeout(() => location.reload(), 1000);
                }
            });
        }
    });
}

function openEditConsumptionModal(id) {
    showToast('info', 'Funcionalidad en desarrollo');
}
</script>
@endsection

