@extends('huellacarbono::layouts.master')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-4xl font-bold text-gray-900 mb-2">
                    <i class="fas fa-flask text-teal-600"></i> Factores de Emisión
                </h1>
                <p class="text-gray-600">Gestión de variables y coeficientes de cálculo</p>
            </div>
            <button type="button" id="btnNewFactor" 
                    class="bg-teal-600 hover:bg-teal-700 text-white px-6 py-3 rounded-xl font-semibold transition shadow-lg flex items-center">
                <i class="fas fa-plus mr-2"></i> Nuevo Factor
            </button>
        </div>

        <!-- Información -->
        <div class="bg-blue-50 border-l-4 border-blue-500 rounded-lg p-4 mb-8">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-blue-600 text-2xl mr-3"></i>
                <div>
                    <h4 class="font-semibold text-gray-900 mb-1">Importante</h4>
                    <p class="text-sm text-gray-700">
                        Los factores de emisión son los coeficientes que convierten las actividades en kg de CO₂. 
                        <strong>Modificar estos valores afectará todos los cálculos futuros.</strong>
                    </p>
                </div>
            </div>
        </div>

        <!-- Tabla de Factores -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-teal-600 to-emerald-700 px-6 py-4">
                <h3 class="text-xl font-bold text-white">
                    <i class="fas fa-list-ol mr-2"></i> Factores de Emisión Configurados
                </h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Orden</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Variable</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Código</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Unidad</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase">Factor</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">% N₂</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Estado</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($factors as $factor)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-teal-100 text-teal-700 font-bold text-sm">
                                    {{ $factor->order }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="bg-teal-100 w-10 h-10 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-leaf text-teal-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">{{ $factor->name }}</p>
                                        @if($factor->description)
                                        <p class="text-xs text-gray-500">{{ $factor->description }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ $factor->code }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                    {{ $factor->unit }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="text-lg font-bold text-green-600">
                                    {{ $factor->factor }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($factor->requires_percentage)
                                    <i class="fas fa-check-circle text-green-600 text-xl" title="Requiere % Nitrógeno"></i>
                                @else
                                    <i class="fas fa-minus-circle text-gray-300 text-xl"></i>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($factor->is_active)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i> Activo
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                                        <i class="fas fa-times-circle mr-1"></i> Inactivo
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center space-x-2">
                                    <button type="button" class="edit-factor-btn text-green-600 hover:text-green-800 transition"
                                            title="Editar Factor"
                                            data-factor-id="{{ $factor->id }}"
                                            data-factor-name="{{ e($factor->name) }}"
                                            data-factor-code="{{ e($factor->code) }}"
                                            data-factor-unit="{{ e($factor->unit) }}"
                                            data-factor-value="{{ $factor->factor }}"
                                            data-factor-description="{{ e($factor->description ?? '') }}"
                                            data-factor-requires-percentage="{{ $factor->requires_percentage ? '1' : '0' }}"
                                            data-factor-order="{{ $factor->order }}">
                                        <i class="fas fa-edit text-lg"></i>
                                    </button>
                                    <button type="button" class="toggle-factor-btn text-{{ $factor->is_active ? 'red' : 'green' }}-600 hover:text-{{ $factor->is_active ? 'red' : 'green' }}-800 transition"
                                            title="{{ $factor->is_active ? 'Desactivar' : 'Activar' }}"
                                            data-factor-id="{{ $factor->id }}"
                                            data-factor-active="{{ $factor->is_active ? '1' : '0' }}">
                                        <i class="fas fa-{{ $factor->is_active ? 'toggle-on' : 'toggle-off' }} text-lg"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Nota Informativa -->
        <div class="mt-8 bg-gradient-to-r from-yellow-50 to-orange-50 border border-yellow-200 rounded-2xl p-6">
            <div class="flex items-start">
                <div class="bg-yellow-400 w-12 h-12 rounded-xl flex items-center justify-center mr-4 flex-shrink-0">
                    <i class="fas fa-lightbulb text-white text-2xl"></i>
                </div>
                <div>
                    <h4 class="font-bold text-gray-900 mb-2">¿Qué son los Factores de Emisión?</h4>
                    <p class="text-gray-700 text-sm leading-relaxed">
                        Son coeficientes científicos que relacionan una actividad específica (consumo de energía, agua, combustibles, etc.) 
                        con la cantidad de CO₂ equivalente que genera. Por ejemplo, el factor de emisión de la electricidad (0.112) 
                        significa que por cada kWh consumido se generan 0.112 kg de CO₂.
                    </p>
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

<!-- Modal Nuevo Factor -->
<div id="createFactorModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full p-6 max-h-[90vh] overflow-y-auto">
        <h3 class="text-2xl font-bold text-gray-900 mb-4">
            <i class="fas fa-plus text-teal-600 mr-2"></i> Nuevo Factor de Emisión
        </h3>
        <form id="createFactorForm">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nombre (Variable) *</label>
                    <input type="text" name="name" id="create_name" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500" placeholder="Ej: Consumo de agua">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Código *</label>
                    <input type="text" name="code" id="create_code" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500" placeholder="Ej: WATER">
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Unidad de Medida *</label>
                    <input type="text" name="unit" id="create_unit" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500" placeholder="Ej: L, Kw/h, galón">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Factor de Emisión *</label>
                    <input type="number" name="factor" id="create_factor" step="0.0000001" min="0" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500" placeholder="Ej: 0.0001427">
                </div>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Descripción (opcional)</label>
                <textarea name="description" id="create_description" rows="2" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500" placeholder="Descripción del factor..."></textarea>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Orden</label>
                    <input type="number" name="order" id="create_order" min="0" value="{{ ($factors->max('order') ?? 0) + 1 }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500">
                </div>
                <div class="flex items-center pt-8">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="requires_percentage" id="create_requires_percentage" value="1" class="rounded border-gray-300 text-teal-600 focus:ring-teal-500">
                        <span class="ml-2 text-sm text-gray-700">Requiere % Nitrógeno</span>
                    </label>
                </div>
            </div>
            <div class="flex space-x-3">
                <button type="submit" class="flex-1 bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg font-semibold transition"><i class="fas fa-save mr-2"></i>Crear Factor</button>
                <button type="button" onclick="closeModal('createFactorModal')" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg font-semibold transition">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Editar Factor -->
<div id="editFactorModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full p-6">
        <h3 class="text-2xl font-bold text-gray-900 mb-4">
            <i class="fas fa-edit text-teal-600 mr-2"></i>
            Editar Factor de Emisión
        </h3>
        
        <form id="editFactorForm">
            @csrf
            <input type="hidden" name="factor_id" id="factor_id">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nombre (Variable)</label>
                    <input type="text" name="name" id="factor_name" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500"
                           placeholder="Ej: Consumo de agua">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Código</label>
                    <input type="text" name="code" id="factor_code" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500"
                           placeholder="Ej: WATER">
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Unidad de Medida</label>
                    <input type="text" name="unit" id="factor_unit" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500"
                           placeholder="Ej: L, Kw/h">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Factor de Emisión</label>
                    <input type="number" name="factor" id="factor_value" step="0.0000001" min="0" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500"
                           placeholder="Ej: 0.0001427">
                </div>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Descripción (opcional)</label>
                <textarea name="description" id="factor_description" rows="2"
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500"
                          placeholder="Descripción del factor..."></textarea>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Orden</label>
                    <input type="number" name="order" id="factor_order" min="0" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500">
                </div>
                <div class="flex items-center pt-8">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="requires_percentage" id="factor_requires_percentage" value="1"
                               class="rounded border-gray-300 text-teal-600 focus:ring-teal-500">
                        <span class="ml-2 text-sm text-gray-700">Requiere % Nitrógeno</span>
                    </label>
                </div>
            </div>
            
            <div class="flex space-x-3">
                <button type="submit" class="flex-1 bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg font-semibold transition">
                    <i class="fas fa-save mr-2"></i>Guardar Cambios
                </button>
                <button type="button" onclick="closeModal('editFactorModal')" 
                        class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg font-semibold transition">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('script')
<script>
var updateFactorUrl = "{{ route('cefa.huellacarbono.admin.factors.update', ['id' => '__ID__']) }}";
var toggleFactorUrl = "{{ route('cefa.huellacarbono.admin.factors.toggle_status', ['id' => '__ID__']) }}";
var storeFactorUrl = "{{ route('cefa.huellacarbono.admin.factors.store') }}";

function openEditFactorModal(btn) {
    document.getElementById('factor_id').value = btn.getAttribute('data-factor-id');
    document.getElementById('factor_name').value = btn.getAttribute('data-factor-name') || '';
    document.getElementById('factor_code').value = btn.getAttribute('data-factor-code') || '';
    document.getElementById('factor_unit').value = btn.getAttribute('data-factor-unit') || '';
    document.getElementById('factor_value').value = btn.getAttribute('data-factor-value') || '';
    document.getElementById('factor_description').value = btn.getAttribute('data-factor-description') || '';
    document.getElementById('factor_order').value = btn.getAttribute('data-factor-order') || '';
    document.getElementById('factor_requires_percentage').checked = btn.getAttribute('data-factor-requires-percentage') === '1';
    document.getElementById('editFactorModal').classList.remove('hidden');
}

function openCreateFactorModal() {
    var modal = document.getElementById('createFactorModal');
    if (!modal) return;
    var orderInput = document.getElementById('create_order');
    if (orderInput) orderInput.value = '{{ ($factors->max("order") ?? 0) + 1 }}';
    var nameEl = document.getElementById('create_name');
    if (nameEl) nameEl.value = '';
    var codeEl = document.getElementById('create_code');
    if (codeEl) codeEl.value = '';
    var unitEl = document.getElementById('create_unit');
    if (unitEl) unitEl.value = '';
    var factorEl = document.getElementById('create_factor');
    if (factorEl) factorEl.value = '';
    var descEl = document.getElementById('create_description');
    if (descEl) descEl.value = '';
    var reqPct = document.getElementById('create_requires_percentage');
    if (reqPct) reqPct.checked = false;
    modal.classList.remove('hidden');
}

var btnNewFactor = document.getElementById('btnNewFactor');
if (btnNewFactor) btnNewFactor.addEventListener('click', function() { openCreateFactorModal(); });

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

document.addEventListener('click', function(e) {
    var editBtn = e.target.closest && e.target.closest('.edit-factor-btn');
    if (editBtn) {
        e.preventDefault();
        openEditFactorModal(editBtn);
        return;
    }
    var toggleBtn = e.target.closest && e.target.closest('.toggle-factor-btn');
    if (toggleBtn) {
        e.preventDefault();
        var factorId = toggleBtn.getAttribute('data-factor-id');
        var isActive = toggleBtn.getAttribute('data-factor-active') === '1';
        Swal.fire({
            title: '¿Estás seguro?',
            text: isActive ? '¿Desactivar este factor?' : '¿Activar este factor?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Sí, cambiar',
            cancelButtonText: 'Cancelar'
        }).then(function(result) {
            if (result.isConfirmed) {
                var url = toggleFactorUrl.replace('__ID__', factorId);
                fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({})
                })
                .then(function(response) { return response.json(); })
                .then(function(data) {
                    if (data.success) {
                        showToast('success', data.message);
                        setTimeout(function() { location.reload(); }, 1000);
                    } else {
                        showToast('error', data.message || 'Error al cambiar estado');
                    }
                })
                .catch(function(err) {
                    showToast('error', 'Error al cambiar estado');
                });
            }
        });
    }
});

var createFactorFormEl = document.getElementById('createFactorForm');
if (createFactorFormEl) {
createFactorFormEl.addEventListener('submit', function(e) {
    e.preventDefault();
    var payload = {
        name: document.getElementById('create_name').value,
        code: document.getElementById('create_code').value,
        unit: document.getElementById('create_unit').value,
        factor: parseFloat(document.getElementById('create_factor').value),
        description: document.getElementById('create_description').value || null,
        order: parseInt(document.getElementById('create_order').value, 10) || 0,
        requires_percentage: document.getElementById('create_requires_percentage').checked
    };
    fetch(storeFactorUrl, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(payload)
    })
    .then(function(response) {
        return response.json().then(function(data) {
            if (response.ok && data.success) {
                showToast('success', data.message || 'Factor creado exitosamente');
                closeModal('createFactorModal');
                setTimeout(function() { location.reload(); }, 1000);
            } else {
                var msg = data.message || (data.errors ? (typeof data.errors === 'object' ? Object.values(data.errors).flat().join(' ') : data.errors) : 'Error al crear');
                showToast('error', msg);
            }
        });
    })
    .catch(function(err) {
        showToast('error', 'Error al crear el factor');
    });
});
}

document.getElementById('editFactorForm').addEventListener('submit', function(e) {
    e.preventDefault();
    var factorId = document.getElementById('factor_id').value;
    var url = updateFactorUrl.replace('__ID__', factorId);
    var payload = {
        name: document.getElementById('factor_name').value,
        code: document.getElementById('factor_code').value,
        unit: document.getElementById('factor_unit').value,
        factor: parseFloat(document.getElementById('factor_value').value),
        description: document.getElementById('factor_description').value || null,
        order: parseInt(document.getElementById('factor_order').value, 10),
        requires_percentage: document.getElementById('factor_requires_percentage').checked
    };
    fetch(url, {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(payload)
    })
    .then(function(response) {
        return response.json().then(function(data) {
            if (response.ok && data.success) {
                showToast('success', data.message);
                closeModal('editFactorModal');
                setTimeout(function() { location.reload(); }, 1000);
            } else {
                showToast('error', (data.message || data.errors) || 'Error al guardar');
            }
        });
    })
    .catch(function(err) {
        showToast('error', 'Error al guardar cambios');
    });
});
</script>
@endsection

