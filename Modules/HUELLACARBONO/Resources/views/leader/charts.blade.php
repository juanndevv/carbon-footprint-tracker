@extends('huellacarbono::layouts.master')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">
                <i class="fas fa-chart-bar text-green-600"></i> Gráficas de mi Unidad
            </h1>
            <p class="text-gray-600">{{ $unit->name }}</p>
        </div>

        <!-- Selector de Período -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
            <label class="block text-sm font-medium text-gray-700 mb-2">Vista por período</label>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('cefa.huellacarbono.leader.charts') }}?period=weekly"
                   class="px-4 py-2 rounded-lg font-medium transition {{ $period === 'weekly' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    Semanal (12 semanas)
                </a>
                <a href="{{ route('cefa.huellacarbono.leader.charts') }}?period=monthly"
                   class="px-4 py-2 rounded-lg font-medium transition {{ $period === 'monthly' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    Mensual (12 meses)
                </a>
                <a href="{{ route('cefa.huellacarbono.leader.charts') }}?period=yearly"
                   class="px-4 py-2 rounded-lg font-medium transition {{ $period === 'yearly' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    Anual (5 años)
                </a>
            </div>
        </div>

        <!-- Gráfica de tendencia CO₂ -->
        <div class="bg-white rounded-2xl shadow-xl p-6 mb-8">
            <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                <i class="fas fa-chart-line text-green-600 mr-3"></i>
                CO₂ generado
                @if($period === 'weekly')
                    (por semana)
                @elseif($period === 'yearly')
                    (por año)
                @else
                    (por mes)
                @endif
            </h3>
            <div class="relative" style="height: 350px;">
                <canvas id="trendChart"></canvas>
            </div>
            @if(empty($chartData))
                <p class="text-gray-500 text-center py-8">No hay datos de consumo en este período.</p>
            @endif
        </div>

        <!-- Enlaces -->
        <div class="flex flex-wrap gap-4">
            <a href="{{ route('cefa.huellacarbono.leader.statistics') }}" 
               class="inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl transition">
                <i class="fas fa-chart-line mr-2"></i> Estadísticas
            </a>
            <a href="{{ route('cefa.huellacarbono.leader.dashboard') }}" 
               class="inline-flex items-center px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-xl transition">
                <i class="fas fa-arrow-left mr-2"></i> Volver al Dashboard
            </a>
        </div>
    </div>
</div>

@if(!empty($chartData))
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var labels = @json($chartLabels);
    var data = @json($chartData);
    var ctx = document.getElementById('trendChart');
    if (!ctx) return;
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'kg CO₂',
                data: data,
                backgroundColor: 'rgba(34, 197, 94, 0.6)',
                borderColor: 'rgb(22, 163, 74)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: { display: true, text: 'kg CO₂' }
                }
            }
        }
    });
});
</script>
@endif
@endsection
