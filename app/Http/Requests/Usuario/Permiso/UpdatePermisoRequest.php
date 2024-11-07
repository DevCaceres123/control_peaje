<?php

namespace App\Http\Requests\Usuario\Permiso;

use App\Http\Requests\BasePrincipalRequest;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePermisoRequest extends BasePrincipalRequest
{
    /**
     * Determina si el usuario está autorizado para hacer esta solicitud.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Reglas de validación que se aplican a la solicitud.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // verifica dd($this->route()) ;
        $permisoId = $this->route('permiso');
        return [
            'permiso' => [
                'required',
                'regex:/^[\p{L}0-9.]+$/u', // Permite letras, números y puntos
                'min:3',
                'max:50',
                'not_regex:/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/i', // Previene inyección de scripts
                'unique:permissions,name,' . $permisoId, // Ignora el registro actual en la verificación de unicidad
            ],
        ];
    }

    /**
     * Mensajes personalizados de validación.
     */
    public function messages()
    {
        return [
            'permiso.required' => 'El campo permiso es requerido.',
            'permiso.unique'   => 'El permiso que ingresó ya existe.',
            'permiso.regex'    => 'El campo permiso solo puede contener letras, números y puntos.',
            'permiso.min'      => 'El campo permiso debe tener al menos :min caracteres.',
            'permiso.max'      => 'El campo permiso no puede tener más de :max caracteres.',
            'permiso.not_regex' => 'El campo permiso no debe contener scripts o etiquetas HTML.',
        ];
    }
}
