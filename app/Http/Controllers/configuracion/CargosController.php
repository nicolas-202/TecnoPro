<?php

namespace App\Http\Controllers\configuracion;

use App\Models\Cargo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Routing\Controller as BaseController;
use PhpParser\Node\Stmt\TryCatch;

class CargosController extends BaseController
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
        $cargos = Cargo::orderBy('idCargo', 'desc')->take(10)->get();
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'cargos' => $cargos->map(function ($cargo) {
                    return [
                        'idCargo' => $cargo->idCargo,
                        'nomCargo' => $cargo->nomCargo,
                        'desCargo' => $cargo->desCargo,
                        'nomeCargo' => $cargo->nomeCargo,
                        'estadoCargo' => $cargo->estadoCargo,
                    ];
                }),
            ]);
        }
        return view('configuracion.cargo', compact('cargos'));
    }

    public function buscarCargo(Request $request)
    {
        Log::info('BuscarCargo iniciado con término: ' . ($request->termino ?? 'No proporcionado'));

        $request->validate([
            'termino' => 'required|string|max:255',
        ]);

        try {
            $termino = trim($request->termino);

            $cargos = Cargo::where('nomCargo', 'LIKE', '%' . $termino . '%')
                ->orWhere('idCargo', 'LIKE', '%' . $termino . '%')
                ->orWhere('nomeCargo', 'LIKE', '%' . $termino . '%')
                ->select('idCargo', 'nomCargo', 'desCargo', 'nomeCargo', 'estadoCargo')
                ->take(10)
                ->get();

            if ($cargos->isNotEmpty()) {
                Log::info('Cargos encontrados: ' . $cargos->toJson());
                return response()->json([
                    'success' => true,
                    'cargos' => $cargos->map(function ($cargo) {
                        return [
                            'idCargo' => $cargo->idCargo,
                            'nomCargo' => $cargo->nomCargo,
                            'desCargo' => $cargo->desCargo,
                            'nomeCargo' => $cargo->nomeCargo,
                            'estadoCargo' => $cargo->estadoCargo,
                        ];
                    }),
                ]);
            } else {
                Log::info('No se encontraron cargos con término: ' . $termino);
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron cargos que coincidan con la búsqueda.',
                    'cargos' => [],
                ], 404);
            }
        } catch (\Exception $e) {
            Log::error('Error en buscarCargo: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al buscar cargos: ' . $e->getMessage(),
                'cargos' => [],
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'nomCargo' => 'required|string|max:255',
            'desCargo' => 'nullable|string|max:500',
            'nomeCargo' => 'nullable|string|max:255',
            'estadoCargo' => 'required|boolean',
        ]);

        $cargo = new Cargo();
        $cargo->nomCargo = $request->nomCargo;
        $cargo->desCargo = $request->desCargo;
        $cargo->nomeCargo = $request->nomeCargo;
        $cargo->estadoCargo = $request->estadoCargo;
        $cargo->save();

        return redirect()->route('cargos.index')->with('success', 'Cargo creado exitosamente.');
    }

    public function update(Request $request, $idCargo)
    {
        $cargo = Cargo::findOrFail($idCargo);

        $request->validate([
            'nomCargo' => 'required|string|max:255',
            'desCargo' => 'nullable|string|max:500',
            'nomeCargo' => 'nullable|string|max:255',
            'estadoCargo' => 'required|boolean',
        ]);

        $cargo->nomCargo = $request->nomCargo;
        $cargo->desCargo = $request->desCargo;
        $cargo->nomeCargo = $request->nomeCargo;
        $cargo->estadoCargo = $request->estadoCargo;
        $cargo->save();

        return redirect()->route('cargos.index')->with('success', 'Cargo actualizado correctamente.');
    }

    public function destroy(Cargo $cargo)
    {
        try {
            $cargo->delete();
            return redirect()->route('cargos.index')->with('success', 'Cargo eliminado exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al eliminar cargo: ' . $e->getMessage());
            return redirect()->route('cargos.index')->with('error', 'Error al eliminar el cargo.');
        }
    }
}