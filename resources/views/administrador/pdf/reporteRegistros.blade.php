<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>REPORTE DE CUOTAS PAGADAS</title>
    <style>
        /* Estilos optimizados */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;

        }

        body {
            font-family: Arial, sans-serif;
            padding: 45px;
        }

        .container_boleta {
            width: 100%;
            height: auto;

            border-radius: 8px;
            position: relative;

        }

        .info_empresa {
            text-align: center;
            margin-bottom: 40px;
            position: relative;
        }

        .info_empresa h2 {
            font-size: 21px;
            padding-right: 5px;
            margin-bottom: 5px;
        }

        .info_empresa p {
            font-size: 14px;
            margin: 4px 0;
        }

        .info_empresa img {
            position: absolute;
            top: 0;
            right: 0;

            border-radius: 5px;
        }

        .detalle_reporte {
            margin-top: 20px;
            font-size: 14px;
            position: relative;
            text-transform: capitalize !important;
            margin-bottom: 8px;

        }

        .detalle_reporte .encargado {
            position: absolute;
            top: 0;
            left: 0;
        }

        .puesto {
            margin: 15px 0px 8px 0px;

        }

        .puesto b {
            display: inline-block;
            background-color: #080625;
            color: white;
            padding: 5px 8px;
            border-radius: 10px;
            font-size: 12px;
        }


        .detalle_reporte hr {
            display: block;
            margin: 10px 0;
            border: none;
            border-top: 2px solid #ddd;
            margin-top: 25px;
        }

        .titulo {
            margin-top: 12px;
            text-align: center;
            font-weight: bold;
        }

        .fecha_generacion {
            text-align: center;
            font-weight: bold;
            font-size: 12px;
            margin-top: 2px;
        }


        .page-break {
            page-break-before: always;
            /* Fuerza un salto de página */
        }



        .tabla {
            width: 60%;
            height: 100vh;

            margin: auto;
            margin-top: 20px;
            border-collapse: collapse;
            margin-bottom: 10px;


        }


        .tabla th,
        .tabla td {
            border: 1px solid #333;
            padding: 4px;
            text-align: center;
            font-size: 12px;

        }

        .tabla th {
            background-color: #080625;
            color: white;
            font-weight: bold;
        }

        .ley {
            margin-top: 5px;
            font-size: 10px;
            text-align: center;
        }

        ley p {
            margin-top: 3px;
            text-align: center;
        }

        .ley .titulo_creacion {
            text-align: center;
            font-weight: 900;

        }

        @page {
            margin: 50px 25px;
        }

        @page :first {
            margin-top: 10px;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 12px;

            font-size: 12px;
            color: #555;
            margin-bottom: 8px;
            text-align: center;

        }

        .footer p {
            position: absolute;
            right: 12px;
            top: 0;

        }
    </style>
</head>

<body>
    <div class="container_boleta">
        <!-- Información de la empresa -->
        <div class="info_empresa">
            <h2>GOBIERNO AUTÓNOMO MUNICIPAL DE CARANAVI</h2>
            <h4>SECRETARIA MUNICIPAL ADMINISTRATIVA FINANCIERA</h4>
            <h4>Direccion de Recaudaciones</h4>
            <p>Reporte Generado: {{ now()->format('d-m-Y H:i:s') }}</p>
            <img src="assets/logo-caranavi.webp" alt="Logo" width="90" height="95">
        </div>

        <!-- Detalles de la reunión -->
        <div class="detalle_reporte">
            <p class="encargado">
                <b>Usuario: </b>
                {{ $nombreCompletoUsuario['nombres'] ?? 'N/A' }}
                {{ $nombreCompletoUsuario['apellidos'] ?? 'N/A' }}
            </p>

            <hr>

        </div>

        <p class="puesto">

            @foreach ($puestos as $puesto)
                <b> {{ $puesto->nombre ?? 'Sin asignar' }}</b>
            @endforeach

        </p>

        <h3 class="titulo">REPORTE DE REGISTROS</h3>
        <p class="fecha_generacion">({{ $fecha_inicio }} - {{ $fecha_fin }})</p>
        {{-- TABLA DE LOS REGISTROS --}}

        @php
            // Filtramos los registros por ley
            $ley13 = collect($registros)->whereIn('precio', [50, 100, 500, 1000]);
            $ley61 = collect($registros)->whereIn('precio', [2, 4, 6, 8, 10, 12, 14]);
        @endphp


        <!-- Tabla Ley 13 -->

        <table class="tabla">
            <thead>
                <tr>
                    <th>Nº</th>
                    <th>Precio</th>
                    <th>Cantidad</th>
                    <th>Importe</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $cont = 1;
                    $cantidad_total_ley13 = 0;
                    $costo_total_ley13 = 0;
                @endphp

                @foreach ($ley13 as $registro13)
                    <tr>
                        <td>{{ $cont++ }}</td>
                        <td>{{ number_format($registro13['precio']) }} Bs</td>
                        <td>{{ $registro13['cantidad'] }}</td>
                        <td>
                            @php
                                $importe13 = $registro13['total'];
                                $cantidad_total_ley13 += $registro13['cantidad'];
                                $costo_total_ley13 += $importe13;
                            @endphp
                            {{ number_format($importe13) }} Bs
                        </td>
                    </tr>
                @endforeach

                <!-- Totales Ley 13 -->
                <tr>
                    <td></td>
                    <td colspan="2"><strong>{{ $cantidad_total_ley13 }} Registros</strong></td>
                    <td><strong>{{ number_format($costo_total_ley13, 2) }} Bs</strong></td>
                </tr>
            </tbody>
        </table>
        <div class="ley">
            <p class="titulo_creacion">LEY AUTÓNOMA MUNICIPAL N.º 13/2021</p>
            <p>LEY MUNICIPAL DE TASA DE RODAJE Y NORMATIVA DE INGRESO DE VEHÍCULOS DE TRANSPORTE, RURAL E
                INTERPROVINCIAL DE CARGA Y DESCARGA</p>
        </div>
        <!-- Salto de página para la segunda tabla -->
        @php $numPagina = 1; @endphp

        <div class="footer">
            {{-- Página {{ $numPagina }} de 2 --}}
            <p>Ley 13/2021</p>
        </div>

        <div class="page-break"></div>
        @php $numPagina++; @endphp


        <!-- Información de la empresa -->
        <div class="info_empresa">
            <h2>GOBIERNO AUTÓNOMO MUNICIPAL DE CARANAVI</h2>
            <h4>SECRETARIA MUNICIPAL ADMINISTRATIVA FINANCIERA</h4>
            <h4>Direccion de Recaudaciones</h4>
            <p>Reporte Generado: {{ now()->format('d-m-Y H:i:s') }}</p>
            <img src="assets/logo-caranavi.webp" alt="Logo" width="90" height="95">
        </div>

        <!-- Detalles de la reunión -->
        <div class="detalle_reporte">
            <p class="encargado">
                <b>Usuario: </b>
                {{ $nombreCompletoUsuario['nombres'] ?? 'N/A' }}
                {{ $nombreCompletoUsuario['apellidos'] ?? 'N/A' }}
            </p>

            <hr>

        </div>

        <p class="puesto">

            @foreach ($puestos as $puesto)
                <b> {{ $puesto->nombre ?? 'Sin asignar' }}</b>
            @endforeach

        </p>

        <h3 class="titulo">REPORTE DE REGISTROS</h3>
        <p class="fecha_generacion">({{ $fecha_inicio }} - {{ $fecha_fin }})</p>
        <!-- Tabla Ley 61 -->

        <table class="tabla">
            <thead>
                <tr>
                    <th>Nº</th>
                    <th>Precio</th>
                    <th>Cantidad</th>
                    <th>Importe</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $cont = 1;
                    $cantidad_total_ley61 = 0;
                    $costo_total_ley61 = 0;
                @endphp

                @foreach ($ley61 as $registro61)
                    <tr>
                        <td>{{ $cont++ }}</td>
                        <td>{{ number_format($registro61['precio']) }} Bs</td>
                        <td>{{ $registro61['cantidad'] }}</td>
                        <td>
                            @php
                                $importe61 = $registro61['total'];
                                $cantidad_total_ley61 += $registro61['cantidad'];
                                $costo_total_ley61 += $importe61;
                            @endphp
                            {{ number_format($importe61) }} Bs
                        </td>
                    </tr>
                @endforeach

                <!-- Totales Ley 61 -->
                <tr>
                    <td></td>
                    <td colspan="2"><strong>{{ $cantidad_total_ley61 }} Registros</strong></td>
                    <td><strong>{{ number_format($costo_total_ley61, 2) }} Bs</strong></td>
                </tr>
            </tbody>
        </table>
        <div class="ley">
            <p class="titulo_creacion">LEY AUTÓNOMA MUNICIPAL N.º 61/2024</p>
            <p>LEY MUNICIPAL DE CREACION DE LA TASA DE RODAJE-PEAJE DEL GOBIERNO AUTÓNOMO
                MUNICIPAL DE CARANAVI</p>
        </div>


    </div>

    <div class="footer">
        {{-- Página {{ $numPagina }} de 2 --}}
        <p>Ley 61/2024</p>
    </div>

</body>

</html>
