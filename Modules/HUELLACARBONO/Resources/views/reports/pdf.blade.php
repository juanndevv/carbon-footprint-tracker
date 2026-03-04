<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Huella de Carbono</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.6;
        }
        .header {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 30px;
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }
        .header p {
            font-size: 14px;
            opacity: 0.9;
        }
        .info-box {
            background: #f3f4f6;
            padding: 15px;
            margin-bottom: 20px;
            border-left: 4px solid #10b981;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: #fff;
            border: 2px solid #10b981;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
        }
        .stat-card .label {
            font-size: 11px;
            color: #6b7280;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .stat-card .value {
            font-size: 24px;
            font-weight: bold;
            color: #10b981;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background: white;
        }
        table thead {
            background: #10b981;
            color: white;
        }
        table th {
            padding: 12px 8px;
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
            font-weight: bold;
        }
        table td {
            padding: 10px 8px;
            border-bottom: 1px solid #e5e7eb;
        }
        table tbody tr:nth-child(even) {
            background: #f9fafb;
        }
        table tbody tr:hover {
            background: #f3f4f6;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #6b7280;
            border-top: 2px solid #e5e7eb;
            padding-top: 20px;
        }
        .total-row {
            background: #dcfce7 !important;
            font-weight: bold;
        }
        .section-title {
            font-size: 18px;
            font-weight: bold;
            color: #1f2937;
            margin: 30px 0 15px 0;
            padding-bottom: 10px;
            border-bottom: 2px solid #10b981;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>🌱 REPORTE DE HUELLA DE CARBONO</h1>
        <p>Centro de Formación Agroindustrial "La Angostura"</p>
        <p>{{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}</p>
    </div>

    <!-- Información General -->
    <div class="info-box">
        <strong>Generado:</strong> {{ now()->format('d/m/Y H:i:s') }}<br>
        <strong>Período:</strong> {{ $startDate->format('d/m/Y') }} al {{ $endDate->format('d/m/Y') }}<br>
        <strong>Total de registros:</strong> {{ number_format($consumptions->count()) }}
    </div>

    <!-- Estadísticas Principales -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="label">Total CO₂ Generado</div>
            <div class="value">{{ number_format($totalCO2, 2) }} kg</div>
        </div>
        <div class="stat-card">
            <div class="label">Árboles Necesarios</div>
            <div class="value">{{ number_format($totalCO2 / 22, 0) }}</div>
        </div>
        <div class="stat-card">
            <div class="label">Registros</div>
            <div class="value">{{ number_format($consumptions->count()) }}</div>
        </div>
    </div>

    <!-- Tabla de Consumos -->
    <h2 class="section-title">📋 Detalle de Consumos por Unidad</h2>
    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Unidad Productiva</th>
                <th>Variable</th>
                <th style="text-align: right;">Cantidad</th>
                <th style="text-align: right;">CO₂ (kg)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($consumptions as $consumption)
            <tr>
                <td>{{ $consumption->consumption_date->format('d/m/Y') }}</td>
                <td>{{ $consumption->productiveUnit->name ?? 'N/A' }}</td>
                <td>{{ $consumption->emissionFactor->name ?? 'N/A' }}</td>
                <td style="text-align: right;">
                    {{ number_format($consumption->quantity, 2) }} {{ $consumption->emissionFactor->unit ?? '' }}
                </td>
                <td style="text-align: right; font-weight: bold; color: #10b981;">
                    {{ number_format($consumption->co2_generated, 3) }}
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="4" style="text-align: right; font-weight: bold;">TOTAL:</td>
                <td style="text-align: right; font-weight: bold; font-size: 14px; color: #059669;">
                    {{ number_format($totalCO2, 2) }} kg
                </td>
            </tr>
        </tfoot>
    </table>

    @if($byUnit->isNotEmpty())
    <!-- Resumen por Unidad -->
    <h2 class="section-title">🏭 Resumen por Unidad Productiva</h2>
    <table>
        <thead>
            <tr>
                <th>Unidad Productiva</th>
                <th style="text-align: center;">Registros</th>
                <th style="text-align: right;">CO₂ Total (kg)</th>
                <th style="text-align: right;">% del Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($byUnit as $item)
            <tr>
                <td>{{ $item->productiveUnit->name ?? 'N/A' }}</td>
                <td style="text-align: center;">{{ number_format($item->count) }}</td>
                <td style="text-align: right; font-weight: bold;">{{ number_format($item->total_co2, 2) }}</td>
                <td style="text-align: right;">{{ number_format(($item->total_co2 / $totalCO2) * 100, 1) }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    @if($byFactor->isNotEmpty())
    <!-- Resumen por Factor -->
    <h2 class="section-title">📊 Resumen por Tipo de Consumo</h2>
    <table>
        <thead>
            <tr>
                <th>Factor de Emisión</th>
                <th style="text-align: center;">Registros</th>
                <th style="text-align: right;">CO₂ Total (kg)</th>
                <th style="text-align: right;">% del Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($byFactor as $item)
            <tr>
                <td>{{ $item->emissionFactor->name ?? 'N/A' }}</td>
                <td style="text-align: center;">{{ number_format($item->count) }}</td>
                <td style="text-align: right; font-weight: bold;">{{ number_format($item->total_co2, 2) }}</td>
                <td style="text-align: right;">{{ number_format(($item->total_co2 / $totalCO2) * 100, 1) }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p><strong>SICEFA - Sistema de Información del Centro de Formación Agroindustrial</strong></p>
        <p>Módulo de Gestión de Huella de Carbono</p>
        <p>Generado el {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>





