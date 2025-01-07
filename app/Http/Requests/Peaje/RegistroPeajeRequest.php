<?php

namespace App\Http\Requests\peaje;

use App\Http\Requests\BasePrincipalRequest;
use Illuminate\Foundation\Http\FormRequest;

class RegistroPeajeRequest extends BasePrincipalRequest
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
        $routeName = $this->route()->getName();

        switch ($routeName) {
            case 'peaje.store':
                return [
                    'ci' => 'nullable|min:6|max:40',
                    'ap_paterno' => 'nullable|min:3|max:20|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
                    'ap_materno' => 'nullable|min:3|max:20|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
                    'nombres' => 'nullable|min:3|max:60|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
                    'id_tipo_veh' => 'nullable|min:1|exists:tipo_vehiculos,id',
                    'id_color' => 'nullable|min:1|exists:colores,id',
                    'placa' => 'nullable|min:3|max:40|regex:/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\-]+$/',

                ];
            case 'puesto_asignar.update':
                // return [
                //     'id_usaurio' => 'required|integer',
                //     'estado' => 'required|string|in:activo,inactivo',
                //     // Más reglas según sea necesario
                // ];
            default:
                return [];
        }
    }


    public function messages()
    {
        return [
            'ap_paterno.regex' => 'El campo  Paterno solo puede contener letras.',
            'ap_materno.regex' => 'El campo  Materno solo puede contener letras.',
            'nombres.regex' => 'El campo  Nombres solo puede contener letras.',
            'placa.regex' => 'El campo Placa solo puede contener letras y guiones, sin espacios.',
        ];
    }
}
