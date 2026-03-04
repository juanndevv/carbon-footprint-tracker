<?php

namespace Modules\HUELLACARBONO\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\HUELLACARBONO\Entities\ProductiveUnit;
use Modules\HUELLACARBONO\Entities\EmissionFactor;
use Modules\HUELLACARBONO\Entities\DailyConsumption;
use Modules\HUELLACARBONO\Entities\ConsumptionRequest;
use Modules\HUELLACARBONO\Entities\ConsumptionRequestItem;
use Modules\HUELLACARBONO\Exports\ConsumptionsExport;
use Modules\HUELLACARBONO\Exports\ByUnitExport;
use Modules\HUELLACARBONO\Exports\ByFactorExport;
use Modules\HUELLACARBONO\Exports\SummaryExport;
use App\Models\User;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Maatwebsite\Excel\Facades\Excel;

class AdminController extends Controller
{
    /**
     * Dashboard del Admin
     */
    public function dashboard()
    {
        if (!checkRol('huellacarbono.admin')) {
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }

        $totalUnits = ProductiveUnit::count();
        $activeUnits = ProductiveUnit::where('is_active', true)->count();
        $totalFactors = EmissionFactor::count();
        $totalConsumptions = DailyConsumption::count();

        $weeklyTotal = DailyConsumption::whereBetween('consumption_date', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ])->sum('co2_generated');

        $monthlyTotal = DailyConsumption::whereBetween('consumption_date', [
            Carbon::now()->startOfMonth(),
            Carbon::now()->endOfMonth()
        ])->sum('co2_generated');

        $yearlyTotal = DailyConsumption::whereBetween('consumption_date', [
            Carbon::now()->startOfYear(),
            Carbon::now()->endOfYear()
        ])->sum('co2_generated');

        $recentConsumptions = DailyConsumption::with(['productiveUnit', 'emissionFactor', 'registeredBy'])
            ->orderBy('consumption_date', 'desc')
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();

        $today = Carbon::today();
        $unitsWithConsumptionToday = DailyConsumption::whereDate('consumption_date', $today)->pluck('productive_unit_id')->unique();
        $unitsWithoutReportToday = ProductiveUnit::where('is_active', true)
            ->whereNotNull('leader_user_id')
            ->whereNotIn('id', $unitsWithConsumptionToday)
            ->get();

        $pendingRequestsCount = ConsumptionRequest::where('status', 'pending')->count();

        return view('huellacarbono::superadmin.dashboard', compact(
            'totalUnits',
            'activeUnits',
            'totalFactors',
            'totalConsumptions',
            'weeklyTotal',
            'monthlyTotal',
            'yearlyTotal',
            'recentConsumptions',
            'unitsWithoutReportToday',
            'pendingRequestsCount'
        ));
    }

    public function productiveUnits()
    {
        if (!checkRol('huellacarbono.admin')) {
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }

        $units = ProductiveUnit::with('leader')->orderBy('name')->get();
        $users = User::with('person:id,first_name,first_last_name,second_last_name')->get(['id', 'nickname', 'email', 'person_id']);

        return view('huellacarbono::superadmin.units', compact('units', 'users'));
    }

    public function emissionFactors()
    {
        $factors = EmissionFactor::orderBy('order')->get();
        return view('huellacarbono::superadmin.factors', compact('factors'));
    }

    public function allConsumptions(Request $request)
    {
        $query = DailyConsumption::with(['productiveUnit', 'emissionFactor', 'registeredBy']);
        if ($request->has('unit_id')) {
            $query->where('productive_unit_id', $request->unit_id);
        }
        if ($request->has('start_date')) {
            $query->where('consumption_date', '>=', $request->start_date);
        }
        if ($request->has('end_date')) {
            $query->where('consumption_date', '<=', $request->end_date);
        }
        $consumptions = $query->orderBy('consumption_date', 'desc')->orderBy('id', 'desc')->paginate(50);
        $units = ProductiveUnit::where('is_active', true)->get();
        return view('huellacarbono::superadmin.consumptions', compact('consumptions', 'units'));
    }

    public function reports()
    {
        if (!checkRol('huellacarbono.admin')) {
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }
        $units = ProductiveUnit::where('is_active', true)->orderBy('name')->get();
        $totalRecords = DailyConsumption::count();
        $totalCO2 = DailyConsumption::sum('co2_generated');
        $totalUnits = ProductiveUnit::where('is_active', true)->count();
        return view('huellacarbono::superadmin.reports', compact('units', 'totalRecords', 'totalCO2', 'totalUnits'));
    }

    public function charts()
    {
        if (!checkRol('huellacarbono.admin')) {
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }
        $weeklyTotal = DailyConsumption::whereBetween('consumption_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->sum('co2_generated');
        $monthlyTotal = DailyConsumption::whereBetween('consumption_date', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->sum('co2_generated');
        $quarterlyTotal = DailyConsumption::whereBetween('consumption_date', [Carbon::now()->startOfQuarter(), Carbon::now()->endOfQuarter()])->sum('co2_generated');
        $yearlyTotal = DailyConsumption::whereBetween('consumption_date', [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()])->sum('co2_generated');
        $monthlyData = DailyConsumption::selectRaw('DATE_FORMAT(consumption_date, "%Y-%m") as period, SUM(co2_generated) as total_co2')
            ->where('consumption_date', '>=', Carbon::now()->subMonths(12))
            ->groupBy('period')->orderBy('period')->get();
        $byUnit = DailyConsumption::selectRaw('productive_unit_id, SUM(co2_generated) as total_co2')
            ->with('productiveUnit')->groupBy('productive_unit_id')->orderBy('total_co2', 'desc')->limit(10)->get();
        $byFactor = DailyConsumption::selectRaw('emission_factor_id, SUM(co2_generated) as total_co2')
            ->with('emissionFactor')->groupBy('emission_factor_id')->orderBy('total_co2', 'desc')->get();
        $yearlyComparison = DailyConsumption::selectRaw('YEAR(consumption_date) as year, SUM(co2_generated) as total_co2')
            ->groupBy('year')->orderBy('year')->get();
        $chartData = [
            'trend' => [
                'labels' => $monthlyData->pluck('period')->map(fn($p) => Carbon::parse($p . '-01')->format('M Y')),
                'data' => $monthlyData->pluck('total_co2')
            ],
            'byUnit' => ['labels' => $byUnit->pluck('productiveUnit.name'), 'data' => $byUnit->pluck('total_co2')],
            'byFactor' => ['labels' => $byFactor->pluck('emissionFactor.name'), 'data' => $byFactor->pluck('total_co2')],
            'yearly' => ['labels' => $yearlyComparison->pluck('year'), 'data' => $yearlyComparison->pluck('total_co2')]
        ];
        $units = ProductiveUnit::where('is_active', true)->orderBy('name')->get();
        return view('huellacarbono::superadmin.charts', compact('weeklyTotal', 'monthlyTotal', 'quarterlyTotal', 'yearlyTotal', 'chartData', 'units'));
    }

    public function users()
    {
        $users = User::with(['roles' => function ($q) {
            $q->where('slug', 'like', 'huellacarbono.%');
        }])->paginate(20);
        $roles = \Modules\SICA\Entities\Role::where('slug', 'like', 'huellacarbono.%')->get();
        return view('huellacarbono::superadmin.users', compact('users', 'roles'));
    }

    public function assignLeader(Request $request, $id)
    {
        $validated = $request->validate(['leader_user_id' => 'nullable|exists:users,id']);
        $unit = ProductiveUnit::findOrFail($id);
        $unit->leader_user_id = $validated['leader_user_id'];
        $unit->save();
        if ($validated['leader_user_id']) {
            $user = User::find($validated['leader_user_id']);
            $role = \Modules\SICA\Entities\Role::where('slug', 'huellacarbono.leader')->first();
            if ($role) {
                $user->roles()->syncWithoutDetaching([$role->id]);
            }
        }
        return response()->json(['success' => true, 'message' => 'Líder asignado exitosamente']);
    }

    public function toggleUnitStatus(Request $request, $id)
    {
        try {
            if (!checkRol('huellacarbono.admin')) {
                return response()->json(['success' => false, 'message' => 'No tienes permisos para realizar esta acción.'], 403);
            }
            \Log::info('Toggle status request', ['unit_id' => $id, 'request_data' => $request->all()]);
            $validated = $request->validate(['is_active' => 'required|boolean']);
            $unit = ProductiveUnit::findOrFail($id);
            $oldStatus = $unit->is_active;
            $unit->is_active = $validated['is_active'];
            $unit->save();
            \Log::info('Unit status changed', ['unit_id' => $id, 'old_status' => $oldStatus, 'new_status' => $unit->is_active]);
            return response()->json([
                'success' => true,
                'message' => $validated['is_active'] ? 'Unidad activada exitosamente' : 'Unidad desactivada exitosamente',
                'unit' => ['id' => $unit->id, 'name' => $unit->name, 'is_active' => $unit->is_active]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error in toggle status', ['errors' => $e->errors(), 'request_data' => $request->all()]);
            return response()->json(['success' => false, 'message' => 'Error de validación: ' . json_encode($e->errors())], 422);
        } catch (\Exception $e) {
            \Log::error('Error in toggle status', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Error al cambiar el estado: ' . $e->getMessage()], 500);
        }
    }

    public function storeProductiveUnit(Request $request)
    {
        try {
            if (!checkRol('huellacarbono.admin')) {
                return response()->json(['success' => false, 'message' => 'No tienes permisos para realizar esta acción.'], 403);
            }
            \Log::info('Creating new unit', ['request_data' => $request->all()]);
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'code' => 'required|string|max:50|unique:hc_productive_units,code',
                'description' => 'nullable|string',
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
                'leader_user_id' => 'nullable|exists:users,id'
            ]);
            $unit = ProductiveUnit::create($validated);
            if (!empty($validated['leader_user_id'])) {
                $user = User::find($validated['leader_user_id']);
                $role = \Modules\SICA\Entities\Role::where('slug', 'huellacarbono.leader')->first();
                if ($role && $user) {
                    $user->roles()->syncWithoutDetaching([$role->id]);
                }
            }
            \Log::info('Unit created successfully', ['unit_id' => $unit->id]);
            return response()->json(['success' => true, 'message' => 'Unidad creada exitosamente', 'unit' => $unit]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error creating unit', ['errors' => $e->errors(), 'request_data' => $request->all()]);
            return response()->json(['success' => false, 'message' => 'Error de validación', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            \Log::error('Error creating unit', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Error al crear la unidad: ' . $e->getMessage()], 500);
        }
    }

    public function updateProductiveUnit(Request $request, $id)
    {
        try {
            if (!checkRol('huellacarbono.admin')) {
                return response()->json(['success' => false, 'message' => 'No tienes permisos para realizar esta acción.'], 403);
            }
            $unit = ProductiveUnit::findOrFail($id);
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'code' => 'required|string|max:50|unique:hc_productive_units,code,' . $id,
                'description' => 'nullable|string',
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180'
            ]);
            $unit->update($validated);
            return response()->json(['success' => true, 'message' => 'Unidad actualizada exitosamente', 'unit' => $unit]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Error de validación', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al actualizar la unidad: ' . $e->getMessage()], 500);
        }
    }

    public function deleteProductiveUnit($id)
    {
        $unit = ProductiveUnit::findOrFail($id);
        $unit->delete();
        return response()->json(['success' => true, 'message' => 'Unidad eliminada exitosamente']);
    }

    public function storeEmissionFactor(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:hc_emission_factors,code',
            'unit' => 'required|string|max:50',
            'factor' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'requires_percentage' => 'nullable|boolean',
            'order' => 'nullable|integer|min:0'
        ]);
        if (!isset($validated['order'])) {
            $validated['order'] = (int) EmissionFactor::max('order') + 1;
        }
        $validated['requires_percentage'] = !empty($validated['requires_percentage']);
        $validated['is_active'] = true;
        $factor = EmissionFactor::create($validated);
        return response()->json(['success' => true, 'message' => 'Factor de emisión creado exitosamente', 'factor' => $factor]);
    }

    public function updateEmissionFactor(Request $request, $id)
    {
        $factor = EmissionFactor::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50',
            'unit' => 'required|string|max:50',
            'factor' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'requires_percentage' => 'boolean',
            'order' => 'required|integer|min:0'
        ]);
        $factor->update($validated);
        return response()->json(['success' => true, 'message' => 'Factor actualizado exitosamente', 'factor' => $factor]);
    }

    public function toggleFactorStatus(Request $request, $id)
    {
        $factor = EmissionFactor::findOrFail($id);
        $factor->is_active = !$factor->is_active;
        $factor->save();
        return response()->json([
            'success' => true,
            'message' => $factor->is_active ? 'Factor activado' : 'Factor desactivado',
            'is_active' => $factor->is_active
        ]);
    }

    public function deleteEmissionFactor($id)
    {
        $factor = EmissionFactor::findOrFail($id);
        $factor->delete();
        return response()->json(['success' => true, 'message' => 'Factor eliminado exitosamente']);
    }

    public function consumptionRequests()
    {
        if (!checkRol('huellacarbono.admin')) {
            abort(403, 'No tienes permisos.');
        }
        $requests = ConsumptionRequest::with(['productiveUnit', 'requestedBy', 'items.emissionFactor'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return view('huellacarbono::superadmin.consumption_requests', compact('requests'));
    }

    public function approveConsumptionRequest($id)
    {
        if (!checkRol('huellacarbono.admin')) {
            return response()->json(['success' => false, 'message' => 'Sin permisos'], 403);
        }
        $requestModel = ConsumptionRequest::with('items.emissionFactor')->findOrFail($id);
        if ($requestModel->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'La solicitud ya fue procesada'], 400);
        }
        foreach ($requestModel->items as $item) {
            $factor = $item->emissionFactor;
            $co2 = $item->quantity * $factor->factor;
            if ($factor->requires_percentage && $item->nitrogen_percentage) {
                $co2 = $co2 * ($item->nitrogen_percentage / 100);
            }
            DailyConsumption::create([
                'productive_unit_id' => $requestModel->productive_unit_id,
                'emission_factor_id' => $item->emission_factor_id,
                'registered_by' => $requestModel->requested_by,
                'consumption_date' => $requestModel->consumption_date,
                'quantity' => $item->quantity,
                'nitrogen_percentage' => $item->nitrogen_percentage,
                'co2_generated' => round($co2, 3),
                'observations' => 'Registro aprobado por Admin (solicitud #' . $requestModel->id . ')'
            ]);
        }
        $requestModel->update([
            'status' => 'approved',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now()
        ]);
        return response()->json(['success' => true, 'message' => 'Solicitud aprobada. Consumos registrados.']);
    }

    public function rejectConsumptionRequest(Request $request, $id)
    {
        if (!checkRol('huellacarbono.admin')) {
            return response()->json(['success' => false, 'message' => 'Sin permisos'], 403);
        }
        $requestModel = ConsumptionRequest::findOrFail($id);
        if ($requestModel->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'La solicitud ya fue procesada'], 400);
        }
        $requestModel->update([
            'status' => 'rejected',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
            'observations' => $request->input('observations', $requestModel->observations)
        ]);
        return response()->json(['success' => true, 'message' => 'Solicitud rechazada']);
    }

    public function assignRole(Request $request, $id)
    {
        $validated = $request->validate(['role_id' => 'nullable|exists:roles,id']);
        $user = User::findOrFail($id);
        $hcRoleIds = \Modules\SICA\Entities\Role::where('slug', 'like', 'huellacarbono.%')->pluck('id');
        $user->roles()->detach($hcRoleIds);
        if (!empty($validated['role_id'])) {
            $user->roles()->attach($validated['role_id']);
            return response()->json(['success' => true, 'message' => 'Rol asignado exitosamente']);
        }
        return response()->json(['success' => true, 'message' => 'Acceso a Huella de Carbono quitado correctamente']);
    }

    public function editConsumption(Request $request, $id)
    {
        $consumption = DailyConsumption::findOrFail($id);
        $validated = $request->validate([
            'quantity' => 'required|numeric|min:0',
            'nitrogen_percentage' => 'nullable|numeric|min:0|max:100',
            'observations' => 'nullable|string'
        ]);
        $consumption->update($validated);
        return response()->json([
            'success' => true,
            'message' => 'Consumo actualizado exitosamente',
            'consumption' => $consumption->fresh()->load('emissionFactor')
        ]);
    }

    public function deleteConsumption($id)
    {
        $consumption = DailyConsumption::findOrFail($id);
        $consumption->delete();
        return response()->json(['success' => true, 'message' => 'Consumo eliminado exitosamente']);
    }

    public function exportPDF(Request $request)
    {
        if (!checkRol('huellacarbono.admin')) {
            abort(403, 'No tienes permisos para realizar esta acción.');
        }
        $period = $request->get('period', 'current_month');
        [$startDate, $endDate] = $this->getDateRange($period, $request);
        $query = DailyConsumption::with(['productiveUnit', 'emissionFactor', 'registeredBy'])
            ->whereBetween('consumption_date', [$startDate, $endDate]);
        if ($request->has('unit_id') && $request->unit_id != '') {
            $query->where('productive_unit_id', $request->unit_id);
        }
        $consumptions = $query->orderBy('consumption_date', 'desc')->get();
        if ($consumptions->isEmpty()) {
            if ($request->has('unit_id') && $request->unit_id != '') {
                return response()->view('huellacarbono::reports.empty', [
                    'message' => 'No hay consumos registrados para la unidad seleccionada en el período (año actual).'
                ], 200)->header('Content-Type', 'text/html');
            }
            return back()->with('error', 'No hay datos para el período seleccionado');
        }
        $totalCO2 = $consumptions->sum('co2_generated');
        $byUnit = $consumptions->groupBy('productive_unit_id')->map(function ($items) {
            return (object)[
                'productiveUnit' => $items->first()->productiveUnit,
                'count' => $items->count(),
                'total_co2' => $items->sum('co2_generated')
            ];
        })->sortByDesc('total_co2');
        $byFactor = $consumptions->groupBy('emission_factor_id')->map(function ($items) {
            return (object)[
                'emissionFactor' => $items->first()->emissionFactor,
                'count' => $items->count(),
                'total_co2' => $items->sum('co2_generated')
            ];
        })->sortByDesc('total_co2');
        $pdf = PDF::loadView('huellacarbono::reports.pdf', compact('consumptions', 'totalCO2', 'startDate', 'endDate', 'byUnit', 'byFactor'));
        $pdf->setPaper('letter', 'portrait')->setOptions(['isHtml5ParserEnabled' => true, 'isPhpEnabled' => true, 'defaultFont' => 'Arial']);
        $filename = 'reporte_huella_carbono_' . $startDate->format('Y-m-d') . '_' . $endDate->format('Y-m-d') . '.pdf';
        return $pdf->download($filename);
    }

    public function exportExcel(Request $request)
    {
        if (!checkRol('huellacarbono.admin')) {
            abort(403, 'No tienes permisos para realizar esta acción.');
        }
        $dataType = $request->get('data_type', 'all_consumptions');
        $period = $request->get('period', 'current_year');
        [$startDate, $endDate] = $this->getDateRange($period, $request);
        $startStr = $startDate->format('Y-m-d');
        $endStr = $endDate->format('Y-m-d');
        $query = DailyConsumption::with(['productiveUnit', 'emissionFactor', 'registeredBy'])
            ->whereBetween('consumption_date', [$startStr, $endStr]);
        $unitId = $request->filled('unit_id') ? (int) $request->unit_id : null;
        if ($unitId > 0) {
            $query->where('productive_unit_id', $unitId);
        }
        $consumptions = $query->orderBy('consumption_date', 'desc')->get();
        if ($consumptions->isEmpty()) {
            if ($unitId > 0) {
                return response()->view('huellacarbono::reports.empty', [
                    'message' => 'No hay consumos registrados para la unidad seleccionada en el período elegido.',
                    'periodLabel' => $period,
                    'startDate' => $startDate->format('d/m/Y'),
                    'endDate' => $endDate->format('d/m/Y'),
                ], 200)->header('Content-Type', 'text/html');
            }
            return back()->with('error', 'No hay datos para exportar');
        }
        $filename = 'huella_carbono_' . $startDate->format('Y-m-d') . '_' . $endDate->format('Y-m-d') . '.xlsx';
        switch ($dataType) {
            case 'by_unit':
                return Excel::download(new ByUnitExport($consumptions), $filename);
            case 'by_factor':
                return Excel::download(new ByFactorExport($consumptions), $filename);
            case 'summary':
                return Excel::download(new SummaryExport($consumptions), $filename);
            default:
                return Excel::download(new ConsumptionsExport($consumptions, 'Huella de Carbono'), $filename);
        }
    }

    private function getDateRange($period, $request)
    {
        switch ($period) {
            case 'current_month':
                return [Carbon::now()->startOfMonth(), Carbon::now()];
            case 'last_month':
                return [Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth()];
            case 'current_quarter':
                return [Carbon::now()->startOfQuarter(), Carbon::now()];
            case 'last_quarter':
                return [Carbon::now()->subQuarter()->startOfQuarter(), Carbon::now()->subQuarter()->endOfQuarter()];
            case 'current_year':
                return [Carbon::now()->startOfYear(), Carbon::now()];
            case 'last_year':
                return [Carbon::now()->subYear()->startOfYear(), Carbon::now()->subYear()->endOfYear()];
            case 'all':
                return [Carbon::create(2022, 1, 1), Carbon::now()];
            case 'custom':
                return [
                    $request->get('start_date') ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth(),
                    $request->get('end_date') ? Carbon::parse($request->end_date) : Carbon::now()
                ];
            default:
                return [Carbon::now()->startOfMonth(), Carbon::now()];
        }
    }

    public function getChartData(Request $request)
    {
        if (!checkRol('huellacarbono.admin')) {
            return response()->json(['success' => false, 'message' => 'Sin permisos'], 403);
        }
        $viewType = $request->get('view_type', 'monthly');
        $unitId = $request->get('unit_id', 'all');
        $baseQuery = DailyConsumption::query();
        if ($unitId !== 'all' && $unitId !== '') {
            $baseQuery->where('productive_unit_id', (int) $unitId);
        }
        $trendLabels = [];
        $trendData = [];
        $now = Carbon::now();
        if ($viewType === 'weekly') {
            $start = $now->copy()->subWeeks(12)->startOfWeek();
            $weeks = [];
            for ($i = 0; $i < 12; $i++) {
                $w = $now->copy()->subWeeks(11 - $i)->startOfWeek();
                $weeks[] = [$w->copy()->startOfWeek(), $w->copy()->endOfWeek()];
            }
            foreach ($weeks as $i => [$s, $e]) {
                $trendLabels[] = 'Sem ' . $s->format('d/m');
                $trendData[] = (clone $baseQuery)->whereBetween('consumption_date', [$s, $e])->sum('co2_generated');
            }
        } elseif ($viewType === 'quarterly') {
            $periods = [];
            for ($i = 7; $i >= 0; $i--) {
                $q = $now->copy()->subQuarters($i);
                $periods[] = [$q->copy()->startOfQuarter(), $q->copy()->endOfQuarter()];
            }
            foreach ($periods as [$s, $e]) {
                $trendLabels[] = $s->format('Y') . ' T' . $s->quarter;
                $trendData[] = (clone $baseQuery)->whereBetween('consumption_date', [$s, $e])->sum('co2_generated');
            }
        } elseif ($viewType === 'yearly') {
            for ($i = 2; $i >= 0; $i--) {
                $y = $now->copy()->subYears($i);
                $s = $y->copy()->startOfYear();
                $e = $y->copy()->endOfYear();
                $trendLabels[] = $y->format('Y');
                $trendData[] = (clone $baseQuery)->whereBetween('consumption_date', [$s, $e])->sum('co2_generated');
            }
        } else {
            for ($i = 11; $i >= 0; $i--) {
                $m = $now->copy()->subMonths($i);
                $s = $m->copy()->startOfMonth();
                $e = $m->copy()->endOfMonth();
                $trendLabels[] = $m->format('M Y');
                $trendData[] = (clone $baseQuery)->whereBetween('consumption_date', [$s, $e])->sum('co2_generated');
            }
        }
        $byUnitQuery = DailyConsumption::selectRaw('productive_unit_id, SUM(co2_generated) as total_co2')->groupBy('productive_unit_id')->orderBy('total_co2', 'desc');
        if ($unitId !== 'all' && $unitId !== '') {
            $byUnitQuery->where('productive_unit_id', (int) $unitId);
        }
        $byUnitRows = $byUnitQuery->limit(10)->get();
        $unitIds = $byUnitRows->pluck('productive_unit_id');
        $unitNames = ProductiveUnit::whereIn('id', $unitIds)->pluck('name', 'id');
        $byUnitLabels = $byUnitRows->map(fn($r) => $unitNames[$r->productive_unit_id] ?? 'N/A');
        $byUnitData = $byUnitRows->pluck('total_co2');
        $byFactorQuery = DailyConsumption::selectRaw('emission_factor_id, SUM(co2_generated) as total_co2')->groupBy('emission_factor_id')->orderBy('total_co2', 'desc');
        if ($unitId !== 'all' && $unitId !== '') {
            $byFactorQuery->where('productive_unit_id', (int) $unitId);
        }
        $byFactorRows = $byFactorQuery->get();
        $factorIds = $byFactorRows->pluck('emission_factor_id');
        $factorNames = EmissionFactor::whereIn('id', $factorIds)->pluck('name', 'id');
        $byFactorLabels = $byFactorRows->map(fn($r) => $factorNames[$r->emission_factor_id] ?? 'N/A');
        $byFactorData = $byFactorRows->pluck('total_co2');
        $yearlyQuery = DailyConsumption::selectRaw('YEAR(consumption_date) as year, SUM(co2_generated) as total_co2')->groupBy('year')->orderBy('year');
        if ($unitId !== 'all' && $unitId !== '') {
            $yearlyQuery->where('productive_unit_id', (int) $unitId);
        }
        $yearlyRows = $yearlyQuery->get();
        $yearlyLabels = $yearlyRows->pluck('year')->map(fn($y) => (string) $y);
        $yearlyData = $yearlyRows->pluck('total_co2');
        $chartData = [
            'trend' => ['labels' => $trendLabels, 'data' => $trendData],
            'byUnit' => ['labels' => $byUnitLabels->values(), 'data' => $byUnitData->values()],
            'byFactor' => ['labels' => $byFactorLabels->values(), 'data' => $byFactorData->values()],
            'yearly' => ['labels' => $yearlyLabels->values(), 'data' => $yearlyData->values()],
        ];
        return response()->json(['success' => true, 'chartData' => $chartData]);
    }
}
