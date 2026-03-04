@extends('huellacarbono::layouts.master')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-4xl font-bold text-gray-900 mb-2">
                    <i class="fas fa-industry text-teal-600"></i> Unidades Productivas
                </h1>
                <p class="text-gray-600">Gestión de unidades y asignación de líderes</p>
            </div>
            <button onclick="openCreateModal()" 
                    class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-xl font-semibold transition shadow-lg flex items-center">
                <i class="fas fa-plus mr-2"></i> Nueva Unidad
            </button>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 mb-1">Total Unidades</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $units->count() }}</p>
                    </div>
                    <i class="fas fa-building text-4xl text-teal-500"></i>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 mb-1">Activas</p>
                        <p class="text-3xl font-bold text-green-600">{{ $units->where('is_active', true)->count() }}</p>
                    </div>
                    <i class="fas fa-check-circle text-4xl text-green-500"></i>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 mb-1">Con Líder Asignado</p>
                        <p class="text-3xl font-bold text-teal-600">{{ $units->whereNotNull('leader_user_id')->count() }}</p>
                    </div>
                    <i class="fas fa-user-tie text-4xl text-teal-500"></i>
                </div>
            </div>
        </div>

        <!-- Tabla de Unidades -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-teal-600 to-emerald-700 px-6 py-4">
                <h3 class="text-xl font-bold text-white">
                    <i class="fas fa-list mr-2"></i> Listado de Unidades Productivas
                </h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Unidad</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Código</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Líder</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Estado</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($units as $unit)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="bg-teal-100 w-10 h-10 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-industry text-teal-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">{{ $unit->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $unit->description }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ $unit->code }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($unit->leader)
                                    <div class="flex items-center">
                                        <i class="fas fa-user-circle text-green-600 mr-2"></i>
                                        <span class="text-sm text-gray-900">{{ $unit->leader->nickname }}</span>
                                    </div>
                                @else
                                    <span class="text-sm text-gray-400 italic">Sin líder asignado</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($unit->is_active)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i> Activa
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                                        <i class="fas fa-times-circle mr-1"></i> Inactiva
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center space-x-2">
                                    @php
                                        $leaderDisplay = $unit->leader ? (($unit->leader->person ? trim($unit->leader->person->first_name.' '.$unit->leader->person->first_last_name.' '.$unit->leader->person->second_last_name) : $unit->leader->nickname) . ' — ' . $unit->leader->email) : '';
                                    @endphp
                                    <button onclick="openAssignLeaderModal({{ $unit->id }}, {{ json_encode($unit->name) }}, {{ $unit->leader_user_id ?? 'null' }}, {{ json_encode($leaderDisplay) }})" 
                                            class="text-teal-600 hover:text-teal-800 transition"
                                            title="{{ $unit->leader ? 'Cambiar Líder' : 'Asignar Líder' }}">
                                        <i class="fas fa-user-plus text-lg"></i>
                                    </button>
                                    <button onclick="openEditModal({{ $unit->id }}, {{ json_encode($unit->name) }}, {{ json_encode($unit->code) }}, {{ json_encode($unit->description ?? '') }}, {{ $unit->latitude !== null ? $unit->latitude : 'null' }}, {{ $unit->longitude !== null ? $unit->longitude : 'null' }})" 
                                            class="text-green-600 hover:text-green-800 transition"
                                            title="Editar">
                                        <i class="fas fa-edit text-lg"></i>
                                    </button>
                                    <button onclick="toggleStatus({{ $unit->id }}, {{ $unit->is_active ? '0' : '1' }})" 
                                            class="{{ $unit->is_active ? 'text-amber-600 hover:text-amber-800' : 'text-green-600 hover:text-green-800' }} transition"
                                            title="{{ $unit->is_active ? 'Desactivar' : 'Activar' }}">
                                        <i class="fas fa-{{ $unit->is_active ? 'toggle-on' : 'toggle-off' }} text-lg"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
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

<!-- Modal Asignar Líder -->
<div id="assignLeaderModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
        <h3 class="text-2xl font-bold text-gray-900 mb-2">
            <i class="fas fa-user-plus text-teal-600 mr-2"></i>
            Asignar o cambiar líder
        </h3>
        <p class="text-gray-600 mb-2" id="unitNameText"></p>
        <p id="currentLeaderText" class="text-sm font-medium text-green-700 mb-4 hidden">
            <i class="fas fa-user-check mr-1"></i><span id="currentLeaderName"></span>
        </p>
        <p id="noLeaderText" class="text-sm text-gray-500 mb-4 hidden">
            <i class="fas fa-info-circle mr-1"></i>Sin líder asignado
        </p>
        
        <form id="assignLeaderForm">
            @csrf
            <input type="hidden" id="unit_id" name="unit_id">
            
            <div class="mb-4 relative">
                <label class="block text-sm font-medium text-gray-700 mb-2">Seleccionar usuario (líder actual o nuevo)</label>
                <input type="text" id="assign_leader_search" placeholder="Escriba nombre o correo para buscar y asignar..." autocomplete="off"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                <input type="hidden" name="leader_user_id" id="assign_leader_user_id" value="">
                <div id="assign_leader_list" class="hidden absolute z-10 left-0 right-0 mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto"></div>
                <p class="mt-2 text-sm text-gray-500 flex items-center gap-3 flex-wrap">
                    <button type="button" id="assign_leader_clear_btn" onclick="clearAssignLeader()" class="text-gray-600 hover:text-gray-800 hover:underline font-medium">
                        <i class="fas fa-eraser mr-1"></i>Limpiar
                    </button>
                    <span>·</span>
                    <span>O elija "— Sin líder —" en la lista al escribir</span>
                </p>
            </div>
            
            <div class="flex space-x-3">
                <button type="submit" class="flex-1 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-semibold transition">
                    <i class="fas fa-save mr-2"></i>Guardar
                </button>
                <button type="button" onclick="closeModal('assignLeaderModal')" 
                        class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg font-semibold transition">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Crear Nueva Unidad -->
<div id="createUnitModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
        <h3 class="text-2xl font-bold text-gray-900 mb-4">
            <i class="fas fa-plus text-green-600 mr-2"></i>
            Nueva Unidad Productiva
        </h3>
        
        <form id="createUnitForm">
            @csrf
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Nombre <span class="text-slate-500">*</span></label>
                <input type="text" name="name" id="create_unit_name" required
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                       placeholder="Ej: Unidad de Café">
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Código <span class="text-slate-500">*</span></label>
                <input type="text" name="code" id="create_unit_code" required
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                       placeholder="Ej: UP-CAFE">
                <p class="text-xs text-gray-500 mt-1">Debe ser único</p>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                <textarea name="description" id="create_unit_description" rows="3"
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                          placeholder="Descripción de la unidad productiva..."></textarea>
            </div>
            
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Latitud</label>
                    <input type="text" name="latitude" id="create_unit_latitude" inputmode="decimal"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                           placeholder="Ej: 4.5709">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Longitud</label>
                    <input type="text" name="longitude" id="create_unit_longitude" inputmode="decimal"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                           placeholder="Ej: -75.6811">
                </div>
            </div>
            <p class="text-xs text-gray-500 -mt-2 mb-4">Opcional. Para mapa de calor por ubicación.</p>
            
            <div class="mb-4 relative">
                <label class="block text-sm font-medium text-gray-700 mb-2">Asignar Líder (Opcional)</label>
                <input type="text" id="create_unit_leader_search" placeholder="Escriba nombre o correo..." autocomplete="off"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                <input type="hidden" name="leader_user_id" id="create_unit_leader" value="">
                <div id="create_unit_leader_list" class="hidden absolute z-10 left-0 right-0 mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto"></div>
            </div>
            
            <div class="flex space-x-3">
                <button type="submit" class="flex-1 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-semibold transition">
                    <i class="fas fa-save mr-2"></i>Crear Unidad
                </button>
                <button type="button" onclick="closeModal('createUnitModal')" 
                        class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg font-semibold transition">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Editar Unidad -->
<div id="editUnitModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
        <h3 class="text-2xl font-bold text-gray-900 mb-4">
            <i class="fas fa-edit text-green-600 mr-2"></i>
            Editar Unidad Productiva
        </h3>
        
        <form id="editUnitForm">
            @csrf
            <input type="hidden" id="edit_unit_id" name="unit_id">
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Nombre <span class="text-slate-500">*</span></label>
                <input type="text" name="name" id="edit_unit_name" required
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Código <span class="text-slate-500">*</span></label>
                <input type="text" name="code" id="edit_unit_code" required
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                <textarea name="description" id="edit_unit_description" rows="3"
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"></textarea>
            </div>
            
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Latitud</label>
                    <input type="text" name="latitude" id="edit_unit_latitude" inputmode="decimal"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                           placeholder="Ej: 4.5709">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Longitud</label>
                    <input type="text" name="longitude" id="edit_unit_longitude" inputmode="decimal"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                           placeholder="Ej: -75.6811">
                </div>
            </div>
            
            <div class="flex space-x-3">
                <button type="submit" class="flex-1 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-semibold transition">
                    <i class="fas fa-save mr-2"></i>Guardar Cambios
                </button>
                <button type="button" onclick="closeModal('editUnitModal')" 
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
@php
    $leaderListForJs = [['value' => '', 'display' => '-- Sin líder --', 'search' => '']];
    foreach ($users as $user) {
        $displayName = $user->person ? trim($user->person->first_name.' '.$user->person->first_last_name.' '.$user->person->second_last_name) : $user->nickname;
        $searchText = strtolower($displayName.' '.$user->email.' '.$user->nickname);
        $leaderListForJs[] = ['value' => (string)$user->id, 'display' => $displayName.' — '.$user->email, 'search' => $searchText];
    }
@endphp
var leaderUsersList = @json($leaderListForJs);

function showLeaderList(inputId, listId) {
    var input = document.getElementById(inputId);
    var listEl = document.getElementById(listId);
    var search = input.value.trim().toLowerCase();
    var filtered = leaderUsersList.filter(function(item) {
        return !search || item.search.indexOf(search) !== -1;
    });
    listEl.innerHTML = filtered.map(function(item) {
        return '<div class="px-4 py-2.5 cursor-pointer hover:bg-green-50 border-b border-gray-100 last:border-0 text-gray-800" data-value="' + item.value + '" data-display="' + item.display.replace(/"/g, '&quot;') + '">' + item.display.replace(/</g, '&lt;') + '</div>';
    }).join('');
    listEl.classList.remove('hidden');
}

function pickLeader(inputId, hiddenId, listId, value, display) {
    document.getElementById(inputId).value = display;
    document.getElementById(hiddenId).value = value;
    document.getElementById(listId).classList.add('hidden');
}

function setupLeaderAutocomplete(inputId, hiddenId, listId) {
    var input = document.getElementById(inputId);
    var listEl = document.getElementById(listId);
    input.addEventListener('focus', function() { showLeaderList(inputId, listId); });
    input.addEventListener('input', function() { showLeaderList(inputId, listId); });
    listEl.addEventListener('click', function(e) {
        var item = e.target.closest('[data-value]');
        if (item) pickLeader(inputId, hiddenId, listId, item.getAttribute('data-value'), item.getAttribute('data-display'));
    });
    document.addEventListener('click', function(e) {
        if (!input.contains(e.target) && !listEl.contains(e.target)) listEl.classList.add('hidden');
    });
}

function openCreateModal() {
    document.getElementById('createUnitForm').reset();
    document.getElementById('create_unit_leader_search').value = '';
    document.getElementById('create_unit_leader').value = '';
    document.getElementById('create_unit_leader_list').classList.add('hidden');
    document.getElementById('createUnitModal').classList.remove('hidden');
}

function clearAssignLeader() {
    document.getElementById('assign_leader_search').value = '';
    document.getElementById('assign_leader_user_id').value = '';
    document.getElementById('assign_leader_list').classList.add('hidden');
}

function openAssignLeaderModal(unitId, unitName, currentLeaderId, currentLeaderName) {
    document.getElementById('unit_id').value = unitId;
    document.getElementById('unitNameText').textContent = 'Unidad: ' + unitName;
    
    document.getElementById('assign_leader_user_id').value = currentLeaderId || '';
    document.getElementById('assign_leader_search').value = (currentLeaderId && currentLeaderName) ? currentLeaderName : '';
    document.getElementById('assign_leader_list').classList.add('hidden');
    
    var currentText = document.getElementById('currentLeaderText');
    var noLeaderText = document.getElementById('noLeaderText');
    if (currentLeaderId && currentLeaderName) {
        document.getElementById('currentLeaderName').textContent = 'Líder actual: ' + currentLeaderName;
        currentText.classList.remove('hidden');
        noLeaderText.classList.add('hidden');
    } else {
        currentText.classList.add('hidden');
        noLeaderText.classList.remove('hidden');
    }
    
    document.getElementById('assignLeaderModal').classList.remove('hidden');
}

setupLeaderAutocomplete('assign_leader_search', 'assign_leader_user_id', 'assign_leader_list');
setupLeaderAutocomplete('create_unit_leader_search', 'create_unit_leader', 'create_unit_leader_list');

function openEditModal(unitId, unitName, unitCode, unitDescription, latitude, longitude) {
    document.getElementById('edit_unit_id').value = unitId;
    document.getElementById('edit_unit_name').value = unitName;
    document.getElementById('edit_unit_code').value = unitCode;
    document.getElementById('edit_unit_description').value = unitDescription || '';
    document.getElementById('edit_unit_latitude').value = (latitude != null && latitude !== '') ? latitude : '';
    document.getElementById('edit_unit_longitude').value = (longitude != null && longitude !== '') ? longitude : '';
    document.getElementById('editUnitModal').classList.remove('hidden');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

function toggleStatus(unitId, newStatus) {
    // Convertir a booleano
    const willBeActive = newStatus == 1 || newStatus === true || newStatus === 'true';
    
    Swal.fire({
        title: '¿Estás seguro?',
        text: willBeActive ? '¿Activar esta unidad?' : '¿Desactivar esta unidad?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Sí, cambiar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/huellacarbono/admin/unidades/${unitId}/toggle-status`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    is_active: willBeActive
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor');
                }
                return response.json();
            })
            .then(data => {
                if(data.success) {
                    showToast('success', 'Estado actualizado exitosamente');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showToast('error', data.message || 'Error al actualizar el estado');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('error', 'Error al actualizar el estado: ' + error.message);
            });
        }
    });
}

// Formulario de crear unidad
document.getElementById('createUnitForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Convertir a JSON
    const data = {
        name: document.getElementById('create_unit_name').value,
        code: document.getElementById('create_unit_code').value,
        description: document.getElementById('create_unit_description').value,
        latitude: document.getElementById('create_unit_latitude').value || null,
        longitude: document.getElementById('create_unit_longitude').value || null,
        leader_user_id: document.getElementById('create_unit_leader').value || null
    };
    
    fetch('/huellacarbono/admin/unidades/store', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => {
                throw new Error(err.message || 'Error en la respuesta del servidor');
            });
        }
        return response.json();
    })
    .then(data => {
        if(data.success) {
            showToast('success', 'Unidad creada exitosamente');
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast('error', data.message || 'Error al crear la unidad');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('error', 'Error al crear la unidad: ' + error.message);
    });
});

// Formulario de asignar líder
document.getElementById('assignLeaderForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const unitId = document.getElementById('unit_id').value;
    const formData = new FormData(this);
    
    fetch(`/huellacarbono/admin/unidades/${unitId}/asignar-lider`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            showToast('success', 'Líder asignado exitosamente');
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast('error', data.message || 'Error al asignar líder');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('error', 'Error al asignar líder');
    });
});

// Formulario de editar unidad
document.getElementById('editUnitForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const unitId = document.getElementById('edit_unit_id').value;
    
    // Convertir a JSON en lugar de FormData
    const data = {
        name: document.getElementById('edit_unit_name').value,
        code: document.getElementById('edit_unit_code').value,
        description: document.getElementById('edit_unit_description').value,
        latitude: document.getElementById('edit_unit_latitude').value || null,
        longitude: document.getElementById('edit_unit_longitude').value || null
    };
    
    fetch(`/huellacarbono/admin/unidades/${unitId}/update`, {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Error en la respuesta del servidor');
        }
        return response.json();
    })
    .then(data => {
        if(data.success) {
            showToast('success', 'Unidad actualizada exitosamente');
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast('error', data.message || 'Error al actualizar la unidad');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('error', 'Error al actualizar la unidad: ' + error.message);
    });
});
</script>
@endsection

