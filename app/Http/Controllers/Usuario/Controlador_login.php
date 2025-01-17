<?php

namespace App\Http\Controllers\Usuario;

use App\Http\Controllers\Controller;
use App\Http\Requests\Login\UsuarioRequest;
use App\Models\HistorialPuesto;
use App\Models\HistorialRegistros;
use App\Models\Puesto;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use function Laravel\Prompts\select;

class Controlador_login extends Controller
{
    /**
     * @version 1.0
     * @author  Rodrigo Lecoña Quispe <rodrigolecona97@gmail.com>
     * @param Controlador Administrar la parte de usuario resgistrados LOGIN
     * ¡Muchas gracias por preferirnos! Esperamos poder servirte nuevamente
     */

    /**
     * PARA EL INGRESO DEL USUARIO POR USUARIO Y CONTRASEÑA
     */
    private $mensajeError = 'Usuario o contraseña inválidos';

    public function ingresar(Request $request)
    {
        if ($this->validarDatos($request)->fails()) {
            return $this->respuestaError('Todos los campos son requeridos');
        }

        $usuario = $this->buscarUsuario($request->usuario);

        if (!$usuario) {
            return $this->respuestaError($this->mensajeError);
        }

        if ($this->autenticarUsuario($request)) {
            return $this->respuestaExitosa('Inicio de sesión con éxito');
        }

        return $this->respuestaError($this->mensajeError);
    }

    private function validarDatos(Request $request)
    {
        return Validator::make($request->all(), [
            'usuario' => 'required',
            'password' => 'required',
        ]);
    }

    private function buscarUsuario($usuario)
    {
        return User::where('usuario', $usuario)->first();
    }

    private function autenticarUsuario(Request $request)
    {
        $credenciales = [
            'usuario' => $request->usuario,
            'password' => $request->password,
            'estado' => 'activo',
        ];

        if (Auth::attempt($credenciales)) {
            $request->session()->regenerate();
            return true;
        }

        return false;
    }

    private function respuestaExitosa($mensaje)
    {
        return response()->json(mensaje_mostrar('success', $mensaje));
    }

    private function respuestaError($mensaje)
    {
        return response()->json(mensaje_mostrar('error', $mensaje));
    }
    /**
     * FIN PARA EL INGRESO DEL USUARIO Y CONTRASEÑA
     */

    /**
     * PARA INGRESAR AL INICIO
     */
    public function inicio()
    {
        $fecha_actual = Carbon::now()->toDateString();

        $puestos_usuario = $this->obtenerPuestoUsuario($fecha_actual);
        $monto_puesto = $this->obtenerMontoDelDia($fecha_actual);

        $fecha_actual = Carbon::now();
        $fecha_parseada = $fecha_actual->translatedFormat('j \d\e F \d\e Y');
        return view('inicio', compact('puestos_usuario', 'monto_puesto', 'fecha_parseada'));
    }

    // obtenemos todos los puestos y que ususarios estan asiganados en la fecha actual
    public function obtenerPuestoUsuario($fecha_actual)
    {
        return $puestos = Puesto::select('id', 'nombre')
            ->with([
                'users' => function ($query) use ($fecha_actual) {
                    $query->select('users.id', 'nombres', 'apellidos')
                    ->where('historial_puesto.estado', 'activo');
                },
            ])
            ->get();
    }

    // obtenemos los montos de los respecitvos puestos
    public function obtenerMontoDelDia($fecha_actual)
    {
        $puestos = Puesto::select('id', 'nombre')->get();
        return HistorialRegistros::select('puesto', DB::raw('COUNT(*) as total_registros'), DB::raw('SUM(precio) as total_precio'))
            ->whereIn('puesto', $puestos->pluck('nombre')) // Filtrar por nombres de los puestos
            ->whereDate('created_at', '=', $fecha_actual) // Filtrar por la fecha actual
            ->groupBy('puesto') // Agrupar por el campo 'puesto'
            ->get();
    }
    /**
     * FIN PARA INGRESAR AL INICIO
     */

    /**
     * CERRAR LA SESSIÓN
     */
    public function cerrar_session(Request $request)
    {

        $this->desvincularPuesto();
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $data = mensaje_mostrar('success', 'Finalizó la session con éxito!');
        return response()->json($data);
    }

    public function desvincularPuesto()
    {
        $usuario_actual = auth()->user()->id;

        $fecha_actual=Carbon::now()->format('Y-m-d');
        DB::table('historial_puesto')
        ->where('usuario_id', $usuario_actual)
        ->whereDate('created_at', $fecha_actual)
        ->where('estado', 'activo')
        ->update([
            'estado' => 'inactivo', // Cambia el estado
            'updated_at' => now()   // Actualiza el timestamp
        ]);
    
        
    }
    /**
     * FIN DE CERRAR LA SESSIÓN
     */
}
