<?php

namespace App\Http\Controllers\configuracion;

use App\Http\Controllers\Controller;
use App\Models\Pais;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller as BaseController;

class PaisController extends BaseController
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
        $paises = Pais::orderBy('idPais', 'desc')->take(10)->get();
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'paises' => $paises->map(function ($pais) {
                    return [
                        'idPais' => $pais->idPais,
                        'nomPais' => htmlspecialchars($pais->nomPais, ENT_QUOTES, 'UTF-8'),
                        'desPais' => htmlspecialchars($pais->desPais ?? '', ENT_QUOTES, 'UTF-8'),
                        'nomePais' => htmlspecialchars($pais->nomePais ?? '', ENT_QUOTES, 'UTF-8'),
                        'estadoPais' => $pais->estadoPais,
                    ];
                })->toArray(),
            ]);
        }
        return view('configuracion.pais', compact('paises'));
    }

    public function buscarPais(Request $request)
    {
        Log::info('BuscarPais iniciado con término: ' . ($request->termino ?? 'No proporcionado'));

        $request->validate([
            'termino' => 'required|string|max:255',
        ]);

        try {
            $termino = trim($request->termino);

            $paises = Pais::where('nomPais', 'LIKE', '%' . $termino . '%')
                ->orWhere('idPais', 'LIKE', '%' . $termino . '%')
                ->orWhere('nomePais', 'LIKE', '%' . $termino . '%')
                ->select('idPais', 'nomPais', 'desPais', 'nomePais', 'estadoPais')
                ->take(10)
                ->get();

            if ($paises->isNotEmpty()) {
                Log::info('Países encontrados: ' . $paises->toJson());
                return response()->json([
                    'success' => true,
                    'paises' => $paises->map(function ($pais) {
                        return [
                            'idPais' => $pais->idPais,
                            'nomPais' => htmlspecialchars($pais->nomPais, ENT_QUOTES, 'UTF-8'),
                            'desPais' => htmlspecialchars($pais->desPais ?? '', ENT_QUOTES, 'UTF-8'),
                            'nomePais' => htmlspecialchars($pais->nomePais ?? '', ENT_QUOTES, 'UTF-8'),
                            'estadoPais' => $pais->estadoPais,
                        ];
                    })->toArray(),
                ]);
            } else {
                Log::info('No se encontraron países con término: ' . $termino);
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron países que coincidan con la búsqueda.',
                    'paises' => [],
                ], 404);
            }
        } catch (\Exception $e) {
            Log::error('Error en buscarPais: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al buscar países: ' . $e->getMessage(),
                'paises' => [],
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'nomPais' => 'required|string|max:255',
            'desPais' => 'nullable|string|max:500',
            'nomePais' => 'nullable|string|max:255',
            'estadoPais' => 'required|boolean',
        ]);

        $pais = new Pais();
        $pais->nomPais = $request->nomPais;
        $pais->desPais = $request->desPais;
        $pais->nomePais = $request->nomePais;
        $pais->estadoPais = $request->estadoPais;
        $pais->save();

        return redirect()->route('paises.index')->with('success', 'País creado exitosamente.');
    }

    public function update(Request $request, $idPais)
    {
        $pais = Pais::findOrFail($idPais);

        $request->validate([
            'nomPais' => 'required|string|max:255',
            'desPais' => 'nullable|string|max:500',
            'nomePais' => 'nullable|string|max:255',
            'estadoPais' => 'required|boolean',
        ]);

        $pais->nomPais = $request->nomPais;
        $pais->desPais = $request->desPais;
        $pais->nomePais = $request->nomePais;
        $pais->estadoPais = $request->estadoPais;
        $pais->save();

        return redirect()->route('paises.index')->with('success', 'País actualizado correctamente.');
    }

    public function destroy(Pais $pais)
    {
        try {
            $pais->delete();
            return redirect()->route('paises.index')->with('success', 'País eliminado exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al eliminar país: ' . $e->getMessage());
            return redirect()->route('paises.index')->with('error', 'Error al eliminar el país.');
        }
    }
}