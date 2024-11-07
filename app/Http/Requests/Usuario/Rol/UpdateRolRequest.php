<?php

namespace App\Http\Requests\Usuario\Rol;

use App\Http\Requests\BasePrincipalRequest;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRolRequest extends BasePrincipalRequest
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
        //dd($this->route());
        $rolId = $this->route('role');
        return [
            'rol' => 'required|string|max:100|unique:roles,name,'.$rolId,
        ];
    }

    public function messages()
    {
        return [
            'rol.required'  => 'El nombre del rol es obligatorio',
            'rol.unique'    => 'El nombre del rol debe ser único',
            'rol.string'    => 'El nombre debe contener solo letras',
            'rol.max'       => 'El límite es de 100 caracteres',
        ];
    }
}
