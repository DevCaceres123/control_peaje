<?php

namespace App\Http\Requests\Usuario\Permiso;

use App\Http\Requests\BasePrincipalRequest;
use Illuminate\Foundation\Http\FormRequest;

class StorePermisoRequest extends BasePrincipalRequest
{
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
            'permiso' => [
                'required',
                'unique:permissions,name',
                'regex:/^[\p{L}0-9.]+$/u', // Allows letters, numbers, and periods
                'min:3',
                'max:50',
                'not_regex:/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/i',
            ],
        ];
    }

    public function messages()
    {
        return [
            'permiso.required' => 'El campo permiso es requerido.',
            'permiso.unique'   => 'Lo que ingresó ya existe en el registro de los permisos.',
            'permiso.regex'    => 'El campo permiso solo puede contener letras, números y puntos.',
            'permiso.min'      => 'El campo permiso debe tener al menos :min caracteres.',
            'permiso.max'      => 'El campo permiso no puede tener más de :max caracteres.',
            'permiso.not_regex' => 'El campo permiso no debe contener scripts o etiquetas HTML.',
        ];
    }
}
