@extends('huellacarbono::layouts.master')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">
                <i class="fas fa-paper-plane text-teal-600"></i> Solicitudes de Registro
            </h1>
            <p class="text-gray-600">Líderes solicitan agregar consumos en fechas pasadas. Aprueba o rechaza cada solicitud.</p>
        </div>

        <!-- Tabla de solicitudes -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-teal-600 to-emerald-600 px-6 py-4">
                <h3 class="text-xl font-bold text-white">
                    <i class="fas fa-list mr-2"></i> Todas las solicitudes
                </h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Fecha solicitud</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Unidad</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Fecha a reportar</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Solicitado por</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Variables</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Estado</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($requests as $req)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $req->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                {{ $req->productiveUnit->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $req->consumption_date->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $req->requestedBy->nickname ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                @foreach($req->items as $item)
                                <span class="block">{{ $item->emissionFactor->name ?? 'N/A' }}: {{ $item->quantity }} {{ $item->emissionFactor->unit ?? '' }}</span>
                                @endforeach
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($req->status === 'pending')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                                    Pendiente
                                </span>
                                @elseif($req->status === 'approved')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                    Aprobada
                                </span>
                                @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-800">
                                    Rechazada
                                </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($req->status === 'pending')
                                <button type="button" onclick="approveRequest({{ $req->id }})" 
                                        class="text-green-600 hover:text-green-800 font-medium mr-2" title="Aprobar">
                                    <i class="fas fa-check-circle text-lg"></i>
                                </button>
                                <button type="button" onclick="rejectRequest({{ $req->id }})" 
                                        class="text-slate-600 hover:text-slate-800 font-medium" title="Rechazar">
                                    <i class="fas fa-times-circle text-lg"></i>
                                </button>
                                @else
                                <span class="text-gray-400 text-sm">—</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-inbox text-5xl text-gray-300 mb-4"></i>
                                <p>No hay solicitudes</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 bg-gray-50">
                {{ $requests->links() }}
            </div>
        </div>

        <div class="mt-8">
            <a href="{{ route('cefa.huellacarbono.admin.dashboard') }}" 
               class="inline-flex items-center px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-xl transition">
                <i class="fas fa-arrow-left mr-2"></i> Volver al Dashboard
            </a>
        </div>
    </div>
</div>

<!-- Modal de confirmación (card) -->
<div id="confirmModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog">
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="fixed inset-0 bg-gray-900/60 transition-opacity" id="confirmModalBackdrop"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden transform transition-all">
            <div id="confirmModalApproveHeader" class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-4">
                <h3 class="text-lg font-bold text-white flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    <span id="confirmModalTitle">Confirmar aprobación</span>
                </h3>
            </div>
            <div id="confirmModalRejectHeader" class="hidden bg-gradient-to-r from-slate-600 to-slate-700 px-6 py-4">
                <h3 class="text-lg font-bold text-white flex items-center">
                    <i class="fas fa-times-circle mr-2"></i>
                    <span>Confirmar rechazo</span>
                </h3>
            </div>
            <div class="px-6 py-5">
                <p id="confirmModalMessage" class="text-gray-600">¿Aprobar esta solicitud? Se crearán los consumos en el sistema.</p>
            </div>
            <div class="px-6 py-4 bg-gray-50 flex justify-end gap-3 rounded-b-2xl">
                <button type="button" id="confirmModalCancel" class="px-4 py-2 rounded-xl font-medium text-gray-700 bg-gray-200 hover:bg-gray-300 transition">
                    Cancelar
                </button>
                <button type="button" id="confirmModalOk" class="px-4 py-2 rounded-xl font-medium text-white bg-green-600 hover:bg-green-700 transition flex items-center">
                    <i class="fas fa-check mr-2"></i> Aceptar
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
var approveUrl = "{{ route('cefa.huellacarbono.admin.requests.approve', ['id' => '__ID__']) }}";
var rejectUrl = "{{ route('cefa.huellacarbono.admin.requests.reject', ['id' => '__ID__']) }}";

var confirmModal = document.getElementById('confirmModal');
var confirmModalApproveHeader = document.getElementById('confirmModalApproveHeader');
var confirmModalRejectHeader = document.getElementById('confirmModalRejectHeader');
var confirmModalMessage = document.getElementById('confirmModalMessage');
var confirmModalOk = document.getElementById('confirmModalOk');
var confirmModalCancel = document.getElementById('confirmModalCancel');
var confirmModalBackdrop = document.getElementById('confirmModalBackdrop');

var pendingRequestId = null;
var pendingAction = null; // 'approve' | 'reject'

function openConfirmCard(action, id) {
    pendingAction = action;
    pendingRequestId = id;
    if (action === 'approve') {
        confirmModalApproveHeader.classList.remove('hidden');
        confirmModalRejectHeader.classList.add('hidden');
        confirmModalMessage.textContent = '¿Aprobar esta solicitud? Se crearán los consumos en el sistema.';
        confirmModalOk.className = 'px-4 py-2 rounded-xl font-medium text-white bg-green-600 hover:bg-green-700 transition flex items-center';
        confirmModalOk.innerHTML = '<i class="fas fa-check mr-2"></i> Aceptar';
    } else {
        confirmModalApproveHeader.classList.add('hidden');
        confirmModalRejectHeader.classList.remove('hidden');
        confirmModalMessage.textContent = '¿Rechazar esta solicitud? No se crearán consumos.';
        confirmModalOk.className = 'px-4 py-2 rounded-xl font-medium text-white bg-slate-600 hover:bg-slate-700 transition flex items-center';
        confirmModalOk.innerHTML = '<i class="fas fa-times mr-2"></i> Rechazar';
    }
    confirmModal.classList.remove('hidden');
}

function closeConfirmCard() {
    confirmModal.classList.add('hidden');
    pendingRequestId = null;
    pendingAction = null;
}

function doConfirmAction() {
    if (!pendingRequestId || !pendingAction) return;
    var url = pendingAction === 'approve' ? approveUrl.replace('__ID__', pendingRequestId) : rejectUrl.replace('__ID__', pendingRequestId);
    closeConfirmCard();
    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({})
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast('success', data.message);
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast('error', data.message || 'Error');
        }
    })
    .catch(() => showToast('error', pendingAction === 'approve' ? 'Error al aprobar' : 'Error al rechazar'));
}

confirmModalOk.addEventListener('click', doConfirmAction);
confirmModalCancel.addEventListener('click', closeConfirmCard);
confirmModalBackdrop.addEventListener('click', closeConfirmCard);

function approveRequest(id) {
    openConfirmCard('approve', id);
}

function rejectRequest(id) {
    openConfirmCard('reject', id);
}
</script>
@endsection
