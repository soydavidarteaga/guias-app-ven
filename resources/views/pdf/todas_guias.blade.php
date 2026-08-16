<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Todas las Guías</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; margin: 0; padding: 20px; }
        h1 { text-align: center; font-size: 18px; margin-bottom: 20px; color: #800000; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .text-center { text-align: center; }
        .badge { padding: 3px 6px; border-radius: 4px; font-weight: bold; color: white; }
        .badge.emitida { background-color: #3b82f6; }
        .badge.transito { background-color: #f59e0b; }
        .badge.completada { background-color: #10b981; }
        .badge.anulada { background-color: #ef4444; }
        .badge.borrador { background-color: #6b7280; }
    </style>
</head>
<body>

    <h1>Reporte General de Guías de Movilización (SICA)</h1>

    <table>
        <thead>
            <tr>
                <th>Nro. Guía</th>
                <th>Fecha Emisión</th>
                <th>Empresa Origen</th>
                <th>Empresa Destino</th>
                <th>Conductor</th>
                <th>Vehículo (Placa)</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($guias as $guia)
            <tr>
                <td class="text-center font-mono">{{ $guia->nro_guia }}</td>
                <td class="text-center">{{ $guia->fecha_emision ? $guia->fecha_emision->format('d/m/Y') : '' }}</td>
                <td>
                    <strong>{{ $guia->empresaOrigen->razon_social ?? 'N/A' }}</strong><br>
                    <small>{{ $guia->empresaOrigen->rif ?? '' }}</small>
                </td>
                <td>
                    <strong>{{ $guia->empresaDestino->razon_social ?? 'N/A' }}</strong><br>
                    <small>{{ $guia->empresaDestino->rif ?? '' }}</small>
                </td>
                <td>{{ $guia->conductor->nombre_completo ?? 'N/A' }}</td>
                <td class="text-center">{{ $guia->vehiculo->placa ?? 'N/A' }}</td>
                <td class="text-center">
                    @php
                        $class = match($guia->estado) {
                            'Emitida' => 'emitida',
                            'En Tránsito' => 'transito',
                            'Completada' => 'completada',
                            'Anulada' => 'anulada',
                            default => 'borrador'
                        };
                    @endphp
                    <span class="badge {{ $class }}">{{ $guia->estado }}</span>
                </td>
            </tr>
            @endforeach
            @if($guias->isEmpty())
            <tr>
                <td colspan="7" class="text-center">No hay guías registradas en el sistema.</td>
            </tr>
            @endif
        </tbody>
    </table>

</body>
</html>
