@extends('principal')
@section('titulo', 'TARIFA')
@section('contenido')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h4 class="card-title badge bg-success p-2 text-light fs-13">TARIFAS</h4>
                    </div>
                    <div class="col-auto">
                        @can('configuracion.tarifa.inicio')
                        <button class="btn btn-primary" onclick="abrirModalTarifa()">
                            <i class="fas fa-plus me-1"></i> Nuevo
                        </button>
                        @endcan
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table" id="tabla_tarifa">
                        <thead class="table-light">
                            <tr>
                                <th>Nº</th>
                                <th>NOMBRE</th>
                                <th>PRECIO</th>
                                <th>DESCRIPCION</th>
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
<div class="modal fade" id="modal_tarifa" data-bs-backdrop="static" tabindex="-1" role="dialog"
    aria-labelledby="tarifaModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title m-0" id="tarifaModalLabel">Tarifa</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                    onclick="cerrarModelTarifa()"></button>
            </div>
            <div class="modal-body">
                <form id="form_tarifa" autocomplete="off" method="POST">
                    <input type="hidden" id="tarifa_id" name="tarifa_id">
                    <div class="mb-3 row">
                        <label for="nombre" class="col-sm-2 col-form-label">Nombre</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control uppercase-input" id="nombre" name="nombre"
                                placeholder="Ingrese el nombre de la tarifa" required>
                            <div id="_nombre"></div>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="precio" class="col-sm-2 col-form-label">Precio</label>
                        <div class="col-sm-10">
                            <input type="double" class="form-control" id="precio" name="precio"
                                placeholder="Ingrese el precio C-Nº">
                            <div id="_precio"></div>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="precio" class="col-sm-2 col-form-label uppercase-input">Descripción</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" id="descripcion" name="descripcion" cols="30" rows="3" placeholder="Ingrese la descripcion" required></textarea>
                            <div id="_descripcion"></div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal"
                    onclick="cerrarModelTarifa()">Cerrar</button>
                <button type="button" id="btn_guardar_tarifa" class="btn btn-dark btn-sm">Guardar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let rutas = {
        listar      : "{{ route('tarifas.listar') }}",
        crear       : "{{ route('tarifas.store') }}",
        editar      : "{{ route('tarifas.edit', ':id') }}",
        actualizar: "{{ route('tarifas.update', ':id') }}",
        eliminar: "{{ route('tarifas.destroy', ':id') }}",
        cambiarEstado: "{{ route('tarifas.show', ':id') }}"
    };
</script>
<script src="{{ asset('js/configuracion/tarifas.js') }}"></script>
@endsection