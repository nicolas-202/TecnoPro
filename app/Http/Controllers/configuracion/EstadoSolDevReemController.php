<?php

namespace App\Http\Controllers\configuracion;

use App\Http\Controllers\Controller;
use App\Models\EstadoSolDevReem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller as BaseController;

class EstadoSolDevReemController extends BaseController
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
        $estados = EstadoSolDevReem::orderBy('idEstadoSolDevReem', 'desc')->take(10)->get();
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'estados' => $estados->map(function ($estado) {
                    return [
                        'idEstadoSolDevReem' => $estado->idEstadoSolDevReem,
                        'nomEstadoSolDevReem' => htmlspecialchars($estado->nomEstadoSolDevReem, ENT_QUOTES, 'UTF-8'),
                        'desEstadoSolDevReem' => htmlspecialchars($estado->desEstadoSolDevReem ?? '', ENT_QUOTES, 'UTF-8'),
                        'nomeEstadoSolDevReem' => htmlspecialchars($estado->nomeEstadoSolDevReem ?? '', ENT_QUOTES, 'UTF-8'),
                        'estadoEstadoSolDevReem' => $estado->estadoEstadoSolDevReem,
                    ];
                })->toArray(),
            ]);
        }
        return view('configuracion.estado_sol_dev_reem', compact('estados'));
    }

    public function buscarEstado(Request $request)
    {
        Log::info('BuscarEstado iniciado con término: ' . ($request->termino ?? 'No proporcionado'));

        $request->validate([
            'termino' => 'required|string|max:255',
        ]);

        try {
            $termino = trim($request->termino);

            $estados = EstadoSolDevReem::where('nomEstadoSolDevReem', 'LIKE', '%' . $termino . '%')
                ->orWhere('idEstadoSolDevReem', 'LIKE', '%' . $termino . '%')
                ->orWhere('nomeEstadoSolDevReem', 'LIKE', '%' . $termino . '%')
                ->select('idEstadoSolDevReem', 'nomEstadoSolDevReem', 'desEstadoSolDevReem', 'nomeEstadoSolDevReem', 'estadoEstadoSolDevReem')
                ->take(10)
                ->get();

            if ($estados->isNotEmpty()) {
                Log::info('Estados encontrados: ' . $estados->toJson());
                return response()->json([
                    'success' => true,
                    'estados' => $estados->map(function ($estado) {
                        return [
                            'idEstadoSolDevReem' => $estado->idEstadoSolDevReem,
                            'nomEstadoSolDevReem' => htmlspecialchars($estado->nomEstadoSolDevReem, ENT_QUOTES, 'UTF-8'),
                            'desEstadoSolDevReem' => htmlspecialchars($estado->desEstadoSolDevReem ?? '', ENT_QUOTES, 'UTF-8'),
                            'nomeEstadoSolDevReem' => htmlspecialchars($estado->nomeEstadoSolDevReem ?? '', ENT_QUOTES, 'UTF-8'),
                            'estadoEstadoSolDevReem' => $estado->estadoEstadoSolDevReem,
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
            'nomEstadoSolDevReem' => 'required|string|max:255',
            'desEstadoSolDevReem' => 'nullable|string|max:500',
            'nomeEstadoSolDevReem' => 'nullable|string|max:255',
            'estadoEstadoSolDevReem' => 'required|boolean',
        ]);

        $estado = new EstadoSolDevReem();
        $estado->nomEstadoSolDevReem = $request->nomEstadoSolDevReem;
        $estado->desEstadoSolDevReem = $request->desEstadoSolDevReem;
        $estado->nomeEstadoSolDevReem = $request->nomeEstadoSolDevReem;
        $estado->estadoEstadoSolDevReem = $request->estadoEstadoSolDevReem;
        $estado->save();

        return redirect()->route('estados_solicitud.index')->with('success', 'Estado creado exitosamente.');
    }

    public function update(Request $request, $idEstadoSolDevReem)
    {
        $estado = EstadoSolDevReem::findOrFail($idEstadoSolDevReem);

        $request->validate([
            'nomEstadoSolDevReem' => 'required|string|max:255',
            'desEstadoSolDevReem' => 'nullable|string|max:500',
            'nomeEstadoSolDevReem' => 'nullable|string|max:255',
            'estadoEstadoSolDevReem' => 'required|boolean',
        ]);

        $estado->nomEstadoSolDevReem = $request->nomEstadoSolDevReem;
        $estado->desEstadoSolDevReem = $request->desEstadoSolDevReem;
        $estado->nomeEstadoSolDevReem = $request->nomeEstadoSolDevReem;
        $estado->estadoEstadoSolDevReem = $request->estadoEstadoSolDevReem;
        $estado->save();

        return redirect()->route('estados_solicitud.index')->with('success', 'Estado actualizado correctamente.');
    }

    public function destroy(EstadoSolDevReem $estado)
    {
        try {
            $estado->delete();
            return redirect()->route('estados_solicitud.index')->with('success', 'Estado eliminado exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al eliminar estado: ' . $e->getMessage());
            return redirect()->route('estados_solicitud.index')->with('error', 'Error al eliminar el estado.');
        }
    }
}