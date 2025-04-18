<?php

namespace App\Http\Requests\Usuario\User;

use App\Http\Requests\BasePrincipalRequest;
use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends BasePrincipalRequest
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
            'ci'        => 'required|unique:users,ci',
            'nombres'   => 'required|string',
            'apellidos' => 'required|string',
            'email'     => 'required|email',
            'roles'     => 'required|exists:roles,id',
            'usuario'   => 'required|unique:users,usuario',
            'password'  => 'required',
        ];
    }

    //para los mensajes
    public function messages(): array
    {
        return [
            'ci.required'        => 'El campo ci es requerido',
            'ci.unique'          => 'El ci debe ser unico ya hay un registro con el mismo ci',
            'nombres.required'   => 'El campo nombre es requerido',
            'email.required'     => 'El campo email es requerido',
            'email.email'        => 'El campo email debe ser un correo valido',
            'apellidos.required' => 'El campo apellido es requerido',
            'usuario.required'   => 'El campo usuario es obligatorio',
            'usuario.unique'     => 'El campo usuario debe ser unico',
            'password.required'  => 'El campo contraseña es obligatorio',
        ];
    }
}
