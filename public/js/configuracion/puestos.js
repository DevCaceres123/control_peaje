let modal_puesto = new bootstrap.Modal(document.getElementById("modal_puesto"));
let form_puesto = document.getElementById("form_puesto");
let btn_guardar_puesto = document.getElementById("btn_guardar_puesto");
let errores_msj = ["_nombre"];

function abrirModalPuesto(id = null) {
    vaciar_errores(errores_msj);
    form_puesto.reset();
    document.getElementById("puesto_id").value = id;
    document.getElementById("puestoModalLabel").textContent = id
        ? "Editar Puesto"
        : "Nuevo Puesto";

    if (id) {
        cargarDatosPuesto(id);
    }
    modal_puesto.show();
}

async function cargarDatosPuesto(id) {
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
        } else {
            alerta_top(data.tipo, data.mensaje);
        }
    } catch (error) {
        console.log("Error al obtener los datos:", error);
    }
}

function cerrarModelPuesto() {
    form_puesto.reset();
    modal_puesto.hide();
    vaciar_errores(errores_msj);
}

btn_guardar_puesto.addEventListener("click", async () => {
    let datos = Object.fromEntries(new FormData(form_puesto).entries());
    let url = datos.puesto_id
        ? rutas.actualizar.replace(":id", datos.puesto_id)
        : rutas.crear;
    let metodo = datos.puesto_id ? "PUT" : "POST";
    validar_boton(true, "Validando . . .", btn_guardar_puesto);

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
                listar_puesto();
                cerrarModelPuesto();
            }
        }
        validar_boton(false, "Guardar", btn_guardar_puesto);
    } catch (error) {
        console.log("Ocurrió un error:", error);
    }
});

async function listar_puesto() {
    try {
        let respuesta = await fetch(rutas.listar, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": token,
            },
        });
        let data = await respuesta.json();
        puesto_tabla(data);
    } catch (error) {
        console.log("Error al obtener los datos:", error);
    }
}

function puesto_tabla(data) {
    $("#tabla_puesto").DataTable({
        responsive: true,
        data: data,
        columns: [
            { data: null, render: (data, type, row, meta) => meta.row + 1 },
            { data: "nombre", className: "table-td" },
            {
                data: null,
                className: "table-td",
                render: (data, type, row) => `
                    <div class="form-check form-switch form-switch-dark">
                        <input class="form-check-input" onclick="estado_puesto('${ row.id }')" type="checkbox"  id="customSwitchDark" ${ row.estado === "activo" ? "checked" : "" }>
                    </div>
                `,
            },
            {
                data: null,
                className: "table-td",
                render: (data, type, row) => `
                    <button class="btn rounded-pill btn-sm btn-warning p-0.5" onclick="abrirModalPuesto('${row.id}')">
                        <i class="las la-pen fs-18"></i>
                    </button>
                    <button class="btn rounded-pill btn-sm btn-danger p-0.5" onclick="eliminarPuesto('${row.id}')">
                        <i class="las la-trash-alt fs-18"></i>
                    </button>
                `,
            },
        ],
        destroy: true,
    });
}

listar_puesto();

async function estado_puesto(id) {
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
                listar_puesto();
            } catch (error) {
                console.log("Error al cambiar el estado:", error);
            }
        }
    });
}

async function eliminarPuesto(id) {
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
                listar_puesto();
            } catch (error) {
                console.log("Error al eliminar:", error);
            }
        }
    });
}
