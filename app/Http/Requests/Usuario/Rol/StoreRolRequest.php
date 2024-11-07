<?php

namespace App\Http\Requests\Usuario\Rol;

use App\Http\Requests\BasePrincipalRequest;
use Illuminate\Foundation\Http\FormRequest;

class StoreRolRequest extends BasePrincipalRequest
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
            'rol' => 'required|unique:roles,name|string|max:100',
        ];
    }

    public function messages()
    {
        return [
            'rol.required'  => 'El nombre del rol es obligatorio',
            'rol.unique'    => 'El nombre del rol debe ser unico',
            'rol.string'    => 'El debe ser letras',
            'rol.max'       => 'El limite es 100 de caracteres ',
        ];

    }
}
