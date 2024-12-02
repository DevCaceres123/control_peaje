<?php

namespace App\Http\Requests\Puestos_asignar;

use App\Http\Requests\BasePrincipalRequest;
use Illuminate\Foundation\Http\FormRequest;

class PuestoRequest extends BasePrincipalRequest
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
            case 'puesto_asignar.store':
                return [
                    'puesto_id' => 'required|exists:puestos,id|integer',
                    'encargado' => 'required|exists:users,id|integer',
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
}
