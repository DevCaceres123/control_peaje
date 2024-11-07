<?php

namespace App\Http\Controllers\Usuario;

use App\Http\Controllers\Controller;
use App\Http\Requests\Perfil\UpdatePasswordRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class Controlador_usuario extends Controller
{
    /**
     * PARA LA PARTE DEL PERFIL
     */
    public function perfil()
    {
        return view('administrador.perfil');
    }
    /**
     * FIN DE LA PARTE DE PERFIL
     */

    /**
     * PARA GUARDAR NUEVA CONTRASEÑA
     */
    public function password_guardar(UpdatePasswordRequest $request){
        // Inicia la transacción
        DB::beginTransaction();
        try {
            $user = User::find(Auth::user()->id);

            // Verifica la contraseña actual
            if (!Hash::check($request->password_actual, $user->password)) {
                return response()->json(mensaje_mostrar('error', 'La contraseña actual no es correcta.'), 403);
            }

            // Verifica que las nuevas contraseñas coincidan
            if ($request->password_nuevo !== $request->password_confirmar) {
                return response()->json(mensaje_mostrar('error', 'Las contraseñas nuevas no coinciden.'), 422);
            }

            // Actualiza la contraseña
            $user->password = Hash::make($request->password_nuevo);
            $user->save();

            // Confirma la transacción
            DB::commit();

            return response()->json(mensaje_mostrar('success', 'Contraseña actualizada correctamente.'));
        } catch (\Exception $e) {
            // Si ocurre un error, deshacer todos los cambios realizados en la transacción
            DB::rollBack();
            return response()->json(mensaje_mostrar('error', 'Ocurrió un error al actualizar la contraseña.'), 500);
        }
    }
    /**
     * FIN DE LA PARTE DE NUEVA CONTRASEÑA
     */
}
