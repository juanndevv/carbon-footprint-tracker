<?php

use Illuminate\Support\Facades\Route;
use Modules\HUELLACARBONO\Http\Controllers\HUELLACARBONOController;
use Modules\HUELLACARBONO\Http\Controllers\AdminController;
use Modules\HUELLACARBONO\Http\Controllers\LeaderController;
use Modules\HUELLACARBONO\Http\Controllers\PublicController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(['lang'])->group(function () {
    Route::prefix('huellacarbono')->group(function() {

        // ===== REDIRECCIONES: antiguas URLs superadmin → admin =====
        Route::get('/superadmin', function () {
            return redirect()->route('cefa.huellacarbono.admin.dashboard', [], 301);
        });
        Route::get('/superadmin/{path}', function ($path) {
            return redirect('/huellacarbono/admin/' . $path, 301);
        })->where('path', '.*');

        // ===== RUTAS PÚBLICAS (Visitantes) =====
        Route::controller(PublicController::class)->group(function() {
            Route::get('/index', 'index')->name('cefa.huellacarbono.index');
            Route::get('/informacion', 'information')->name('cefa.huellacarbono.information');
            Route::get('/calculadora-personal', 'personalCalculator')->name('cefa.huellacarbono.personal_calculator');
            Route::post('/calculadora-personal/calcular', 'calculatePersonalFootprint')->name('cefa.huellacarbono.calculate_personal');
            Route::get('/estadisticas-publicas', 'publicStatistics')->name('cefa.huellacarbono.public_statistics');
            Route::get('/desarrolladores', 'developers')->name('cefa.huellacarbono.developers');
        });

        // ===== RUTAS ADMIN =====
        Route::middleware(['auth'])->prefix('admin')->controller(AdminController::class)->group(function() {
            Route::get('/dashboard', 'dashboard')->name('cefa.huellacarbono.admin.dashboard');

            // Gestión de Unidades Productivas
            Route::get('/unidades', 'productiveUnits')->name('cefa.huellacarbono.admin.units.index');
            Route::post('/unidades/store', 'storeProductiveUnit')->name('cefa.huellacarbono.admin.units.store');
            Route::put('/unidades/{id}/update', 'updateProductiveUnit')->name('cefa.huellacarbono.admin.units.update');
            Route::delete('/unidades/{id}/delete', 'deleteProductiveUnit')->name('cefa.huellacarbono.admin.units.delete');
            Route::post('/unidades/{id}/asignar-lider', 'assignLeader')->name('cefa.huellacarbono.admin.units.assign_leader');
            Route::post('/unidades/{id}/toggle-status', 'toggleUnitStatus')->name('cefa.huellacarbono.admin.units.toggle_status');

            // Gestión de Factores de Emisión
            Route::get('/factores-emision', 'emissionFactors')->name('cefa.huellacarbono.admin.factors.index');
            Route::post('/factores-emision/store', 'storeEmissionFactor')->name('cefa.huellacarbono.admin.factors.store');
            Route::put('/factores-emision/{id}/update', 'updateEmissionFactor')->name('cefa.huellacarbono.admin.factors.update');
            Route::post('/factores-emision/{id}/toggle-status', 'toggleFactorStatus')->name('cefa.huellacarbono.admin.factors.toggle_status');
            Route::delete('/factores-emision/{id}/delete', 'deleteEmissionFactor')->name('cefa.huellacarbono.admin.factors.delete');

            // Gestión de Usuarios
            Route::get('/usuarios', 'users')->name('cefa.huellacarbono.admin.users.index');
            Route::post('/usuarios/{id}/roles', 'assignRole')->name('cefa.huellacarbono.admin.users.assign_role');

            // Visualización y Edición de Datos
            Route::get('/consumos', 'allConsumptions')->name('cefa.huellacarbono.admin.consumptions.index');
            Route::put('/consumos/{id}/editar', 'editConsumption')->name('cefa.huellacarbono.admin.consumptions.edit');
            Route::delete('/consumos/{id}/eliminar', 'deleteConsumption')->name('cefa.huellacarbono.admin.consumptions.delete');

            // Reportes y Exportaciones
            Route::get('/reportes', 'reports')->name('cefa.huellacarbono.admin.reports.index');
            Route::get('/reportes/exportar-pdf', 'exportPDF')->name('cefa.huellacarbono.admin.reports.export_pdf');
            Route::get('/reportes/exportar-excel', 'exportExcel')->name('cefa.huellacarbono.admin.reports.export_excel');

            // Gráficas
            Route::get('/graficas', 'charts')->name('cefa.huellacarbono.admin.charts.index');
            Route::post('/graficas/datos', 'getChartData')->name('cefa.huellacarbono.admin.charts.data');

            // Solicitudes de registro (Líder → aprobación Admin)
            Route::get('/solicitudes-registro', 'consumptionRequests')->name('cefa.huellacarbono.admin.requests.index');
            Route::post('/solicitudes-registro/{id}/aprobar', 'approveConsumptionRequest')->name('cefa.huellacarbono.admin.requests.approve');
            Route::post('/solicitudes-registro/{id}/rechazar', 'rejectConsumptionRequest')->name('cefa.huellacarbono.admin.requests.reject');
        });

        // ===== RUTAS LÍDER DE UNIDAD =====
        Route::middleware(['auth'])->prefix('lider')->controller(LeaderController::class)->group(function() {
            Route::get('/dashboard', 'dashboard')->name('cefa.huellacarbono.leader.dashboard');
            Route::get('/alertas-solicitudes', 'alertsAndRequests')->name('cefa.huellacarbono.leader.alerts_requests');

            // Registro de Consumos Diarios
            Route::get('/registrar-consumo', 'registerConsumption')->name('cefa.huellacarbono.leader.register');
            Route::post('/registrar-consumo/guardar', 'storeConsumption')->name('cefa.huellacarbono.leader.store_consumption');
            Route::post('/registrar-consumo/guardar-multiples', 'storeMultipleConsumptions')->name('cefa.huellacarbono.leader.store_multiple_consumptions');

            // Ver Historial de su Unidad
            Route::get('/historial', 'history')->name('cefa.huellacarbono.leader.history');
            Route::get('/consumo/{id}/editar', 'editOwnConsumption')->name('cefa.huellacarbono.leader.edit_consumption');
            Route::put('/consumo/{id}/actualizar', 'updateOwnConsumption')->name('cefa.huellacarbono.leader.update_consumption');

            // Estadísticas de su Unidad
            Route::get('/estadisticas', 'statistics')->name('cefa.huellacarbono.leader.statistics');
            Route::get('/graficas', 'charts')->name('cefa.huellacarbono.leader.charts');

            // Solicitar registro para fecha no reportada (requiere aprobación Admin)
            Route::get('/solicitar-registro', 'requestConsumptionForm')->name('cefa.huellacarbono.leader.request_form');
            Route::post('/solicitar-registro/guardar', 'storeConsumptionRequest')->name('cefa.huellacarbono.leader.store_request');
        });
    });
});
