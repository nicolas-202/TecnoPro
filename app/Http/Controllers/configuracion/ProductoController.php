<?php

namespace App\Http\Controllers\configuracion;

use App\Models\Producto;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Routing\Controller as BaseController;

class ProductoController extends BaseController
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
        $productos = Producto::with('categoria')->take(10)->get();
        return response()->json([
            'success' => true,
            'productos' => $productos->map(function ($producto) {
                return [
                    'idProducto' => $producto->idProducto,
                    'nomProducto' => $producto->nomProducto,
                    'desProducto' => $producto->desProducto,
                    'stockMinimo' => $producto->stockMinimo,
                    'stockMaximo' => $producto->stockMaximo,
                    'cantidadExistente' => $producto->cantidadExistente,
                    'precioVenta' => $producto->precioVenta,
                    'idCategoria' => $producto->idCategoria,
                    'nomCategoria' => $producto->categoria->nomCategoria ?? '',
                    'estadoProducto' => $producto->estadoProducto,
                    'imagen' => $producto->imagen ? asset('storage/' . $producto->imagen) : null,
                ];
            }),
        ]);
    }

    public function index()
    {
        $productos = Producto::with('categoria')->paginate(10);
        $categorias = Categoria::all();
        return view('configuracion.producto', compact('productos', 'categorias'));
    }

    public function show(Producto $producto)
    {
        $this->authorizeEmployee();
        $producto->load('categoria');
        return view('configuracion.producto_detalle', compact('producto'));
    }

    public function buscarProducto(Request $request)
    {
        Log::info('BuscarProducto iniciado con término: ' . ($request->termino ?? 'No proporcionado'));

        $request->validate([
            'termino' => 'required|string|max:255',
        ]);

        try {
            $termino = trim($request->termino);

            $productos = Producto::with('categoria')
                ->where('idProducto', 'LIKE', '%' . $termino . '%')
                ->orWhere('nomProducto', 'LIKE', '%' . $termino . '%')
                ->select('idProducto', 'nomProducto', 'desProducto', 'stockMinimo', 'stockMaximo', 'cantidadExistente', 'precioVenta', 'idCategoria', 'estadoProducto', 'imagen')
                ->take(10)
                ->get();

            if ($productos->isNotEmpty()) {
                Log::info('Productos encontrados: ' . $productos->toJson());
                return response()->json([
                    'success' => true,
                    'productos' => $productos->map(function ($producto) {
                        return [
                            'idProducto' => $producto->idProducto,
                            'nomProducto' => $producto->nomProducto,
                            'desProducto' => $producto->desProducto,
                            'stockMinimo' => $producto->stockMinimo,
                            'stockMaximo' => $producto->stockMaximo,
                            'cantidadExistente' => $producto->cantidadExistente,
                            'precioVenta' => $producto->precioVenta,
                            'idCategoria' => $producto->idCategoria,
                            'nomCategoria' => $producto->categoria->nomCategoria ?? '',
                            'estadoProducto' => $producto->estadoProducto,
                            'imagen' => $producto->imagen ? asset('storage/' . $producto->imagen) : null,
                        ];
                    }),
                ]);
            } else {
                Log::info('No se encontraron productos con término: ' . $termino);
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron productos que coincidan con la búsqueda.',
                    'productos' => [],
                ], 404);
            }
        } catch (\Exception $e) {
            Log::error('Error en buscarProducto: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al buscar productos: ' . $e->getMessage(),
                'productos' => [],
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'nomProducto' => 'required|string|max:255',
            'desProducto' => 'required|string',
            'stockMinimo' => 'required|integer|min:0',
            'stockMaximo' => 'required|integer|min:0',
            'idCategoria' => 'required|exists:categoria,idCategoria',
            'estadoProducto' => 'required|boolean',
            'imagen' => 'nullable|image|max:2048',
        ]);

        $producto = new Producto();
        $producto->nomProducto = $request->nomProducto;
        $producto->desProducto = $request->desProducto;
        $producto->stockMinimo = $request->stockMinimo;
        $producto->stockMaximo = $request->stockMaximo;
        $producto->cantidadExistente = 0;
        $producto->precioVenta = 0;
        $producto->idCategoria = $request->idCategoria;
        $producto->estadoProducto = $request->estadoProducto;

        if ($request->hasFile('imagen')) {
            $producto->imagen = $request->file('imagen')->store('productos', 'public');
        }

        $producto->save();
        return redirect()->route('productos.index')->with('success', 'Producto creado exitosamente.');
    }

    public function update(Request $request, $idProducto)
    {
        $producto = Producto::findOrFail($idProducto);

        $request->validate([
            'nomProducto' => 'required|string|max:255',
            'desProducto' => 'required|string',
            'stockMinimo' => 'required|integer|min:0',
            'stockMaximo' => 'required|integer|min:0',
            'idCategoria' => 'required|exists:categoria,idCategoria',
            'estadoProducto' => 'required|boolean',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $producto->nomProducto = $request->nomProducto;
        $producto->desProducto = $request->desProducto;
        $producto->stockMinimo = $request->stockMinimo;
        $producto->stockMaximo = $request->stockMaximo;
        $producto->idCategoria = $request->idCategoria;
        $producto->estadoProducto = $request->estadoProducto;

        if ($request->hasFile('imagen')) {
            if ($producto->imagen && Storage::exists('public/' . $producto->imagen)) {
                Storage::delete('public/' . $producto->imagen);
            }
            $path = $request->file('imagen')->store('productos', 'public');
            $producto->imagen = $path;
        }

        $producto->save();

        return redirect()->route('productos.index')->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy(Producto $producto)
    {
        if ($producto->imagen) {
            Storage::disk('public')->delete($producto->imagen);
        }
        $producto->delete();
        return redirect()->route('productos.index')->with('success', 'Producto eliminado exitosamente.');
    }
}