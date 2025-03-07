document.getElementById('fetchNotifications').addEventListener('click', async function() {
    try {
        const response = await fetch(`verificarBoletasNoimpresas`, {
            method: "GET",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute(
                    "content"),
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }

        const data = await response.json();
        console.log("Respuesta del servidor:", data);

        let content = "";
        if (data.length > 0) {
            data.forEach(notificacion => {
                content += `
                <a href="#" class="dropdown-item py-3">
                    <small class="float-end text-muted ps-2">${notificacion.tiempo}</small>
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-primary-subtle text-primary thumb-md rounded-circle">
                            <i class="iconoir-wolf fs-4"></i>
                        </div>
                        <div class="flex-grow-1 ms-2 text-truncate">
                            <h6 class="my-0 fw-normal text-dark fs-13">${notificacion.titulo}</h6>
                            <small class="text-muted mb-0">${notificacion.mensaje}</small>
                        </div>
                    </div>
                </a>`;
            });
        } else {
            content = `<p class="text-center text-muted py-3">No hay notificaciones pendientes</p>`;
        }

        document.getElementById('All').innerHTML = content;
        document.getElementById('badgeCount').textContent = data.length;

    } catch (error) {
        console.error("Error en la solicitud fetch:", error);
    }
});