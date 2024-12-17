@extends('principal')
@section('titulo', 'PERFIL')
@section('contenido')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-12 mb-2">
                            <h4 class="card-title badge bg-success p-2 text-light fs-13">REPORTES DE PAGOS</h4>
                        </div>

                    </div>

                </div>
                <div class="card-body">
                    <form id="form-reportes">
                        <div class="row">
                            <div class="col-md-6 m-auto ">
                                <div class="mb-3 ">
                                    <label for="exampleInputEmail1" class="form-label">Seleccionar fecha: </label>
                                    <input type="date" class="form-control" id="fecha" name="fecha"
                                        value="{{ \Carbon\Carbon::now()->toDateString() }}" />

                                    <div id="_fecha">

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
    </div>



@endsection
@section('scripts')
    <script src="{{ asset('js/reporte/reporte.js') }}" type="module"></script>
@endsection
