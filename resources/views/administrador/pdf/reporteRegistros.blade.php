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
         margin: 15px 0px 8px 0px ;

        }
        .puesto b{
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
                    $cont = 1; // Contador para las filas
                    $costo_total = 0; // Acumulador para el total de importes
                    $cantidad_total = 0; // Acumulador para la cantidad total
                @endphp

                @foreach ($registros as $registro)
                    <tr>
                        <td>{{ $cont++ }}</td>
                        <td>{{ number_format($registro['precio']) }} Bs</td>
                        <td>{{ $registro['cantidad'] }}</td>
                        <td>{{ number_format($registro['total']) }} Bs</td>
                        @php
                            $cantidad_total += $registro['cantidad'];
                            $costo_total += $registro['total'];
                        @endphp
                    </tr>
                @endforeach

                <!-- Fila de totales -->
                <tr>
                    <td colspan="2" style="text-align: right;"><strong>Total:</strong></td>
                    <td><strong>{{ $cantidad_total }} Registros</strong></td>
                    <td><strong>{{ number_format($costo_total, 2) }} Bs</strong></td>
                </tr>
            </tbody>
        </table>




    </div>
</body>

</html>
