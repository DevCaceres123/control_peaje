import { mensajeAlerta } from '../../../funciones_helper/notificaciones/mensajes.js';
import { crud } from '../../../funciones_helper/operaciones_crud/crud.js';
import { vaciar_errores, vaciar_formulario } from '../../../funciones_helper/vistas/formulario.js';



let permissions;
let tabla_historialPuesto;

$(document).ready(function () {

    listar_usuarios();
});

function listar_usuarios() {
    // Inicializa la tabla con DataTables
    tabla_historialPuesto = $('#tabla_historialPuesto').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            url: 'listar_historial', // Ruta que recibe la solicitud en el servidor
            type: 'GET', // Método de la solicitud (GET o POST)
            data: function (d) {
                d.fecha = $('#filterFecha').val(); // Agrega la fecha al request

            },
            dataSrc: function (json) {
                permissions = json.permissions; // Guardar los permisos si es necesario
                return json.usuarios; // Data que se pasará al DataTable
            }
        },
        columns: [
            {
                data: null,
                className: 'table-td',
                render: function (data, type, row, meta) {
                    // Calcula el índice global usando el start actual
                    let start = $('#tabla_historialPuesto').DataTable().page.info().start;
                    return start + meta.row + 1;
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
                data: 'nombres',
                className: 'table-td text-capitalize',
                render: function (data) {
                    return `${data}`;
                }
            },
            {
                data: 'puesto_nombre',
                className: 'table-td',
                render: function (data) {
                    return `
                  
                    <span class="badge bg-dark fs-6">${data}</span>
                    `;
                }
            },
            {
                data: 'fecha_asignado',
                className: 'table-td',
                render: function (data) {
                    return `${data}`;
                }
            },
        ],

    });

    // Permite filtrar por una fecha diferente
    $('#filterFecha').on('change', function () {
        tabla_historialPuesto.ajax.reload();
    });

    // Listar todos los registros al presionar el botón
    $('#btnListarTodo').on('click', function () {
        $('#filterFecha').val(''); // Limpia el valor del campo de fecha
        tabla_historialPuesto.ajax.reload(); // Recarga la tabla sin filtrar
    });
}



function actualizarTabla() {

    tabla_historialPuesto.ajax.reload(null, false); // Recarga los datos sin resetear el paginado
}