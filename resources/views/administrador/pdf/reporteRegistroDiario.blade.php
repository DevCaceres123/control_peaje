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
            margin: 120px 0px 0px 0px;

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

        .page-break {
            page-break-before: always;
            /* Fuerza un salto de página */
        }


        .footer {}

        .footer p {
            position: fixed;
            bottom: 0px;
            left: 0px;
            width: 100%;
            text-align: center;
            font-size: 14px;

        }
    </style>
</head>

<body>



    <?php
    $ley13 = [];
    $ley61 = [];
    $ley13Eliminados = [];
    $ley61Eliminados = [];
    $contadorHojas = 0;
    ?>


    {{-- Si la persona no tiene turno creado ese dia --}}

    @if (count($registros_por_turno) == 0)
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
        <h3 class="titulo" style="color: #922121; margin-top: 80px">EL USUARIO NO TIENE ASIGANDO UN TURNO EN LA FECHA
        </h3>
    @endif


    {{-- Si solo existe un turno entonces se  va a visualizar la entrada y salida --}}
    @if (count($registros_por_turno) == 1)

        @foreach ($registros_por_turno as $turno_info)
            @php
                $ley13 = collect($turno_info['registros_agrupados'])->only([50, 100, 500, 1000]); // Filtrar solo las claves que necesitamos
                $ley61 = collect($turno_info['registros_agrupados'])->only([2, 4, 6, 8, 10, 12, 14]);
                $ley13Eliminados = collect($turno_info['registros_eliminados'])->whereIn('precio', [
                    50,
                    100,
                    500,
                    1000,
                ]); // Filtrar solo las claves que necesitamos
                $ley61Eliminados = collect($turno_info['registros_eliminados'])->whereIn('precio', [
                    2,
                    4,
                    6,
                    8,
                    10,
                    12,
                    14,
                ]); // Filtrar solo las claves que necesitamos
            @endphp

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
                        @foreach ($ley13 as $precio => $detalle)
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

                {{-- titulo de ley 13/2021 --}}
                <div class="ley">
                    <p class="titulo_creacion">LEY AUTÓNOMA MUNICIPAL N.º 13/2021</p>
                    <p>LEY MUNICIPAL DE TASA DE RODAJE Y NORMATIVA DE INGRESO DE VEHÍCULOS DE TRANSPORTE, RURAL E
                        INTERPROVINCIAL DE CARGA Y DESCARGA</p>
                </div>

                <!-- Tabla de registros eliminados ese dia -->
                @if (!$ley13Eliminados->isEmpty())
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
                            @foreach ($ley13Eliminados as $registros_eliminado)
                                <tr>
                                    <td>{{ $count++ }}</td>
                                    <td>{{ $registros_eliminado->deleted_at }}</td>
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
                <div class="footer">
                    {{-- <?php
                    $contadorHojas++;
                    ?>
                Página. {{ $contadorHojas }} --}}
                    <p>Ley 13/2021</p>

                </div>

            </div>


            <div class="page-break"></div>


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
                        @foreach ($ley61 as $precio => $detalle)
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

                {{-- ley 61/2024 --}}
                <div class="ley">
                    <p class="titulo_creacion">LEY AUTÓNOMA MUNICIPAL N.º 61/2024</p>
                    <p>LEY MUNICIPAL DE CREACION DE LA TASA DE RODAJE-PEAJE DEL GOBIERNO AUTÓNOMO
                        MUNICIPAL DE CARANAVI</p>
                </div>


                <!-- Tabla de registros eliminados ese dia -->
                @if (!$ley61Eliminados->isEmpty())
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
                            @foreach ($ley61Eliminados as $registros_eliminado)
                                <tr>
                                    <td>{{ $count++ }}</td>
                                    <td>{{ $registros_eliminado->deleted_at }}</td>
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
                <div class="footer">
                    {{-- <?php
                    $contadorHojas++;
                    ?>
                Página. {{ $contadorHojas }} --}}
                    <p>Ley 61/2024</p>
                </div>
            </div>
            @if (!$loop->last)
                <div class="page-break"></div> {{-- Esto solo se ejecuta si NO es el último --}}
            @endif
        @endforeach
    @endif


    {{-- en caso de que el usuario por error haya genrado varios turnos solo se mostrara el que tenga registros --}}
    @if (count($registros_por_turno) > 1)
        @foreach ($registros_por_turno as $turno_info)
            {{ count($registros_por_turno) }}
            @if (count($turno_info['registros_agrupados']) > 0)
                @php
                    $ley13 = collect($turno_info['registros_agrupados'])->only([50, 100, 500, 1000]); // Filtrar solo las claves que necesitamos
                    $ley61 = collect($turno_info['registros_agrupados'])->only([2, 4, 6, 8, 10, 12, 14]);
                    $ley13Eliminados = collect($turno_info['registros_eliminados'])->whereIn('precio', [
                        50,
                        100,
                        500,
                        1000,
                    ]); // Filtrar solo las claves que necesitamos
                    $ley61Eliminados = collect($turno_info['registros_eliminados'])->whereIn('precio', [
                        2,
                        4,
                        6,
                        8,
                        10,
                        12,
                        14,
                    ]); // Filtrar solo las claves que necesitamos
                @endphp

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
                            @foreach ($ley13 as $precio => $detalle)
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

                    {{-- titulo de ley 13/2021 --}}
                    <div class="ley">
                        <p class="titulo_creacion">LEY AUTÓNOMA MUNICIPAL N.º 13/2021</p>
                        <p>LEY MUNICIPAL DE TASA DE RODAJE Y NORMATIVA DE INGRESO DE VEHÍCULOS DE TRANSPORTE, RURAL E
                            INTERPROVINCIAL DE CARGA Y DESCARGA</p>
                    </div>

                    <!-- Tabla de registros eliminados ese dia -->
                    @if (!$ley13Eliminados->isEmpty())
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
                                @foreach ($ley13Eliminados as $registros_eliminado)
                                    <tr>
                                        <td>{{ $count++ }}</td>
                                        <td>{{ $registros_eliminado->deleted_at }}</td>
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
                    <div class="footer">
                        {{-- <?php
                        $contadorHojas++;
                        ?>
                Página. {{ $contadorHojas }} --}}
                        <p>Ley 13/2021</p>

                    </div>

                </div>


                <div class="page-break"></div>


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
                            @foreach ($ley61 as $precio => $detalle)
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

                    {{-- ley 61/2024 --}}
                    <div class="ley">
                        <p class="titulo_creacion">LEY AUTÓNOMA MUNICIPAL N.º 61/2024</p>
                        <p>LEY MUNICIPAL DE CREACION DE LA TASA DE RODAJE-PEAJE DEL GOBIERNO AUTÓNOMO
                            MUNICIPAL DE CARANAVI</p>
                    </div>


                    <!-- Tabla de registros eliminados ese dia -->
                    @if (!$ley61Eliminados->isEmpty())
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
                                @foreach ($ley61Eliminados as $registros_eliminado)
                                    <tr>
                                        <td>{{ $count++ }}</td>
                                        <td>{{ $registros_eliminado->deleted_at }}</td>
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
                    <div class="footer">
                        {{-- <?php
                        $contadorHojas++;
                        ?>
                Página. {{ $contadorHojas }} --}}
                        <p>Ley 61/2024</p>
                    </div>
                </div>
                @if (!$loop->last)
                    <div class="page-break"></div> {{-- Esto solo se ejecuta si NO es el último --}}
                @endif
            @endif
        @endforeach

    @endif




    </div>
</body>

</html>
