<?php

namespace App\Http\Controllers\configuracion;

use App\Models\Empleado;
use App\Models\Usuario;
use App\Models\Cargo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

use Illuminate\Routing\Controller as BaseController;

class EmpleadoController extends BaseController
{
    private function authorizeEmployee()
    {
        $user = Auth::user();
        if (!$user || ($user->empleado && !$user->empleado->estadoEmpleado) || !$user->empleado) {
            abort(403, 'Acceso no autorizado. Solo empleados pueden acceder a esta página.');
        }
    }

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->authorizeEmployee();
            return $next($request);
        });
    }
    public function listaJson()
{
    $empleados = Empleado::with('user', 'cargo')->take(10)->get();
    return response()->json([
        'success' => true,
        'empleados' => $empleados->map(function ($empleado) {
            return [
                'idEmpleado' => $empleado->idEmpleado,
                'user_id' => $empleado->user_id,
                'nombre' => $empleado->user->nombre ?? '',
                'numero_documento' => $empleado->user->numero_documento ?? '',
                'idCargo' => $empleado->idCargo,
                'nomCargo' => $empleado->cargo->nomCargo ?? '',
                'estadoEmpleado' => $empleado->estadoEmpleado,
                'imagen' => $empleado->imagen ? asset('storage/' . $empleado->imagen) : null,
            ];
        }),
    ]);
}
        public function index()
    {
    $empleados = Empleado::with('user', 'cargo')->paginate(10);
    $cargos = Cargo::where('estadoCargo', true)->get();
    return view('configuracion.empleados', compact('empleados', 'cargos'));
    }
    public function show(Empleado $empleado)
    {
        $this->authorizeEmployee();
        $empleado->load('user', 'cargo');
        return view('configuracion.empleado_detalle', compact('empleado'));
    }

    public function buscarUsuario(Request $request)
    {
        Log::info('BuscarUsuario iniciado con buscar_user_id: ' . ($request->buscar_user_id ?? 'No proporcionado'));

        $request->validate([
            'buscar_user_id' => 'required|string|max:255',
        ]);

        try {
            $userId = trim($request->buscar_user_id);

            // Buscar usuarios que coincidan parcialmente con user_id y no estén asociados a empleados activos
            $usuarios = Usuario::where('user_id', 'LIKE', '%' . $userId . '%')
                ->whereDoesntHave('empleado', function ($query) {
                    $query->where('estadoEmpleado', 1);
                })
                ->orWhere('nombre','LIKE','%'.$userId.'%')
                ->whereDoesntHave('empleado', function ($query) {
                    $query->where('estadoEmpleado', 1);
                })
                ->select('user_id', 'nombre', 'numero_documento')
                ->take(10) // Limitar a 10 resultados para mejor rendimiento
                ->get();

            if ($usuarios->isNotEmpty()) {
                Log::info('Usuarios encontrados: ' . $usuarios->toJson());
                return response()->json([
                    'success' => true,
                    'usuarios' => $usuarios->map(function ($usuario) {
                        return [
                            'user_id' => $usuario->user_id,
                            'nombre' => $usuario->nombre ?? '',
                            'numero_documento' => $usuario->numero_documento ?? '',
                        ];
                    }),
                ]);
            } else {
                Log::info('No se encontraron usuarios con user_id: ' . $userId);
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron usuarios que coincidan con la búsqueda.',
                    'usuarios' => [],
                ], 404);
            }
        } catch (\Exception $e) {
            Log::error('Error en buscarUsuario: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al buscar usuarios: ' . $e->getMessage(),
                'usuarios' => [],
            ], 500);
        }
    }

     public function buscarEmpleado(Request $request)
    {
        Log::info('BuscarEmpleado iniciado con término: ' . ($request->termino ?? 'No proporcionado'));

        $request->validate([
            'termino' => 'required|string|max:255',
        ]);

        try {
            $termino = trim($request->termino);

            // Buscar empleados por idEmpleado o nombre del usuario asociado
            $empleados = Empleado::with('user', 'cargo')
                ->where('idEmpleado', 'LIKE', '%' . $termino . '%')
                ->orWhereHas('user', function ($query) use ($termino) {
                    $query->where('nombre', 'LIKE', '%' . $termino . '%');
                })
                ->select('idEmpleado', 'user_id', 'idCargo', 'estadoEmpleado', 'imagen')
                ->take(10) // Limitar a 10 resultados
                ->get();

            if ($empleados->isNotEmpty()) {
                Log::info('Empleados encontrados: ' . $empleados->toJson());
                return response()->json([
                    'success' => true,
                    'empleados' => $empleados->map(function ($empleado) {
                        return [
                            'idEmpleado' => $empleado->idEmpleado,
                            'user_id' => $empleado->user_id,
                            'nombre' => $empleado->user->nombre ?? '',
                            'numero_documento' => $empleado->user->numero_documento ?? '',
                            'idCargo' => $empleado->idCargo,
                            'nomCargo' => $empleado->cargo->nomCargo ?? '',
                            'estadoEmpleado' => $empleado->estadoEmpleado,
                            'imagen' => $empleado->imagen ? asset('storage/' . $empleado->imagen) : null,
                        ];
                    }),
                ]);
            } else {
                Log::info('No se encontraron empleados con término: ' . $termino);
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron empleados que coincidan con la búsqueda.',
                    'empleados' => [],
                ], 404);
            }
        } catch (\Exception $e) {
            Log::error('Error en buscarEmpleado: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al buscar empleados: ' . $e->getMessage(),
                'empleados' => [],
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:usuario,user_id',
            'idCargo' => 'required|exists:cargo,idCargo',
            'estadoEmpleado' => 'required|boolean',
            'imagen' => 'nullable|image|max:2048',
        ]);

        $empleado = new Empleado();
        $empleado->user_id = $request->user_id;
        $empleado->idCargo = $request->idCargo;
        $empleado->estadoEmpleado = $request->estadoEmpleado;
        $empleado->fecIngreso = now();

        if ($request->hasFile('imagen')) {
            $empleado->imagen = $request->file('imagen')->store('fotos', 'public');
        }

        $empleado->save();
        return redirect()->route('empleados.index')->with('success', 'Empleado creado exitosamente.');
    }

    public function update(Request $request, $idEmpleado)
    {
        // Obtener el empleado autenticado
        $user = Auth::user();
        
        // Buscar el empleado que se intenta modificar
        $empleado = Empleado::findOrFail($idEmpleado);

        // Verificar si el empleado autenticado está intentando modificarse
        if ($empleado->user_id === $user->user_id) {
            return redirect()->back()->with('error', 'No puedes modificar tu propio registro.');
        }

        // Validar los datos del formulario
        $validated = $request->validate([
            'idCargo' => 'required|exists:cargo,idCargo',
            'estadoEmpleado' => 'required|boolean',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            // Otros campos según sea necesario
        ]);

        // Actualizar el empleado
        $empleado->idCargo = $validated['idCargo'];
        $empleado->estadoEmpleado = $validated['estadoEmpleado'];

        // Manejar la subida de la imagen si existe
        if ($request->hasFile('imagen')) {
            // Eliminar imagen anterior si existe
            if ($empleado->imagen && Storage::exists('public/' . $empleado->imagen)) {
                Storage::delete('public/' . $empleado->imagen);
            }
            // Guardar nueva imagen
            $path = $request->file('imagen')->store('empleados', 'public');
            $empleado->imagen = $path;
        }

        $empleado->save();

        return redirect()->route('empleados.index')->with('success', 'Empleado actualizado correctamente.');
    }

    public function destroy(Empleado $empleado)
    {
        // Obtener el empleado autenticado
        $user = Auth::user();
    
        // Verificar si el empleado autenticado está intentando modificarse
        if ($empleado->user_id === $user->user_id) {
            return redirect()->back()->with('error', 'No puedes eliminar tu propio registro.');
        }
        if ($empleado->imagen) {
            Storage::disk('public')->delete($empleado->imagen);
        }
        $empleado->delete();
        return redirect()->route('empleados.index')->with('success', 'Empleado eliminado exitosamente.');
    }
}