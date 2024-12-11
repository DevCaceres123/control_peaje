<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BOLETA DE PAGO</title>
    <style>
        /* Estilos optimizados */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            color: #333;
        }

        .container_boleta {
            padding: 15px;
            border: 3px dashed #8b5050;
            border-radius: 8px;
            position: relative;
        }

        .info_empresa {
            text-align: center;
            margin-bottom: 10px;
            position: relative;
        }

        .info_empresa h2 {
            font-size: 14px;
            margin-bottom: 5px;
            font-weight: 100;
            letter-spacing: 2px;
        }

        .info_empresa p {
            font-size: 14px;
            margin: 4px 0;
            text-transform: capitalize;
        }



        .detalles_reunion {
            margin-top: 20px;
            font-size: 14px;

            text-transform: capitalize !important;
        }


        .detalles_reunion hr {
            margin: 10px 0;
            border: none;
            border-top: 1px solid #ddd;
        }

        .titulo {
            margin-top: 12px;
            text-align: center;
            font-weight: bold;
        }

        .tabla {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        .tabla th,
        .tabla td {
            border: 1px solid #333;
            padding: 8px;
            text-align: center;
            font-size: 12px;
        }

        .tabla th {
            background-color: #080625;
            color: white;
            font-weight: bold;
        }

        .totalBoletas {
            width: 100%;
            position: relative;
        }

        .totalBoletas .inasitencia {
            position: absolute;
            top: 0;
            left: 0;

            margin-top: 20px;
            font-size: 16px;
            font-weight: bold;
        }


        .totalBoletas .observados {
            position: absolute;
            top: 0;
            right: 50%;
            left: 50%;
            margin-top: 20px;
            font-size: 16px;
            font-weight: bold;
            text-align: center
        }

        .totalBoletas .asistencia {
            position: absolute;
            top: 0;
            right: 0;

            margin-top: 20px;
            font-size: 16px;
            font-weight: bold;
        }

        .cod_precio {
            width: 100%;
            position: relative;
            height: 30px;
            border-bottom: 5px dotted #000
        }

        .cod_precio .codigo {
            position: absolute;
            left: 0;
            top: 0;
        }

        .cod_precio .precio {
            position: absolute;
            right: 0;
            top: 0;
        }

        .qr_code {

            width: 160px;
            margin: auto;
            height: 160px;
            
            margin-top: 15px;
            margin-bottom: 20px;
            box-sizing: border-box;
        }

        .qr_code img {
            display: inline-block;
            margin: auto;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .vehiculo {
            width: 100%;
            text-align: center;
            font-size: 13px;
            letter-spacing: 1px;
            margin-top: 8px;
            padding-top: 8px;
            border-top: 5px dotted #000;
            border-bottom: 5px dotted #000
        }

        .vehiculo h3 {
            text-transform: uppercase;
            font-size: 14px;
        }

        .vehiculo h3,
        p {
            margin-bottom: 4px;
        }

        .fechas {
            width: 100%;
            text-align: center;
            font-size: 13px;
            letter-spacing: 1px;
            margin-top: 8px;
            padding-top: 8px;

        }

        .nota {
            margin-top: 10px;
            text-align: center;
            font-size: 12px;
        }

        .ley {
            margin-top: 15px;
            font-size: 13px;
            text-align: justify;
        }

        .ley .titulo_creacion {
            text-align: center;
            font-weight: 900;
            margin-bottom: 8px;
        }
    </style>
</head>

<body>
    <div class="container_boleta">
        <!-- Información de la empresa -->
        <div class="info_empresa">
            <h2>GOBIERNO AUTONOMO MUNICIPAL DE CARANAVI</h2>
            <hr>
            <h2>DIRECCION DE RECAUDACIONES</h2>

            <hr>
            <p class="usuario">Us: {{ $usuario['nombres'] ?? 'NA' }} {{ $usuario['apellidos'] ?? 'NA' }}</p>
            <p class="puesto">Puesto: {{ $puesto->nombre ?? 'N/A' }}</p>


        </div>

        <div class="cod_precio">
            <span class="codigo">{{ $tarifa->descripcion ?? 'N/A' }}</span>
            <span class="precio">{{ $tarifa->precio ?? 'N/A' }} <b>Bs</b></span>
        </div>

        <div class="qr_code">

            <img src="data:image/png;base64,{{ $qrCodeBase64 }}" alt="QR Code">
        </div>

        <div class="vehiculo">
            @if ($placa != null)
                <h3>Datos del Vehiculo</h3>
                <p><b>Placa: </b> {{ strtoupper($placa) }}</p>
            @endif
            @if ($color != null)
                <p><b>color: </b> {{ $color }}</p>
            @endif

            @if ($tipo_auto != null)
                <p><b>Tipo: </b> {{ $tipo_auto }}</p>
            @endif


        </div>

        <div class="fechas">
            <p><b> Fecha Generada: </b></p>
            <p>{{ now()->format('Y-m-d H:i:s') }}</p>
            <p><b> Valida hasta:</b></p>
            <p>{{ $fecha_finalizacion }}</p>

        </div>



        <div class="ley">
            <p class="titulo_creacion">LEY AUTÓNOMA MUNICIPAL N.º 61/2024</p>
            <P>LEY MUNICIPAL DE CREACION DE LA TASA DE RODAJE-PEAJE DEL GOBIERNO AUTÓNOMO
                MUNICIPAL DE CARANAVI</P>
        </div>


    </div>
</body>

</html>
