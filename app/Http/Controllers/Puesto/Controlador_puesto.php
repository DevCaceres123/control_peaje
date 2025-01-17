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
use PhpParser\Node\Stmt\Return_;
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

        if (!auth()->user()->can('puesto.asignar.inicio')) {
            return redirect()->route('inicio');
        }

        $fecha_actual = $this->fecha->toDateString();

        //listar puestos y quienes estan asignados
        $puestos = Puesto::with(['users' => function ($query) use ($fecha_actual) {
            $query->select('users.id', 'nombres', 'apellidos')
                ->where('historial_puesto.estado', 'activo'); // verificamos si el usuario esta usando el puesto
        }])
            ->select('id', 'nombre')
            ->where('estado', 'activo')
            ->get();

    
        // me filtra todos los usuarios que no tienen registros en la fecha actual y que su estado no en activo
       // $encargados_sin_registro = User::select('id', 'nombres', 'apellidos')
        //    ->role('encargado_puesto')
        //    ->whereDoesntHave('puestos', function ($query) use ($fecha_actual) {
         //       $query->whereDate('historial_puesto.created_at', '=', $fecha_actual)
         //       ->where('historial_puesto.estado', 'activo');
         //   })
         //   ->where('estado','activo')
         //   ->get();


        // me filtra todos los usuarios que no tienen registros en la fecha actual y que su estado no en activo
        $encargados_sin_registro = User::select('id', 'nombres', 'apellidos')
            ->role('encargado_puesto')
            ->whereDoesntHave('puestos', function ($query) use ($fecha_actual) {
                $query ->where('historial_puesto.estado', 'activo');               
            })
            ->where('estado','activo')
            ->get();


        return view("administrador.puestos.asignar_puesto", compact('puestos', 'encargados_sin_registro'));
    }

    // redirigue a la vista de historial
    public function historial()
    {
        return view("administrador.puestos.historial");
    }

    // listar el historial de los puestos

    public function listar_historial(Request $request)
    {

        if (!auth()->user()->can('puesto.historial.inicio')) {
            return redirect()->route('inicio');
        }
        // Obtén la fecha del request o usa la fecha actual como predeterminada
        $fecha_actual = $request->input('fecha') ? Carbon::parse($request->input('fecha'))->toDateString() : null;

        // Inicia la consulta con los usuarios y sus puestos
        $usuariosQuery = User::with(['puestos' => function ($query) use ($fecha_actual) {
            if ($fecha_actual) {
                $query->whereDate('historial_puesto.created_at', '=', $fecha_actual);
            }
        }])->role('encargado_puesto');

        // Filtro de búsqueda
        if (!empty($request->search['value'])) {
            $usuariosQuery->where(function ($query) use ($request) {
                $query->where('nombres', 'like', '%' . $request->search['value'] . '%')
                    ->orWhere('apellidos', 'like', '%' . $request->search['value'] . '%')
                    ->orWhere('ci', 'like', '%' . $request->search['value'] . '%')
                    ->orWhere('email', 'like', '%' . $request->search['value'] . '%');
            });
        }

        // Total de registros antes del filtrado
        $recordsTotal = User::role('encargado_puesto')->count();

        // Total de registros filtrados
        $recordsFilter = $usuariosQuery->count();

        // Aplicar paginación y obtener datos
        $usuarios = $usuariosQuery
            ->skip($request->start)
            ->take($request->length)
            ->get();

        // Transformar datos
        $datos_usuarios = $usuarios->flatMap(function ($usuario) {
            return $usuario->puestos->map(function ($puesto) use ($usuario) {
                return [
                    'ci' => $usuario->ci,
                    'nombres' => $usuario->nombres . " " . $usuario->apellidos,
                    'puesto_nombre' => $puesto->nombre,
                    'estado'=>$puesto->pivot->estado,
                    'fecha_asignado' => Carbon::parse($puesto->pivot->created_at)
                        ->locale('es') // Establecer idioma a español
                        ->translatedFormat('d \d\e F \d\e Y H:i:s'), // Fecha con mes en español
                    'fecha_terminado' => Carbon::parse($puesto->pivot->updated_at)
                        ->locale('es') // Establecer idioma a español
                        ->translatedFormat('d \d\e F \d\e Y H:i:s'),
                    'fecha_asignado_raw' => $puesto->pivot->created_at, // Fecha en formato crudo para ordenar
                ];
            });
        })->sortByDesc('fecha_asignado_raw')->values(); // Ordena y reindexa

        // Permisos para el frontend
        $permissions = [
            'desactivar' => auth()->user()->can('admin.usuario.desactivar'),
            'reset' => auth()->user()->can('admin.usuario.reset'),
            'editarRol' => auth()->user()->can('admin.usuario.editarRol'),
            'editarTargeta' => auth()->user()->can('admin.usuario.editarTargeta'),
        ];

        // Retorna el JSON
        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFilter,
            'usuarios' => $datos_usuarios,
            'permissions' => $permissions,
        ]);
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

                       
            // Agregamos un usuario a un puesto con el estado "activo"
            $user->puestos()->attach($puesto_id, [  
                'estado' => 'activo', // Asignamos el estado
            ]);


            DB::commit();

            $this->mensaje('exito', "Adicionado correctamente....");
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
            ->where('estado', 'activo') 
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

           

            $fecha_actual = $this->fecha->toDateString();

            DB::table('historial_puesto')
                ->where('puesto_id', $id)
                ->where('estado', 'activo') 
                ->update([
                    'estado' => 'inactivo',
                    'updated_at' => now(),
                ]);

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
