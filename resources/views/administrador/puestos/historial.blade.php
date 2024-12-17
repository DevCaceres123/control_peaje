@extends('principal')
@section('titulo', 'PERFIL')
@section('contenido')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-12 mb-2">
                            <h4 class="card-title badge bg-success p-2 text-light fs-13">HISTORIAL DE PUESTOS</h4>
                        </div>

                    </div>
                    <div class="card-body">
                        <div class="row">
                            <label for="filterFecha" class="mb-2">Filtrar por fecha:</label>
                            <div class="col-auto ">

                                <input type="date" id="filterFecha" class="form-control"
                                    value="{{ \Carbon\Carbon::now()->toDateString() }}" />
                            </div>
                            <div class="col-3 mb-2">
                                <button id="btnListarTodo" class="btn btn-success ">
                                    <i class="fas fa-clipboard-list me-1"></i>Listar Todo</button>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table" id="tabla_historialPuesto">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nº</th>
                                        <th>CI</th>
                                        <th>NOMBRE COMPLETO</th>
                                        <th>PUESTO</th>
                                        <th>FECHA ASIGNADA</th>
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
    <script src="{{ asset('js/puesto/historialPuestos.js') }}" type="module"></script>
@endsection
