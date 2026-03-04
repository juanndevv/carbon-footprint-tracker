@extends('huellacarbono::layouts.master')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">
                <i class="fas fa-paper-plane text-teal-600"></i> Solicitar Registro (fecha pasada)
            </h1>
            <p class="text-gray-600">Unidad: <strong>{{ $unit->name }}</strong></p>
        </div>

        <!-- Información -->
        <div class="bg-teal-50 border-l-4 border-teal-500 rounded-lg p-4 mb-8">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-teal-600 text-2xl mr-3"></i>
                <div>
                    <h4 class="font-semibold text-teal-900 mb-1">Requiere aprobación del Admin</h4>
                    <p class="text-sm text-teal-800">
                        Solo puedes registrar el consumo del <strong>día actual</strong> directamente. Para agregar consumos de días anteriores debes enviar esta solicitud; un Admin la revisará y aprobará o rechazará.
                    </p>
                </div>
            </div>
        </div>

        <!-- Formulario -->
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <form id="requestForm">
                @csrf
                
                <!-- Fecha (solo pasada) -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Fecha del consumo a reportar <span class="text-slate-500">*</span>
                    </label>
                    <input type="date" name="consumption_date" id="consumption_date"
                           max="{{ \Carbon\Carbon::yesterday()->format('Y-m-d') }}"
                           required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                    <p class="text-xs text-gray-500 mt-1">Solo fechas anteriores (el día actual se registra en "Registrar Consumo")</p>
                </div>

                <!-- Observaciones opcionales -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Motivo u observaciones (opcional)</label>
                    <textarea name="observations" id="observations" rows="2"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500"
                              placeholder="Ej: Se olvidó reportar ese día..."></textarea>
                </div>

                <!-- Variables (mismo componente que registrar) -->
                <div class="mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-list-ul text-teal-600 mr-2"></i>
                            Variables a incluir en la solicitud
                        </h3>
                        <button type="button" onclick="addVariableRow()" 
                                class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg font-semibold transition shadow">
                            <i class="fas fa-plus mr-2"></i>Agregar Variable
                        </button>
                    </div>
                    <div id="variablesContainer" class="space-y-4"></div>
                </div>

                <!-- Preview -->
                <div id="totalPreview" class="bg-gradient-to-r from-teal-50 to-emerald-50 border-l-4 border-teal-500 rounded-lg p-6 mb-6">
                    <h4 class="font-semibold text-gray-900 mb-3 flex items-center">
                        <i class="fas fa-calculator text-teal-600 mr-2"></i>
                        Resumen de la solicitud
                    </h4>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-600">Variables:</p>
                            <p class="font-bold text-gray-900 text-2xl" id="totalVariables">0</p>
                        </div>
                        <div class="text-right">
                            <p class="text-gray-600 mb-1">CO₂ Total estimado:</p>
                            <p class="text-4xl font-bold text-teal-600" id="totalCO2">0 kg</p>
                        </div>
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex space-x-4">
                    <button type="submit" 
                            class="flex-1 bg-teal-600 hover:bg-teal-700 text-white px-6 py-4 rounded-xl font-bold text-lg transition shadow-lg">
                        <i class="fas fa-paper-plane mr-2"></i> Enviar Solicitud
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

function addVariableRow() {
    variableCounter++;
    const rowId = `variable_${variableCounter}`;
    const factorsOptions = emissionFactors.map(factor => 
        `<option value="${factor.id}" 
                 data-unit="${factor.unit}" 
                 data-factor="${factor.factor}"
                 data-requires-percentage="${factor.requires_percentage ? '1' : '0'}">
            ${factor.name} (${factor.unit}) - Factor: ${factor.factor}
         </option>`
    ).join('');
    
    const html = `
        <div id="${rowId}" class="bg-gray-50 border border-gray-200 rounded-xl p-6 relative">
            <button type="button" onclick="removeVariableRow('${rowId}')" class="absolute top-4 right-4 text-slate-600 hover:text-slate-800">
                <i class="fas fa-times-circle text-2xl"></i>
            </button>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Variable <span class="text-slate-500">*</span></label>
                    <select class="variable-select w-full px-4 py-3 border border-gray-300 rounded-lg" data-row-id="${rowId}" required>
                        <option value="">-- Seleccione --</option>
                        ${factorsOptions}
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cantidad <span class="text-slate-500">*</span></label>
                    <input type="number" class="quantity-input w-full px-4 py-3 border border-gray-300 rounded-lg" step="0.001" min="0" required>
                </div>
            </div>
            <div class="nitrogen-container hidden mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">% Nitrógeno</label>
                <input type="number" class="nitrogen-input w-full px-4 py-3 border border-gray-300 rounded-lg" step="0.01" min="0" max="100">
            </div>
        </div>
    `;
    document.getElementById('variablesContainer').insertAdjacentHTML('beforeend', html);
    attachVariableEvents(rowId);
    updateTotalPreview();
}

function attachVariableEvents(rowId) {
    const row = document.getElementById(rowId);
    const select = row.querySelector('.variable-select');
    const quantityInput = row.querySelector('.quantity-input');
    const nitrogenContainer = row.querySelector('.nitrogen-container');
    const nitrogenInput = row.querySelector('.nitrogen-input');
    
    select.addEventListener('change', function() {
        const opt = this.options[this.selectedIndex];
        const requires = opt.getAttribute('data-requires-percentage') === '1';
        nitrogenContainer.classList.toggle('hidden', !requires);
        if (!requires) nitrogenInput.value = '';
        updateTotalPreview();
    });
    quantityInput.addEventListener('input', () => updateTotalPreview());
    if (nitrogenInput) nitrogenInput.addEventListener('input', () => updateTotalPreview());
}

function removeVariableRow(rowId) {
    document.getElementById(rowId).remove();
    updateTotalPreview();
}

function updateTotalPreview() {
    let totalCO2 = 0, count = 0;
    document.querySelectorAll('#variablesContainer > div').forEach(function(row) {
        const select = row.querySelector('.variable-select');
        const qty = parseFloat(row.querySelector('.quantity-input').value) || 0;
        const factor = parseFloat(select.options[select.selectedIndex]?.getAttribute('data-factor')) || 0;
        const requires = select.options[select.selectedIndex]?.getAttribute('data-requires-percentage') === '1';
        const n2 = parseFloat(row.querySelector('.nitrogen-input')?.value) || 0;
        if (factor && qty > 0) {
            let co2 = qty * factor;
            if (requires && n2 > 0) co2 = co2 * (n2 / 100);
            totalCO2 += co2;
            count++;
        }
    });
    document.getElementById('totalVariables').textContent = count;
    document.getElementById('totalCO2').textContent = totalCO2.toFixed(3) + ' kg';
}

document.addEventListener('DOMContentLoaded', function() {
    addVariableRow();
});

document.getElementById('requestForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const consumptionDate = document.getElementById('consumption_date').value;
    const observations = document.getElementById('observations').value;
    const variables = [];
    document.querySelectorAll('#variablesContainer > div').forEach(function(row) {
        const factorId = row.querySelector('.variable-select').value;
        const quantity = row.querySelector('.quantity-input').value;
        const nitrogen = row.querySelector('.nitrogen-input')?.value;
        if (factorId && quantity) {
            variables.push({
                emission_factor_id: parseInt(factorId, 10),
                quantity: parseFloat(quantity),
                nitrogen_percentage: nitrogen ? parseFloat(nitrogen) : null
            });
        }
    });
    if (variables.length === 0) {
        showToast('error', 'Debes agregar al menos una variable');
        return;
    }
    fetch('{{ route("cefa.huellacarbono.leader.store_request") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            consumption_date: consumptionDate,
            variables: variables,
            observations: observations || null
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast('success', data.message);
            setTimeout(() => { window.location.href = '{{ route("cefa.huellacarbono.leader.dashboard") }}'; }, 1500);
        } else {
            showToast('error', data.message || 'Error al enviar la solicitud');
        }
    })
    .catch(() => showToast('error', 'Error al enviar la solicitud'));
});
</script>
@endsection
