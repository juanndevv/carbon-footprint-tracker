@extends('huellacarbono::layouts.master')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">
                <i class="fas fa-plus-circle text-green-600"></i> Registrar Consumo Diario
            </h1>
            <p class="text-gray-600">Unidad: <strong>{{ $unit->name }}</strong></p>
        </div>

        <!-- Información -->
        <div class="bg-blue-50 border-l-4 border-blue-500 rounded-lg p-4 mb-8">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-blue-600 text-2xl mr-3"></i>
                <div>
                    <h4 class="font-semibold text-gray-900 mb-1">Instrucciones</h4>
                    <p class="text-sm text-gray-700">
                        Registra los consumos diarios de tu unidad productiva. Puedes registrar múltiples factores para una misma fecha.
                        El cálculo de CO₂ se realizará automáticamente.
                    </p>
                </div>
            </div>
        </div>

        <!-- Formulario -->
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <form id="consumptionForm">
                @csrf
                
                <!-- Fecha -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Fecha del Consumo <span class="text-slate-500">*</span>
                    </label>
                    <input type="date" name="consumption_date" id="consumption_date"
                           min="{{ date('Y-m-d') }}"
                           max="{{ date('Y-m-d') }}"
                           value="{{ date('Y-m-d') }}"
                           required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    <p class="text-xs text-gray-500 mt-1">Solo se puede registrar el consumo del día actual</p>
                </div>

                <!-- Sección de Variables Múltiples -->
                <div class="mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-list-ul text-green-600 mr-2"></i>
                            Variables a Registrar
                        </h3>
                        <button type="button" onclick="addVariableRow()" 
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-semibold transition shadow">
                            <i class="fas fa-plus mr-2"></i>Agregar Variable
                        </button>
                    </div>

                    <!-- Contenedor de variables -->
                    <div id="variablesContainer" class="space-y-4">
                        <!-- Aquí se agregarán las variables dinámicamente -->
                    </div>
                </div>

                <!-- Preview del Cálculo Total -->
                <div id="totalPreview" class="bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 rounded-lg p-6 mb-6">
                    <h4 class="font-semibold text-gray-900 mb-3 flex items-center">
                        <i class="fas fa-calculator text-green-600 mr-2"></i>
                        Resumen del Registro
                    </h4>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-600">Variables a registrar:</p>
                            <p class="font-bold text-gray-900 text-2xl" id="totalVariables">0</p>
                        </div>
                        <div class="text-right">
                            <p class="text-gray-600 mb-1">CO₂ Total que se generará:</p>
                            <p class="text-4xl font-bold text-green-600" id="totalCO2">0 kg</p>
                        </div>
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex space-x-4">
                    <button type="submit" 
                            class="flex-1 bg-green-600 hover:bg-green-700 text-white px-6 py-4 rounded-xl font-bold text-lg transition shadow-lg transform hover:scale-105">
                        <i class="fas fa-save mr-2"></i> Guardar Registro
                    </button>
                    <a href="{{ route('cefa.huellacarbono.leader.dashboard') }}" 
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
let variableCounter = 0;
const emissionFactors = @json($emissionFactors);

// Agregar una fila de variable
function addVariableRow() {
    variableCounter++;
    const rowId = `variable_${variableCounter}`;
    
    const factorsOptions = emissionFactors.map(factor => 
        `<option value="${factor.id}" 
                 data-unit="${factor.unit}" 
                 data-factor="${factor.factor}"
                 data-name="${factor.name}"
                 data-requires-percentage="${factor.requires_percentage ? '1' : '0'}">
            ${factor.name} (${factor.unit}) - Factor: ${factor.factor}
         </option>`
    ).join('');
    
    const html = `
        <div id="${rowId}" class="bg-gray-50 border border-gray-200 rounded-xl p-6 relative">
            <button type="button" onclick="removeVariableRow('${rowId}')" 
                    class="absolute top-4 right-4 text-slate-600 hover:text-slate-800 transition">
                <i class="fas fa-times-circle text-2xl"></i>
            </button>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Variable -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Variable <span class="text-slate-500">*</span></label>
                    <select class="variable-select w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500" 
                            data-row-id="${rowId}" required>
                        <option value="">-- Seleccione --</option>
                        ${factorsOptions}
                    </select>
                </div>
                
                <!-- Cantidad -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Cantidad <span class="text-slate-500">*</span>
                        <span class="unit-display text-green-600 font-semibold"></span>
                    </label>
                    <input type="number" class="quantity-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500" 
                           step="0.001" min="0" placeholder="0" required>
                </div>
            </div>
            
            <!-- Porcentaje de Nitrógeno (condicional) -->
            <div class="nitrogen-container hidden mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Porcentaje de Nitrógeno (%) <span class="text-slate-500">*</span>
                </label>
                <input type="number" class="nitrogen-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500" 
                       step="0.01" min="0" max="100" placeholder="Ej: 46">
                <p class="text-xs text-gray-500 mt-1">Revisa la etiqueta del fertilizante</p>
            </div>
            
            <!-- Preview individual -->
            <div class="preview-box hidden mt-4 bg-white border-l-4 border-green-500 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">CO₂ de esta variable:</p>
                        <p class="text-2xl font-bold text-green-600 co2-value">0 kg</p>
                    </div>
                    <i class="fas fa-leaf text-4xl text-green-200"></i>
                </div>
            </div>
        </div>
    `;
    
    $('#variablesContainer').append(html);
    attachVariableEvents(rowId);
    updateTotalPreview();
}

// Adjuntar eventos a una fila de variable
function attachVariableEvents(rowId) {
    const row = $(`#${rowId}`);
    const select = row.find('.variable-select');
    const quantityInput = row.find('.quantity-input');
    const nitrogenInput = row.find('.nitrogen-input');
    const nitrogenContainer = row.find('.nitrogen-container');
    const unitDisplay = row.find('.unit-display');
    const previewBox = row.find('.preview-box');
    const co2Value = row.find('.co2-value');
    
    select.on('change', function() {
        const selectedOption = $(this).find(':selected');
        const requiresPercentage = selectedOption.data('requires-percentage');
        const unit = selectedOption.data('unit');
        
        unitDisplay.text(unit ? `(${unit})` : '');
        
        if (requiresPercentage == 1) {
            nitrogenContainer.removeClass('hidden');
            nitrogenInput.prop('required', true);
        } else {
            nitrogenContainer.addClass('hidden');
            nitrogenInput.prop('required', false).val('');
        }
        
        calculateRowCO2(rowId);
    });
    
    quantityInput.on('input', () => calculateRowCO2(rowId));
    nitrogenInput.on('input', () => calculateRowCO2(rowId));
}

// Calcular CO2 de una fila específica
function calculateRowCO2(rowId) {
    const row = $(`#${rowId}`);
    const select = row.find('.variable-select');
    const selectedOption = select.find(':selected');
    const factor = parseFloat(selectedOption.data('factor'));
    const quantity = parseFloat(row.find('.quantity-input').val()) || 0;
    const nitrogenPercentage = parseFloat(row.find('.nitrogen-input').val()) || 0;
    const requiresPercentage = selectedOption.data('requires-percentage');
    const previewBox = row.find('.preview-box');
    const co2ValueElement = row.find('.co2-value');
    
    if (factor && quantity > 0) {
        let co2 = quantity * factor;
        
        if (requiresPercentage == 1 && nitrogenPercentage > 0) {
            co2 = co2 * (nitrogenPercentage / 100);
        }
        
        previewBox.removeClass('hidden');
        co2ValueElement.text(co2.toFixed(3) + ' kg');
        
        // Guardar CO2 en atributo para cálculo total
        row.data('co2', co2);
    } else {
        previewBox.addClass('hidden');
        row.data('co2', 0);
    }
    
    updateTotalPreview();
}

// Eliminar una fila de variable
function removeVariableRow(rowId) {
    $(`#${rowId}`).remove();
    updateTotalPreview();
}

// Actualizar preview total
function updateTotalPreview() {
    let totalCO2 = 0;
    let count = 0;
    
    $('#variablesContainer > div').each(function() {
        const co2 = $(this).data('co2') || 0;
        totalCO2 += co2;
        count++;
    });
    
    $('#totalVariables').text(count);
    $('#totalCO2').text(totalCO2.toFixed(3) + ' kg');
}

// Agregar primera variable al cargar
$(document).ready(function() {
    addVariableRow();
});

// Submit form
$('#consumptionForm').on('submit', function(e) {
    e.preventDefault();
    
    // Recopilar datos de todas las variables
    const consumptionDate = $('#consumption_date').val();
    const variables = [];
    
    $('#variablesContainer > div').each(function() {
        const row = $(this);
        const factorId = row.find('.variable-select').val();
        const quantity = row.find('.quantity-input').val();
        const nitrogenPercentage = row.find('.nitrogen-input').val();
        
        if (factorId && quantity) {
            variables.push({
                emission_factor_id: factorId,
                quantity: quantity,
                nitrogen_percentage: nitrogenPercentage || null
            });
        }
    });
    
    if (variables.length === 0) {
        showToast('error', 'Debes agregar al menos una variable');
        return;
    }
    
    // Enviar datos
    $.ajax({
        url: '{{ route("cefa.huellacarbono.leader.store_multiple_consumptions") }}',
        method: 'POST',
        data: JSON.stringify({
            consumption_date: consumptionDate,
            variables: variables
        }),
        contentType: 'application/json',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        success: function(response) {
            if(response.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Registro Exitoso!',
                    html: `
                        <p class="mb-2">${response.count} variable(s) registrada(s)</p>
                        <div class="bg-green-50 p-4 rounded-lg">
                            <p class="text-2xl font-bold text-green-600">${response.total_co2} kg CO₂</p>
                            <p class="text-sm text-gray-600">generados en total</p>
                        </div>
                    `,
                    confirmButtonColor: '#10b981',
                }).then(() => {
                    window.location.href = '{{ route("cefa.huellacarbono.leader.dashboard") }}';
                });
            }
        },
        error: function(xhr) {
            const error = xhr.responseJSON?.error || 'Error al guardar el registro';
            showToast('error', error);
        }
    });
});
</script>
@endsection

