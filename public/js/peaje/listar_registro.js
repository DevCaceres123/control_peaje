import { mensajeAlerta } from '../../../funciones_helper/notificaciones/mensajes.js';
import { crud } from '../../../funciones_helper/operaciones_crud/crud.js';
import { vaciar_errores, vaciar_formulario } from '../../../funciones_helper/vistas/formulario.js';




let permissions;
let tabla_historialRegistro;

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
                // d.fecha = $('#filterFecha').val(); // Agrega la fecha al request

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
                className: 'table-td',
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
                    return `${data}`;
                }
            },
            {
                data: 'placa',
                className: 'table-td',
                render: function (data) {
                    return `${data}`;
                }
            },

            {
                data: 'ci',
                className: 'table-td',
                render: function (data) {
                    return `${data}`;
                }
            },

            {
                data: 'created_at',
                className: 'table-td',
                render: function (data) {
                    return `${data}`;
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
        tabla_historialRegistro.ajax.reload(); // Recarga la tabla sin filtrar
    });
}