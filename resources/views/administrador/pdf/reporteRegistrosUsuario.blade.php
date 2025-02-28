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
            color: #333;
        }

        .container_boleta {
            padding: 15px;

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
            margin-bottom: 5px;
        }

        .fecha_generacion {
            text-align: center;
            font-weight: bold;
            font-size: 12px;
            margin-top: 2px;
        }

        .detalle_reporte hr {
            display: block;
            margin: 10px 0;
            border: none;
            border-top: 2px solid #ddd;
            margin-top: 25px;
        }

        .titulo {
            position: relative;
            width: 100%;
            height: auto;
            margin-top: 12px;
            margin-bottom: 3px;
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 13px;
        }

        .entrada,
        .salida {
            text-align: center;
            font-size: 14px;
        }

        .puesto {
            margin-top: 18px;
        }

        .contenedor_tablas {
            width: 100%;
            height: 30vh;
            margin: auto;
        }

        .tabla {
            width: 60%;
            margin: auto;
            margin-top: 20px;
            border-collapse: collapse;
            margin-bottom: 30px;
            page-break-inside: avoid;

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

        .firmaencargado {
            text-align: center;
            margin: 60px 0px;

        }

        .nombreFirma {
            text-transform: uppercase;
            font-weight: bold;
            font-size: 13px;
        }

        .rol {
            font-size: 12px;
        }

        .aclaracionFirma {
            font-size: 11px;
        }

        .total_dia {
            position: absolute;
            top: 0px;
            right: 0px;
            padding: 4px;

            border: 1px solid #080625;
            border-top: none;
            font-size: 12px;
        }

        .page-break {
            page-break-before: always;
            /* Fuerza un salto de página */
        }

        @page {
            margin: 50px 25px;
        }

        .footer {
            position: fixed;
            bottom: 0px;
            left: 0px;
            width: 100%;
            text-align: center;
            font-size: 12px;
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
                <b>Encargado: </b>
                {{ $nombreCompletoUsuario['nombres'] ?? 'N/A' }}
                {{ $nombreCompletoUsuario['apellidos'] ?? 'N/A' }}
            </p>



            <hr>

        </div>
        <?php
        $totalTurno = 0;
        $contadorTabla = 0;
        $contadorTablasGRandes = 0;
        $usuarioAnterior = null; // Variable para rastrear el usuario anterior
        $contadorHojas = 0;
        ?>

        {{-- TABLA DE LOS REGISTROS --}}
        @if ($listarTurno != null)
            @foreach ($registros as $registro)
                @foreach ($registro as $index => $turno_info)
                    @if (count($turno_info['registros_agrupados']) > 0)
                        <?php
                        $cantidadRegistros = count($turno_info['registros_agrupados']);
                        
                        // Si la tabla tiene 5 o más registros, contarla como "grande"
                        if ($cantidadRegistros >= 5) {
                            $contadorTablasGRandes++;
                        }
                        
                        // Sumar total del turno
                        foreach ($turno_info['registros_agrupados'] as $precio => $detalle) {
                            $totalTurno += $detalle['total'];
                        }
                        ?>

                        {{-- SALTO DE PÁGINA CUANDO CAMBIA EL USUARIO (evita doble salto) --}}
                        @if ($usuarioAnterior !== null && $usuarioAnterior !== $turno_info['nombreEncargado']['id'])
                            <div class="footer">
                                <?php
                                $contadorHojas++;
                                
                                ?>
                                Página. {{ $contadorHojas }}
                            </div>
                            <div class="page-break"></div>
                            <hr>
                            <?php
                            $contadorTabla = 0;
                            $contadorTablasGRandes = 0;
                            ?>
                        @else
                            {{-- SALTO DE PÁGINA SI HAY 2 TABLAS GRANDES --}}
                            @if ($contadorTablasGRandes == 2)
                                <div class="footer">
                                    <?php
                                    $contadorHojas++;
                                    
                                    ?>
                                    Página. {{ $contadorHojas }}
                                </div>
                                <div class="page-break"></div>
                                <?php
                                $contadorTabla = 0;
                                $contadorTablasGRandes = 0;
                                ?>
                            @endif

                            {{-- SALTO DE PÁGINA SI HAY 3 TABLAS PEQUEÑAS (solo si no hay cambio de usuario) --}}
                            @if ($contadorTabla == 3)
                                <div class="footer">
                                    <?php
                                    $contadorHojas++;
                                    
                                    ?>
                                    Página. {{ $contadorHojas }}
                                </div>
                                <div class="page-break"></div>
                                <?php
                                $contadorTabla = 0;
                                $contadorTablasGRandes = 0;
                                ?>
                            @endif
                        @endif

                        {{-- Actualizamos el usuario anterior --}}
                        <?php $usuarioAnterior = $turno_info['nombreEncargado']['id']; ?>

                        <div class="contenedor_tablas">
                            <h3 class="titulo">
                                {{ $turno_info['nombreEncargado']['nombres'] ?? 'N/A' }}
                                {{ $turno_info['nombreEncargado']['apellidos'] ?? 'N/A' }}
                                <b class="total_dia">Total: {{ $totalTurno }} Bs</b>
                            </h3>
                            <p class="entrada">Entrada: {{ $turno_info['entrada'] }}</p>
                            <p class="salida">Salida: {{ $turno_info['salida'] }}</p>
                            <p class="puesto">
                                <b>{{ $turno_info['puesto']['nombre'] ?? 'Sin puesto' }}</b>
                            </p>

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
                                            <?php
                                            $cantidad += $detalle['cantidad'];
                                            $costo_total += $detalle['total'];
                                            ?>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="2" style="text-align: right;"></td>
                                        <td style="text-align: center;"><b>{{ $cantidad }} Registros </b></td>
                                        <td style="text-align: center;"><b>TOTAL: {{ $costo_total }} Bs </b></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <?php $contadorTabla++; ?>
                    @endif
                @endforeach
                @if ($loop->last)
                    <div class="footer">
                        <?php
                        $contadorHojas++;
                        
                        ?>
                        Página. {{ $contadorHojas }}
                    </div>
                @endif
            @endforeach
        @else
            <p class="puesto">
                @foreach ($usuarios as $usuario)
                    <b>{{ $usuario ?? 'N/A' }}</b>
                @endforeach
            </p>

            <p class="fecha_generacion">({{ $fecha_inicio }} - {{ $fecha_fin }})</p>


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
                    
                    // Filtramos los registros por ley
                    $ley13 = collect($registros)->only([50, 100, 500, 1000]);
                    ?>



                    @foreach ($ley13 as $key => $registro)
                        <tr>
                            <td>{{ $cont++ }}</td>
                            <td>{{ $key }} Bs</td>
                            <td>{{ $registro['cantidad'] }}</td>
                            <td>{{ $registro['total'] }} Bs</td>
                            {{ $cantidad = $cantidad + $registro['cantidad'] }}
                            {{ $costo_total = $costo_total + $registro['total'] }}
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="2" style="text-align: right;"></td>
                        <td style="text-center: right;"><b>{{ $cantidad }} Registros </b></td>
                        <td style="text-center: right;"><b>TOTAL: {{ $costo_total }} (Bs) </b></td>

                    </tr>
                </tbody>

            </table>


            <div class="ley">
                <p class="titulo_creacion">LEY AUTÓNOMA MUNICIPAL N.º 13/2021</p>
                <p>LEY MUNICIPAL DE TASA DE RODAJE Y NORMATIVA DE INGRESO DE VEHÍCULOS DE TRANSPORTE, RURAL E
                    INTERPROVINCIAL DE CARGA Y DESCARGA</p>
            </div>

            <div class="footer">
                {{-- <?php
                $contadorHojas++;
                ?>
                Página. {{ $contadorHojas }} --}}

                <p>Ley 13/2021</p>

            </div>
            <div class="page-break"></div>

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
                    <b>Encargado: </b>
                    {{ $nombreCompletoUsuario['nombres'] ?? 'N/A' }}
                    {{ $nombreCompletoUsuario['apellidos'] ?? 'N/A' }}
                </p>



                <hr>

            </div>
            <p class="puesto">
                @foreach ($usuarios as $usuario)
                    <b>{{ $usuario ?? 'N/A' }}</b>
                @endforeach
            </p>

            <p class="fecha_generacion">({{ $fecha_inicio }} - {{ $fecha_fin }})</p>
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
                    
                    // Filtramos los registros por ley
                    $ley61 = collect($registros)->only([2, 4, 6, 8, 10, 12, 14]);
                    ?>



                    @foreach ($ley61 as $key => $registro)
                        <tr>
                            <td>{{ $cont++ }}</td>
                            <td>{{ $key }} Bs</td>
                            <td>{{ $registro['cantidad'] }}</td>
                            <td>{{ $registro['total'] }} Bs</td>
                            {{ $cantidad = $cantidad + $registro['cantidad'] }}
                            {{ $costo_total = $costo_total + $registro['total'] }}
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="2" style="text-align: right;"></td>
                        <td style="text-center: right;"><b>{{ $cantidad }} Registros </b></td>
                        <td style="text-center: right;"><b>TOTAL: {{ $costo_total }} (Bs) </b></td>

                    </tr>
                </tbody>

            </table>

            <div class="ley">
                <p class="titulo_creacion">LEY AUTÓNOMA MUNICIPAL N.º 61/2024</p>
                <p>LEY MUNICIPAL DE CREACION DE LA TASA DE RODAJE-PEAJE DEL GOBIERNO AUTÓNOMO
                    MUNICIPAL DE CARANAVI</p>
            </div>

            <div class="footer">
                {{-- <?php
                $contadorHojas++;
                ?>
                Página. {{ $contadorHojas }} --}}

                <p>Ley 61/2024</p>

            </div>
        @endif

    </div>
</body>

</html>
