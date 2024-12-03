<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
        }

        .ticket {
            width: 80mm;
            /* Ancho estándar para muchas impresoras térmicas */
            padding: 10mm;
            box-sizing: border-box;
            border: 1px solid #000;
            text-align: center;
            font-size: 12px;
        }

        .title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10mm;
        }

        .content {
            font-size: 14px;
            margin-bottom: 10mm;
        }

        .qr-code {
            width: 100%;
            /* Asegura que el QR ocupe todo el ancho disponible */
            height: auto;
            margin-bottom: 10mm;
        }

        .footer {
            font-size: 10px;
            margin-top: 10mm;
            color: #666;
        }
    </style>
</head>

<body>

    <div class="ticket">
        <div class="title">Reporte de Ventas</div>

        <div class="content">
            Este es un ejemplo de un ticket generado en Laravel.
            <br>
            El código QR es el elemento más importante.
        </div>

        <div class="qr-code">
            {{-- <img src="data:image/png;base64,{{ $qrCodeBase64 }}" alt="QR Code"> --}}
        </div>

        <div class="footer">
            ¡Gracias por tu compra! <br>
            Visita nuestro sitio web para más información.
        </div>
    </div>

</body>

</html>
