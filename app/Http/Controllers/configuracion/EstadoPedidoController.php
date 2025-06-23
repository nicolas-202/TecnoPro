<?php

namespace App\Http\Controllers\configuracion;

use App\Http\Controllers\Controller;
use App\Models\EstadoPedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller as BaseController;

class EstadoPedidoController extends BaseController
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

    public function index(Request $request)
    {
        $estados = EstadoPedido::orderBy('idEstadoPedido', 'desc')->take(10)->get();
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'estados' => $estados->map(function ($estado) {
                    return [
                        'idEstadoPedido' => $estado->idEstadoPedido,
                        'nomEstadoPedido' => htmlspecialchars($estado->nomEstadoPedido, ENT_QUOTES, 'UTF-8'),
                        'desEstadoPedido' => htmlspecialchars($estado->desEstadoPedido ?? '', ENT_QUOTES, 'UTF-8'),
                        'nomeEstadoPedido' => htmlspecialchars($estado->nomeEstadoPedido ?? '', ENT_QUOTES, 'UTF-8'),
                        'estadoEstadoPedido' => $estado->estadoEstadoPedido,
                    ];
                })->toArray(),
            ]);
        }
        return view('configuracion.estadoPedido', compact('estados'));
    }

    public function buscarEstado(Request $request)
    {
        Log::info('BuscarEstado iniciado con término: ' . ($request->termino ?? 'No proporcionado'));

        $request->validate([
            'termino' => 'required|string|max:255',
        ]);

        try {
            $termino = trim($request->termino);

            $estados = EstadoPedido::where('nomEstadoPedido', 'LIKE', '%' . $termino . '%')
                ->orWhere('idEstadoPedido', 'LIKE', '%' . $termino . '%')
                ->orWhere('nomeEstadoPedido', 'LIKE', '%' . $termino . '%')
                ->select('idEstadoPedido', 'nomEstadoPedido', 'desEstadoPedido', 'nomeEstadoPedido', 'estadoEstadoPedido')
                ->take(10)
                ->get();

            if ($estados->isNotEmpty()) {
                Log::info('Estados encontrados: ' . $estados->toJson());
                return response()->json([
                    'success' => true,
                    'estados' => $estados->map(function ($estado) {
                        return [
                            'idEstadoPedido' => $estado->idEstadoPedido,
                            'nomEstadoPedido' => htmlspecialchars($estado->nomEstadoPedido, ENT_QUOTES, 'UTF-8'),
                            'desEstadoPedido' => htmlspecialchars($estado->desEstadoPedido ?? '', ENT_QUOTES, 'UTF-8'),
                            'nomeEstadoPedido' => htmlspecialchars($estado->nomeEstadoPedido ?? '', ENT_QUOTES, 'UTF-8'),
                            'estadoEstadoPedido' => $estado->estadoEstadoPedido,
                        ];
                    })->toArray(),
                ]);
            } else {
                Log::info('No se encontraron estados con término: ' . $termino);
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron estados que coincidan con la búsqueda.',
                    'estados' => [],
                ], 404);
            }
        } catch (\Exception $e) {
            Log::error('Error en buscarEstado: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al buscar estados: ' . $e->getMessage(),
                'estados' => [],
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'nomEstadoPedido' => 'required|string|max:255',
            'desEstadoPedido' => 'nullable|string|max:500',
            'nomeEstadoPedido' => 'nullable|string|max:255',
            'estadoEstadoPedido' => 'required|boolean',
        ]);

        $estado = new EstadoPedido();
        $estado->nomEstadoPedido = $request->nomEstadoPedido;
        $estado->desEstadoPedido = $request->desEstadoPedido;
        $estado->nomeEstadoPedido = $request->nomeEstadoPedido;
        $estado->estadoEstadoPedido = $request->estadoEstadoPedido;
        $estado->save();

        return redirect()->route('estados_pedido.index')->with('success', 'Estado creado exitosamente.');
    }

    public function update(Request $request, $idEstadoPedido)
    {
        $estado = EstadoPedido::findOrFail($idEstadoPedido);

        $request->validate([
            'nomEstadoPedido' => 'required|string|max:255',
            'desEstadoPedido' => 'nullable|string|max:500',
            'nomeEstadoPedido' => 'nullable|string|max:255',
            'estadoEstadoPedido' => 'required|boolean',
        ]);

        $estado->nomEstadoPedido = $request->nomEstadoPedido;
        $estado->desEstadoPedido = $request->desEstadoPedido;
        $estado->nomeEstadoPedido = $request->nomeEstadoPedido;
        $estado->estadoEstadoPedido = $request->estadoEstadoPedido;
        $estado->save();

        return redirect()->route('estados_pedido.index')->with('success', 'Estado actualizado correctamente.');
    }

    public function destroy(EstadoPedido $estado)
    {
        try {
            $estado->delete();
            return redirect()->route('estados_pedido.index')->with('success', 'Estado eliminado exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al eliminar estado: ' . $e->getMessage());
            return redirect()->route('estados_pedido.index')->with('error', 'Error al eliminar el estado.');
        }
    }
}