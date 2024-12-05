import { mensajeAlerta } from '../../../funciones_helper/notificaciones/mensajes.js';
import { crud } from '../../../funciones_helper/operaciones_crud/crud.js';
import { vaciar_errores, vaciar_formulario } from '../../../funciones_helper/vistas/formulario.js';


// obtenemos el input donde el lector enviara la informacion para  verificar
let qrInput = $('#cod_qr');
console.log(qrInput);
// tiempo de espera que se tomara para leer los datos enviados
let typingTimer;


// buscar cliente por el ci
$('#ci').keyup(function () {

    let ci_persona = $(this).val(); // Captura el valor actual del campo de entrada

    // si no es mayor a  6 entonces no enviara la peticion
    if (ci_persona.length < 6) {

        return;
    }
    // enviamos al metodo show para busczar el cliente
    crud("admin/peaje", "GET", ci_persona, null, function (error, response) {

        console.log(response);
        // Verificamos que no haya un error o que todos los campos sean llenados
        if (response.tipo === "errores") {
            // dibujar los respectivos errores en campo de cada formulario
            mensajeAlerta(response.mensaje, "errores");
            return;
        }
        if (response.tipo != "exito") {
            $('#ap_paterno').val("");
            $("#ap_materno").val("");
            $("#nombres").val("");

            return;
        }

        // Si se haya encontrado realiza ell llenado de los campos

        $('#ap_paterno').val(response.mensaje.ap_paterno);
        $("#ap_materno").val(response.mensaje.ap_materno);
        $("#nombres").val(response.mensaje.nombres);
    })
});


// CREAR NUEVO REGISTRO CON DATOS
$('#nuevo_registro').submit(function (e) {

    e.preventDefault();
    $("#btn-nuevoRegistro").prop("disabled", true);
    let datosFormulario = $('#nuevo_registro').serialize();

    // se vacian los errores de los formualrios
    vaciar_errores("nuevo_registro");
    // Se envian los datos ah store para ser procesados
    crud("admin/peaje", "POST", null, datosFormulario, function (error, response) {

        // console.log(response);
        // Verificamos que no haya un error o que todos los campos sean llenados
        if (response.tipo == "errores") {
            $("#btn-nuevoRegistro").prop("disabled", false);
            mensajeAlerta(response.mensaje, "errores");
            return;

        }
        if (response.tipo != "exito") {
            $("#btn-nuevoRegistro").prop("disabled", false);
            mensajeAlerta(response.mensaje, response.tipo);
            return;
        }

        // si todo esta correcto muestra el mensaje de correcto
        mensajeAlerta("Procesado correctamente.", "exito");
        vaciar_formulario("nuevo_registro");
        const iframe = document.getElementById('pdf-iframe');

        iframe.src = `data:application/pdf;base64,${response.mensaje}`;
        // activar ventana del generado de pdf
        $('#pdf-container').css('display', 'block');
        $('#nuevo_registro').css('display', 'none'); // no mostrar formulario
        $('#verificarQr').css('display', 'none'); // no mostrar el verificar qr
        $("#btn-nuevoRegistro").prop("disabled", false);

        //Una ves registrado se le quita los valores a los montos
        $('#precio').html('');
        $('#id_tarifa').val("");

    })
})

// Generar QR sin informacion
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

                console.log(response);
                // Verificamos que no haya un error o que todos los campos sean llenados
                if (response.tipo === "errores") {
                    $("#btn-generarQr").prop("disabled", false);
                    mensajeAlerta(response.mensaje, "errores");
                    return;
                }
                if (response.tipo != "exito") {
                    $("#btn-generarQr").prop("disabled", false);
                    mensajeAlerta(response.mensaje, response.tipo);
                    return;
                }


                mensajeAlerta("Procesado correctamente.", "exito");
                vaciar_formulario("nuevo_registro");
                const iframe = document.getElementById('pdf-iframe');

                iframe.src = `data:application/pdf;base64,${response.mensaje}`;
                // activar ventana del generado de pdf
                $('#pdf-container').css('display', 'block');
                $('#nuevo_registro').css('display', 'none'); // no mostrar el formulario
                $('#verificarQr').css('display', 'none'); // no mostrar el verificar qr
                $("#btn-generarQr").prop("disabled", false);

                //    Una ves registrado se le quita los valores a los montos
                $('#precio').html('');
                $('#id_tarifa').val("");
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
    $('#pdf-container').css('display', 'none');
    $('#verificarQr').css('display', 'none'); // no mostrar el verificar qr
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


// VERIFICAR EL QR ENVIADO

$('#btn-verificarQr').on('click', function () {
    // Colocar el cursor en el input
   
    $('#verificarQr').css('display', 'block');


    $('#pdf-container').css('display', 'none');
    $('#nuevo_registro').css('display', 'none'); // no mostrar formulario

    qrInput.focus();
});


// Detectar escritura del lector de QR
qrInput.on('input', function () {
    clearTimeout(typingTimer); // Reinicia el temporizador

    // Esperar 300 ms después de la última entrada
    typingTimer = setTimeout(() => {
        const qrContent = qrInput.val(); // Obtén el contenido del input

        // Si no está vacío, envía los datos al servidor
        if (qrContent.length > 0) {

            // Empieza a escanear el codigo
            $('#estado_qr').text("Escaneado QR espere por favor....!");


            // enviamos al metodo show para busczar el cliente
            crud("admin/verificarQr", "GET", qrContent, null, function (error, response) {

                console.log(response);

                $('#estado_qr').text(response.tipo);

                if (response.tipo == "exito") {
                    $('#respuesta_servidor').html(`<span class="text-success">${response.mensaje}  <i class="fas fa-check-circle ms-1"></i></span>`);
                }
                else{
                    $('#respuesta_servidor').html(`<span class="text-danger">${response.mensaje} <i class="fas fa-exclamation-triangle ms-1"></i></span>`);
                }

                qrInput.val("");
            })
        }
    }, 300); // Ajusta el tiempo según sea necesario
});








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