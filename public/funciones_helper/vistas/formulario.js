export function mensajeInputs(mensaje, color, campo) {

    $(`#error_${campo} spam b`).val = "";
    $(`#error_${campo} spam b`).html(mensaje).css("color", color);
}


export function vaciar_errores(nombre_formulario) {


    // Seleccionar el formulario
    const form = document.getElementById(nombre_formulario);
    console.log(form);

    // Seleccionar solo los campos de tipo input, textarea y select, excluyendo input[type="hidden"]
    const elements = form.querySelectorAll("input:not([type='hidden']), textarea, select");

    // Obtener los nombres de los campos
    const fieldNames = Array.from(elements).map(element => element.name);

    // Limpiar los mensajes de error de cada campo
    fieldNames.forEach(element => {
        const errorElement = document.getElementById("_" + element);
        if (errorElement) { // Verifica si el elemento existe
            errorElement.innerHTML = '';
        }
    });

}


//para vaciar los input, textrarea y select
export function vaciar_formulario(formulario) {

    document.getElementById(formulario).reset();
}