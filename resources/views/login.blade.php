<!DOCTYPE html>
<html lang="es" data-startbar="dark" data-bs-theme="light">

<head>
    <meta charset="utf-8" />
    <title>LOGIN</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta content="" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('admin_template/images/favicon.ico') }}">

    <!-- App css -->
    <link href="{{ asset('admin_template/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('admin_template/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('admin_template/css/app.min.css') }}" rel="stylesheet" type="text/css" />
    {{-- diseño para el login --}}
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>

<!-- Top Bar Start -->

<body>




    <div class="contenedor_fondo">
        <div class="contenedor_fondo-img slider">

        </div>
        <div class="contenedor_login">

            <div class="contenedor_login-formulario-carusel">

                <div class="contenedor-formulario">

                    <div class="card-body p-5">
                        <h2 class="contenedor-formulario">
                            Bienvenido
                        </h2>
                        <div class="text-center py-2" id="mensaje_error"></div>
                        <form id="formulario_login" autocomplete="off">
                            @csrf
                            <div class="form-group mb-2">
                                <label class="form-label" for="usuario">Usuario</label>
                                <input type="text" class="form-control rounded-pill text-light" id="usuario" name="usuario"
                                    placeholder="Ingrese usuario">
                            </div><!--end form-group-->

                            <div class="form-group">
                                <label class="form-label" for="password">Contraseña</label>
                                <input type="password" class="form-control rounded-pill text-light" name="password" id="password"
                                    placeholder="Ingrese la contraseña">
                            </div><!--end form-group-->
                        </form>

                        <div class="form-group mb-0 row">
                            <div class="col-12">
                                <div class="d-grid mt-3">
                                    <button class="btn  rounded-pill" type="button"
                                        id="btn_ingresar_usuario">INGRESAR <i
                                            class="fas fa-sign-in-alt ms-1"></i></button>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="contenedor-carusel">
                    <div class="contenedor-carusel-titulo-descripcion">

                        <h3>GOBIERNO AUTÓNOMO MUNICIPAL DE CARANAVI</h3>
                        <h5>Departamento de Recaudaciones</h5>
                        <div class="contenedor_login-logo">
                            <img src="{{ asset('assets/logo-caranavi.webp') }}" alt="">
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

</body>

</html>

<script>
    // Seleccionar elementos del DOM
    let loginBtn = document.getElementById('btn_ingresar_usuario');
    let formularioLogin = document.getElementById('formulario_login');
    let mensajeError = document.getElementById('mensaje_error');

    // Función para crear y mostrar alertas
    function mostrarAlerta(tipo, mensaje) {
        let iconoClase = tipo === 'success' ? 'fa-check' : 'fa-xmark';
        let color = tipo === 'success' ? 'success' : 'danger';
        mensajeError.innerHTML = `
        <div class="alert alert-${color} shadow-sm border-theme-white-2" role="alert">
            <div class="d-inline-flex justify-content-center align-items-center thumb-xs bg-${color} rounded-circle mx-auto me-1">
                <i class="fas ${iconoClase} align-self-center mb-0 text-white"></i>
            </div>
            <strong>${mensaje}</strong>
        </div>
        `;
        // Configurar el temporizador para ocultar la alerta después de 5 segundos
        setTimeout(() => {
            mensajeError.innerHTML = '';
        }, 4000);
    }

    // Función para validar el botón
    function validarBoton(estaDeshabilitado, mensaje) {
        loginBtn.textContent = mensaje;
        loginBtn.disabled = estaDeshabilitado;
    }

    // Manejar el envío del formulario
    loginBtn.addEventListener('click', async (e) => {
        let datos = Object.fromEntries(new FormData(formularioLogin).entries());
        validarBoton(true, "Verificando datos...");
        try {
            let respuesta = await fetch("{{ route('log_ingresar') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(datos)
            });
            if (!respuesta.ok) {
                throw new Error(`HTTP error! status: ${respuesta.status}`);
            }
            let data = await respuesta.json();
            mostrarAlerta(data.tipo, data.mensaje);
            if (data.tipo === 'success') {
                validarBoton(true, 'Datos correctos...');
                setTimeout(() => window.location.reload(), 1500);
            } else {
                validarBoton(false, 'INGRESAR');
            }
        } catch (error) {
            console.error('Error:', error);
            mostrarAlerta('error', 'Ocurrió un error al procesar la solicitud');
            validarBoton(false, 'INGRESAR');
        }
    });
</script>
