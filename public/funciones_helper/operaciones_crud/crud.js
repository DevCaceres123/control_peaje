
export async function crud(url, metodo, idRegistro = null, datos = null, callback) {

    let response;
    try {
        // Hacer la solicitud con fetch
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');


        // CUANDO SE REALIZA UN PUT
        if (idRegistro != null && datos != null) {
            // console.log(datos);
            response = await fetch(`/${url}/${idRegistro}`, {
                method: metodo, // or 'PUT'
                headers: {
                    'Content-Type': 'application/json',
                    "X-CSRF-TOKEN": csrfToken  // Añadir el token CSRF aquí
                },
                body: JSON.stringify(datos),
            });

        }

        // CUANDO SE REALIZA UN POST
        if (datos != null && idRegistro == null) {

            // console.log(datos);
            response = await fetch(`/${url}`, {
                method: metodo, // or 'PUT'
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-TOKEN': csrfToken  // Añadir el token CSRF aquí
                },
                body: datos // Enviando el objeto como JSON,
            });
        }

        // CUANDO SE REALIZA UN GET
        if (idRegistro != null && datos == null) {

            response = await fetch(`/${url}/${idRegistro}`, {
                method: metodo, // or 'PUT'
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken  // Añadir el token CSRF aquí
                },
            });
        }

        // CUANDO SE REALIZA GET A INDEX
        if (datos == null && idRegistro == null) {
            // console.log("entro sin datos");
            response = await fetch(`/${url}`, {
                method: metodo, // or 'PUT'
                headers: {
                    "Content-Type": "application/json",
                },
            });
        }



        // Verificar si hubo error HTTP
        if (!response.ok && response.status != "422") {
            throw new Error(`Ocurrio algun error: ${response.status}`);
        }
        const contentType = response.headers.get('Content-Type');
        if (contentType && contentType.startsWith('application/pdf')) {
            // Si la respuesta es un PDF, lo manejamos como un Blob
            const pdfBlob = await response.blob();

            // Llamamos al callback con el PDF Blob
            callback(null, pdfBlob);
        } else {
            // Si la respuesta es JSON, la parseamos
            const respuestaParseada = await response.json();
            callback(null, respuestaParseada);
        }


    } catch (error) {
        callback(error, null);

    }
}



export function crearRegistro(url, datos, callback) {
    $.ajax({
        url: url,
        method: 'POST',
        data: datos,
        success: function (datos) {
            callback(null, datos);
        },
        error: function (error) {
            callback(error, null);
        }
    });
}


export function listarRegistros(callback) {
    $.ajax({
        url: 'listar_nota',
        method: 'POST',
        dataType: 'json',
        success: function (datos) {
            callback(null, datos);
        },
        error: function (error) {
            callback(error, null);
        }
    });
}



export function obtenerDatosDeUnRegistro(id, ruta, callback) {
    $.ajax({
        url: ruta,
        type: "POST",
        data: { id_dato: id },
        dataType: "JSON",
        success: function (respuesta) {
            callback(null, respuesta);
        },
        error: function (error) {
            callback(error, null);
        }



    });
}


export function actualizarRegistro(url, nuevosDatos, callback) {
    $.ajax({
        url: url,
        type: "POST",
        data: nuevosDatos,
        dataType: "JSON",
        success: function (respuesta) {
            callback(null, respuesta);
        },
        error: function (error) {
            callback(error, null);
        }
    });
}


export function eliminarRegistro(id, ruta, callback) {
    $.ajax({
        url: ruta,
        type: "POST",
        data: { id_dato: id },
        dataType: "JSON",
        success: function (respuesta) {
            callback(null, respuesta);
        },
        error: function (error) {
            callback(error, null);
        }



    });
}
