<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 11px; }
        .header { text-align: center; border-bottom: 2px solid #333; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #444; color: white; padding: 10px; }
        td { border: 1px solid #ddd; padding: 8px; }
        .total-box { margin-top: 20px; text-align: right; font-size: 16px; font-weight: bold; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 9px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ $titulo }}</h2>
        <p>Periodo: {{ $desde }} al {{ $hasta }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Cliente</th>
                <th>Equipo</th>
                <th>Monto</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ordenes as $orden)
            <tr>
                <td>{{ $orden->updated_at->format('d/m/Y') }}</td>
                <td>{{ $orden->device->customer->name }}</td>
                <td>{{ $orden->device->model }}</td>
                <td>S/ {{ number_format($orden->price, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-box">
        TOTAL RECAUDADO: S/ {{ number_format($total, 2) }}
    </div>

    <div class="footer">Generado el {{ now()->format('d/m/Y H:i') }} - Sistema Infotech</div>
</body>
</html>