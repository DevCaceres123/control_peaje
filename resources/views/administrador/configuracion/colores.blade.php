@extends('principal')
@section('titulo', 'PUESTO')
@section('contenido')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title badge bg-success p-2 text-light fs-13">COLORES</h4>
                        </div>
                        <div class="col-auto">
                            @can('configuracion.color.crear')
                                <button class="btn btn-primary" onclick="abrirModalColor()">
                                    <i class="fas fa-plus me-1"></i> Nuevo
                                </button>
                            @endcan
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="tabla_color">
                            <thead class="table-light">
                                <tr>
                                    <th>Nº</th>
                                    <th>NOMBRE</th>
                                    <th>COLOR</th>
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
    <div class="modal fade" id="modal_color" data-bs-backdrop="static" tabindex="-1" role="dialog"
        aria-labelledby="colorModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title m-0" id="colorModalLabel">Color</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        onclick="cerrarModelPuesto()"></button>
                </div>
                <div class="modal-body">
                    <form id="form_color" autocomplete="off" method="POST">
                        <input type="hidden" id="color_id" name="color_id">
                        <div class="mb-3 row">
                            <label for="nombre" class="col-sm-2 col-form-label text-end">Nombre</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control uppercase-input" id="nombre" name="nombre">
                            </div>
                            <div id="_nombre"></div>
                        </div>
                        <div class="mb-3 row">
                            <label for="color" class="col-sm-2 col-form-label text-end">Color</label>
                            <div class="col-sm-10">
                                <input type="color" class="form-control" id="color" name="color">
                            </div>
                            <div id="_color"></div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal"
                        onclick="cerrarModelColor()">Cerrar</button>
                    <button type="button" id="btn_guardar_color" class="btn btn-dark btn-sm">Guardar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        let rutas = {
            listar: "{{ route('color.listar') }}",
            crear: "{{ route('color.store') }}",
            editar: "{{ route('color.edit', ':id') }}",
            actualizar: "{{ route('color.update', ':id') }}",
            eliminar: "{{ route('color.destroy', ':id') }}",
            cambiarEstado: "{{ route('color.show', ':id') }}"
        };
    </script>
    <script src="{{ asset('js/configuracion/colores.js') }}"></script>
@endsection
