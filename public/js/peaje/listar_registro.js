import { mensajeAlerta } from '../../../funciones_helper/notificaciones/mensajes.js';
import { crud } from '../../../funciones_helper/operaciones_crud/crud.js';
import { vaciar_errores, vaciar_formulario } from '../../../funciones_helper/vistas/formulario.js';




let permissions;
let tabla_historialRegistro;
let valorSeleccionado;
$(document).ready(function () {

    listar_registros();
});


function listar_registros() {
    // Inicializa la tabla con DataTables
    tabla_historialRegistro = $('#tabla_historialRegistro').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            url: 'listar_registro', // Ruta que recibe la solicitud en el servidor
            type: 'GET', // Método de la solicitud (GET o POST)
            data: function (d) {
                d.fecha = $('#filterFecha').val(); // Agrega la fecha al request
                d.encargado = valorSeleccionado;

            },
            dataSrc: function (json) {
                permissions = json.permissions; // Guardar los permisos si es necesario
                return json.registros; // Data que se pasará al DataTable
            }
        },
        columns: [
            {
                data: null,
                className: 'table-td',
                render: function (data, type, row, meta) {
                    // Calcula el índice global usando el start actual
                    let start = $('#tabla_historialRegistro').DataTable().page.info().start;
                    return start + meta.row + 1;
                }
            },
            {
                data: 'nombre_usuario',
                className: 'table-td text-capitalize',
                render: function (data) {
                    return `${data}`;
                }
            },
            {
                data: 'puesto',
                className: 'table-td',
                render: function (data) {
                    return `
                  
                    <span class="badge bg-dark fs-6">${data}</span>
                    `;
                }
            },

            {
                data: 'precio',
                className: 'table-td',
                render: function (data) {
                    return `${data} <b class='text-muted'>Bs</b>`;
                }
            },
            {
                data: 'placa',
                className: 'table-td text-uppercase',
                render: function (data) {
                    if (data != null) {
                        return `
                  
                        <span class="badge bg-success fs-6">${data}</span>
                        `;

                    }
                    else {
                        return `
                  
                        <span class="badge bg-danger fs-6">NINGUNO...</span>
                        `;

                    }

                }
            },

            {
                data: 'ci',
                className: 'table-td',
                render: function (data) {
                    if (data != null) {
                        return `
                  
                        <span class="badge bg-success fs-6">${data}</span>
                        `;

                    }
                    else {
                        return `
                  
                        <span class="badge bg-danger fs-6">NINGUNO...</span>
                        `;

                    }

                }
            },

            {
                data: 'created_at',
                className: 'table-td',
                render: function (data) {
                    return `${data}`;
                }
            },

            {
                data: 'num_aprobados',
                className: 'table-td',
                render: function (data) {
                    if (data != null) {
                        return `
                  
                        <span class="badge bg-success fs-6">${data}</span>
                        `;

                    }
                    else {
                        return `
                  
                        <span class="badge bg-success fs-6">0</span>
                        `;

                    }
                }
            },

            {
                data: null,
                className: 'table-td',
                render: function (data, type, row) {
                    return `

                    <div class="d-flex justify-content-center">

                   ${permissions['eliminar'] ?
                            ` <a  class="btn btn-sm btn-outline-danger px-2 d-inline-flex align-items-center eliminar_registro" data-id="${row.id}">
                      <i class="fas fa-window-close fs-16"></i>
                  </a>`
                            : ``}

                        
                    
                 
                 </div> `;
                }
            },

        ],

    });

    // Permite filtrar por una fecha diferente
    $('#filterFecha').on('change', function () {

        tabla_historialRegistro.ajax.reload();
    });

    // Listar todos los registros al presionar el botón
    $('#btnListarTodo').on('click', function () {
        $('#filterFecha').val(''); // Limpia el valor del campo de fecha
        $('#encargados').val(''); // Limpia el valor del campo de fecha
        tabla_historialRegistro.ajax.reload(); // Recarga la tabla sin filtrar
    });

    // Escuchar el evento change en el select
    $('#encargados').on('change', function () {
        // Obtener el valor seleccionado
        valorSeleccionado = $(this).val(); // Obtiene el valor (attribute value)
        tabla_historialRegistro.ajax.reload();
        // Mostrar en la consola los valores obtenidos

    });
}

function actualizarTabla() {

    tabla_historialRegistro.ajax.reload(null, false); // Recarga los datos sin resetear el paginado
}





// ELIMINAR REGISTRO

$('#tabla_historialRegistro').on('click', '.eliminar_registro', function (e) {

    e.preventDefault(); // Evitar que el enlace recargue la página
    let id_registro = $(this).data('id'); // Obtener el id 


    Swal.fire({
        title: "NOTA!",
        text: "¿Está seguro de eliminar el registro?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Sí, Estoy seguro",
        cancelButtonText: "Cancelar",
    }).then(async function (result) {
        if (result.isConfirmed) {

            crud("admin/peaje", "DELETE", id_registro, null, function (error, response) {

                console.log(response);
                // Verificamos que no haya un error o que todos los campos sean llenados
                if (response.tipo === "errores") {
                    mensajeAlerta(response.mensaje, "errores");
                    return;
                }
                if (response.tipo != "exito") {
                    mensajeAlerta(response.mensaje, response.tipo);
                    return;
                }
                // si todo esta correcto muestra el mensaje de correcto
                mensajeAlerta(response.mensaje, response.tipo);
                actualizarTabla();

            })
        } else {
            alerta_top('error', 'Se canceló la operacion');
        }
    })
});