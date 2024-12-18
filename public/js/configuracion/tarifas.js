let modal_tarifa = new bootstrap.Modal(document.getElementById("modal_tarifa"));
let form_tarifa = document.getElementById("form_tarifa");
let btn_guardar_tarifa = document.getElementById("btn_guardar_tarifa");
let errores_msj = ["_nombre", "_precio", "_descripcion"];

function abrirModalTarifa(id = null) {
    vaciar_errores(errores_msj);
    form_tarifa.reset();
    document.getElementById("tarifa_id").value = id;
    document.getElementById("tarifaModalLabel").textContent = id
        ? "Editar Tarifa"
        : "Nuevo Tarifa";

    if (id) {
        cargarDatosTarifa(id);
    }
    modal_tarifa.show();
}

async function cargarDatosTarifa(id) {
    try {
        let url = rutas.editar.replace(":id", id);
        let respuesta = await fetch(url, {
            method: "GET",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": token,
            },
        });
        let data = await respuesta.json();
        if (data.tipo === "success") {
            document.getElementById("nombre").value = data.mensaje.nombre;
            document.getElementById("precio").value = data.mensaje.precio;
            document.getElementById("descripcion").value = data.mensaje.descripcion;
        } else {
            alerta_top(data.tipo, data.mensaje);
        }
    } catch (error) {
        console.log("Error al obtener los datos:", error);
    }
}

function cerrarModelTarifa() {
    form_tarifa.reset();
    modal_tarifa.hide();
    vaciar_errores(errores_msj);
}

btn_guardar_tarifa.addEventListener("click", async () => {
    let datos = Object.fromEntries(new FormData(form_tarifa).entries());
    let url = datos.tarifa_id
        ? rutas.actualizar.replace(":id", datos.tarifa_id)
        : rutas.crear;
    let metodo = datos.tarifa_id ? "PUT" : "POST";
    validar_boton(true, "Validando . . .", btn_guardar_tarifa);

    try {
        let respuesta = await fetch(url, {
            method: metodo,
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": token,
            },
            body: JSON.stringify(datos),
        });
        let data = await respuesta.json();
        vaciar_errores(errores_msj);
        if (data.tipo === "errores") {
            mostrarErrores(data.mensaje);
        } else {
            alerta_top(data.tipo, data.mensaje);
            if (data.tipo === "success") {
                listar_tarifa();
                cerrarModelTarifa();
            }
        }
        validar_boton(false, "Guardar", btn_guardar_tarifa);
    } catch (error) {
        console.log("Ocurrió un error:", error);
    }
});

async function listar_tarifa() {
    try {
        let respuesta = await fetch(rutas.listar, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": token,
            },
        });
        let data = await respuesta.json();
        tarifa_tabla(data);
    } catch (error) {
        console.log("Error al obtener los datos:", error);
    }
}

function tarifa_tabla(dato) {
    $("#tabla_tarifa").DataTable({
        responsive: true,
        data: dato.tarifa,
        columns: [
            { data: null, render: (data, type, row, meta) => meta.row + 1 },
            { data: "nombre", className: "table-td" },
            { data: "precio", className: "table-td" },
            { data: "descripcion", className: "table-td" },
            {
                data: null,
                className: "table-td",
                render: (data, type, row) => `
                    <div class="form-check form-switch form-switch-dark">
                        <input class="form-check-input" onclick="estado_tarifa('${row.id}')" type="checkbox"  id="customSwitchDark" ${row.estado === "activo" ? "checked" : ""}>
                    </div>
                `,
            },
            {
                data: null,
                className: "table-td",
                render: (data,type, row) => `

                    ${dato.permissions['editar'] ?
                        `  <button class="btn rounded-pill btn-sm btn-warning p-0.5" onclick="abrirModalTarifa('${row.id}')">
                        <i class="las la-pen fs-18"></i>
                    </button>`
                        : ``}

 ${dato.permissions['eliminar'] ?
                        `  <button class="btn rounded-pill btn-sm btn-danger p-0.5" onclick="eliminarTarifa('${row.id}')">
                        <i class="las la-trash-alt fs-18"></i>
                    </button>`
                        : ``}
                        
                   
                   
                `,
            },
        ],
        destroy: true,
    });
}

listar_tarifa();

async function estado_tarifa(id) {
    Swal.fire({
        title: "NOTA!",
        text: "¿Está seguro de cambiar el estado?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, Cambiar",
        cancelButtonText: "Cancelar",
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                let url = rutas.cambiarEstado.replace(":id", id);
                let respuesta = await fetch(url, {
                    method: "GET",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": token,
                    },
                });
                let data = await respuesta.json();
                alerta_top(data.tipo, data.mensaje);
                listar_tarifa();
            } catch (error) {
                console.log("Error al cambiar el estado:", error);
            }
        } else {
            listar_tarifa();
        }
    });
}

async function eliminarTarifa(id) {
    Swal.fire({
        title: "NOTA!",
        text: "¿Está seguro de eliminar?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, Eliminar",
        cancelButtonText: "Cancelar",
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                let url = rutas.eliminar.replace(":id", id);
                let respuesta = await fetch(url, {
                    method: "DELETE",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": token,
                    },
                });
                let data = await respuesta.json();
                alerta_top(data.tipo, data.mensaje);
                listar_tarifa();
            } catch (error) {
                console.log("Error al eliminar:", error);
                listar_tarifa();
            }
        } else {
            listar_tarifa();
        }
    });
}
