<?php

namespace App\Http\Controllers\configuracion;

use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Routing\Controller as BaseController;

class CategoriasController extends BaseController
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
    $categorias = Categoria::orderBy('idCategoria', 'desc')->take(10)->get();
    if ($request->expectsJson()) {
        return response()->json([
            'success' => true,
            'categorias' => $categorias->map(function ($categoria) {
                return [
                    'idCategoria' => $categoria->idCategoria,
                    'nomCategoria' => $categoria->nomCategoria,
                    'desCategoria' => $categoria->desCategoria,
                    'nomeCategoria' => $categoria->nomeCategoria,
                    'estadoCategoria' => $categoria->estadoCategoria,
                ];
            })->toArray(),
        ]);
    }
    return view('configuracion.categoria', compact('categorias'));
}

public function buscarCategoria(Request $request)
{
    Log::info('BuscarCategoria iniciado con término: ' . ($request->termino ?? 'No proporcionado'));

    $request->validate([
        'termino' => 'required|string|max:255',
    ]);

    try {
        $termino = trim($request->termino);

        $categorias = Categoria::where('nomCategoria', 'LIKE', '%' . $termino . '%')
            ->orWhere('idCategoria', 'LIKE', '%' . $termino . '%')
            ->orWhere('nomeCategoria', 'LIKE', '%' . $termino . '%')
            ->select('idCategoria', 'nomCategoria', 'desCategoria', 'nomeCategoria', 'estadoCategoria')
            ->take(10)
            ->get();

        return response()->json([
            'success' => true,
            'categorias' => $categorias->map(function ($categoria) {
                return [
                    'idCategoria' => $categoria->idCategoria,
                    'nomCategoria' => $categoria->nomCategoria,
                    'desCategoria' => $categoria->desCategoria,
                    'nomeCategoria' => $categoria->nomeCategoria,
                    'estadoCategoria' => $categoria->estadoCategoria,
                ];
            })->toArray(),
            'message' => $categorias->isEmpty() ? 'No se encontraron categorías.' : null,
        ]);
    } catch (\Exception $e) {
        Log::error('Error en buscarCategoria: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error al buscar categorías: ' . $e->getMessage(),
            'categorias' => [],
        ], 500);
    }
}
    public function store(Request $request)
    {
        $request->validate([
            'nomCategoria' => 'required|string|max:255',
            'desCategoria' => 'nullable|string|max:500',
            'nomeCategoria' => 'nullable|string|max:255',
            'estadoCategoria' => 'required|boolean',
        ]);

        $categoria = new Categoria();
        $categoria->nomCategoria = $request->nomCategoria;
        $categoria->desCategoria = $request->desCategoria;
        $categoria->nomeCategoria = $request->nomeCategoria;
        $categoria->estadoCategoria = $request->estadoCategoria;
        $categoria->save();

        return redirect()->route('categorias.index')->with('success', 'Categoría creada exitosamente.');
    }

    public function update(Request $request, $idCategoria)
    {
        $categoria = Categoria::findOrFail($idCategoria);

        $request->validate([
            'nomCategoria' => 'required|string|max:255',
            'desCategoria' => 'nullable|string|max:500',
            'nomeCategoria' => 'nullable|string|max:255',
            'estadoCategoria' => 'required|boolean',
        ]);

        $categoria->nomCategoria = $request->nomCategoria;
        $categoria->desCategoria = $request->desCategoria;
        $categoria->nomeCategoria = $request->nomeCategoria;
        $categoria->estadoCategoria = $request->estadoCategoria;
        $categoria->save();

        return redirect()->route('categorias.index')->with('success', 'Categoría actualizada correctamente.');
    }

    public function destroy(Categoria $categoria)
    {
        try {
            $categoria->delete();
            return redirect()->route('categorias.index')->with('success', 'Categoría eliminada exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al eliminar categoría: ' . $e->getMessage());
            return redirect()->route('categorias.index')->with('error', 'Error al eliminar la categoría.');
        }
    }
}