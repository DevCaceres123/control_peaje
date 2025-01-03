@extends('principal')
@section('titulo', 'INICIO')
@section('contenido')

    <div class="row justify-content-center">
        @can('inicio.estadistica')
            @foreach ($puestos_usuario as $puesto)
                <div class="col-md-6 col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="row d-flex justify-content-center border-dashed-bottom pb-3">
                                <div class="col-9">
                                    <p class="b-0 fw-semibold fs-13 badge bg-primary p-1 text-light">{{ $puesto->nombre }}</p>
                                    <h5 class="mt-2 mb-0 fw-bold text-capitalize">{{ $fecha_parseada }}</h5>

                                    @if (!$puesto->users->isEmpty())
                                        @foreach ($puesto->users as $user)
                                            <h5 class="mt-2 mb-0 text-muted text-capitalize">{{ $user->nombres }}
                                                {{ $user->apellidos }}</h5>
                                        @endforeach
                                    @else
                                        <h5 class="mt-2 mb-0 text-muted text-capitalize">Sin asignar...</h5>
                                    @endif



                                </div>
                                <!--end col-->
                                <div class="col-3 align-self-center">
                                    <div
                                        class="d-flex justify-content-center align-items-center thumb-xxl bg-dark rounded-circle mx-auto">
                                        @foreach ($monto_puesto as $monto)
                                            @if ($puesto->nombre == $monto['puesto'])
                                                <span class="fs-14 text-light">{{ $monto['total_precio'] }} <b>Bs</b></span>
                                            @endif
                                        @endforeach


                                    </div>
                                </div>
                                <!--end col-->
                            </div>
                            <!--end row-->

                            <p class="mb-0 text-truncate text-bold mt-3 text-light mt-3 bg-dark p-2 rounded ">Registros:
                                @foreach ($monto_puesto as $monto)
                                    @if ($puesto->nombre == $monto['puesto'])
                                        <span class="text-warning">{{ $monto['total_registros'] }} boletas generadas</span>
                                    @endif
                                @endforeach
                            </p>


                        </div>
                        <!--end card-body-->
                    </div>
                    <!--end card-->
                </div>
            @endforeach
        @endcan


    </div>
@endsection
