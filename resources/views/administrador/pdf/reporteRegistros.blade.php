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
            padding: 20px;
            color: #333;
        }

        .container_boleta {
            padding: 15px;
            border: 1px solid #8b5050;
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

        .tabla {
            width: 70%;
            margin: auto;
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
            margin-top: 12px;
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

        .ley13 {
            background-color: #c01c28;
            color: wheat;
        }

        .ley64 {
            background-color: #2b8a3e;
            color: wheat;
        }
    </style>
</head>

<body>
    <div class="container_boleta">
        <!-- Información de la empresa -->
        <div class="info_empresa">
            <h2>GOBIERNO AUTÓNOMO MUNICIPAL DE CARANAVI</h2>
            <h3>Direccion de Recaudaciones</h3>
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


        <table class="tabla">
            <thead>
                <tr>
                    <th>Nº</th>
                    @if ($ley13->count() > 0)
                        <th>Ley 13/2021</th>
                        <th>Cantidad</th>
                        <th>Importe</th>
                    @endif
                    @if ($ley61->count() > 0)
                        <th>Ley 61/2024</th>
                        <th>Cantidad</th>
                        <th>Importe</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @php
                    $cont = 1;
                    $cantidad_total_ley13 = 0;
                    $costo_total_ley13 = 0;
                    $cantidad_total_ley61 = 0;
                    $costo_total_ley61 = 0;

                    $maxFilas = max($ley13->count(), $ley61->count());
                @endphp

                @for ($i = 0; $i < $maxFilas; $i++)
                    <tr>
                        <td>{{ $cont++ }}</td>

                        @php
                            $registro13 = $ley13->values()->get($i);
                            $registro61 = $ley61->values()->get($i);
                        @endphp

                        @if ($ley13->count() > 0)
                            <td>{{ $registro13 ? number_format($registro13['precio']) . ' Bs' : '' }}</td>
                            <td>{{ $registro13 ? $registro13['cantidad'] : '' }}</td>
                            <td>
                                @php
                                    $importe13 = $registro13 ? $registro13['total'] : 0;
                                    $cantidad_total_ley13 += $registro13 ? $registro13['cantidad'] : 0;
                                    $costo_total_ley13 += $importe13;
                                @endphp
                                {{ $registro13 ? number_format($importe13) . ' Bs' : '' }}
                            </td>
                        @endif

                        @if ($ley61->count() > 0)
                            <td>{{ $registro61 ? number_format($registro61['precio']) . ' Bs' : '' }}</td>
                            <td>{{ $registro61 ? $registro61['cantidad'] : '' }}</td>
                            <td>
                                @php
                                    $importe61 = $registro61 ? $registro61['total'] : 0;
                                    $cantidad_total_ley61 += $registro61 ? $registro61['cantidad'] : 0;
                                    $costo_total_ley61 += $importe61;
                                @endphp
                                {{ $registro61 ? number_format($importe61) . ' Bs' : '' }}
                            </td>
                        @endif
                    </tr>
                @endfor

                <!-- Fila de totales por ley -->
                <tr>
                    <td></td>
                    @if ($ley13->count() > 0)
                        <td colspan="2" class="ley13"><strong>{{ $cantidad_total_ley13 }} Registros</strong></td>
                        <td class="ley13"><strong>{{ number_format($costo_total_ley13, 2) }} Bs</strong></td>
                    @endif
                    @if ($ley61->count() > 0)
                        <td colspan="2" class="ley64"><strong>{{ $cantidad_total_ley61 }} Registros</strong></td>
                        <td class="ley64"><strong>{{ number_format($costo_total_ley61, 2) }} Bs</strong></td>
                    @endif
                </tr>

                <!-- Fila total de cantidad -->
                <tr>
                    <td colspan="{{ $ley13->count() > 0 && $ley61->count() > 0 ? 3 : 2 }}" style="text-align: right;">
                        <strong>Total Registros:</strong></td>
                    <td colspan="{{ $ley13->count() > 0 && $ley61->count() > 0 ? 4 : 2 }}"
                        style="text-align: center; background-color: #252525; color: #fff;">
                        <strong>{{ $cantidad_total_ley13 + $cantidad_total_ley61 }} Registros</strong>
                    </td>
                </tr>

                <!-- Fila del importe total -->
                <tr>
                    <td colspan="{{ $ley13->count() > 0 && $ley61->count() > 0 ? 3 : 2 }}" style="text-align: right;">
                        <strong>Importe Total:</strong></td>
                    <td colspan="{{ $ley13->count() > 0 && $ley61->count() > 0 ? 4 : 2 }}"
                        style="text-align: center; background-color: #080625; color: #ddd">
                        <strong>{{ number_format($costo_total_ley13 + $costo_total_ley61, 2) }} Bs</strong>
                    </td>
                </tr>
            </tbody>
        </table>





    </div>
</body>

</html>
