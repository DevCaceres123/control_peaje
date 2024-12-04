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
            strongElement.textContent = "No encontrado...";
            $("#btn-asignar_encargado").prop("disabled", true);

            return;
        }

        let nombre_completo = response.mensaje.nombres + " " + response.mensaje.apellidos;
        strongElement.textContent = nombre_completo;
        $("#btn-asignar_encargado").prop("disabled", false);
        $("#user_id").val(response.mensaje.id);
    })
});


// ASIGNAR EN USUARIO AL PUESTO
$('.form-asignar_encargado').submit(function (e) {
    e.preventDefault(); // Evitar el comportamiento predeterminado del formulario

    const formulario = $(this); // Obtener el formulario actual que dispara el evento
    const boton = formulario.find("button[type='submit']"); // Buscar el botón de submit dentro del formulario actual

    // Desactivar el botón para evitar múltiples envíos
    boton.prop("disabled", true);

    // Serializar los datos del formulario actual
    let datosFormulario = formulario.serialize();

    // Hacer la solicitud AJAX
    crud("admin/puesto_asignar", "POST", null, datosFormulario, function (error, response) {
        if (response.tipo === "errores") {
            mensajeAlerta(response.mensaje, "errores");
            boton.prop("disabled", false); // Reactivar el botón
            return;
        }

        if (response.tipo !== "exito") {
            mensajeAlerta(response.mensaje, response.tipo);
            boton.prop("disabled", false); // Reactivar el botón
            return;
        }

        // Mostrar el mensaje de éxito
        mensajeAlerta(response.mensaje, response.tipo);

        // Reactivar el botón y recargar después de un breve tiempo
        boton.prop("disabled", false);
        setTimeout(() => {
            location.reload();
        }, 1500);
    });
});





// Agregar el evento de clic después de que la tabla haya sido creada
$(document).on('click', '.terminar_reunion', function () {


    Swal.fire({
        title: '¿Eliminar Registro?',
        text: "Estas seguro que quiere desvincular el puesto!",
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







