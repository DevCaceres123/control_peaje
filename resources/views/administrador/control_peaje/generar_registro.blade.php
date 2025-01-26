@extends('principal')
@section('titulo', 'PERFIL')

@section('contenido')
    <link rel="stylesheet" href="{{ asset('css/precios.css') }}">
    <div class="row">
        <div class="col-12">
            <div class="contenedor_precio">
                @foreach ($tarifas as $tarifa)
                    @if ($tarifa->precio == 2 || $tarifa->precio == 4 || $tarifa->precio == 8)
                        <div class="card_precio masUsados" data-id="{{ $tarifa->id }}"
                            style="width: 15%; min-width: 160px; height: 115px; background-color: rgb(223, 99, 42);font-size: 12px;">

                            <p>{{ $tarifa->nombre }}</p>
                            <div class="icon"><i class="fas fa-truck fs-24"></i></div>
                            <strong class="bg-success">{{ $tarifa->precio }}Bs</strong>
                        </div>
                    @else
                        <div class="card_precio bg-primary" data-id="{{ $tarifa->id }}">

                            <p>{{ $tarifa->nombre }}</p>
                            <div class="icon"><i class="fas fa-truck fs-24"></i></div>
                            <strong class="bg-success">{{ $tarifa->precio }}Bs</strong>
                        </div>
                    @endif
                @endforeach

            </div>

        </div>
        <div class="col-12 col-md-5 mt-3">
            <div class="card">
                <div class="card-body">
                    <h3 class="card-title position-relative ">Importe::
                        <span class="badge bg-dark fs-12 p-1 rounded  position-absolute end-0 bottom-0">
                            {{ $puestos_registrado_usuario->nombre ?? 'Sin asignar' }}
                        </span>
                    </h3>
                    <h3 class="text-center ">
                        <span class="badge bg-warning fs-24" id="precio">

                        </span>
                    </h3>
                    <div class="row mt-1">
                        <div class="col-12 d-flex flex-wrap  m-auto justify-content-between ">
                            @can('control.generar.verificar')
                                <button type="button" class="btn btn-danger  d-inline-flex align-items-center   mb-2 m-auto"
                                    id="btn-verificarQr">
                                    <i class="far fa-question-circle fs-20 me-1"></i>Verificar QR
                                </button>
                            @endcan

                            @can('control.generar.generar')
                                <button type="button"
                                    class="btn btn-success px-2 d-inline-flex align-items-center mb-2  m-auto"
                                    id="btn-generarQr">
                                    <i class="fas fa-qrcode fs-20 me-1"></i>Generar QR
                                </button>
                            @endcan

                            @can('control.generar.llenar')
                                <button type="button" class="btn btn-primary  d-inline-flex align-items-center mb-2  m-auto"
                                    id="btn-llenar_informacion">
                                    <i class="fas fa-shipping-fast fs-20 me-1"></i>Llenar informacion
                                </button>
                            @endcan
                        </div>
                        <hr>
                        <label for="" class="">GENERAR BOLETAS:::</label>
                        <div class="row border border-success rounded p-2 mt-1 m-auto">
                            <form id="generar_boletas">


                                <div class="row">
                                    <div class="col-6">
                                        <label for="" class="form-label">Precio</label>

                                        <select name="id_precios" id="id_precios" class="form-control  rounded" require>
                                            <option disabled selected>Seleccionar</option>
                                            @foreach ($tarifas as $tarifa)
                                                @if ($tarifa->precio == 2 || $tarifa->precio == 4 || $tarifa->precio == 8)
                                                    <option class="text-capitalize " value="{{ $tarifa->id }}">
                                                        {{ $tarifa->precio }} {{ $tarifa->nombre }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>

                                    </div>

                                    <div class="col-3">
                                        <label for="" class="form-label">(2 - 20)</label>
                                        <input type="number" class="form-control rounded" name="cantidad" id="cantidad"
                                            min="2" max="20">

                                    </div>

                                    <div class="col-3 mt-2">

                                        <button type="submit" class="btn btn-info rounded btn-md" id="btn-generar_boleta">
                                            <i class="fas fa-print"></i>
                                            Generar</button>
                                    </div>


                                </div>

                            </form>

                            <div id="error_cantidad" class="text-danger mt-1"></div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-6 col-lg-7 mt-3 m-auto">
            <div class="card m-auto">

                <form id="nuevo_registro" style="display: none;">
                    <div class="row p-3">


                        <h5 class="mt-2">Datos Vehiculo:: </h5>
                        <div class="row m-auto border border-success rounded p-2">




                            <div class="form-group py-2 col-6 col-md-12">

                                <label for="" class="form-label">PLACA</label>
                                <div class="container-validation" id="group_usuarioReset">
                                    <input type="text" class="form-control rounded" name="placa" id="placa"
                                        style="text-transform: uppercase">
                                    <div id="_placa">

                                    </div>
                                </div>
                            </div>

                            <div class="form-group py-2 col-md-6">
                                <label for="" class="form-label">VEHICULO</label>


                                <select name="id_tipo_veh" id="id_tipo_veh" class="form-control  rounded" require>
                                    <option disabled selected>Opciones</option>
                                    @foreach ($vehiculos as $vehiculo)
                                        <option class="text-capitalize" value="{{ $vehiculo->id }}">
                                            {{ $vehiculo->nombre }}
                                        </option>
                                    @endforeach



                                </select>
                                <div id="_id_tipo_veh"></div>

                            </div>


                            <div class="form-group py-2 col-md-6">


                                <div class="form-group">
                                    <label for="" class="form-label">COLOR</label>
                                    <select id="id_color" name="id_color">
                                        <option disabled selected>Elige una opción</option>
                                        @foreach ($colores as $colore)
                                            <option value="{{ $colore->id }}"> {{ $colore->nombre }}</option>
                                        @endforeach

                                    </select>
                                </div>

                                <div id="_id_color"></div>

                            </div>

                        </div>


                        <h5 class="mt-1">Datos Cliente:: </h5>
                        <div class="row m-auto border border-primary rounded p-2">
                            <div class="form-group py-2 col-12 col-md-12">

                                <input type="hidden" name="id_tarifa" id="id_tarifa" value>
                                <label for="" class="form-label">DOCUMENTO DE IDENTIDAD</label>
                                <div class="container-validation" id="group_usuarioReset">
                                    <input type="text" class="form-control rounded" name="ci" id="ci">
                                    <div id="_ci">

                                    </div>
                                </div>
                            </div>


                            <div class="form-group py-2 col-6 col-md-4">

                                <label for="" class="form-label">NOMBRES</label>
                                <div class="container-validation" id="group_usuarioReset">
                                    <input type="text" class="form-control rounded" name="nombres" id="nombres">
                                    <div id="_nombres">

                                    </div>
                                </div>
                            </div>


                            <div class="form-group py-2 col-6 col-md-4">

                                <label for="" class="form-label">PATERNO</label>
                                <div class="container-validation" id="group_usuarioReset">
                                    <input type="text" class="form-control rounded" name="ap_paterno"
                                        id="ap_paterno">
                                    <div id="_ap_paterno">

                                    </div>
                                </div>
                            </div>

                            <div class="form-group py-2 col-6 col-md-4">

                                <label for="" class="form-label">MATERNO</label>
                                <div class="container-validation" id="group_usuarioReset">
                                    <input type="text" class="form-control rounded" name="ap_materno"
                                        id="ap_materno">
                                    <div id="_ap_materno">

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex mt-1">
                            <button type="button" class="btn btn-danger rounded btn-sm me-2"
                                id="btn-terminar_formulario">
                                <i class="ri-close-line  align-middle"></i> Terminar</button>
                            <button type="submit" class="btn btn-info rounded btn-sm" id="btn-nuevoRegistro"><i
                                    class="far fa-save me-1 align-middle"></i> Guardar</button>
                        </div>


                    </div>



                </form>

                <div id="pdf-container" style="width: 100%; height: 400px; margin-top: 20px;display:none">
                    <iframe id="pdf-iframe" style="display:block; width: 70%; height: 90%; margin:auto"></iframe>
                </div>

                <div id="verificarQr" class="row p-3" style="display: none;">
                    <label for="" class="form-labbel fs-18 text-center text-uppercase text-bold"><b>Escanee el qr
                            para verificar los datos....</b></label>
                    <input type="text" name="cod_qr" id="cod_qr" class="input_qr">
                    <span id="estado_qr" class=" text-primary fs-18 text-uppercase"></span>
                    <div class="contenido_respuesta" id="respuesta_servidor">

                    </div>
                </div>



                <div id="boletas_generadas" style=" display:none;" class="p-2">
                    <div
                        style="width: 100%; height: 320px; display: flex; justify-content: center; align-items: center; flex-direction: column;">
                        <h3 class="text-primary">Boletas en proceso de impresión</h3>
                        <p>El número total de boletas a imprimir. <b class="text-danger" id="numero_boletas"></b></p>
                        <h1 class="text-success display-1">
                            <span id="contadorBoletas">0</span>
                            <span class="fs-4">Boletas restantes por imprimir</span>
                        </h1>
                        <div class="progress w-50 mt-2" style="height: 20px;">
                            <div id="barraProgreso" class="progress-bar bg-success" style="width: 100%;">0%</div>
                        </div>
                        <p class="text-success mt-1 fs-4 text-center" id="correcto_impresion"></p>
                        <p class="text-info mt-1" id="nota_impresion"></p>
                    </div>

                </div>

            </div>
        </div>




    </div>

    <script>
        // Inicialización de Selectr
        document.addEventListener('DOMContentLoaded', function() {
            const selectElement = document.getElementById('id_color');
            const selectElement2 = document.getElementById('id_precios');
            new Selectr(selectElement, {
                searchable: true, // Activa la barra de búsqueda
                placeholder: 'Busca o selecciona una opción...' // Texto de placeholder
            });

            new Selectr(selectElement2, {
                searchable: true, // Activa la barra de búsqueda
                placeholder: 'Busca o selecciona una opción...' // Texto de placeholder
            });


        });
    </script>




@endsection
@section('scripts')
    <script src="{{ asset('js/peaje/generar_registro.js') }}" type="module"></script>
@endsection
