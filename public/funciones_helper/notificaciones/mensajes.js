// Define las funciones de notificación utilizando SweetAlert
const notificaciones = {
  'exito': (mensaje = "", titulo = "") => {
    Swal.mixin({
      toast: !0,
      position: "top-end",
      showConfirmButton: !1,
      timer: 1500,
      timerProgressBar: !0,
      didOpen: e => {
        e.addEventListener("mouseenter", Swal.stopTimer), e.addEventListener("mouseleave", Swal
          .resumeTimer)
      }
    }).fire({
      icon: "success",
      title: mensaje,
    })
  },
  'error': (mensaje = "", titulo = "") => {
    Swal.fire({
      position: "top-end",
      icon: 'error',
      title: titulo,
      text: mensaje,
      showConfirmButton: false,
      timer: 1800,
    });
  },
  'warning': (mensaje = "", titulo = "") => {
    Swal.fire({
      position: "top-end",
      icon: 'warning',
      title: titulo,
      text: mensaje,
      showConfirmButton: false,
      timer: 1800,
    });
  },

  'error_validacion': (mensaje = "", titulo = "") => {

    Command: toastr["error"](mensaje);
  },
  'errores': (obj) => {

    let isValid = true; // Bandera para verificar si todos los campos están correctos.

    for (let key in obj) {
      let element = document.getElementById('_' + key);

      if (element) { // Verifica si el elemento existe
        element.innerHTML = `<p class="text-danger">${obj[key]}</p>`;
      } else {
        console.warn(`El campo con id '_${key}' no existe.`);
        isValid = false; // Marca como no válido si falta algún campo
      }
    }

    // Si quieres hacer algo solo si todos los campos son válidos
    // if (isValid) {
    //   console.log("Todos los campos son correctos.");
    // } else {
    //   console.log("Faltan algunos campos o tienen errores.");
    // }

  }
  // Puedes agregar más tipos según sea necesario
};


export function mensajeAlerta(mensaje = "", titulo = "") {

  if (notificaciones.hasOwnProperty(titulo)) {
    notificaciones[titulo](mensaje, titulo);
  }





}


