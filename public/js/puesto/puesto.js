import { mensajeAlerta } from '../../../funciones_helper/notificaciones/mensajes.js';
import { crud } from '../../../funciones_helper/operaciones_crud/crud.js';
import { vaciar_errores, vaciar_formulario } from '../../../funciones_helper/vistas/formulario.js';





let permissions;
let table_user;


// BUSCAR ENCARGADO POR CI
$('#ci_encargado').keyup(function () {
    let ci_encargado = $(this).val(); // Captura el valor actual del campo de entrada
    
    // Encuentra el contenedor más cercano que tiene la clase 'card-body'
    var cardBody = this.closest('.card-body');
    
    // Dentro de ese contenedor, encuentra el <strong> por su selector
    var strongElement = cardBody.querySelector('strong');

    
    
    
    
    

    if (ci_encargado.length < 6) {
        strongElement.textContent = "...."; 
        $("#btn-asignar_encargado").prop("disabled", true);
        return;
    }

    crud("admin/buscar_encargado", "GET", ci_encargado, null, function (error, response) {

        // console.log(response);
        // Verificamos que no haya un error o que todos los campos sean llenados
        if (response.tipo === "errores") {

            mensajeAlerta(response.mensaje, "errores");
            return;
        }
        if (response.tipo != "exito") {
            strongElement.textContent="No encontrado...";
            $("#btn-asignar_encargado").prop("disabled", true);

            return;
        }

        let nombre_completo = response.mensaje.nombres +" "+ response.mensaje.apellidos;
        strongElement.textContent=nombre_completo;
        $("#btn-asignar_encargado").prop("disabled", false);
        $("#user_id").val(response.mensaje.id);
    })
});


// function listar_usuarios() {

//     table_user = $('#table_user').DataTable({
//         processing: true,
//         serverSide: true,
//         responsive: true,
//         ajax: {
//             url: 'listarUsuarios', // Ruta que recibe la solicitud en el servidor
//             type: 'GET', // Método de la solicitud (GET o POST)
//             dataSrc: function (json) {

//                 permissions = json.permissions;
//                 // console.log(permisosGlobal); // Guardar los permisos para usarlos en las columnas
//                 return json.usuarios; // Data que se pasará al DataTable
//             }
//         },
//         columns: [
//             {
//                 data: null,
//                 className: 'table-td',
//                 render: function (data, type, row, meta) {
//                     // Calcula el índice global usando el start actual
//                     let start = $('#table_user').DataTable().page.info().start;
//                     return start + meta.row + 1;
//                 }
//             },
//             {
//                 data: 'nombres',
//                 className: 'table-td',
//                 render: function (data) {
//                     return `

//                         ${data}
//                     `;
//                 }
//             },
//             {
//                 data: 'paterno',
//                 className: 'table-td',
//                 render: function (data) {
//                     return data;
//                 }
//             },
//             {
//                 data: 'materno',
//                 className: 'table-td',
//                 render: function (data) {
//                     return data;
//                 }
//             },
//             {
//                 data: null,
//                 className: 'table-td',
//                 render: function (data, type, row) {

//                     return `<b class="text-muted">${row.ci}</b>`;

//                 }
//             },
//             {
//                 data: 'roles',
//                 render: function (data) {
//                     if (data.length != 0) {
//                         return data.map(role =>
//                             `<span class="badge bg-success fs-5">${role.name}</span>`
//                         ).join(' ');
//                     } else {
//                         return `<span class="badge bg-success fs-5">Sin roles asignados</span>`;
//                     }
//                 }
//             },
//             {
//                 data: null,
//                 render: function (data, type, row) {
//                     let estadoChecked = row.estado === "activo" ? 'checked' : '';

//                     // Aquí verificamos el permiso de desactivar
//                     let desactivarContent = permissions['desactivar'] ? `
//                         <a class="cambiar_estado_usuario" data-id="${row.id},${row.estado}">
//                             <div class="form-check form-switch ms-3">
//                                 <input class="form-check-input" type="checkbox" 
//                                        ${estadoChecked} style="transform: scale(2.0);">
//                             </div>
//                         </a>` : `
//                        <p>No permitido...<p/>
//                     `;

//                     return `
//                         <div data-class="">
//                             ${desactivarContent}
//                         </div>`;
//                 }
//             },

//             {
//                 data: 'cod_targeta',
//                 render: function (data) {
//                     return data == null
//                         ? `<span class="badge bg-danger fs-5">Sin asignar</span>`
//                         : `<span class="badge bg-success fs-5">${data}</span>`;
//                 }
//             },
//             {
//                 data: null,
//                 className: 'table-td',
//                 render: function (data, type, row) {
//                     return `
//                         <div class="text-end">
//                             <td>
//                                 <div class="d-flex justify-content-between">
//                                     ${permissions['reset'] ? `
//                                         <a class="btn btn-sm btn-outline-info px-2 d-inline-flex align-items-center resetear_usuario" data-id="${row.id}">
//                                             <i class="fas fa-redo fs-16"></i>
//                                         </a>
//                                     ` : ''}

//                                     ${permissions['editarRol'] ? `
//                                         <a class="btn btn-sm btn-outline-primary px-2 d-inline-flex align-items-center cambiar_rol" data-id="${row.id}">
//                                             <i class="far fa-edit fs-16"></i>
//                                         </a>
//                                     ` : ''}

//                                     ${permissions['editarTargeta'] && row.roles[0].name === "estudiante" ? `
//                                         <a class="btn btn-sm btn-outline-warning px-2 d-inline-flex align-items-center asignar_targeta" data-id="${row.id}">
//                                             <i class="fas fa-id-card fs-16"></i>
//                                         </a>
//                                     ` : ''}
//                                 </div>   
//                             </td>
//                         </div>
//                     `;
//                 }
//             }

//         ],
//     });
// }


// function actualizarTabla() {

//     table_user.ajax.reload(null, false); // Recarga los datos sin resetear el paginado
// }

// REGISTRAR  USUARIO
$('#form-asignar_encargado').submit(function (e) {

    e.preventDefault();
    $("#btn-asignar_encargado").prop("disabled", true);
   
 
   
    let datosFormulario = $('#form-asignar_encargado').serialize();

    crud("admin/puesto_asignar", "POST", null, datosFormulario, function (error, response) {
      
        //console.log(response);
        // if (error != null) {
        //     mensajeAlerta(error, "error");
        //     return;
        // }

        console.log(response);
        if (response.tipo === "errores") {

            mensajeAlerta(response.mensaje, "errores");
         
            return;
        }
        if (response.tipo != "exito") {
            mensajeAlerta(response.mensaje, response.tipo);
            $("#btn-asignar_encargado").prop("disabled", false);
            return;
        }
        
        mensajeAlerta(response.mensaje, response.tipo);
        $("#btn-asignar_encargado").prop("disabled", false);
        setTimeout(() => {
            location.reload();
        }, 1500);


    });

});




// Agregar el evento de clic después de que la tabla haya sido creada
$(document).on('click', '.terminar_reunion', function () {
   

    Swal.fire({
        title: '¿Eliminar Registro?',
        text: "Estas seguro que quiere eliminar el registro!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si,estoy seguro!'
    }).then((result) => {
        if (result.isConfirmed) {
            let id_puesto = $(this).data('id'); // Obtener el id del alumno desde el data-id
            // console.log(id_alumno);
            crud("admin/puesto_asignar", "DELETE", id_puesto, null, function (error, response) {

                console.log(response);
                if (error != null) {
                    mensajeAlerta(error, "error");
                    return;
                }
                if (response.tipo != "exito") {
                    mensajeAlerta(response.mensaje, response.tipo);
                    return;
                }

                setTimeout(() => {
                    location.reload();
                }, 1500);
                mensajeAlerta(response.mensaje, response.tipo);

            });
        }
    })
});







