@extends('huellacarbono::layouts.master')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">
                <i class="fas fa-chart-bar text-teal-600"></i> Gráficas y Análisis
            </h1>
            <p class="text-gray-600">Visualización avanzada de datos históricos</p>
        </div>

        <!-- Selector de Período -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Vista</label>
                    <select id="viewType" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500">
                        <option value="weekly">📅 Semanal (últimas 12 semanas)</option>
                        <option value="monthly" selected>📆 Mensual (últimos 12 meses)</option>
                        <option value="quarterly">📊 Trimestral (últimos 8 trimestres)</option>
                        <option value="yearly">🗓️ Anual (últimos 3 años)</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Unidad Productiva</label>
                    <select id="unitFilter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500">
                        <option value="all">Todas las unidades</option>
                        @foreach($units as $unit)
                        <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="flex items-end">
                    <button onclick="updateCharts()" class="w-full bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg font-semibold transition">
                        <i class="fas fa-sync-alt mr-2"></i>Actualizar
                    </button>
                </div>
            </div>
        </div>

        <!-- Estadísticas Rápidas -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl shadow-lg p-6 text-white">
                <i class="fas fa-calendar-week text-3xl opacity-80 mb-2"></i>
                <p class="text-sm opacity-90">Esta Semana</p>
                <p class="text-3xl font-bold">{{ number_format($weeklyTotal, 2) }}</p>
                <p class="text-xs opacity-75">kg CO₂</p>
            </div>
            
            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl shadow-lg p-6 text-white">
                <i class="fas fa-calendar-alt text-3xl opacity-80 mb-2"></i>
                <p class="text-sm opacity-90">Este Mes</p>
                <p class="text-3xl font-bold">{{ number_format($monthlyTotal, 2) }}</p>
                <p class="text-xs opacity-75">kg CO₂</p>
            </div>
            
            <div class="bg-gradient-to-br from-teal-500 to-cyan-600 rounded-2xl shadow-lg p-6 text-white">
                <i class="fas fa-calendar text-3xl opacity-80 mb-2"></i>
                <p class="text-sm opacity-90">Este Trimestre</p>
                <p class="text-3xl font-bold">{{ number_format($quarterlyTotal, 2) }}</p>
                <p class="text-xs opacity-75">kg CO₂</p>
            </div>
            
            <div class="bg-gradient-to-br from-emerald-500 to-green-600 rounded-2xl shadow-lg p-6 text-white">
                <i class="fas fa-calendar-check text-3xl opacity-80 mb-2"></i>
                <p class="text-sm opacity-90">Este Año</p>
                <p class="text-3xl font-bold">{{ number_format($yearlyTotal, 2) }}</p>
                <p class="text-xs opacity-75">kg CO₂</p>
            </div>
        </div>

        <!-- Gráfica Principal de Tendencia -->
        <div class="bg-white rounded-2xl shadow-xl p-6 mb-8">
            <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                <i class="fas fa-chart-line text-blue-600 mr-3"></i>
                Tendencia Histórica de CO₂
            </h3>
            <div class="relative" style="height: 400px;">
                <canvas id="trendChart"></canvas>
            </div>
        </div>

        <!-- Gráficas de Comparación -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Por Unidad Productiva -->
            <div class="bg-white rounded-2xl shadow-xl p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-industry text-green-600 mr-3"></i>
                    Top 10 Unidades con Mayor CO₂
                </h3>
                <div class="relative" style="height: 400px;">
                    <canvas id="unitChart"></canvas>
                </div>
            </div>

            <!-- Por Factor de Emisión -->
            <div class="bg-white rounded-2xl shadow-xl p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-leaf text-amber-600 mr-3"></i>
                    Distribución por Tipo de Consumo
                </h3>
                <div class="relative" style="height: 400px;">
                    <canvas id="factorChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Comparación Anual -->
        <div class="bg-white rounded-2xl shadow-xl p-6 mb-8">
            <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                <i class="fas fa-calendar-alt text-teal-600 mr-3"></i>
                Comparación Anual (2022-2024)
            </h3>
            <div class="relative" style="height: 350px;">
                <canvas id="yearlyComparisonChart"></canvas>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
// Datos desde el backend
const chartData = @json($chartData);

// Colores consistentes
const colors = [
    '#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899',
    '#eab308', '#ef4444', '#14b8a6', '#a855f7', '#22c55e'
];

// 1. Gráfica de Tendencia Histórica
const trendChart = new Chart(document.getElementById('trendChart'), {
    type: 'line',
    data: {
        labels: chartData.trend.labels,
        datasets: [{
            label: 'CO₂ Generado (kg)',
            data: chartData.trend.data,
            borderColor: '#10b981',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointRadius: 5,
            pointBackgroundColor: '#10b981',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointHoverRadius: 7
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: true,
                position: 'top'
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12,
                callbacks: {
                    label: (context) => context.parsed.y.toFixed(2) + ' kg CO₂'
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: (value) => value.toLocaleString() + ' kg'
                }
            }
        }
    }
});

// 2. Gráfica por Unidad
unitChart = new Chart(document.getElementById('unitChart'), {
    type: 'bar',
    data: {
        labels: chartData.byUnit.labels,
        datasets: [{
            label: 'CO₂ (kg)',
            data: chartData.byUnit.data,
            backgroundColor: colors,
            borderRadius: 8
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        indexAxis: 'y',
        plugins: {
            legend: { display: false }
        },
        scales: {
            x: {
                beginAtZero: true,
                ticks: {
                    callback: (value) => value.toLocaleString() + ' kg'
                }
            }
        }
    }
});

// 3. Gráfica por Factor (Donut)
const factorChart = new Chart(document.getElementById('factorChart'), {
    type: 'doughnut',
    data: {
        labels: chartData.byFactor.labels,
        datasets: [{
            data: chartData.byFactor.data,
            backgroundColor: colors,
            borderColor: '#fff',
            borderWidth: 3
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'right',
                labels: {
                    padding: 15,
                    usePointStyle: true
                }
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = ((context.parsed / total) * 100).toFixed(1);
                        return context.label + ': ' + context.parsed.toFixed(2) + ' kg (' + percentage + '%)';
                    }
                }
            }
        }
    }
});

// 4. Comparación Anual
yearlyChart = new Chart(document.getElementById('yearlyComparisonChart'), {
    type: 'bar',
    data: {
        labels: chartData.yearly.labels,
        datasets: [{
            label: 'CO₂ Generado (kg)',
            data: chartData.yearly.data,
            backgroundColor: ['#3b82f6', '#10b981', '#f59e0b'],
            borderRadius: 10
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: (context) => context.parsed.y.toLocaleString() + ' kg CO₂'
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: (value) => value.toLocaleString() + ' kg'
                }
            }
        }
    }
});

// Función para actualizar gráficas con filtros
function updateCharts() {
    const viewType = document.getElementById('viewType').value;
    const unitId = document.getElementById('unitFilter').value;
    const url = '{{ route("cefa.huellacarbono.admin.charts.data") }}';
    const csrf = '{{ csrf_token() }}';

    if (typeof showToast === 'function') showToast('info', 'Actualizando gráficas...');

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrf
        },
        body: JSON.stringify({
            view_type: viewType,
            unit_id: unitId
        })
    })
    .then(r => r.json())
    .then(res => {
        if (!res.success || !res.chartData) {
            if (typeof showToast === 'function') showToast('error', res.message || 'Error al cargar datos');
            return;
        }
        const d = res.chartData;
        trendChart.data.labels = d.trend.labels;
        trendChart.data.datasets[0].data = d.trend.data;
        trendChart.update();
        unitChart.data.labels = d.byUnit.labels;
        unitChart.data.datasets[0].data = d.byUnit.data;
        unitChart.update();
        factorChart.data.labels = d.byFactor.labels;
        factorChart.data.datasets[0].data = d.byFactor.data;
        factorChart.update();
        yearlyChart.data.labels = d.yearly.labels;
        yearlyChart.data.datasets[0].data = d.yearly.data;
        yearlyChart.update();
        if (typeof showToast === 'function') showToast('success', 'Gráficas actualizadas');
    })
    .catch(() => {
        if (typeof showToast === 'function') showToast('error', 'Error de conexión');
    });
}
</script>
@endsection





