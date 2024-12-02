<?php

namespace App\Http\Controllers\Puesto;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\Puestos_asignar\PuestoRequest;
use App\Models\Puesto;
use App\Models\Registro;
use App\Models\User;
use Carbon\Carbon;
use Exception;

use Illuminate\Http\Request;
use PhpParser\Node\Expr\Cast\String_;
use PhpParser\Node\Stmt\TryCatch;

use function Laravel\Prompts\select;

class Controlador_puesto extends Controller
{
    public $mensaje = [];
    public $fecha;

    public function __construct()
    {
        // Asignar la fecha actual a la propiedad pública
        $this->fecha = Carbon::now();
    }

    public function index()
    {

        $fecha_actual = $this->fecha->toDateString();

        $puestos = Puesto::with(['users' => function ($query) use ($fecha_actual) {
            $query->select('users.id', 'nombres', 'apellidos')
                ->whereDate('historial_puesto.created_at', '=', $fecha_actual); // Usamos whereDate para comparar solo la fecha
        }])
            ->select('id', 'nombre')
            ->where('estado', 'activo')
            ->get();


        $encargados_sin_registro = User::select('id', 'nombres', 'apellidos')
            ->role('encargado_puesto')
            ->whereDoesntHave('puestos', function ($query) use ($fecha_actual) {
                $query->whereDate('historial_puesto.created_at', '=', $fecha_actual);
            })
            ->get();


        return view("administrador.puestos.asignar_puesto", compact('puestos', 'encargados_sin_registro'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */

    // DESIGNAMOS UN USUARIO A UN PUESTO
    public function store(PuestoRequest $request)
    {
        try {
            $encargado_id = $request->encargado;
            $puesto_id = $request->puesto_id;
            $user = User::find($encargado_id);

            if (!$user->hasRole('encargado_puesto')) {
                throw new Exception('el usuario seleccionado no tiene el rol de encargado de puesto');
            }

            DB::beginTransaction();


            $resultado = $this->varficarRegistro($encargado_id);
            if ($resultado) {
                throw new Exception('el usuario seleccionado ya tiene un puesto seleccionado');
            }

            // agregamos un usuario a un puesto
            $user->puestos()->attach($puesto_id);


            DB::commit();

            $this->mensaje('exito', "El encargado fue adicionado a un puesto correctamente");
            return response()->json($this->mensaje, 200);
        } catch (Exception $e) {
            DB::rollBack();
            $this->mensaje("error", "Error " . $e->getMessage());

            return response()->json($this->mensaje, 200);
        }
    }

    // verificamos que no se pueda registrar un usuario dos veces en un puesto el mismo dia
    public function varficarRegistro($encargado_id)
    {
        $fecha_actual = $this->fecha->toDateString();


        return  DB::table('historial_puesto')
            ->where('usuario_id', $encargado_id)
            ->whereDate('created_at', $fecha_actual)
            ->first();
    }



    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id) {}

    // Se elimina el registro de un puesto
    public function destroy(string $id)
    {

        try {

            DB::beginTransaction();

            $puesto = Puesto::find($id);
            if (!$puesto) {
                throw new Exception('el puesto seleccionado no existe');
            }

            $resultado = $this->buscarRegistrosPuesto($id);

            if ($resultado) {
                throw new Exception('el puesto seleccionado ya tiene registros en el historial por lo cual no se puede cambiar');
            }

            $fecha_actual = $this->fecha->toDateString();

            DB::table('historial_puesto')
                ->where('puesto_id', $id)
                ->whereDate('created_at', $fecha_actual)
                ->delete();
            
            DB::commit();

            $this->mensaje('exito', "El puesto fue desvinculado correctamente");
            return response()->json($this->mensaje, 200);
        } catch (Exception $e) {



            DB::rollBack();
            $this->mensaje("error", "Error " . $e->getMessage());

            return response()->json($this->mensaje, 200);
        }
    }

    // Se buscara si existen registros en la fecha actual de este puesto
    public function buscarRegistrosPuesto($id_puesto)
    {

        $fecha_actual = $this->fecha->toDateString();
        return Registro::select('id')
            ->where('puesto_id', $id_puesto)
            ->whereDate('created_at', $fecha_actual)
            ->first();
    }

    //  SE BUSCARA ENCARGADO PARA ADICIONAR A UN PUESTO
    public function buscar_encargado(string $ci_encargado)
    {

        try {

            $encargado = User::select('id', 'nombres', 'apellidos')
                ->where('ci', $ci_encargado)
                ->role('encargado_puesto')
                ->first();

            if (!$encargado) {
                throw new Exception('no encontrado');
            }
            $this->mensaje('exito', $encargado);
            return response()->json($this->mensaje, 200);
        } catch (Exception $e) {
            $this->mensaje("error", "error " . $e->getMessage());

            return response()->json($this->mensaje, 200);
        }
    }


    // MENSAJES PARA ENVIARLOS DE RESPUESTA
    public function mensaje($titulo, $mensaje)
    {

        $this->mensaje = [
            'tipo' => $titulo,
            'mensaje' => $mensaje
        ];
    }




    public function base(string $id)
    {

        try {

            DB::beginTransaction();





            DB::commit();

            $this->mensaje('exito', "El encargado fue adicionado a un puesto correctamente");
            return response()->json($this->mensaje, 200);
        } catch (Exception $e) {

            DB::rollBack();
            $this->mensaje("error", "Error " . $e->getMessage());

            return response()->json($this->mensaje, 200);
        }
    }
}
