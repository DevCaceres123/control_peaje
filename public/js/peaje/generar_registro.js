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

    vaciar_errores("nuevo_registro");

    crud("admin/peaje", "POST", null, datosFormulario, function (error, response) {

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

        mensajeAlerta("Procesado correctamente.", "exito");
        vaciar_formulario("nuevo_registro");


        let pdfUrl = generarURlBlob(response.mensaje.resultado);

        // Cargar el PDF en el iframe
        const iframe = document.getElementById('pdf-iframe');
        iframe.src = pdfUrl;

        // Mostrar el contenedor del PDF
        $('#pdf-container').css('display', 'block');
        $('#nuevo_registro').css('display', 'none');
        $('#verificarQr').css('display', 'none');
        $('#boletas_generadas').css('display', 'none');


        // Esperar a que el iframe termine de cargar antes de imprimir
        iframe.onload = () => {
            iframe.contentWindow.focus();
            iframe.contentWindow.print(); // Disparar impresión automática desde el iframe
            marcarBoletaImpresa(response.mensaje.cod_qr_unico);
        };

        $("#btn-nuevoRegistro").prop("disabled", false);

        // Reiniciar valores
        $('#precio').html('');
        $('#id_tarifa').val("");
    });
});



// Generar QR sin informacion
$('#btn-generarQr').click(function (e) {

    let datosFormulario = $('#nuevo_registro').serialize();

    console.log(datosFormulario);
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

        let pdfUrl = generarURlBlob(response.mensaje['resultado']);
        iframe.src = pdfUrl;
        // activar ventana del generado de pdf
        $('#pdf-container').css('display', 'block');
        $('#nuevo_registro').css('display', 'none'); // no mostrar el formulario
        $('#verificarQr').css('display', 'none'); // no mostrar el verificar qr
        $('#boletas_generadas').css('display', 'none');
        $("#btn-generarQr").prop("disabled", false);

        //    Una ves registrado se le quita los valores a los montos
        $('#precio').html('');
        $('#id_tarifa').val("");

        // Esperar a que el iframe termine de cargar antes de imprimir
        iframe.onload = () => {
            iframe.contentWindow.focus();
            iframe.contentWindow.print(); // Disparar impresión automática desde el iframe
            // 🔹 Después de imprimir, marcar la boleta como impresa
            marcarBoletaImpresa(response.mensaje['cod_qr_unico']);
        };
    })

})


function marcarBoletaImpresa(QrCodigo) {
    fetch("marcarBoletaImpresa/" + QrCodigo, {
        method: "PUT",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
        },
        body: JSON.stringify({})
    });
}



// Formulario para llenar informacion
$('#btn-llenar_informacion').click(function (e) {
    e.preventDefault();
    $('#nuevo_registro').css('display', 'block'); // Elimina el color
    $('#pdf-container').css('display', 'none');
    $('#verificarQr').css('display', 'none'); // no mostrar el verificar qr
    $('#boletas_generadas').css('display', 'none'); // no mostrar el verificar qr
    
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


// GENERAR VARIOS COMPROBANTES A LA VES

$('#generar_boletas').submit(function (e) {
    e.preventDefault();
    $("#btn-generar_boleta").prop("disabled", true);
    let datosFormulario = $('#generar_boletas').serialize();
    let cantidad_generar = $("#cantidad").val();

    cantidad_generar = parseInt(cantidad_generar); // Convertir el valor a un número

    if (!isNaN(cantidad_generar) && cantidad_generar > 1 && cantidad_generar <= 20) {
        let colaDeImpresion = [];
        let pdfUrl;
        let boletasProcesadas=1;




        // Función autoejecutada para manejar async/await
        (async function () {
            try {
                // Obtener los datos del servidor
                const response = await new Promise((resolve, reject) => {
                    crud("admin/generar_varias_boletas", "POST", null, datosFormulario, function (error, response) {
                        if (error) {
                            reject(error);
                        } else {
                            resolve(response);
                        }
                    });
                });

                if (response.tipo !== "exito") {
                    $("#btn-generar_boleta").prop("disabled", false);
                    mensajeAlerta(response.mensaje, response.tipo);
                    return;
                }

                mensajeAlerta("Procesado correctamente.", "exito");

                // se ocualta las otras ventanas
                $('#boletas_generadas').css('display', 'block');

                $('#verificarQr').css('display', 'none');

                $('#pdf-container').css('display', 'none');
                $('#nuevo_registro').css('display', 'none'); // no mostrar formulario


                console.log(response);

                // Convertir las URLs de respuesta
                response.mensaje.forEach(element => {
                    pdfUrl = generarURlBlob(element[0]);
                    let nuevoDocumento = {
                        pdfUrl: pdfUrl,
                        qrcod: element[1]
                    };
                    colaDeImpresion.push(nuevoDocumento);
                });

                $('#numero_boletas').text(colaDeImpresion.length);
                let totalBoletas = colaDeImpresion.length; // Inicializar el total de boletas
                let boletasRestantes = colaDeImpresion.length;

                console.log(totalBoletas);
                // Imprimir una por una
                for (let i = 0; i < colaDeImpresion.length; i++) {
                    
                    let pdfUrl = colaDeImpresion[i]['pdfUrl'];
                    const iframe = document.createElement('iframe');
                    iframe.style.display = 'none';
                    iframe.src = pdfUrl;
                   document.body.appendChild(iframe);

                    await new Promise(resolve => {
                        iframe.onload = () => {
                            iframe.contentWindow.focus();
                            iframe.contentWindow.print(); // Imprimir el documento
                            setTimeout(() => {
                                boletasRestantes--; // Reducir el contador
                                actualizarBoletasRestantes(boletasProcesadas, boletasRestantes, totalBoletas); // Actualizar visualmente
                                marcarBoletaImpresa(colaDeImpresion[i]['qrcod']);
                                
                                console.log(boletasRestantes);
                                boletasProcesadas++;
                                resolve(); // Resolver la promesa
                            }, 2500); // Esperar 2.5 segundos antes de resolver
                        };
                    });
                }
            } catch (error) {
                console.error("Error al procesar la boleta:", error);
                mensajeAlerta("Error al procesar la boleta. Intenta nuevamente.", "error");
            } finally {

                $("#btn-generar_boleta").prop("disabled", false);
                $('#generar_boletas')[0].reset(); // Limpia el formulario
            }
        })();
    } else {
        $("#error_cantidad").text("La cantidad debe estar entre 2 y 20.");
    }
});

// Actualizar visualmente el progreso de las boletas procesadas
function actualizarBoletasRestantes(boletasProcesadas, boletasRestantes, totalBoletas) {
    const contador = document.getElementById("contadorBoletas");
    const barraProgreso = document.getElementById("barraProgreso");

    // Actualizar contador de boletas procesadas
    contador.textContent = boletasRestantes;

    // Calcular el porcentaje progresivo
    const porcentaje = (boletasProcesadas / totalBoletas) * 100;

    // Ajustar la barra de progreso
    barraProgreso.style.width = `${porcentaje}%`;
    barraProgreso.textContent = `${Math.round(porcentaje)}%`;

    // Cambiar a rojo si faltan pocas boletas (opcional, adaptado para el caso progresivo)
    barraProgreso.classList.toggle("bg-success", totalBoletas - boletasProcesadas <= 2);

    if(boletasProcesadas=== totalBoletas){

        
        $("#correcto_impresion").text("Correcto....");
        $("#nota_impresion").text("Nota: Si existe alguna boleta faltante verificar en la cola de impresion...");
    }
}









// VERIFICAR EL QR ENVIADO

$('#btn-verificarQr').on('click', function () {
    // Colocar el cursor en el input

    $('#verificarQr').css('display', 'block');

    $('#pdf-container').css('display', 'none');
    $('#boletas_generadas').css('display', 'none');
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

              

                if (response.tipo == "exito") {
                    $('#estado_qr').html(`<b class="text-success fs-3">${response.tipo}  <i class="fas fa-check-circle ms-1"></i></b>`);
                    $('#respuesta_servidor').html(`<span class="text-success fs-4">${response.mensaje}  <i class="fas fa-check-circle ms-1"></i></span>`);
                }
                else {
                    $('#estado_qr').html(`<b class="text-danger  fs-3">${response.tipo}</b>`);
                    $('#respuesta_servidor').html(`<span class="text-danger fs-4">${response.mensaje} <i class="fas fa-exclamation-triangle ms-1"></i></span>`);
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


// nos servira para crear una url para poder visualizar nuestro pdf

function generarURlBlob(pdfbase64) {

    // Convertir Base64 a un Blob
    const byteCharacters = atob(pdfbase64); // Decodifica el Base64
    const byteNumbers = Array.from(byteCharacters).map(c => c.charCodeAt(0));
    const byteArray = new Uint8Array(byteNumbers);
    const blob = new Blob([byteArray], { type: 'application/pdf' });

    // Crear una URL para el Blob
    return URL.createObjectURL(blob);
}