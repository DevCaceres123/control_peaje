@extends('principal')
@section('titulo', 'PERFIL')

@section('contenido')
    <link rel="stylesheet" href="{{ asset('css/precios.css') }}">
    <div class="row">
        <div class="col-sm-12 col-6 col-lg-12">
            <div class="contenedor_precio">
                @foreach ($tarifas as $tarifa)
                    <div class="card_precio " data-id="{{ $tarifa->id }}">

                        <p>{{ $tarifa->nombre }}</p>
                        <div class="icon"><i class="fas fa-truck fs-24"></i></div>
                        <strong>{{ $tarifa->precio }}Bs</strong>
                    </div>
                @endforeach

            </div>

        </div>
        <div class="col-sm-12 col-6 col-lg-5 mt-3">
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
                    <div class="row">
                        <div class="col-12 d-flex  m-auto justify-content-between mt-1">
                            <button type="button" class="btn btn-success px-2 d-inline-flex align-items-center mb-2"
                                id="btn-generarQr">
                                <i class="fas fa-qrcode fs-20 me-1"></i>Generar QR
                            </button>
                            <button type="button" class="btn btn-primary px-2 d-inline-flex align-items-center mb-2"
                                id="btn-llenar_informacion">
                                <i class="fas fa-shipping-fast fs-20 me-1"></i>Llenar informacion
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-6 col-lg-7 mt-3 m-auto">
            <div class="card m-auto">

                <form id="nuevo_registro" style="display: none;">
                    <div class="row p-3">
                        <h4 class="text-center ">REGISTRO DE VEHICULO</h4>
                        <div class="form-group py-2 col-12 col-md-6">

                            <input type="hidden" name="id_tarifa" id="id_tarifa" value>
                            <label for="" class="form-label">DOCUMENTO DE IDENTIDAD</label>
                            <div class="container-validation" id="group_usuarioReset">
                                <input type="text" class="form-control rounded" name="ci" id="ci">
                                <div id="_ci">

                                </div>
                            </div>
                        </div>

                        <div class="form-group py-2 col-6 col-md-3">

                            <label for="" class="form-label">PATERNO</label>
                            <div class="container-validation" id="group_usuarioReset">
                                <input type="text" class="form-control rounded" name="ap_paterno" id="ap_paterno">
                                <div id="_ap_paterno">

                                </div>
                            </div>
                        </div>

                        <div class="form-group py-2 col-6 col-md-3">

                            <label for="" class="form-label">MATERNO</label>
                            <div class="container-validation" id="group_usuarioReset">
                                <input type="text" class="form-control rounded" name="ap_materno" id="ap_materno">
                                <div id="_ap_materno">

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


                        <div class="form-group py-2 col-md-3">
                            <label for="" class="form-label">VEHICULO</label>


                            <select name="id_tipo_veh" id="id_tipo_veh" class="form-control  rounded" require>
                                <option disabled selected>Opciones</option>
                                @foreach ($vehiculos as $vehiculo)
                                    <option class="text-capitalize" value="{{ $vehiculo->id }}">{{ $vehiculo->nombre }}
                                    </option>
                                @endforeach



                            </select>
                            <div id="_id_tipo_veh"></div>

                        </div>


                        <div class="form-group py-2 col-md-5">


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


                        <div class="form-group py-2 col-6 col-md-12">

                            <label for="" class="form-label">PLACA</label>
                            <div class="container-validation" id="group_usuarioReset">
                                <input type="text" class="form-control rounded" name="placa" id="placa"
                                    style="text-transform: uppercase">
                                <div id="_placa">

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

                <div id="pdf-container" style="width: 100%; height: 500px; margin-top: 20px;display:none">
                    <iframe id="pdf-iframe" style="display:block; width: 70%; height: 80%; margin:auto"></iframe>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Inicialización de Selectr
        document.addEventListener('DOMContentLoaded', function() {
            const selectElement = document.getElementById('id_color');
            new Selectr(selectElement, {
                searchable: true, // Activa la barra de búsqueda
                placeholder: 'Busca o selecciona una opción...' // Texto de placeholder
            });
        });
    </script>




@endsection
@section('scripts')
    <script src="{{ asset('js/peaje/generar_registro.js') }}" type="module"></script>
@endsection
