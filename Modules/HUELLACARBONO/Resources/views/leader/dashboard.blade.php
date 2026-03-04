@extends('huellacarbono::layouts.master')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-bold text-gray-900 mb-2">
                        <i class="fas fa-user-cog text-green-600"></i> Panel del Líder
                    </h1>
                    <p class="text-gray-600">{{ $unit->name }}</p>
                </div>
                <a href="{{ route('cefa.huellacarbono.leader.register') }}" 
                   class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-xl font-semibold transition shadow-lg flex items-center">
                    <i class="fas fa-plus mr-2"></i> Registrar Consumo
                </a>
            </div>
        </div>

        <!-- Stats de la Semana -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between mb-4">
                    <i class="fas fa-calendar-week text-4xl opacity-80"></i>
                    <span class="text-sm font-medium bg-white/20 px-3 py-1 rounded-full">Esta Semana</span>
                </div>
                <p class="text-4xl font-bold mb-1">{{ number_format($weeklyTotal, 2) }}</p>
                <p class="text-sm opacity-90">kg CO₂ generados</p>
                <div class="mt-4 bg-white/20 h-2 rounded-full overflow-hidden">
                    <div class="bg-white h-full" style="width: 75%"></div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <i class="fas fa-tree text-4xl text-green-500"></i>
                    <span class="text-sm font-medium bg-green-100 text-green-800 px-3 py-1 rounded-full">Equivalente</span>
                </div>
                <p class="text-3xl font-bold text-gray-900 mb-1">
                    {{ number_format($weeklyTotal / 22, 2) }}
                </p>
                <p class="text-sm text-gray-600">árboles necesarios/año</p>
            </div>

            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <i class="fas fa-clipboard-list text-4xl text-blue-500"></i>
                    <span class="text-sm font-medium bg-blue-100 text-blue-800 px-3 py-1 rounded-full">Total</span>
                </div>
                <p class="text-3xl font-bold text-gray-900 mb-1">{{ $recentConsumptions->count() }}</p>
                <p class="text-sm text-gray-600">registros recientes</p>
            </div>
        </div>

        <!-- Accesos Rápidos -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <a href="{{ route('cefa.huellacarbono.leader.register') }}" 
               class="bg-gradient-to-br from-teal-500 to-cyan-600 rounded-2xl shadow-lg p-8 text-white hover:shadow-xl transition transform hover:-translate-y-1">
                <i class="fas fa-plus-circle text-5xl mb-4"></i>
                <h3 class="text-2xl font-bold mb-2">Registrar Consumo Diario</h3>
                <p class="opacity-90">Ingresa los datos de consumo de hoy</p>
            </a>

            <a href="{{ route('cefa.huellacarbono.leader.history') }}" 
               class="bg-gradient-to-br from-emerald-500 to-green-600 rounded-2xl shadow-lg p-8 text-white hover:shadow-xl transition transform hover:-translate-y-1">
                <i class="fas fa-history text-5xl mb-4"></i>
                <h3 class="text-2xl font-bold mb-2">Ver Historial</h3>
                <p class="opacity-90">Consulta registros anteriores</p>
            </a>
        </div>

        <!-- Últimos Registros (sincronizados con la actividad de tu unidad productiva) -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-gray-700 to-gray-900 px-6 py-4 flex items-center justify-between">
                <h3 class="text-xl font-bold text-white">
                    <i class="fas fa-clock mr-2"></i> Últimos 10 Registros
                </h3>
                <a href="{{ route('cefa.huellacarbono.leader.history') }}" class="text-white/90 hover:text-white text-sm font-medium">Ver historial →</a>
            </div>
            <p class="text-xs text-gray-500 px-6 py-2 bg-gray-50">Consumos de {{ $unit->name }} (del más reciente al más antiguo por fecha)</p>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Fecha</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Variable</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase">Cantidad</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase">CO₂ Generado</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Observaciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($recentConsumptions as $consumption)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                                <div class="flex flex-col gap-1">
                                    <span>{{ $consumption->consumption_date->format('d/m/Y') }}</span>
                                    @if($consumption->isDelayFromAdminApproval())
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-800 w-fit" title="Registro agregado en fecha distinta con permiso del Admin">
                                        <i class="fas fa-clock mr-1"></i> Retraso
                                    </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <i class="fas fa-leaf text-green-500 mr-2"></i>
                                    <span class="text-sm text-gray-900">{{ $consumption->emissionFactor->name ?? 'N/A' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-right text-gray-900">
                                {{ $consumption->quantity }} <span class="text-gray-500">{{ $consumption->emissionFactor->unit ?? '' }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="text-lg font-bold text-green-600">
                                    {{ number_format($consumption->co2_generated, 3) }} kg
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $consumption->observations ?? '-' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <i class="fas fa-clipboard text-6xl text-gray-300 mb-4"></i>
                                <p class="text-gray-500 text-lg">Aún no hay registros</p>
                                <a href="{{ route('cefa.huellacarbono.leader.register') }}" 
                                   class="inline-block mt-4 text-green-600 hover:text-green-700 font-semibold">
                                    Registra el primer consumo →
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
