<?php

namespace Modules\HUELLACARBONO\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\HUELLACARBONO\Entities\PersonalCarbonCalculation;
use Modules\HUELLACARBONO\Entities\EmissionFactor;
use Modules\HUELLACARBONO\Entities\DailyConsumption;
use Carbon\Carbon;

class PublicController extends Controller
{
    /**
     * Página principal pública del módulo
     * Redirige automáticamente según el rol del usuario
     */
    public function index()
    {
        // Si el usuario está autenticado, redirigir según su rol (Líder tiene prioridad: no ver Admin)
        if (Auth::check()) {
            if (checkRol('huellacarbono.leader')) {
                return redirect()->route('cefa.huellacarbono.leader.dashboard');
            }
            if (checkRol('huellacarbono.admin')) {
                return redirect()->route('cefa.huellacarbono.admin.dashboard');
            }
        }
        
        // Centro: Centro de Formación Agroindustrial La Angostura, Campo Alegre, Campoalegre, Huila
        $centerLat = (float) env('MAPBOX_CENTER_LAT', 2.612606);
        $centerLng = (float) env('MAPBOX_CENTER_LNG', -75.361439);
        $heatmapZones = [
            ['name' => 'Complejo Agroindustrial', 'lat' => $centerLat, 'lng' => $centerLng, 'co2' => 420],
            ['name' => 'PTAR', 'lat' => $centerLat - 0.0004, 'lng' => $centerLng - 0.0005, 'co2' => 380],
            ['name' => 'PTAP', 'lat' => $centerLat + 0.0002, 'lng' => $centerLng - 0.0003, 'co2' => 180],
            ['name' => 'Ganadería', 'lat' => $centerLat - 0.0003, 'lng' => $centerLng + 0.0004, 'co2' => 350],
            ['name' => 'Corral', 'lat' => $centerLat - 0.0005, 'lng' => $centerLng + 0.0002, 'co2' => 320],
            ['name' => 'Invernadero', 'lat' => $centerLat - 0.0006, 'lng' => $centerLng - 0.0002, 'co2' => 220],
            ['name' => 'Vivero', 'lat' => $centerLat + 0.0003, 'lng' => $centerLng + 0.0003, 'co2' => 90],
            ['name' => 'Agroquímicos', 'lat' => $centerLat + 0.0005, 'lng' => $centerLng + 0.0005, 'co2' => 280],
            ['name' => 'Cítricos', 'lat' => $centerLat + 0.0006, 'lng' => $centerLng + 0.0006, 'co2' => 70],
            ['name' => 'Unidad Piscícola', 'lat' => $centerLat + 0.0004, 'lng' => $centerLng + 0.0007, 'co2' => 95],
            ['name' => 'Biblioteca', 'lat' => $centerLat + 0.0001, 'lng' => $centerLng - 0.0004, 'co2' => 60],
            ['name' => 'Restaurante', 'lat' => $centerLat - 0.0001, 'lng' => $centerLng - 0.0001, 'co2' => 190],
            ['name' => 'Gimnasio', 'lat' => $centerLat - 0.0002, 'lng' => $centerLng, 'co2' => 85],
            ['name' => 'Tecnoparque', 'lat' => $centerLat - 0.0007, 'lng' => $centerLng - 0.0004, 'co2' => 150],
            ['name' => 'Lago / Casa de Lago', 'lat' => $centerLat - 0.0004, 'lng' => $centerLng - 0.0006, 'co2' => 45],
            ['name' => 'Centro de Acopio Residuos', 'lat' => $centerLat - 0.0006, 'lng' => $centerLng + 0.0001, 'co2' => 110],
            ['name' => 'Subestación', 'lat' => $centerLat + 0.0002, 'lng' => $centerLng - 0.0005, 'co2' => 260],
            ['name' => 'Lab. Ciencias Básicas', 'lat' => $centerLat - 0.0002, 'lng' => $centerLng + 0.00035, 'co2' => 120],
            ['name' => 'Centro de Convivencia', 'lat' => $centerLat + 0.00055, 'lng' => $centerLng + 0.0004, 'co2' => 75],
            ['name' => 'Administrativos Casona', 'lat' => $centerLat + 0.00005, 'lng' => $centerLng + 0.0001, 'co2' => 140],
        ];
        $mapboxToken = config('services.mapbox.token', env('MAPBOX_TOKEN'));

        return view('huellacarbono::public.index', compact('heatmapZones', 'mapboxToken'));
    }

    /**
     * Información sobre la huella de carbono
     */
    public function information()
    {
        $emissionFactors = EmissionFactor::active()->get();
        return view('huellacarbono::public.information', compact('emissionFactors'));
    }

    /**
     * Página de desarrolladores y herramientas del proyecto
     */
    public function developers()
    {
        return view('huellacarbono::public.developers');
    }

    /**
     * Calculadora personal de huella de carbono
     */
    public function personalCalculator()
    {
        $emissionFactors = EmissionFactor::active()->get();
        return view('huellacarbono::public.calculator', compact('emissionFactors'));
    }

    /**
     * Calcular huella de carbono personal
     */
    public function calculatePersonalFootprint(Request $request)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'water_consumption' => 'nullable|numeric|min:0',
            'energy_consumption' => 'nullable|numeric|min:0',
            'gasoline_consumption' => 'nullable|numeric|min:0',
            'diesel_consumption' => 'nullable|numeric|min:0',
            'waste_generation' => 'nullable|numeric|min:0',
            'number_of_animals' => 'nullable|integer|min:0',
            'synthetic_fertilizers' => 'nullable|numeric|min:0',
            'fertilizer_nitrogen_percentage' => 'nullable|numeric|min:0|max:100',
            'insecticides' => 'nullable|numeric|min:0',
            'fungicides' => 'nullable|numeric|min:0',
            'herbicides' => 'nullable|numeric|min:0',
            'period' => 'required|in:daily,weekly,monthly,yearly'
        ]);

        // Calcular el total de CO2
        $totalCO2 = PersonalCarbonCalculation::calculateTotalCO2($validated);
        $validated['total_co2'] = $totalCO2;

        // Guardar el cálculo
        $calculation = PersonalCarbonCalculation::create($validated);

        return response()->json([
            'success' => true,
            'total_co2' => $totalCO2,
            'message' => 'Cálculo realizado exitosamente',
            'calculation_id' => $calculation->id
        ]);
    }

    /**
     * Estadísticas públicas del centro de formación
     */
    public function publicStatistics(Request $request)
    {
        $period = $request->get('period', 'weekly'); // weekly, monthly, yearly
        
        $startDate = match($period) {
            'weekly' => Carbon::now()->startOfWeek(),
            'monthly' => Carbon::now()->startOfMonth(),
            'yearly' => Carbon::now()->startOfYear(),
            default => Carbon::now()->startOfWeek(),
        };
        
        $endDate = Carbon::now();

        // Obtener total de CO2 del centro
        $totalCO2 = DailyConsumption::whereBetween('consumption_date', [$startDate, $endDate])
            ->sum('co2_generated');

        // CO2 por unidad productiva (Top 10)
        $co2ByUnit = DailyConsumption::whereBetween('consumption_date', [$startDate, $endDate])
            ->selectRaw('productive_unit_id, SUM(co2_generated) as total_co2')
            ->groupBy('productive_unit_id')
            ->with('productiveUnit')
            ->orderBy('total_co2', 'desc')
            ->limit(10)
            ->get();

        // CO2 por tipo de consumo
        $co2ByType = DailyConsumption::whereBetween('consumption_date', [$startDate, $endDate])
            ->selectRaw('emission_factor_id, SUM(co2_generated) as total_co2')
            ->groupBy('emission_factor_id')
            ->with('emissionFactor')
            ->orderBy('total_co2', 'desc')
            ->get();

        return view('huellacarbono::public.statistics', compact(
            'totalCO2',
            'co2ByUnit',
            'co2ByType',
            'period',
            'startDate',
            'endDate'
        ));
    }
}

