@extends('principal')
@section('titulo', 'PERFIL')

@section('contenido')


    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-12 mb-2">
                            <h4 class="card-title badge bg-success p-2 text-light fs-13">REGISTROS DEL DIA</h4>
                        </div>

                    </div>
                    <div class="card-body">
                        <div class="row position-relative" style="width: 100%; height: 80px;">
                            @can('control.listar.fechas')
                                <div class="col-auto mb-3">
                                    <label for="filterFecha" class="mb-2">Filtrar por fecha:</label>
                                    <input type="date" id="filterFecha" class="form-control"
                                        value="{{ \Carbon\Carbon::now()->toDateString() }}" />
                                </div>
                            @endcan

                            @can('control.listar.encargado')
                                <div class="col-auto mb-3">
                                    <label for="filterFecha" class="mb-2">Filtrar por encargados:</label>
                                    <select class="form-select" aria-label="Default select example " name="encargados"
                                        id="encargados">
                                        <option selected disabled>Encargados</option>
                                        @foreach ($encargados_puesto as $item)
                                            <option value="{{ $item->id }}" class="text-capitalize">
                                                {{ $item->nombres }} {{ $item->apellidos }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endcan

                            @can('control.listar.listar_todo')
                                <div class="col-3 mt-3">
                                    <button id="btnListarTodo" class="btn btn-success mt-1">
                                        <i class="fas fa-clipboard-list me-1"></i>Listar Todo</button>
                                </div>
                            @endcan

                            @can('control.listar.reporte_diario')
                                <div class="col-auto mt-3 position-absolute end-0 top-0">
                                    <a href="{{ route('peaje.reporte_diario') }}" id="reporte_diario"
                                        class="btn btn-primary mt-1" target="_blank">
                                        <i class="fas fa-file-pdf me-1"></i>Reporte Diario</a>
                                </div>
                            @endcan

                        </div>



                        <div class="table-responsive">
                            <table class="table" id="tabla_historialRegistro">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nº</th>
                                        <th>NOMBRE ENCARGADO</th>
                                        <th>PUESTO</th>
                                        <th>PRECIO </th>
                                        <th>PLACA</th>
                                        <th>CI </th>
                                        <th>FECHA REGISTRO</th>
                                        <th>CONTEO</th>
                                        <th>ACCION</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('scripts')
    <script src="{{ asset('js/peaje/listar_registro.js') }}" type="module"></script>
@endsection
