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



     <!-- Modal para nuevo y editar -->
<div class="modal fade" id="modalAsignarPuesto" data-bs-backdrop="static" tabindex="-1" role="dialog"
aria-labelledby="puestoModalLabel" aria-hidden="true">
<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h6 class="modal-title m-0" id="puestoModalLabel">Seleccionar Puesto</h6>
        </div>
        <div class="modal-body">
            <div class="row">
                @foreach ($puestos_usuario as $puesto)
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card shadow-sm">
                            <div class="card-header d-flex justify-content-center align-items-center bg-dark text-light">
                                <strong class="text-uppercase">{{ $puesto->nombre }}</strong>
                            </div>
                            <div class="card-body text-center bg-light">
                                <h5 class="card-title text-capitalize text-dark">
                                    @if (count($puesto->users) > 0)
                                        @foreach ($puesto->users as $user)
                                            <p class="text-success"> {{ $user->nombres }} {{ $user->apellidos }}
                                            </p>
                                        @endforeach
                                    @else
                                        <p>Sin asignar...</p>
                                    @endif
                                </h5>
                                <p class="card-text text-muted text-bold"> Encargado del puesto</p>
                                <hr>
                                <!-- Botón para enviar el ID directamente -->
                                <div class="d-flex justify-content-center">
                                    @if (count($puesto->users) == 0)
                                        <a href="{{ route('asignar.puesto',$puesto->id) }}"
                                            class="btn btn-primary ms-2">
                                            Registrar <i class="fas fa-check-circle ms-2"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
</div>



    </div>

    <script>
        @if (auth()->user()->hasRole('encargado_puesto'))
            (async function() {
                const data = await PuestoUsuario();

                if (data.titulo === 'error') {
                    const modal = new bootstrap.Modal(document.getElementById('modalAsignarPuesto'));
                    modal.show();
                }

            })();
        @endif


        //verificar si el usuario tiene un puesto asignado
        async function PuestoUsuario() {
            try {
                const response = await fetch(`puesto_usuario`, {
                    method: 'GET',
                    headers: {
                        "Content-Type": "application/json",
                    },
                });

                if (!response.ok) {
                    throw new Error(`Error en la solicitud: ${response.status}`);
                }

                // Convertir la respuesta a JSON
                const data = await response.json();

                // Devolver los datos
                return data;
            } catch (error) {
                console.error("Error al obtener los datos:", error);
            }
        }



        // Lista todos los puestos con su respectivo usuario
        async function listarPuestoUsuario() {
            try {
                const response = await fetch(`puesto_usuario`, {
                    method: 'GET',
                    headers: {
                        "Content-Type": "application/json",
                    },
                });

                if (!response.ok) {
                    throw new Error(`Error en la solicitud: ${response.status}`);
                }

                // Convertir la respuesta a JSON
                const data = await response.json();

                // Devolver los datos
                return data;
            } catch (error) {
                console.error("Error al obtener los datos:", error);
            }
        }


        // Esperar a que el DOM esté completamente cargado
        document.addEventListener('DOMContentLoaded', function() {
            // Escuchar el evento click en el contenedor de la tabla
            document.getElementById('form-asignar_encargado_puesto').addEventListener('click', async function(e) {
                // Verificar si el elemento clicado tiene la clase 'asignar_puesto'
                if (e.target && e.target.classList.contains('asignar_puesto')) {
                    e.preventDefault(); // Evitar comportamiento por defecto

                    const id_registro = e.target.dataset.id; // Obtener el id del dataset

                    alert(id_registro);
                    // Mostrar el cuadro de confirmación
                    const result = await Swal.fire({
                        title: "NOTA!",
                        text: "¿Está seguro de eliminar el registro?",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Sí, Estoy seguro",
                        cancelButtonText: "Cancelar",
                    });

                    // Si se confirma la acción
                    if (result.isConfirmed) {
                        try {
                            // Llamar a la función crud para eliminar el registro
                            const response = await crud("admin/peaje", "DELETE", id_registro);

                            // Verificar el tipo de respuesta
                            if (response.tipo === "errores") {
                                mensajeAlerta(response.mensaje, "errores");
                                return;
                            }
                            if (response.tipo !== "exito") {
                                mensajeAlerta(response.mensaje, response.tipo);
                                return;
                            }

                            // Todo está correcto, mostrar el mensaje de éxito y actualizar la tabla
                            mensajeAlerta(response.mensaje, response.tipo);
                            actualizarTabla();
                        } catch (error) {
                            console.error("Error al eliminar el registro:", error);
                            alerta_top("error", "Hubo un problema al procesar la solicitud.");
                        }
                    } else {
                        alerta_top('error', 'Se canceló la operación');
                    }
                }
            });
        });

        // Función CRUD simulada (puedes reemplazarla con tu implementación real)
        async function crud(url, method, id, data = null) {
            const options = {
                method: method,
                headers: {
                    "Content-Type": "application/json",
                },
            };
            if (data) {
                options.body = JSON.stringify(data);
            }

            const response = await fetch(`${url}/${id}`, options);
            if (!response.ok) {
                throw new Error(`Error en la solicitud: ${response.status}`);
            }
            return await response.json();
        }

        // Función de ejemplo para mostrar alertas
        function mensajeAlerta(mensaje, tipo) {
            Swal.fire({
                title: tipo === "exito" ? "Éxito" : "Error",
                text: mensaje,
                icon: tipo === "exito" ? "success" : "error",
            });
        }

        // Función para actualizar la tabla (reemplaza con tu implementación real)
        function actualizarTabla() {
            console.log("Tabla actualizada");
        }
    </script>

@endsection
