<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class BasePrincipalRequest extends FormRequest
{
    //PARA EL ENVIO COMO RESPUESTA DE TIPO JSON
    protected function failedValidation(Validator $validator)
    {
        throw (new ValidationException($validator, response()->json([
            'tipo' => 'errores',
            'mensaje' => $validator->errors(),
        ], 422)));
    }
}
