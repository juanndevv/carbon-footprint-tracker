@extends('huellacarbono::layouts.master')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">
                <i class="fas fa-users text-teal-600"></i> Gestión de Usuarios
            </h1>
            <p class="text-gray-600">Control de accesos y asignación de roles</p>
        </div>

        <!-- Roles Disponibles -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            @foreach($roles as $role)
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center mb-4">
                    <div class="bg-{{ $loop->index == 0 ? 'red' : ($loop->index == 1 ? 'blue' : 'green') }}-100 w-12 h-12 rounded-xl flex items-center justify-center mr-3">
                        <i class="fas fa-{{ $loop->index == 0 ? 'user-shield' : ($loop->index == 1 ? 'user-tie' : 'user-cog') }} text-2xl text-{{ $loop->index == 0 ? 'red' : ($loop->index == 1 ? 'blue' : 'green') }}-600"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900">{{ $role->name }}</h3>
                        <p class="text-xs text-gray-500">{{ $role->slug }}</p>
                    </div>
                </div>
                <p class="text-sm text-gray-600">{{ $role->description }}</p>
            </div>
            @endforeach
        </div>

        <!-- Tabla de Usuarios -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-teal-600 to-emerald-700 px-6 py-4">
                <h3 class="text-xl font-bold text-white">
                    <i class="fas fa-list mr-2"></i> Usuarios del Sistema
                </h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Usuario</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Email</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Roles en HC</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($users as $user)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="bg-teal-100 w-10 h-10 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-user text-teal-600"></i>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-900">{{ $user->nickname }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $user->email }}</td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-2">
                                    @php
                                        $hcRoles = $user->roles->filter(function ($role) { $s = (string) ($role->slug ?? ''); return strpos($s, 'huellacarbono.') === 0; });
                                    @endphp
                                    @forelse($hcRoles as $role)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold 
                                              {{ $role->slug == 'huellacarbono.admin' ? 'bg-emerald-100 text-emerald-800' : 'bg-green-100 text-green-800' }}">
                                            {{ $role->name }}
                                        </span>
                                    @empty
                                        <span class="text-xs text-gray-400 italic">Sin roles asignados</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @php
                                        $currentHcRole = $user->roles->filter(function ($r) { $s = (string) ($r->slug ?? ''); return strpos($s, 'huellacarbono.') === 0; })->first();
                                    @endphp
                                    <button type="button"
                                        class="assign-role-btn text-teal-600 hover:text-teal-800 transition"
                                        title="Asignar o cambiar rol"
                                        data-user-id="{{ $user->id }}"
                                        data-user-nickname="{{ e($user->nickname) }}"
                                        data-current-role-id="{{ $currentHcRole ? $currentHcRole->id : '' }}">
                                    <i class="fas fa-user-tag text-xl"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Paginación -->
            <div class="px-6 py-4 bg-gray-50">
                {{ $users->links() }}
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

<!-- Modal Asignar Rol -->
<div id="assignRoleModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
        <h3 class="text-2xl font-bold text-gray-900 mb-4">
            <i class="fas fa-user-tag text-teal-600 mr-2"></i>
            Asignar Rol
        </h3>
        <p class="text-gray-600 mb-4" id="userNameText"></p>
        
        <form id="assignRoleForm">
            @csrf
            <input type="hidden" id="user_id" name="user_id">
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Rol en Huella de Carbono</label>
                <select name="role_id" id="assign_role_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500">
                    <option value="">— Sin rol (quitar acceso) —</option>
                    @foreach($roles as $role)
                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="flex space-x-3">
                <button type="submit" class="flex-1 bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg font-semibold transition">
                    <i class="fas fa-check mr-2"></i>Guardar
                </button>
                <button type="button" onclick="closeModal('assignRoleModal')" 
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
var assignRoleUrl = "{{ route('cefa.huellacarbono.admin.users.assign_role', ['id' => '__ID__']) }}";

function openAssignRoleModal(btn) {
    var userId = btn.getAttribute('data-user-id');
    var userName = btn.getAttribute('data-user-nickname') || '';
    var currentRoleId = btn.getAttribute('data-current-role-id') || '';
    document.getElementById('user_id').value = userId;
    document.getElementById('userNameText').textContent = 'Usuario: ' + userName;
    var selectEl = document.getElementById('assign_role_id');
    if (selectEl) selectEl.value = currentRoleId;
    document.getElementById('assignRoleModal').classList.remove('hidden');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

// Enlazar clic a todos los botones de asignar rol (delegación por si hay paginación)
document.addEventListener('click', function(e) {
    var btn = e.target.closest && e.target.closest('.assign-role-btn');
    if (btn) {
        e.preventDefault();
        openAssignRoleModal(btn);
    }
});

document.getElementById('assignRoleForm').addEventListener('submit', function(e) {
    e.preventDefault();
    var userId = document.getElementById('user_id').value;
    var roleId = document.getElementById('assign_role_id').value;
    var url = assignRoleUrl.replace('__ID__', userId);
    
    var formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('role_id', roleId);
    
    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(function(response) {
        return response.json().then(function(data) {
            if (response.ok && data.success) {
                showToast('success', data.message || 'Rol actualizado');
                setTimeout(function() { location.reload(); }, 1000);
            } else {
                showToast('error', data.message || 'Error al asignar rol');
            }
        });
    })
    .catch(function(error) {
        console.error('Error:', error);
        showToast('error', 'Error al asignar rol');
    });
});
</script>
@endsection

