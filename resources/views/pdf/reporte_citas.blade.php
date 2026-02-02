<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Citas</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 20px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header img { width: 150px; margin-bottom: 10px; }
        h2 { color: #ee7826; margin: 5px 0; }
        .info { margin-bottom: 20px; }
        .info p { margin: 2px 0; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; font-size: 11px; }
        th, td { border: 1px solid #ccc; padding: 5px; text-align: center; }
        th { background-color: #ee7826; color: white; }
        tbody tr:nth-child(even) { background-color: #f9f9f9; }
        .footer { margin-top: 20px; font-size: 10px; text-align: center; color: #666; }
    </style>
</head>
<body>

    <div class="header">
        <img src="{{ public_path('assets/img/logo.png') }}" alt="Logo">
        <h2>Reporte de Citas</h2>
    </div>

    <div class="info">
        <p><strong>Fecha Inicio:</strong> {{ $fechaInicio }}</p>
        <p><strong>Fecha Fin:</strong> {{ $fechaFin }}</p>
        <p><strong>Sucursal:</strong> {{ $sucursal ?? 'Todas' }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Hora</th>
                <th>Proveedor</th>
                <th>Sucursal</th>
                <th>Transporte</th>
                <th>Ordenes</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($reservaciones as $reserva)
            <tr>
                <td>{{ \Carbon\Carbon::parse($reserva->fecha)->format('d/m/Y') }}</td>
                <td>{{ $reserva->hora }}</td>
                <td>{{ $reserva->proveedor_nombre ?? '-' }}</td>
                <td>{{ $reserva->sucursal_nombre ?? '-' }}</td>
                <td>{{ $reserva->transporte_nombre ?? '-' }}</td>
                <td>{{ $reserva->ordenes_detalle ?? '-' }}</td>
                <td>{{ $reserva->estado }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Generado automáticamente el {{ now()->format('d/m/Y H:i') }}</p>
    </div>

</body>
</html>
