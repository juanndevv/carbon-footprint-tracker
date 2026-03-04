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
use Carbon\Carbon;

class LeaderController extends Controller
{
    /**
     * Dashboard del líder
     */
    public function dashboard()
    {
        $user = Auth::user();
        $unit = ProductiveUnit::where('leader_user_id', $user->id)->first();
        
        if (!$unit) {
            return redirect()->route('cefa.huellacarbono.index')
                ->with('error', 'No tienes una unidad productiva asignada');
        }

        // Estadísticas de la semana actual
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();
        
        $weeklyTotal = $unit->calculateCarbonFootprint($startOfWeek, $endOfWeek);
        
        // Últimos registros: del más reciente al más antiguo por fecha de consumo
        $recentConsumptions = $unit->dailyConsumptions()
            ->with(['emissionFactor', 'registeredBy'])
            ->orderBy('consumption_date', 'desc')
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();

        return view('huellacarbono::leader.dashboard', compact(
            'unit',
            'weeklyTotal',
            'recentConsumptions'
        ));
    }

    /**
     * Alertas (días sin reporte) y estado de solicitudes en una sola vista
     */
    public function alertsAndRequests()
    {
        $user = Auth::user();
        $unit = ProductiveUnit::where('leader_user_id', $user->id)->first();
        if (!$unit) {
            return redirect()->route('cefa.huellacarbono.index')->with('error', 'No tienes una unidad productiva asignada');
        }
        $today = Carbon::today();
        $daysWithoutReport = [];
        for ($i = 1; $i <= 7; $i++) {
            $date = $today->copy()->subDays($i);
            $hasConsumption = DailyConsumption::where('productive_unit_id', $unit->id)->whereDate('consumption_date', $date)->exists();
            if (!$hasConsumption) {
                $daysWithoutReport[] = $date;
            }
        }
        $myRequests = ConsumptionRequest::where('requested_by', $user->id)
            ->with(['items.emissionFactor', 'productiveUnit'])
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        // Marcar como "vistas" las alertas actuales para que el punto rojo desaparezca
        $seenDates = array_map(function ($d) { return $d->format('Y-m-d'); }, $daysWithoutReport);
        session(['leader_alerts_seen_dates' => $seenDates]);

        return view('huellacarbono::leader.alerts_and_requests', compact('unit', 'daysWithoutReport', 'myRequests'));
    }

    /**
     * Formulario para registrar consumo diario
     */
    public function registerConsumption()
    {
        $user = Auth::user();
        $unit = ProductiveUnit::where('leader_user_id', $user->id)->first();
        
        if (!$unit) {
            return redirect()->back()->with('error', 'No tienes una unidad productiva asignada');
        }

        $emissionFactors = EmissionFactor::active()->get();
        
        return view('huellacarbono::leader.register', compact('unit', 'emissionFactors'));
    }

    /**
     * Guardar múltiples consumos (múltiples variables en una fecha)
     */
    public function storeMultipleConsumptions(Request $request)
    {
        $user = Auth::user();
        $unit = ProductiveUnit::where('leader_user_id', $user->id)->first();
        
        if (!$unit) {
            return response()->json(['error' => 'No tienes una unidad productiva asignada'], 403);
        }

        $validated = $request->validate([
            'consumption_date' => 'required|date|date_equals:today',
            'variables' => 'required|array|min:1',
            'variables.*.emission_factor_id' => 'required|exists:hc_emission_factors,id',
            'variables.*.quantity' => 'required|numeric|min:0',
            'variables.*.nitrogen_percentage' => 'nullable|numeric|min:0|max:100'
        ]);

        $totalCO2 = 0;
        $count = 0;

        foreach ($validated['variables'] as $variable) {
            // Verificar si ya existe un registro para esta fecha y factor
            $existing = DailyConsumption::where('productive_unit_id', $unit->id)
                ->where('emission_factor_id', $variable['emission_factor_id'])
                ->where('consumption_date', $validated['consumption_date'])
                ->first();

            if ($existing) {
                continue; // Saltar si ya existe
            }

            // Obtener el factor de emisión
            $emissionFactor = EmissionFactor::find($variable['emission_factor_id']);
            
            // Calcular CO2
            $co2 = $variable['quantity'] * $emissionFactor->factor;
            
            if ($emissionFactor->requires_percentage && !empty($variable['nitrogen_percentage'])) {
                $co2 = $co2 * ($variable['nitrogen_percentage'] / 100);
            }

            // Crear registro
            DailyConsumption::create([
                'productive_unit_id' => $unit->id,
                'emission_factor_id' => $variable['emission_factor_id'],
                'registered_by' => $user->id,
                'consumption_date' => $validated['consumption_date'],
                'quantity' => $variable['quantity'],
                'nitrogen_percentage' => $variable['nitrogen_percentage'] ?? null,
                'co2_generated' => round($co2, 3),
                'observations' => 'Registro múltiple'
            ]);

            $totalCO2 += $co2;
            $count++;
        }

        return response()->json([
            'success' => true,
            'message' => "{$count} variable(s) registrada(s) exitosamente",
            'count' => $count,
            'total_co2' => number_format($totalCO2, 3)
        ]);
    }

    /**
     * Guardar consumo diario (método individual - mantener por compatibilidad)
     */
    public function storeConsumption(Request $request)
    {
        $user = Auth::user();
        $unit = ProductiveUnit::where('leader_user_id', $user->id)->first();
        
        if (!$unit) {
            return response()->json(['error' => 'No tienes una unidad productiva asignada'], 403);
        }

        $validated = $request->validate([
            'emission_factor_id' => 'required|exists:hc_emission_factors,id',
            'consumption_date' => 'required|date|date_equals:' . now()->toDateString(),
            'quantity' => 'required|numeric|min:0',
            'nitrogen_percentage' => 'nullable|numeric|min:0|max:100',
            'observations' => 'nullable|string|max:500'
        ]);

        // Verificar si ya existe un registro para esta fecha y factor
        $existing = DailyConsumption::where('productive_unit_id', $unit->id)
            ->where('emission_factor_id', $validated['emission_factor_id'])
            ->where('consumption_date', $validated['consumption_date'])
            ->first();

        if ($existing) {
            return response()->json([
                'error' => 'Ya existe un registro para este factor en la fecha seleccionada'
            ], 422);
        }

        $consumption = DailyConsumption::create([
            'productive_unit_id' => $unit->id,
            'emission_factor_id' => $validated['emission_factor_id'],
            'registered_by' => $user->id,
            'consumption_date' => $validated['consumption_date'],
            'quantity' => $validated['quantity'],
            'nitrogen_percentage' => $validated['nitrogen_percentage'] ?? null,
            'observations' => $validated['observations'] ?? null
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Consumo registrado exitosamente',
            'consumption' => $consumption->load('emissionFactor'),
            'co2_generated' => $consumption->co2_generated
        ]);
    }

    /**
     * Historial de consumos de la unidad
     */
    public function history(Request $request)
    {
        $user = Auth::user();
        $unit = ProductiveUnit::where('leader_user_id', $user->id)->first();
        
        if (!$unit) {
            return redirect()->back()->with('error', 'No tienes una unidad productiva asignada');
        }

        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now());

        $consumptions = $unit->dailyConsumptions()
            ->whereBetween('consumption_date', [$startDate, $endDate])
            ->with(['emissionFactor', 'registeredBy'])
            ->orderBy('consumption_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(20);

        $totalCO2 = $unit->calculateCarbonFootprint($startDate, $endDate);

        return view('huellacarbono::leader.history', compact(
            'unit',
            'consumptions',
            'totalCO2',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Editar consumo propio
     */
    public function editOwnConsumption($id)
    {
        $user = Auth::user();
        $unit = ProductiveUnit::where('leader_user_id', $user->id)->first();
        
        $consumption = DailyConsumption::where('id', $id)
            ->where('productive_unit_id', $unit->id)
            ->where('registered_by', $user->id)
            ->firstOrFail();

        $emissionFactors = EmissionFactor::active()->get();

        return view('huellacarbono::leader.edit', compact('consumption', 'emissionFactors'));
    }

    /**
     * Actualizar consumo propio
     */
    public function updateOwnConsumption(Request $request, $id)
    {
        $user = Auth::user();
        $unit = ProductiveUnit::where('leader_user_id', $user->id)->first();
        
        $consumption = DailyConsumption::where('id', $id)
            ->where('productive_unit_id', $unit->id)
            ->where('registered_by', $user->id)
            ->firstOrFail();

        $validated = $request->validate([
            'quantity' => 'required|numeric|min:0',
            'nitrogen_percentage' => 'nullable|numeric|min:0|max:100',
            'observations' => 'nullable|string|max:500'
        ]);

        $consumption->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Consumo actualizado exitosamente',
            'consumption' => $consumption->fresh()->load('emissionFactor')
        ]);
    }

    /**
     * Estadísticas de la unidad
     */
    public function statistics()
    {
        $user = Auth::user();
        $unit = ProductiveUnit::where('leader_user_id', $user->id)->first();
        
        if (!$unit) {
            return redirect()->back()->with('error', 'No tienes una unidad productiva asignada');
        }

        // Estadísticas semanales, mensuales y anuales
        $weeklyTotal = $unit->calculateCarbonFootprint(
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        );

        $monthlyTotal = $unit->calculateCarbonFootprint(
            Carbon::now()->startOfMonth(),
            Carbon::now()->endOfMonth()
        );

        $yearlyTotal = $unit->calculateCarbonFootprint(
            Carbon::now()->startOfYear(),
            Carbon::now()->endOfYear()
        );

        return view('huellacarbono::leader.statistics', compact(
            'unit',
            'weeklyTotal',
            'monthlyTotal',
            'yearlyTotal'
        ));
    }

    /**
     * Gráficas de la unidad
     */
    public function charts(Request $request)
    {
        $user = Auth::user();
        $unit = ProductiveUnit::where('leader_user_id', $user->id)->first();
        
        if (!$unit) {
            return redirect()->back()->with('error', 'No tienes una unidad productiva asignada');
        }

        $period = $request->get('period', 'monthly');
        
        $startDate = match($period) {
            'weekly' => Carbon::now()->subWeeks(12),
            'monthly' => Carbon::now()->subMonths(12),
            'yearly' => Carbon::now()->subYears(5),
            default => Carbon::now()->subMonths(12),
        };

        $endDate = Carbon::now();

        // Consumos de la unidad en el rango
        $consumptions = DailyConsumption::where('productive_unit_id', $unit->id)
            ->whereBetween('consumption_date', [$startDate, $endDate])
            ->get();

        $chartLabels = [];
        $chartData = [];

        if ($period === 'weekly') {
            $grouped = $consumptions->groupBy(function ($c) {
                return Carbon::parse($c->consumption_date)->format('Y-W');
            });
            foreach ($grouped->sortKeys() as $weekKey => $items) {
                $chartLabels[] = 'Sem ' . substr($weekKey, 5);
                $chartData[] = round($items->sum('co2_generated'), 2);
            }
        } elseif ($period === 'yearly') {
            $grouped = $consumptions->groupBy(function ($c) {
                return Carbon::parse($c->consumption_date)->format('Y');
            });
            foreach ($grouped->sortKeys() as $year => $items) {
                $chartLabels[] = $year;
                $chartData[] = round($items->sum('co2_generated'), 2);
            }
        } else {
            $grouped = $consumptions->groupBy(function ($c) {
                return Carbon::parse($c->consumption_date)->format('Y-m');
            });
            foreach ($grouped->sortKeys() as $monthKey => $items) {
                $chartLabels[] = Carbon::parse($monthKey . '-01')->translatedFormat('M Y');
                $chartData[] = round($items->sum('co2_generated'), 2);
            }
        }

        return view('huellacarbono::leader.charts', compact('unit', 'period', 'startDate', 'chartLabels', 'chartData'));
    }

    /**
     * Formulario para solicitar registro de consumo (fecha pasada - requiere aprobación SuperAdmin)
     */
    public function requestConsumptionForm()
    {
        $user = Auth::user();
        $unit = ProductiveUnit::where('leader_user_id', $user->id)->first();
        if (!$unit) {
            return redirect()->back()->with('error', 'No tienes una unidad productiva asignada');
        }
        $emissionFactors = EmissionFactor::active()->get();
        $today = Carbon::today();
        $daysWithoutReport = [];
        for ($i = 1; $i <= 30; $i++) {
            $date = $today->copy()->subDays($i);
            $hasConsumption = DailyConsumption::where('productive_unit_id', $unit->id)
                ->whereDate('consumption_date', $date)
                ->exists();
            if (!$hasConsumption) {
                $daysWithoutReport[] = $date;
            }
        }
        return view('huellacarbono::leader.request_consumption', compact('unit', 'emissionFactors', 'daysWithoutReport'));
    }

    /**
     * Guardar solicitud de registro (pendiente de aprobación SuperAdmin)
     */
    public function storeConsumptionRequest(Request $request)
    {
        $user = Auth::user();
        $unit = ProductiveUnit::where('leader_user_id', $user->id)->first();
        if (!$unit) {
            return response()->json(['success' => false, 'message' => 'No tienes una unidad productiva asignada'], 403);
        }
        $validated = $request->validate([
            'consumption_date' => 'required|date|before:today',
            'variables' => 'required|array|min:1',
            'variables.*.emission_factor_id' => 'required|exists:hc_emission_factors,id',
            'variables.*.quantity' => 'required|numeric|min:0',
            'variables.*.nitrogen_percentage' => 'nullable|numeric|min:0|max:100',
            'observations' => 'nullable|string|max:500'
        ]);
        $requestModel = ConsumptionRequest::create([
            'productive_unit_id' => $unit->id,
            'requested_by' => $user->id,
            'consumption_date' => $validated['consumption_date'],
            'status' => 'pending',
            'observations' => $validated['observations'] ?? null
        ]);
        foreach ($validated['variables'] as $variable) {
            ConsumptionRequestItem::create([
                'consumption_request_id' => $requestModel->id,
                'emission_factor_id' => $variable['emission_factor_id'],
                'quantity' => $variable['quantity'],
                'nitrogen_percentage' => $variable['nitrogen_percentage'] ?? null
            ]);
        }
        return response()->json([
            'success' => true,
            'message' => 'Solicitud enviada. El SuperAdmin debe aprobarla para que se registren los consumos.'
        ]);
    }
}

