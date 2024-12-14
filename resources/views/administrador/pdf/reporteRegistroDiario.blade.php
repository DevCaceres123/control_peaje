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
    </style>
</head>

<body>
    <div class="container_boleta">
        <!-- Información de la empresa -->
        <div class="info_empresa">
            <h2>GOBIERNO AUTÓNOMO MUNICIPAL DE CARANAVI</h2>
            <h3>Departamento de Recaudaciones</h3>
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


            <p class="puesto">
                <b>Puesto: </b>
                {{ $puesto->nombre ?? 'Sin asignar' }}
            </p>
            <hr>

        </div>

        <h3 class="titulo">REPORTE DE REGISTROS ({{ now()->format('d-m-Y') }} )</h3>

        <!-- Tabla de asistencia -->
        <table class="tabla">
            <thead>
                <tr>
                    <th>Nº</th>
                    <th>CONTEO</th>
                    <th>PLACA</th>
                    <th>CI</th>
                    <th>MONTO</th>


                </tr>
            </thead>
            <tbody>
                <?php
                $count = 1;
                $costo_total = 0;
                $count_pagados = 0;
                ?>
                @foreach ($registros as $registro)
                    <tr>
                        <td>{{ $count++ }}</td>
                        <td>{{ $registro->num_aprobados ?? 0 }} Validados</td>
                        <td>{{ $registro->placa ?? 'Sin registrar' }}</td>
                        <td>{{ $registro->ci ?? 'Sin registrar' }}</td>
                        <td>{{ $registro->precio }} <b>Bs</b></td>
                        {{ $costo_total = $costo_total + $registro->precio }}
                    </tr>
                @endforeach
                <tr>
                    <td colspan="4" style="text-align: right;"><b>TOTAL MONTO:</b></td>
                    <td><b>{{ $costo_total }} Bs</b></td>
                </tr>
            </tbody>
        </table>






        <!-- Tabla de asistencia -->
        @if (!$registros_eliminados->isEmpty())
            <h4 class="titulo">REGISTROS ELIMINADOS({{ now()->format('d-m-Y') }} )</h4>

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


    </div>
</body>

</html>
