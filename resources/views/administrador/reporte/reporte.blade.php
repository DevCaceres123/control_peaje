@extends('principal')
@section('titulo', 'PERFIL')
@section('contenido')

    {{-- <div class="row">
        <div class="col-6">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-12 mb-2 text-center">
                            <h4 class="badge bg-success p-2 text-light fs-13">REPORTES DE PAGOS</h4>
                        </div>
                    </div>

                </div>
                <div class="card-body">
                    <form id="form-reportes">
                        <div class="row">
                            <div class="col-md-6 m-auto ">
                                <div class="mb-3 row">
                                    <div class="col-6">
                                        <label for="exampleInputEmail1" class="form-label">Fecha de Inicio: </label>
                                        <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio"
                                            value="{{ \Carbon\Carbon::now()->toDateString() }}" />

                                        <div id="_fecha_inicio">

                                        </div>

                                    </div>

                                    <div class="col-6">
                                        <label for="exampleInputEmail1" class="form-label">Fecha Final: </label>
                                        <input type="date" class="form-control" id="fecha_final" name="fecha_final"
                                            value="{{ \Carbon\Carbon::now()->toDateString() }}" />

                                        <div id="_fecha_final">

                                        </div>

                                    </div>

                                </div>
                                <div class="mb-3">

                                    <label for="exampleInputEmail1" class="form-label">Seleccionar encargado de puesto:
                                    </label>
                                    <select class="form-select" aria-label="Default select example " name="encargado"
                                        id="encargado">
                                        <option selected disabled>Seleccionar encargado</option>
                                        @foreach ($encargados_puesto as $item)
                                            <option value="{{ $item->id }}" class="text-capitalize">
                                                {{ $item->nombres }} {{ $item->apellidos }}</option>
                                        @endforeach
                                    </select>


                                    <div id="_encargado">

                                    </div>
                                </div>

                                <div>
                                    <button type="submit" class="btn btn-primary" id="btn-reporte">Generar Reporte</button>
                                </div>

                            </div>

                        </div>


                    </form>
                </div>
            </div>
        </div>
    </div> --}}

    <div class="row">
        <div class="col-12 col-md-6 col-lg-6">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-12 mb-2 text-center">
                            <h4 class="badge bg-success p-2 text-light fs-13">REPORTE POR FECHAS</h4>
                        </div>
                    </div>

                </div>
                <div class="card-body">
                    <form id="form-reportes">
                        <div class="row">
                            <div class="col-md-12 m-auto ">
                                <div class="mb-3 row">
                                    <div class="col-6">
                                        <label for="fecha_inicio" class="form-label">Fecha de Inicio: </label>
                                        <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio"
                                            value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" />
                                        <div id="_fecha_inicio"></div>
                                    </div>

                                    <div class="col-6">
                                        <label for="fecha_final" class="form-label">Fecha Final: </label>
                                        <input type="date" class="form-control" id="fecha_final" name="fecha_final"
                                            value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" />
                                        <div id="_fecha_final"></div>
                                    </div>

                                </div>
                                <div class="mb-3">

                                    <label for="exampleInputEmail1" class="form-label">Seleccionar Puesto:
                                    </label>
                                    <div class="row border border-secondary   border-2 rounded p-2 ">
                                        <!-- Checkbox para seleccionar todo -->
                                        <div class="col-md-12">
                                            <div class="form-check d-flex justify-content-center align-items-center">
                                                <label class="form-check-label me-4" for="select_all">Seleccionar
                                                    todo</label>
                                                <input class="form-check-input" type="checkbox" id="select_all"
                                                    style="border-color: #007bff">
                                            </div>
                                        </div>
                                        <!-- Checkbox para Listar Meses -->
                                        <div class="row" id="mesesPagados">
                                            @foreach ($puestos as $puesto)
                                                <div class="col-12 col-md-6">

                                                    <div
                                                        class="form-check d-flex justify-content-between align-items-center mt-2">
                                                        <label class="form-check-label me-3"
                                                            for="enero">{{ $puesto->nombre }}</label>
                                                        <input class="form-check-input" type="checkbox"
                                                            id="{{ $puesto->nombre }}" name="puestos[]"
                                                            value="{{ $puesto->id }}" style="border-color: #007bff">
                                                    </div>


                                                </div>
                                            @endforeach
                                        </div>

                                    </div>

                                    <div id="_encargado">

                                    </div>
                                </div>

                                <div class="col-12 m-auto">
                                    <button type="submit" class="btn btn-primary" id="btn-reporte">Generar Reporte</button>
                                </div>

                            </div>

                        </div>


                    </form>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-lg-6">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-12 mb-2 text-center">
                            <h4 class="badge bg-success p-2 text-light fs-13">REPORTE POR USUARIO</h4>
                        </div>
                    </div>

                </div>
                <div class="card-body">
                    <form id="form-reporte_usuario">
                        <div class="row">
                            <div class="col-md-12 m-auto ">
                                <div class="mb-3 row">
                                    <div class="col-12">
                                        <label for="fecha_inicio" class="form-label">Fecha de Inicio: </label>
                                        <input type="date" class="form-control" id="fecha_inicio_usuario" name="fecha_inicio_usuario"
                                            value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" />
                                        <div id="_fecha_inicio_usuario"></div>
                                    </div>
                                    <label for="exampleInputEmail1" class="form-label mt-1">Seleccionar encargado de
                                        puesto:
                                    </label>
                                    <div class="col-12 mt-1 border border-secondary   border-2 rounded p-2">

                                        <!-- Checkbox para seleccionar todo -->
                                        <div class="col-md-12">
                                            <div class="form-check d-flex justify-content-center align-items-center">
                                                <label class="form-check-label me-4" for="select_all">Seleccionar
                                                    todo</label>
                                                <input class="form-check-input" type="checkbox" id="select_all_user"
                                                    style="border-color: #007bff">
                                            </div>
                                        </div>
                                        <!-- Checkbox para Listar Meses -->
                                        <div class="row " id="mesesPagados">
                                            @foreach ($encargados_puesto as $encargados)
                                                <div class="col-12 col-md-6">
                                                    
                                                    <div
                                                        class="form-check d-flex justify-content-between align-items-center mt-2">
                                                        <label class="form-check-label me-3 text-uppercase"
                                                            for="enero">{{ $encargados->nombres }}
                                                            {{ $encargados->apellidos }}</label>
                                                        <input class="form-check-input" type="checkbox"
                                                            id="" name="encargados_puesto[]"
                                                            value="{{ $encargados->id}}" style="border-color: #007bff">
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div id="_encargado">

                                        </div>
                                    </div>

                                </div>


                                <div class="col-12 m-auto">
                                    <button type="submit" class="btn btn-primary" id="btn-reporte_usuario">Generar
                                        Reporte</button>
                                </div>

                            </div>

                        </div>


                    </form>
                </div>
            </div>
        </div>
    </div>



@endsection
@section('scripts')
    <script src="{{ asset('js/reporte/reporte.js') }}" type="module"></script>
@endsection
