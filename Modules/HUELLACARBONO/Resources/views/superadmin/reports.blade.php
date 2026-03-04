@extends('huellacarbono::layouts.master')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">
                <i class="fas fa-file-pdf text-teal-600"></i> Reportes y Exportaciones
            </h1>
            <p class="text-gray-600">Genera y descarga reportes en PDF o Excel</p>
        </div>

        <!-- Opciones de Reporte -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
            <!-- Reporte General -->
            <div class="bg-white rounded-2xl shadow-xl p-8">
                <div class="flex items-center mb-6">
                    <div class="bg-teal-100 w-16 h-16 rounded-xl flex items-center justify-center mr-4">
                        <i class="fas fa-file-pdf text-3xl text-teal-600"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900">Reporte General</h3>
                        <p class="text-sm text-gray-600">PDF con todas las unidades</p>
                    </div>
                </div>
                
                <form id="generalReportForm">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Período</label>
                        <select name="period" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500">
                            <option value="current_month">Mes Actual</option>
                            <option value="last_month">Mes Anterior</option>
                            <option value="current_quarter">Trimestre Actual</option>
                            <option value="last_quarter">Trimestre Anterior</option>
                            <option value="current_year">Año Actual</option>
                            <option value="last_year">Año Anterior</option>
                            <option value="custom">Personalizado</option>
                        </select>
                    </div>
                    
                    <div id="customDates" class="hidden mb-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Desde</label>
                                <input type="date" name="start_date" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Hasta</label>
                                <input type="date" name="end_date" max="{{ date('Y-m-d') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="w-full bg-teal-600 hover:bg-teal-700 text-white px-6 py-3 rounded-xl font-bold transition shadow-lg">
                        <i class="fas fa-file-pdf mr-2"></i>Generar PDF
                    </button>
                </form>
            </div>

            <!-- Reporte Excel -->
            <div class="bg-white rounded-2xl shadow-xl p-8">
                <div class="flex items-center mb-6">
                    <div class="bg-green-100 w-16 h-16 rounded-xl flex items-center justify-center mr-4">
                        <i class="fas fa-file-excel text-3xl text-green-600"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900">Reporte Excel</h3>
                        <p class="text-sm text-gray-600">Datos para análisis</p>
                    </div>
                </div>
                
                <form id="excelReportForm">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Datos</label>
                        <select name="data_type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                            <option value="all_consumptions">Todos los Consumos</option>
                            <option value="by_unit">Por Unidad Productiva</option>
                            <option value="by_factor">Por Factor de Emisión</option>
                            <option value="summary">Resumen Mensual</option>
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Período</label>
                        <select name="period" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                            <option value="current_year">Año Actual</option>
                            <option value="last_year">Año Anterior</option>
                            <option value="all">Todo el Histórico</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-xl font-bold transition shadow-lg">
                        <i class="fas fa-file-excel mr-2"></i>Generar Excel
                    </button>
                </form>
            </div>
        </div>

        <!-- Reportes por Unidad -->
        <div class="bg-white rounded-2xl shadow-xl p-8 mb-8">
            <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                <i class="fas fa-industry text-teal-600 mr-3"></i>
                Reporte por Unidad Específica
            </h3>
            
            <form id="unitReportForm" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Unidad Productiva</label>
                        <select name="unit_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Seleccione --</option>
                            @foreach($units as $unit)
                            <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Formato</label>
                        <select name="format" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="pdf">PDF</option>
                            <option value="excel">Excel</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Período</label>
                        <select name="period" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="current_month">Mes Actual</option>
                            <option value="last_month">Mes Anterior</option>
                            <option value="current_quarter">Trimestre Actual</option>
                            <option value="last_quarter">Trimestre Anterior</option>
                            <option value="current_year">Año Actual</option>
                            <option value="last_year">Año Anterior</option>
                            <option value="all" selected>Todo el Histórico</option>
                            <option value="custom">Personalizado</option>
                        </select>
                    </div>
                    
                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-bold transition">
                            <i class="fas fa-download mr-2"></i>Descargar
                        </button>
                    </div>
                </div>
                
                <div id="unitCustomDates" class="hidden grid grid-cols-1 md:grid-cols-2 gap-4 max-w-2xl">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Desde</label>
                        <input type="date" name="start_date" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Hasta</label>
                        <input type="date" name="end_date" max="{{ date('Y-m-d') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    </div>
                </div>
            </form>
        </div>

        <!-- Estadísticas de Exportaciones -->
        <div class="bg-gradient-to-r from-teal-500 to-cyan-600 rounded-2xl shadow-xl p-8 text-white mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-2xl font-bold mb-2">
                        <i class="fas fa-info-circle mr-2"></i>
                        Información de Reportes
                    </h3>
                    <p class="opacity-90">Datos disponibles para exportación</p>
                </div>
                <div class="text-right">
                    <p class="text-5xl font-bold">{{ number_format($totalRecords) }}</p>
                    <p class="text-sm opacity-90">registros totales</p>
                </div>
            </div>
            <div class="mt-6 grid grid-cols-3 gap-4">
                <div class="bg-white/20 rounded-lg p-4">
                    <p class="text-sm opacity-90">Total CO₂</p>
                    <p class="text-2xl font-bold">{{ number_format($totalCO2, 0) }} kg</p>
                </div>
                <div class="bg-white/20 rounded-lg p-4">
                    <p class="text-sm opacity-90">Unidades</p>
                    <p class="text-2xl font-bold">{{ $totalUnits }}</p>
                </div>
                <div class="bg-white/20 rounded-lg p-4">
                    <p class="text-sm opacity-90">Período</p>
                    <p class="text-2xl font-bold">2022-2024</p>
                </div>
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
// Mostrar/ocultar fechas personalizadas
$('select[name="period"]').on('change', function() {
    if ($(this).val() === 'custom') {
        $('#customDates').removeClass('hidden');
    } else {
        $('#customDates').addClass('hidden');
    }
});

// Form de reporte general PDF
$('#generalReportForm').on('submit', function(e) {
    e.preventDefault();
    
    const formData = $(this).serialize();
    
    showToast('info', 'Generando reporte PDF...');
    
    window.open('/huellacarbono/admin/reportes/exportar-pdf?' + formData, '_blank');
    
    setTimeout(() => {
        showToast('success', 'Reporte generado');
    }, 1500);
});

// Form de reporte Excel (data_type y period en la URL)
$('#excelReportForm').on('submit', function(e) {
    e.preventDefault();
    var formData = $(this).serialize();
    showToast('info', 'Generando reporte Excel...');
    window.open("{{ url('/huellacarbono/admin/reportes/exportar-excel') }}?" + formData, '_blank');
    setTimeout(function() { showToast('success', 'Reporte generado'); }, 1500);
});

// Form de reporte por unidad (sin depender de jQuery)
(function() {
    var form = document.getElementById('unitReportForm');
    if (!form) return;
    var basePdf = "{{ url('/huellacarbono/admin/reportes/exportar-pdf') }}";
    var baseExcel = "{{ url('/huellacarbono/admin/reportes/exportar-excel') }}";
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        var unitSelect = form.querySelector('select[name="unit_id"]');
        var formatSelect = form.querySelector('select[name="format"]');
        var periodSelect = form.querySelector('select[name="period"]');
        var unitId = unitSelect ? unitSelect.value : '';
        var format = formatSelect ? formatSelect.value : 'pdf';
        var period = periodSelect ? periodSelect.value : 'all';
        if (!unitId) {
            if (typeof showToast === 'function') showToast('error', 'Selecciona una unidad productiva');
            return;
        }
        if (period === 'custom') {
            var startInput = form.querySelector('input[name="start_date"]');
            var endInput = form.querySelector('input[name="end_date"]');
            if (!startInput || !endInput || !startInput.value || !endInput.value) {
                if (typeof showToast === 'function') showToast('error', 'Indica las fechas Desde y Hasta para período personalizado');
                return;
            }
        }
        if (typeof showToast === 'function') showToast('info', 'Generando reporte...');
        var params = new URLSearchParams({ unit_id: unitId, period: period });
        if (period === 'custom') {
            params.set('start_date', form.querySelector('input[name="start_date"]').value);
            params.set('end_date', form.querySelector('input[name="end_date"]').value);
        }
        var url = (format === 'pdf' ? basePdf : baseExcel) + '?' + params.toString();
        window.open(url, '_blank');
    });
})();
</script>
@endsection





