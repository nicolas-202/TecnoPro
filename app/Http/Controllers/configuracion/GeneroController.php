<?php

namespace App\Http\Controllers\configuracion;

use App\Http\Controllers\Controller;
use App\Models\Genero;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller as BaseController;

class GeneroController extends BaseController
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
        $generos = Genero::orderBy('idGenero', 'desc')->take(10)->get();
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'generos' => $generos->map(function ($genero) {
                    return [
                        'idGenero' => $genero->idGenero,
                        'nomGenero' => htmlspecialchars($genero->nomGenero, ENT_QUOTES, 'UTF-8'),
                        'desGenero' => htmlspecialchars($genero->desGenero ?? '', ENT_QUOTES, 'UTF-8'),
                        'nomeGenero' => htmlspecialchars($genero->nomeGenero ?? '', ENT_QUOTES, 'UTF-8'),
                        'estadoGenero' => $genero->estadoGenero,
                    ];
                })->toArray(),
            ]);
        }
        return view('configuracion.genero', compact('generos'));
    }

    public function buscarGenero(Request $request)
    {
        Log::info('BuscarGenero iniciado con término: ' . ($request->termino ?? 'No proporcionado'));

        $request->validate([
            'termino' => 'required|string|max:255',
        ]);

        try {
            $termino = trim($request->termino);

            $generos = Genero::where('nomGenero', 'LIKE', '%' . $termino . '%')
                ->orWhere('idGenero', 'LIKE', '%' . $termino . '%')
                ->orWhere('nomeGenero', 'LIKE', '%' . $termino . '%')
                ->select('idGenero', 'nomGenero', 'desGenero', 'nomeGenero', 'estadoGenero')
                ->take(10)
                ->get();

            if ($generos->isNotEmpty()) {
                Log::info('Géneros encontrados: ' . $generos->toJson());
                return response()->json([
                    'success' => true,
                    'generos' => $generos->map(function ($genero) {
                        return [
                            'idGenero' => $genero->idGenero,
                            'nomGenero' => htmlspecialchars($genero->nomGenero, ENT_QUOTES, 'UTF-8'),
                            'desGenero' => htmlspecialchars($genero->desGenero ?? '', ENT_QUOTES, 'UTF-8'),
                            'nomeGenero' => htmlspecialchars($genero->nomeGenero ?? '', ENT_QUOTES, 'UTF-8'),
                            'estadoGenero' => $genero->estadoGenero,
                        ];
                    })->toArray(),
                ]);
            } else {
                Log::info('No se encontraron géneros con término: ' . $termino);
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron géneros que coincidan con la búsqueda.',
                    'generos' => [],
                ], 404);
            }
        } catch (\Exception $e) {
            Log::error('Error en buscarGenero: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al buscar géneros: ' . $e->getMessage(),
                'generos' => [],
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'nomGenero' => 'required|string|max:255',
            'desGenero' => 'nullable|string|max:500',
            'nomeGenero' => 'nullable|string|max:255',
            'estadoGenero' => 'required|boolean',
        ]);

        $genero = new Genero();
        $genero->nomGenero = $request->nomGenero;
        $genero->desGenero = $request->desGenero;
        $genero->nomeGenero = $request->nomeGenero;
        $genero->estadoGenero = $request->estadoGenero;
        $genero->save();

        return redirect()->route('generos.index')->with('success', 'Género creado exitosamente.');
    }

    public function update(Request $request, $idGenero)
    {
        $genero = Genero::findOrFail($idGenero);

        $request->validate([
            'nomGenero' => 'required|string|max:255',
            'desGenero' => 'nullable|string|max:500',
            'nomeGenero' => 'nullable|string|max:255',
            'estadoGenero' => 'required|boolean',
        ]);

        $genero->nomGenero = $request->nomGenero;
        $genero->desGenero = $request->desGenero;
        $genero->nomeGenero = $request->nomeGenero;
        $genero->estadoGenero = $request->estadoGenero;
        $genero->save();

        return redirect()->route('generos.index')->with('success', 'Género actualizado correctamente.');
    }

    public function destroy(Genero $genero)
    {
        try {
            $genero->delete();
            return redirect()->route('generos.index')->with('success', 'Género eliminado exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al eliminar género: ' . $e->getMessage());
            return redirect()->route('generos.index')->with('error', 'Error al eliminar el género.');
        }
    }
}