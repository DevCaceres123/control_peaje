<?php

namespace App\Http\Requests\Color;

use App\Http\Requests\BasePrincipalRequest;
use Illuminate\Foundation\Http\FormRequest;

class UpdateColorRequest extends BasePrincipalRequest
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
        $color_id = $this->route('color');
        return [
            'nombre'    => 'required|unique:colores,nombre,'.$color_id,
            'color'     => 'required'
        ];
    }

    //para los mensajes
    public function messages()
    {
        return [
            'nombre.required'   =>'El campo nombre es requerido',
            'nombre.unique'     => 'El campo nombre debe ser unico',
            'color.required'    => 'El campo color es requerido'
        ];
    }
}
