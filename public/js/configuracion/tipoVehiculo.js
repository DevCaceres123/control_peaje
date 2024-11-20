let modal_tipoVehiculo        = new bootstrap.Modal(document.getElementById("modal_tipoVehiculo"));
let form_tipoVehiculo         = document.getElementById("form_tipoVehiculo");
let btn_guardar_tipoVehiculo  = document.getElementById("btn_guardar_tipoVehiculo");
let errores_msj = ["_nombre"];

function abrirModalTipoVehiculo(id = null) {
    vaciar_errores(errores_msj);
    form_tipoVehiculo.reset();
    document.getElementById("tipoVehiculo_id").value = id;
    document.getElementById("tipoVehiculoModalLabel").textContent = id
        ? "Editar Tarifa"
        : "Nuevo Tarifa";

    if (id) {
        cargarDatosTipoVehiculo(id);
    }
    modal_tipoVehiculo.show();
}


async function cargarDatosTipoVehiculo(id) {
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
            document.getElementById("nombre").value         = data.mensaje.nombre;
        } else {
            alerta_top(data.tipo, data.mensaje);
            modal_tipoVehiculo.hide();
        }
    } catch (error) {
        console.log("Error al obtener los datos:", error);
    }
}

function cerrarModelTipoVehiculo() {
    form_tipoVehiculo.reset();
    modal_tipoVehiculo.hide();
    vaciar_errores(errores_msj);
}

btn_guardar_tipoVehiculo.addEventListener("click", async () => {
    let datos = Object.fromEntries(new FormData(form_tipoVehiculo).entries());
    let url = datos.tipoVehiculo_id
        ? rutas.actualizar.replace(":id", datos.tipoVehiculo_id)
        : rutas.crear;
    let metodo = datos.tipoVehiculo_id ? "PUT" : "POST";
    validar_boton(true, "Validando . . .", btn_guardar_tipoVehiculo);

    //try {
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
                listar_tipoVehiculo();
                cerrarModelTipoVehiculo();
            }
        }
        validar_boton(false, "Guardar", btn_guardar_tipoVehiculo);
    /* } catch (error) {
        console.log("Ocurrió un error:", error);
    } */
});

async function listar_tipoVehiculo() {
    try {
        let respuesta = await fetch(rutas.listar, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": token,
            },
        });
        let data = await respuesta.json();
        tipoVehiculo_tabla(data);
    } catch (error) {
        console.log("Error al obtener los datos:", error);
    }
}

function tipoVehiculo_tabla(data) {
    $("#tabla_tipoVehiculo").DataTable({
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
                        <input class="form-check-input" onclick="estado_tipoVehiculo('${ row.id }')" type="checkbox"  id="customSwitchDark" ${ row.estado === "activo" ? "checked" : "" }>
                    </div>
                `,
            },
            {
                data: null,
                className: "table-td",
                render: (data, type, row) => `
                    <button class="btn rounded-pill btn-sm btn-warning p-0.5" onclick="abrirModalTipoVehiculo('${row.id}')">
                        <i class="las la-pen fs-18"></i>
                    </button>
                    <button class="btn rounded-pill btn-sm btn-danger p-0.5" onclick="eliminarTipoVehiculo('${row.id}')">
                        <i class="las la-trash-alt fs-18"></i>
                    </button>
                `,
            },
        ],
        destroy: true,
    });
}

listar_tipoVehiculo();

async function estado_tipoVehiculo(id) {
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
                listar_tipoVehiculo();
            } catch (error) {
                console.log("Error al cambiar el estado:", error);
            }
        }else{
            listar_tipoVehiculo();
        }
    });
}

async function eliminarTipoVehiculo(id) {
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
                listar_tipoVehiculo();
            } catch (error) {
                console.log("Error al eliminar:", error);
                listar_tipoVehiculo();
            }
        }else{
            listar_tipoVehiculo();
        }
    });
}
