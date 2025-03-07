<!-- Top Bar Start -->
<div class="topbar d-print-none ">
    <div class="container">
        <nav class="topbar-custom d-flex justify-content-between" id="topbar-custom">


            <ul class="topbar-item list-unstyled d-inline-flex align-items-center mb-0">
                <li>
                    <button class="nav-link mobile-menu-btn nav-icon" id="togglemenu">
                        <i class="iconoir-menu-scale"></i>
                    </button>
                </li>
                <li class="mx-3 welcome-text">
                    <h5 class="mb-0 fw-bold text-uppercase text-muted">Usuario: {{ Auth::user()->nombres }}</h5>
                    <!-- <h6 class="mb-0 fw-normal text-muted text-truncate fs-14">Here's your overview this week.</h6> -->
                </li>
            </ul>
            <ul class="topbar-item list-unstyled d-inline-flex align-items-center mb-0">

                {{-- notificaciones de las boletas no impresas --}}

                <li class="dropdown topbar-item">
                    <button id="fetchNotifications" class="nav-link dropdown-toggle arrow-none nav-icon"
                        data-bs-toggle="dropdown" role="button" aria-haspopup="false" aria-expanded="false">
                        <i class="fas fa-print text-danger"></i>
                        <span class="alert-badge"></span>
                    </button>
                    <div class="dropdown-menu stop dropdown-menu-end dropdown-lg py-0">
                        <h5
                            class="dropdown-item-text m-0 py-3 d-flex justify-content-between align-items-center text-muted">
                            IMPRESIONES FALTANTES: <a href="#" class="badge text-body-tertiary badge-pill"></a>
                        </h5>
                        <ul class="nav nav-tabs nav-tabs-custom nav-success nav-justified mb-1" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link mx-0 active" data-bs-toggle="tab" href="#All" role="tab"
                                    aria-selected="true">
                                    Boletas <span id="badgeCount"
                                        class="badge bg-primary-subtle text-primary badge-pill ms-1">0</span>
                                </a>
                            </li>
                        </ul>
                        <div class="ms-0" style="max-height:230px;" data-simplebar>
                            <div class="tab-content" id="myTabContent">
                                <div class="tab-pane fade show active" id="All" role="tabpanel"
                                    aria-labelledby="all-tab" tabindex="0">
                                    <!-- Las notificaciones se insertarán aquí -->
                                </div>
                            </div>
                        </div>
                    </div>
                </li>

                {{-- configuracion de usuario --}}
                <li class="dropdown topbar-item">
                    <a class="nav-link dropdown-toggle arrow-none nav-icon" data-bs-toggle="dropdown" href="#"
                        role="button" aria-haspopup="false" aria-expanded="false">
                        <i class="fas fa-user-cog text-success"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end py-0">
                        <div class="d-flex align-items-center dropdown-item py-2 bg-secondary-subtle">
                            <div class="flex-shrink-0">
                                <i class="fas fa-user-cog text-success"></i>
                            </div>
                            <div class="flex-grow-1 ms-2 text-truncate align-self-center">
                                <h6 class="my-0 fw-medium text-dark fs-13">Admin</h6>
                                <small class="text-muted mb-0">Rol</small>
                            </div><!--end media-body-->
                        </div>
                        <div class="dropdown-divider mt-0"></div>
                        <small class="text-muted px-2 pb-1 d-block">Cuenta</small>
                        <a class="dropdown-item text-primary" href="javascript:void(0)" id="btn-terminar_turno"><i
                                class="fas fa-close fs-18 me-1 align-text-bottom"></i> Terminar Turno</a>

                        <a class="dropdown-item" href="{{ route('perfil') }}">
                            <i class="las la-user fs-18 me-1 align-text-bottom"></i>
                            Perfil
                        </a>
                        <div class="dropdown-divider mb-0"></div>
                        <a class="dropdown-item text-danger" href="javascript:void(0)" id="btn-cerrar-session"><i
                                class="las la-power-off fs-18 me-1 align-text-bottom"></i> Salir</a>


                    </div>
                    <form id="formulario_salir" method="POST">@csrf</form>
                </li>
            </ul><!--end topbar-nav-->
        </nav>
        <!-- end navbar-->
    </div>
</div>
<!-- Top Bar End -->

<script>
    // Definir la URL base de la ruta sin ID
    const verificarBoletasURL = "{{ route('peaje.verificar_boleta') }}";
    const generarReporteBaseURL = "{{ url('admin/reporte') }}/"; // Aquí Laravel ya genera la URL base

    document.getElementById('fetchNotifications').addEventListener('click', async function() {
        try {
            const response = await fetch(verificarBoletasURL, {
                method: 'GET',
                headers: {
                    "Content-Type": "application/json",
                },
            });

            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }

            const data = await response.json();
           

            renderizarNotificaciones(data);
            agregarEventosEnlaces();
        } catch (error) {
            console.error("Error en la solicitud fetch:", error);
        }
    });

    /**
     * Renderiza las notificaciones en el HTML.
     */
    function renderizarNotificaciones(data) {
        let content = "";
    
        if (data.length > 0) {
            data.forEach(notificacion => {
                content += `
            <button href="#" class="dropdown-item py-3 boleta-link" 
                data-id="${notificacion.id}" 
                data-qrcode="${notificacion.cod_qr}">
                <small class="float-end ps-2 fs-13 badge bg-success-subtle text-primary badge-pill">
                    ${notificacion.precio} <b>Bs</b>
                </small>
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 bg-primary-subtle text-primary thumb-md rounded-circle">
                        <i class="fas fa-file-alt fs-4"></i>
                    </div>
                    <div class="flex-grow-1 ms-2 text-truncate">
                        <h6 class="my-0 fw-normal text-dark fs-13 text-muted">${notificacion.nombre_usuario}</h6>
                        <small class="mb-0">${notificacion.created_at}</small>
                    </div>
                </div>
            </button>`;
            });
        } else {
            content = `<p class="text-center text-muted py-3">No hay notificaciones pendientes</p>`;
        }

        document.getElementById('All').innerHTML = content;
        document.getElementById('badgeCount').textContent = data.length;
    }

    /**
     * Agrega eventos de clic a los enlaces generados dinámicamente.
     */
    function agregarEventosEnlaces() {
        document.querySelectorAll('.boleta-link').forEach(link => {
            link.addEventListener('click', async function(event) {
                event.preventDefault();

                this.disabled = true; // Deshabilita solo el botón clickeado

                let boletaId = this.getAttribute('data-id');
                let qrCode = this.getAttribute('data-qrcode');

                try {
                    await generarReporteYImprimir(boletaId, qrCode);
                  
                } catch (error) {
                    console.error("Error al generar reporte:", error);
                }
            });
        });
    }

    /**
     * Genera el reporte y lanza la impresión automática.
     */
    async function generarReporteYImprimir(boletaId, qrCode) {
        let urlDestino = `${generarReporteBaseURL}${boletaId}`;

        const response = await fetch(urlDestino, {
            method: 'GET',
            headers: {
                "Content-Type": "application/json",
            }
        });

        if (!response.ok) {
            throw new Error(`Error al generar el reporte: ${response.status}`);
        }

        const pdfData = await response.json(); // Recibir el PDF como Blob

        
        let pdfUrl = generarURlBlob(pdfData.mensaje); // Convertir el Blob en una URL

        imprimirPDF(pdfUrl, qrCode);
    }

    /**
     * Imprime el PDF en un iframe oculto.
     */
    function imprimirPDF(pdfUrl, qrCode) {
        const iframe = document.createElement('iframe');
        iframe.style.display = 'none';
        iframe.src = pdfUrl;

        iframe.onload = () => {
            iframe.contentWindow.focus();
            iframe.contentWindow.print();

            marcarBoletaImpresa(qrCode);
        };

        document.body.appendChild(iframe);
    }




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


    function generarURlBlob(pdfbase64) {

        // Convertir Base64 a un Blob
        const byteCharacters = atob(pdfbase64); // Decodifica el Base64
        const byteNumbers = Array.from(byteCharacters).map(c => c.charCodeAt(0));
        const byteArray = new Uint8Array(byteNumbers);
        const blob = new Blob([byteArray], {
            type: 'application/pdf'
        });

        // Crear una URL para el Blob
        return URL.createObjectURL(blob);
    }
</script>
