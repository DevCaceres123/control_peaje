<!DOCTYPE html>
<html lang="es" dir="ltr" data-startbar="light" data-bs-theme="light">

<head>
    <title>ADMIN | @yield('titulo')</title>
    {{-- Estilos que utililizaremos  --}}
    @include('plantilla_admin.estilos')
    {{-- Si utilizamos estilos propios --}}
    @yield('estilos')
</head>


<body>
    <!-- Loader -->

    <!-- Loading wrapper start -->
    <div id="loading-wrapper">
    <div class="spinner-border-loader"></div>
        Cargando . . . .
    </div>
    <!-- Loading wrapper end -->
    {{-- Para el header --}}
    @include('plantilla_admin.header')

    {{-- Para el menu --}}
    @include('plantilla_admin.menu')

    <div class="page-wrapper">

        <div class="page-content">
            <div class="container-xxl">
                @yield('contenido')
            </div>

            {{-- Para el footer --}}
            @include('plantilla_admin.footer')

        </div>
    </div>
    {{-- Incluimos la libreria de script --}}
    @include('plantilla_admin.scripts')
    {{-- Si utilizamos scripts js propios --}}
    @yield('scripts')
</body>

</html>
