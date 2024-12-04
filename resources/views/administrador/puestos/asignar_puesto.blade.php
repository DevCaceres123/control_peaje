@extends('principal')
@section('titulo', 'PERFIL')
@section('contenido')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h4 class="card-title">PUESTOS DISPONIBLES</h4>
                    </div>
                    <div class="col-auto">
                       
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


    <div class="container">
        <div class="row">
            @foreach ($puestos as $puesto)
                {{-- inicio targeta --}}
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-header d-flex justify-content-between align-items-center bg-dark text-light">
                            @if (count($puesto->users) > 0)
                                <strong class="badge bg-primary fs-12">Activo</strong>
                            @else
                                <strong class="badge bg-danger fs-12">Inactivo</strong>
                            @endif

                            <strong class="text-uppercase">{{ $puesto->nombre }}</strong>

                        </div>
                        <div class="card-body text-center bg-light">
                            <img src="{{ asset('admin_template/images/peaje.webp') }}" alt="Icono puesto"
                                class="rounded mb-1 p-1" width="80">
                            <h5 class="card-title text-capitalize text-dark">
                                @if (count($puesto->users) > 0)
                                    @foreach ($puesto->users as $user)
                                        <p> {{ $user->nombres }} {{ $user->apellidos }}</p>
                                    @endforeach
                                @else
                                    <p>Sin asignar...</p>
                                @endif

                            </h5>
                            <p class="card-text text-muted text-bold"> Encargado del puesto</p>
                            <strong class="mb-2 text-success form-label text-capitalize"></strong>
                            <hr>
                            <!-- Botón para acciones -->
                            <div class="d-flex justify-content-center">
                                @if (count($puesto->users) == 0)
                                    <form class="form-asignar_encargado">

                                        <div class="d-flex">


                                            <input type="hidden" value="{{ $puesto->id }}" name="puesto_id"
                                                id="puesto_id">
                                            <select class="form-select" aria-label="Default select example "
                                                name="encargado" id="encargado">
                                                <option selected disabled>Encargados</option>
                                                @foreach ($encargados_sin_registro as $item)
                                                    <option value="{{ $item->id }}" class="text-capitalize">
                                                        {{ $item->nombres }} {{ $item->apellidos }}</option>
                                                @endforeach
                                            </select>
                                            <button type="submit" class="btn btn btn-primary ms-2"
                                                id="btn-asignar_encargado">
                                                <i class="fas fa-check-circle"></i>
                                            </button>
                                        </div>

                                        <div id="_encargado">

                                        </div>
                                    </form>
                                @endif


                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-between">
                            @if (count($puesto->users) > 0)
                                

                                <a class="btn btn-sm btn-danger terminar_reunion" data-id="{{$puesto->id}}">
                                    <i class="fas fa-window-close me-1"></i>Terminar
                                </a>
                            @endif


                        </div>
                    </div>
                </div>
            @endforeach
            <!-- Fin de la tarjeta -->
        </div>
    </div>

@endsection
@section('scripts')
    <script src="{{ asset('js/puesto/puesto.js') }}" type="module"></script>
@endsection
