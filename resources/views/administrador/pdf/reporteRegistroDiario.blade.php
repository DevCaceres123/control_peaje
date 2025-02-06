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

        .detalle_reporte .puesto {
            position: absolute;
            top: 0;
            right: 0;
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
            margin-bottom: 3px;
            text-align: center;
            font-weight: bold;
        }

        .entrada,
        .salida {
            text-align: center;
            font-size: 14px;
        }

        .puesto {
            margin-top: 18px;
        }

        .tabla {
            width: 60%;
            margin: auto;
            margin-top: 20px;
            border-collapse: collapse;
            margin-bottom: 30px;


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
        .firmaencargado{
            text-align: center;
            margin:60px 0px;
        
        }
        .nombreFirma{
            text-transform: uppercase;
            font-weight: bold;
            font-size: 13px;
        }
        .rol{
            font-size: 12px;
        }
        .aclaracionFirma{
            font-size: 11px;
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
                <b>Encargado: </b>
                {{ $nombreCompletoUsuario['nombres'] ?? 'N/A' }}
                {{ $nombreCompletoUsuario['apellidos'] ?? 'N/A' }}
            </p>



            <hr>

        </div>




        {{-- TABLA DE LOS REGISTROS --}}

        @foreach ($registros_por_turno as $turno_info)
            <h3 class="titulo">REPORTE DE REGISTROS</h3>
            <p class="entrada">Entrada: {{ $turno_info['entrada'] }}</p>
            <p class="salida">Salida: {{ $turno_info['salida'] }}</p>

            <h5 class="puesto">{{ $turno_info['puesto']['nombre'] }}</h5>
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
                    <?php
                    $cont = 1;
                    $costo_total = 0;
                    $cantidad = 0;
                    ?>
                    @foreach ($turno_info['registros_agrupados'] as $precio => $detalle)
                        <tr>
                            <td>{{ $cont++ }}</td>
                            <td>{{ $precio }} Bs</td>
                            <td>{{ $detalle['cantidad'] }}</td>
                            <td>{{ $detalle['total'] }} Bs</td>
                            {{ $cantidad = $cantidad + $detalle['cantidad'] }}
                            {{ $costo_total = $costo_total + $detalle['total'] }}
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="2" style="text-align: right;"></td>
                        <td style="text-center: right;"><b>CANTIDAD: {{ $cantidad }} Registros </b></td>
                        <td style="text-center: right;"><b>TOTAL: {{ $costo_total }} (Bs) </b></td>

                    </tr>
                </tbody>
            </table>
        @endforeach

        <!-- Tabla de registros eliminados ese dia -->
        @if (!$registros_eliminados->isEmpty())
            <h4 class="titulo">REGISTROS ELIMINADOS </h4>

            <table class="tabla" style="width: 60%; margin:15px auto 0px auto;">
                <thead>
                    <tr>
                        <th>Nº</th>
                        <th>FECHA ELIMINACION</th>
                        <th>MONTO</th>


                    </tr>
                </thead>
                <tbody>
                    <?php
                    $count = 1;
                    $costo_total = 0;
                    
                    ?>
                    @foreach ($registros_eliminados as $registros_eliminado)
                        <tr>
                            <td>{{ $count++ }}</td>
                            <td>{{ $registros_eliminado->created_at }}</td>
                            <td>{{ $registros_eliminado->precio }}</td>

                            {{ $costo_total = $costo_total + $registros_eliminado->precio }}
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="2" style="text-align: right;"><b>TOTAL MONTO:</b></td>
                        <td><b>{{ $costo_total }} Bs</b></td>
                    </tr>
                </tbody>
            </table>
        @endif

        <div class="firmaencargado">
            <p>....................................................</p>
            <p class="nombreFirma">
                {{ $nombreCompletoUsuario['nombres'] ?? 'N/A' }}
                {{ $nombreCompletoUsuario['apellidos'] ?? 'N/A' }}
            </p>
            <p class="rol">
                Cajero(a) de Turno
            </p>
            <p class="aclaracionFirma">
                Aclaracion de firma
            </p>
        </div>


    </div>
</body>

</html>
