let modal_color = new bootstrap.Modal(document.getElementById("modal_color"));
let form_color = document.getElementById("form_color");
let btn_guardar_color = document.getElementById("btn_guardar_color");
let errores_msj = ["_nombre", "_color"];

function abrirModalColor(id = null) {
    vaciar_errores(errores_msj);
    form_color.reset();
    document.getElementById("color_id").value = id;
    document.getElementById("colorModalLabel").textContent = id
        ? "Editar Color"
        : "Nuevo Color";

    if (id) {
        cargarDatosColor(id);
    }
    modal_color.show();
}


async function cargarDatosColor(id) {
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
            document.getElementById("color").value = data.mensaje.color;
        } else {
            alerta_top(data.tipo, data.mensaje);
        }
    } catch (error) {
        console.log("Error al obtener los datos:", error);
    }
}

function cerrarModelColor() {
    form_color.reset();
    modal_color.hide();
    vaciar_errores(errores_msj);
}

btn_guardar_color.addEventListener("click", async () => {
    let datos = Object.fromEntries(new FormData(form_color).entries());
    let url = datos.color_id
        ? rutas.actualizar.replace(":id", datos.color_id)
        : rutas.crear;
    let metodo = datos.color_id ? "PUT" : "POST";
    validar_boton(true, "Validando . . .", btn_guardar_color);

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
                listar_colores();
                cerrarModelColor();
            }
        }
        validar_boton(false, "Guardar", btn_guardar_color);
    } catch (error) {
        console.log("Ocurrió un error:", error);
    }
});

async function listar_colores() {
    try {
        let respuesta = await fetch(rutas.listar, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": token,
            },
        });
        let data = await respuesta.json();
        color_tabla(data);
    } catch (error) {
        console.log("Error al obtener los datos:", error);
    }
}

function color_tabla(data) {
    $("#tabla_color").DataTable({
        responsive: true,
        data: data,
        columns: [
            { data: null, render: (data, type, row, meta) => meta.row + 1 },
            { data: "nombre", className: "table-td" },
            {
                data: null,
                className: "table-td",
                render: (data, type, row) => `
                    <div class="form-check form-switch" style="background-color: ${data.color};  border-radius: 5px; padding: 10px;   width: 60px; height: 30px;">
                    </div>
                `,
            },
            {
                data: null,
                className: "table-td",
                render: (data, type, row) => `
                    <button class="btn rounded-pill btn-sm btn-warning p-0.5" onclick="abrirModalColor('${row.id}')">
                        <i class="las la-pen fs-18"></i>
                    </button>
                    <button class="btn rounded-pill btn-sm btn-danger p-0.5" onclick="eliminarColor('${row.id}')">
                        <i class="las la-trash-alt fs-18"></i>
                    </button>
                `,
            },
        ],
        destroy: true,
    });
}

listar_colores();

async function estado_color(id) {
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
                listar_colores();
            } catch (error) {
                console.log("Error al cambiar el estado:", error);
            }
        }else{
            listar_colores();
        }
    });
}

async function eliminarColor(id) {
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
                listar_colores();
            } catch (error) {
                console.log("Error al eliminar:", error);
            }
        }else{
            listar_colores();
        }
    });
}
