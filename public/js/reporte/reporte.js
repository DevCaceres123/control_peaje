import { mensajeAlerta } from '../../../funciones_helper/notificaciones/mensajes.js';
import { crud } from '../../../funciones_helper/operaciones_crud/crud.js';
import { vaciar_errores, vaciar_formulario } from '../../../funciones_helper/vistas/formulario.js';


// // GENERAR REPORTE
// $('#form-reportes').submit(function (e) {
//     e.preventDefault();
//     $("#btn-reporte").prop("disabled", true);
//     let datosFormulario = $('#form-reportes').serialize();

//     vaciar_errores("form-reportes");

//     // envia a la funcion de store
//     crud("admin/reportes", "POST", null, datosFormulario, function (error, response) {

//         console.log(response);
//         if (response.tipo == "errores") {
//             $("#btn-reporte").prop("disabled", false);
//             mensajeAlerta(response.mensaje, "errores");
//             return;
//         }

//         if (response.tipo != "exito") {
//             $("#btn-reporte").prop("disabled", false);
//             mensajeAlerta(response.mensaje, response.tipo);
//             return;
//         }

     
//         mensajeAlerta("Generando Reporte.....", "exito");
//         const blobUrl = generarURlBlob(response.mensaje); // Genera la URL del Blob
       

//         setTimeout(() => {
//             window.open(blobUrl, '_blank'); // Abre en una nueva pestaña
//             $("#btn-reporte").prop("disabled", false);
//         }, 1500);

//     });
// })


// GENERAR REPORTE POR FECHA
$('#form-reportes').submit(function (e) {
    e.preventDefault();
    $("#btn-reporte").prop("disabled", true);
    let datosFormulario = $('#form-reportes').serialize();

    vaciar_errores("form-reportes");

    // envia a la funcion de store
    crud("admin/reportes_fecha", "POST", null, datosFormulario, function (error, response) {

        console.log(response);
        if (response.tipo == "errores") {
            $("#btn-reporte").prop("disabled", false);
            mensajeAlerta(response.mensaje, "errores");
            return;
        }

        if (response.tipo != "exito") {
            $("#btn-reporte").prop("disabled", false);
            mensajeAlerta(response.mensaje, response.tipo);
            return;
        }

     
        mensajeAlerta("Generando Reporte.....", "exito");
        const blobUrl = generarURlBlob(response.mensaje); // Genera la URL del Blob
       

        setTimeout(() => {
            window.open(blobUrl, '_blank'); // Abre en una nueva pestaña
            $("#btn-reporte").prop("disabled", false);
        }, 1500);

    });
})




// GENERAR REPORTE POR USUARIO
$('#form-reporte_usuario').submit(function (e) {
    e.preventDefault();
    $("#btn-reporte_usuario").prop("disabled", true);
    let datosFormulario = $('#form-reporte_usuario').serialize();

    vaciar_errores("form-reportes");

    // envia a la funcion de store
    crud("admin/reportes_usuario", "POST", null, datosFormulario, function (error, response) {

        console.log(response);
        if (response.tipo == "errores") {
            $("#btn-reporte").prop("disabled", false);
            mensajeAlerta(response.mensaje, "errores");
            return;
        }

        if (response.tipo != "exito") {
            $("#btn-reporte").prop("disabled", false);
            mensajeAlerta(response.mensaje, response.tipo);
            $("#btn-reporte_usuario").prop("disabled", false);
            return;
        }

     
        mensajeAlerta("Generando Reporte.....", "exito");
        const blobUrl = generarURlBlob(response.mensaje); // Genera la URL del Blob
       

        setTimeout(() => {
            window.open(blobUrl, '_blank'); // Abre en una nueva pestaña
            $("#btn-reporte_usuario").prop("disabled", false);
        }, 1500);

    });
})


function generarURlBlob(pdfbase64) {

    // Convertir Base64 a un Blob
    const byteCharacters = atob(pdfbase64); // Decodifica el Base64
    const byteNumbers = Array.from(byteCharacters).map(c => c.charCodeAt(0));
    const byteArray = new Uint8Array(byteNumbers);
    const blob = new Blob([byteArray], { type: 'application/pdf' });

    // Crear una URL para el Blob
    return URL.createObjectURL(blob);
}


// MARCAR DESMARCAR CHECK
$('#select_all').on('change', function () {
    // Cambia el estado de todos los checkboxes dentro de #form_rol
    $('#form-reportes input[type="checkbox"]').prop('checked', this.checked);
});


// MARCAR DESMARCAR CHECK
$('#select_all_user').on('change', function () {
    // Cambia el estado de todos los checkboxes dentro de #form_rol
    $('#form-reporte_usuario input[type="checkbox"]').prop('checked', this.checked);
});