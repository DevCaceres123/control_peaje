<?php
    //para la parte de los mensajes
    function mensaje_mostrar($tipo, $mensaje){
        return array(
            'tipo'=>$tipo,
            'mensaje'=>$mensaje
        );
    }


    function manejarHttpError($statusCode) {
        switch ($statusCode) {
            case 400:
                echo "400 Bad Request: La solicitud no se pudo procesar debido a un error del cliente.";
                // Manejar el error, por ejemplo, redirigir o mostrar un mensaje específico.
                break;
            case 401:
                echo "401 Unauthorized: Se requiere autenticación.";
                // Redirigir a la página de inicio de sesión o mostrar un mensaje.
                break;
            case 403:
                echo "403 Forbidden: No tienes permiso para acceder a este recurso.";
                // Mostrar un mensaje de error o redirigir al usuario.
                break;
            case 404:
                echo "404 Not Found: El recurso solicitado no fue encontrado.";
                // Redirigir a una página de error 404 personalizada o mostrar un mensaje.
                break;
            case 500:
                echo "500 Internal Server Error: Hubo un error en el servidor.";
                // Informar al usuario que algo salió mal y que intente más tarde.
                break;
            case 502:
                echo "502 Bad Gateway: Recibí una respuesta no válida de un servidor upstream.";
                // Manejar la situación adecuadamente, tal vez reintentar la solicitud.
                break;
            case 503:
                echo "503 Service Unavailable: El servicio está temporalmente no disponible.";
                // Mostrar un mensaje al usuario indicando que el servicio está en mantenimiento.
                break;
            case 504:
                echo "504 Gateway Timeout: No se recibió respuesta a tiempo del servidor.";
                // Sugerir al usuario que intente nuevamente más tarde.
                break;
            default:
                echo "Error desconocido: " . $statusCode;
                // Manejar otros errores no específicos.
                break;
        }
    }



