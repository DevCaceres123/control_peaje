import { mensajeAlerta } from '../../../funciones_helper/notificaciones/mensajes.js';
import { crud } from '../../../funciones_helper/operaciones_crud/crud.js';
import { vaciar_errores, vaciar_formulario } from '../../../funciones_helper/vistas/formulario.js';

// Generar QR
$('#btn-generarQr').click(function (e) {
    e.preventDefault();
    Swal.fire({
        title: "Se generara un nuevo registro..!!!",
        text: "¿Estas seguro?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Sí, Terminar",
        cancelButtonText: "Cancelar",
    }).then(async function (result) {
        if (result.isConfirmed) {
            let datosFormulario = $('#nuevo_registro').serialize();

            $("#btn-generarQr").prop("disabled", true);
            crud("admin/generar_qr", "POST", null, datosFormulario, function (error, response) {

                $("#btn-generarQr").prop("disabled", false);
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
              
            })


        } else {
            alerta_top('error', 'Se canceló la operacion');
        }
    })
})

// Formulario para llenar informacion
$('#btn-llenar_informacion').click(function (e) {
    e.preventDefault();
    $('#nuevo_registro').css('display', 'block'); // Elimina el color
})


// Terminar un registro
$('#btn-terminar_formulario').click(function (e) {
    e.preventDefault();

    Swal.fire({
        title: "NOTA!",
        text: "¿Se limpiaran todos campos del formulario?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Sí, Terminar",
        cancelButtonText: "Cancelar",
    }).then(async function (result) {
        if (result.isConfirmed) {

            $('#nuevo_registro').css('display', 'none');
            $('#nuevo_registro').trigger('reset');


        } else {
            alerta_top('error', 'Se canceló la operacion');
        }
    })
})





//  SECCION PARA OBTENER PRECIOS       
// Selecciona todas las tarjetas
const cards = document.querySelectorAll('.card_precio');

// Recorre las tarjetas y añade el evento click
cards.forEach((card) => {
    card.addEventListener('click', () => {
        // Obtiene el atributo data-id
        const id = card.getAttribute('data-id');

        // Obtiene la descripción y el precio
        const descripcion = card.querySelector('p').innerText;
        const precio = card.querySelector('strong').innerText;

        // Muestra los valores en la consola
        $('#id_tarifa').val(id);
        $('#precio').html(precio + '<i class="far fa-money-bill-alt ms-1"></i>');
        console.log(`Descripción: ${descripcion}`);
        console.log(`Precio: ${precio}`);


    });
});