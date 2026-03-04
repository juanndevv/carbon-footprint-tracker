@extends('huellacarbono::layouts.master')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">
                <i class="fas fa-history text-teal-600"></i> Historial de Registros
            </h1>
            <p class="text-gray-600">{{ $unit->name }}</p>
        </div>

        <!-- Resumen del Período -->
        <div class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-2xl shadow-xl p-8 text-white mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-lg opacity-90 mb-2">Total CO₂ del Período</p>
                    <p class="text-5xl font-bold">{{ number_format($totalCO2, 2) }} <span class="text-2xl">kg</span></p>
                    <p class="text-sm opacity-75 mt-2">
                        {{ $startDate instanceof \Carbon\Carbon ? $startDate->format('d/m/Y') : $startDate }} - 
                        {{ $endDate instanceof \Carbon\Carbon ? $endDate->format('d/m/Y') : $endDate }}
                    </p>
                </div>
                <div class="text-right">
                    <i class="fas fa-cloud text-8xl opacity-50"></i>
                </div>
            </div>
        </div>

        <!-- Filtros de Fecha -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
            <form method="GET" class="flex flex-wrap items-end gap-4">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Inicio</label>
                    <input type="date" name="start_date" 
                           value="{{ $startDate instanceof \Carbon\Carbon ? $startDate->format('Y-m-d') : $startDate }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                </div>
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Fin</label>
                    <input type="date" name="end_date" 
                           value="{{ $endDate instanceof \Carbon\Carbon ? $endDate->format('Y-m-d') : $endDate }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                </div>
                <button type="submit" 
                        class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-semibold transition">
                    <i class="fas fa-filter mr-2"></i>Aplicar
                </button>
            </form>
        </div>

        <!-- Tabla de Historial -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-teal-600 to-emerald-700 px-6 py-4">
                <h3 class="text-xl font-bold text-white">
                    <i class="fas fa-list mr-2"></i> Registros de Consumo
                </h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Fecha</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Variable</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase">Cantidad</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase">CO₂</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Observaciones</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($consumptions as $consumption)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1">
                                    <div class="flex items-center">
                                        <i class="fas fa-calendar text-blue-500 mr-2"></i>
                                        <span class="text-sm font-semibold text-gray-900">
                                            {{ $consumption->consumption_date->format('d/m/Y') }}
                                        </span>
                                    </div>
                                    @if($consumption->isDelayFromAdminApproval())
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-800 w-fit" title="Registro agregado en fecha distinta con permiso del Admin">
                                        <i class="fas fa-clock mr-1"></i> Retraso
                                    </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-900">
                                    {{ $consumption->emissionFactor->name ?? 'N/A' }}
                                </span>
                                <span class="text-xs text-gray-500 ml-1">({{ $consumption->emissionFactor->unit ?? '' }})</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="text-sm font-semibold text-gray-900">{{ $consumption->quantity }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="text-lg font-bold text-green-600">
                                    {{ number_format($consumption->co2_generated, 3) }}
                                </span>
                                <span class="text-xs text-gray-500">kg</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ Str::limit($consumption->observations ?? '-', 50) }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="{{ route('cefa.huellacarbono.leader.edit_consumption', $consumption->id) }}" 
                                       class="text-blue-600 hover:text-blue-800 transition"
                                       title="Editar">
                                        <i class="fas fa-edit text-lg"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
                                <p class="text-gray-500 text-lg">No hay registros en este período</p>
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
            <a href="{{ route('cefa.huellacarbono.leader.dashboard') }}" 
               class="inline-flex items-center px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-xl transition">
                <i class="fas fa-arrow-left mr-2"></i> Volver al Dashboard
            </a>
        </div>
    </div>
</div>
@endsection

