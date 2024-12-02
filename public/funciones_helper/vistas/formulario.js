export function mensajeInputs(mensaje, color, campo) {

    $(`#error_${campo} spam b`).val = "";
    $(`#error_${campo} spam b`).html(mensaje).css("color", color);
}


export function vaciar_errores(nombre_formulario) {

    
    // Seleccionar el formulario
    const form = document.getElementById(nombre_formulario);
    // console.log(form);
    // Seleccionar solo los campos de tipo input, textarea y select
    const elements = form.querySelectorAll("input, textarea, select");
    const fieldNames = Array.from(elements).map(element => element.name);

    fieldNames.forEach(element => {
        document.getElementById("_" + element).innerHTML = '';
    });
}


//para vaciar los input, textrarea y select
export function vaciar_formulario(formulario) {
   
    document.getElementById(formulario).reset();
}