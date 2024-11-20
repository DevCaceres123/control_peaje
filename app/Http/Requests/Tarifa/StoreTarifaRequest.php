<?php

namespace App\Http\Requests\Tarifa;

use App\Http\Requests\BasePrincipalRequest;
use Illuminate\Foundation\Http\FormRequest;

class StoreTarifaRequest extends BasePrincipalRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nombre'        => 'required|unique:tarifas,nombre',
            'precio'        => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/', // Permite decimales con hasta 2 dígitos
            'descripcion'   => 'required',
        ];
    }

    public function messages()
    {
        return[
            'nombre.required'       =>'El campo nombre es requerido',
            'nombre.unique'         => 'El nombre debe ser unico',

            'precio.required'       =>'El precio es requerido',
            'precio.regex'          => 'El precio debe ser un número con hasta 1 dígito antes del punto decimal y hasta 2 dígitos después.',

            'descripcion.required'  => 'El campo descripcion es requerido',
        ];
        
    }
}
