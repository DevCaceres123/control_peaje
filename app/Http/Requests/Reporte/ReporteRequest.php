<?php

namespace App\Http\Requests\Reporte;

use App\Http\Requests\BasePrincipalRequest;
use Illuminate\Foundation\Http\FormRequest;

class ReporteRequest extends BasePrincipalRequest
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
            case 'reportes.store':
                return [
                    'fecha_inicio' => 'required|date',
                    'fecha_final' => 'required|date',
                    'encargado' => 'required|exists:users,id|integer',
                ];
            case 'reportes.update':
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
