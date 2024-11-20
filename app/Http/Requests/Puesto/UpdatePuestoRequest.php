<?php

namespace App\Http\Requests\Puesto;

use App\Http\Requests\BasePrincipalRequest;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePuestoRequest extends BasePrincipalRequest
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
        $puestoId = $this->route('puesto');
        return [
            'nombre'=>'required|unique:puestos,nombre,'.$puestoId,
        ];
    }

    //para los mensajes
    public function  messages()
    {
        return [
            'nombre.required'=>'El campo nombre es requerido',
            'nombre.unique'=>'El campo nombre debe ser unico',
        ];
    }
}
