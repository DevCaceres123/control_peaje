<?php

namespace App\Http\Requests\Perfil;

use App\Http\Requests\BasePrincipalRequest;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePasswordRequest extends BasePrincipalRequest
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
    // Form Request Validation Rules
    public function rules()
    {
        return [
            'password_actual' => ['required', 'string'],
            'password_nuevo' => [
                'required',
                'string',
                //Password::min(8)->mixedCase()->numbers()->symbols(), // Puedes ajustar las reglas
            ],
            'password_confirmar' => ['required', 'same:password_nuevo'],
        ];
    }

    public function messages()
    {
        return [
            'password_actual.required'      => 'Debes ingresar tu contraseña actual.',
            'password_nuevo.required'       => 'La nueva contraseña es obligatoria.',
            //'password_nuevo.min'            => 'La nueva contraseña debe tener al menos :min caracteres.',
            'password_confirmar.required'   => 'Debes confirmar la nueva contraseña.',
            'password_confirmar.same'       => 'La confirmación de la contraseña no coincide con la nueva contraseña.',
        ];
    }

}
