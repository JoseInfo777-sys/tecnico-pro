<!DOCTYPE html>
<html>
<head>
    <title>Reporte de Equipos Listos</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .header { text-align: center; margin-bottom: 30px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>REPORTE DE EQUIPOS LISTOS PARA ENTREGA</h1>
        <p>Rango: {{ $desde }} al {{ $hasta }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Cliente</th>
                <th>Equipo</th>
                <th>Falla</th>
                <th>Precio</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ordenes as $orden)
            <tr>
                <td>{{ $orden->updated_at->format('d/m/Y') }}</td>
                <td>{{ $orden->device->customer->name }}</td>
                <td>{{ $orden->device->model }}</td>
                <td>{{ $orden->issue }}</td>
                <td>S/ {{ number_format($orden->price, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>