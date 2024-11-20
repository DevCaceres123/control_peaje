<?php

namespace App\Http\Requests\TipoVehiculo;

use App\Http\Requests\BasePrincipalRequest;
use Illuminate\Foundation\Http\FormRequest;

class StoreTipoVehiculoRequest extends BasePrincipalRequest
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
            'nombre'=>'required|unique:tipo_vehiculos,nombre'
        ];
    }
    //para la parte de los mensajes
    
    public function messages()
    {
        return [
            'nombre.required'=>'El campo nombre es requerido',
            'nombre.unique'=>'El campo nombre debe ser unico, ya existe uno con el mismo nombre'
        ];
    }
}
