@extends('principal')
@section('titulo', 'PERFIL')

@section('contenido')


    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-12 mb-2">
                            <h4 class="card-title">HISTORIAL DE PUESTOS</h4>
                        </div>

                    </div>
                    <div class="card-body">
                        <div class="row">

                            <div class="col-auto mb-3">
                                <label for="filterFecha" class="mb-2">Filtrar por fecha:</label>
                                <input type="date" id="filterFecha" class="form-control"
                                    value="{{ \Carbon\Carbon::now()->toDateString() }}" />
                            </div>


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
                            <div class="col-3 mt-3">
                                <button id="btnListarTodo" class="btn btn-success mt-1">
                                    <i class="fas fa-clipboard-list me-1"></i>Listar Todo</button>
                            </div>


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
