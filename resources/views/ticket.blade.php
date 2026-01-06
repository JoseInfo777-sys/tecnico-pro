<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: sans-serif; font-size: 14px; }
        .text-center { text-align: center; }
        .header { margin-bottom: 20px; border-bottom: 1px solid #000; padding-bottom: 10px; }
        .section { margin-bottom: 10px; }
        .bold { font-weight: bold; }
        .footer { margin-top: 30px; font-size: 11px; text-align: center; border-top: 1px dashed #ccc; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header text-center">
        <h2 style="margin:0;">ORDEN DE SERVICIO</h2>
        <p>Folio: #{{ $order->id }}</p>
    </div>

    <div class="section">
        <span class="bold">Cliente:</span> {{ $order->device->customer->name }}<br>
        <span class="bold">Teléfono:</span> {{ $order->device->customer->phone }}<br>
        <span class="bold">Fecha:</span> {{ $order->created_at->format('d/m/Y H:i') }}
    </div>

    <div class="section" style="background: #f4f4f4; padding: 10px;">
        <span class="bold">EQUIPO:</span> {{ $order->device->brand }} {{ $order->device->model }}<br>
        <span class="bold">FALLA:</span> {{ $order->issue }}
    </div>

    <div class="section">
        <span class="bold">Estado inicial:</span> {{ $order->status }}<br>
        <span class="bold">Presupuesto:</span> S/ {{ number_format($order->price, 2) }}
    </div>

    <div class="footer">
        <p>Favor de presentar este ticket para recoger su equipo.<br>¡Gracias por su preferencia!</p>
    </div>
</body>
</html>