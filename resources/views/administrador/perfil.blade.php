@extends('principal')
@section('titulo', 'PERFIL')
@section('contenido')
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Cambiar Contraseña</h4>
                </div><!--end card-header-->
                <div class="card-body pt-0">
                    <form action="#" id="form_password" method="post">
                        @csrf
                        <div class="form-group mb-3 row">
                            <label class="col-xl-3 col-lg-3 text-end mb-lg-0 align-self-center form-label">Contraseña
                                Actual</label>
                            <div class="col-lg-9 col-xl-8">
                                <input class="form-control" type="password" id="password_actual" name="password_actual"
                                    placeholder="Contraseña Actual">
                                <div id="_password_actual"></div>
                            </div>

                        </div>
                        <div class="form-group mb-3 row">
                            <label class="col-xl-3 col-lg-3 text-end mb-lg-0 align-self-center form-label">Nueva
                                Contraseña</label>
                            <div class="col-lg-9 col-xl-8">
                                <input class="form-control" type="password" id="password_nuevo" name="password_nuevo"
                                    placeholder="Ingrese la nueva Contraseña">
                                <div id="_password_nuevo"></div>
                            </div>

                        </div>
                        <div class="form-group mb-3 row">
                            <label class="col-xl-3 col-lg-3 text-end mb-lg-0 align-self-center form-label">Confirmar
                                Contraseña</label>
                            <div class="col-lg-9 col-xl-8">
                                <input class="form-control" type="password" id="password_confirmar"
                                    name="password_confirmar" placeholder="Repita la contraseña nueva">
                                <div id="_password_confirmar"></div>
                            </div>
                        </div>
                    </form>
                    <div class="form-group row">
                        <div class="col-lg-9 col-xl-8 offset-lg-3">
                            <button type="button" id="perfil_btn" class="btn btn-primary">Guardar Cambios</button>
                        </div>
                    </div>
                </div><!--end card-body-->
            </div><!--end card-->
        </div> <!--end col-->
    </div><!--end row-->
@endsection
@section('scripts')
    <script>
        let btn_perfil = document.getElementById('perfil_btn');
        let form_password = document.getElementById('form_password');
        //capturamos los que vana mostrar errores
        let valores_errores = ['_password_actual', '_password_nuevo', '_password_confirmar'];

        btn_perfil.addEventListener('click', async () => {
            let datos = Object.fromEntries(new FormData(form_password).entries());
            validar_boton(true, "Verificando datos . . . . ", btn_perfil);

            try {
                let respuesta = await fetch("{{ route('pwd_guardar') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify(datos)
                });

                let data = await respuesta.json();
                vaciar_errores(valores_errores);

                if (data.tipo === 'errores') {
                    mostrarErrores(data.mensaje);
                    validar_boton(false, 'Guardar Cambios', btn_perfil);
                }
                if (data.tipo === 'error') {
                    alerta_top(data.tipo, data.mensaje);
                    validar_boton(false, 'Guardar Cambios', btn_perfil);
                }
                if (data.tipo === 'success') {
                    alerta_top(data.tipo, data.mensaje);
                    validar_boton(true, 'Guardar Cambios', btn_perfil);
                    await cerrarSesion();
                }
            } catch (error) {
                console.log('Existe un error: ' + error);
                validar_boton(false, "Guardar Cambios", btn_perfil);
            }
        });

        async function cerrarSesion() {
            setTimeout(async function() {
                await cerrar_session_cam();
            }, 2000);
        }
    </script>
@endsection
