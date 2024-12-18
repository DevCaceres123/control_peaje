@extends('principal')
@section('titulo', 'PUESTO')
@section('contenido')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title badge bg-success p-2 text-light fs-13">PUESTOS</h4>
                        </div>
                        <div class="col-auto">
                            @can('configuracion.puesto.crear')
                                <button class="btn btn-primary" onclick="abrirModalPuesto()">
                                    <i class="fas fa-plus me-1"></i> Nuevo
                                </button>
                            @endcan
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="tabla_puesto">
                            <thead class="table-light">
                                <tr>
                                    <th>Nº</th>
                                    <th>NOMBRE</th>
                                    <th>ESTADO</th>
                                    <th>ACCION</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para nuevo y editar -->
    <div class="modal fade" id="modal_puesto" data-bs-backdrop="static" tabindex="-1" role="dialog"
        aria-labelledby="puestoModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title m-0" id="puestoModalLabel">Puesto</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        onclick="cerrarModelPuesto()"></button>
                </div>
                <div class="modal-body">
                    <form id="form_puesto" autocomplete="off" method="POST">
                        <input type="hidden" id="puesto_id" name="puesto_id">
                        <div class="mb-3 row">
                            <label for="nombre" class="col-sm-2 col-form-label">Puesto</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control uppercase-input" id="nombre" name="nombre"
                                    placeholder="Ingrese el nombre del puesto">
                                <div id="_nombre"></div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal"
                        onclick="cerrarModelPuesto()">Cerrar</button>
                    <button type="button" id="btn_guardar_puesto" class="btn btn-dark btn-sm">Guardar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        let rutas = {
            listar: "{{ route('puesto.listar') }}",
            crear: "{{ route('puesto.store') }}",
            editar: "{{ route('puesto.edit', ':id') }}",
            actualizar: "{{ route('puesto.update', ':id') }}",
            eliminar: "{{ route('puesto.destroy', ':id') }}",
            cambiarEstado: "{{ route('puesto.show', ':id') }}"
        };
    </script>
    <script src="{{ asset('js/configuracion/puestos.js') }}"></script>
@endsection
