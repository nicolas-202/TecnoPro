<?php

namespace App\Http\Controllers\configuracion;

use App\Http\Controllers\Controller;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller as BaseController;

class ProveedorController extends BaseController
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
        $proveedores = Proveedor::orderBy('idProveedor', 'desc')->take(10)->get();
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'proveedores' => $proveedores->map(function ($proveedor) {
                    return [
                        'idProveedor' => $proveedor->idProveedor,
                        'nomProveedor' => htmlspecialchars($proveedor->nomProveedor, ENT_QUOTES, 'UTF-8'),
                        'desProveedor' => htmlspecialchars($proveedor->desProveedor ?? '', ENT_QUOTES, 'UTF-8'),
                        'telProveedor' => htmlspecialchars($proveedor->telProveedor ?? '', ENT_QUOTES, 'UTF-8'),
                        'emailProveedor' => htmlspecialchars($proveedor->emailProveedor ?? '', ENT_QUOTES, 'UTF-8'),
                        'nitProveedor' => htmlspecialchars($proveedor->nitProveedor ?? '', ENT_QUOTES, 'UTF-8'),
                        'estadoProveedor' => $proveedor->estadoProveedor,
                    ];
                })->toArray(),
            ]);
        }
        return view('configuracion.proveedor', compact('proveedores'));
    }

    public function buscarProveedor(Request $request)
    {
        Log::info('BuscarProveedor iniciado con término: ' . ($request->termino ?? 'No proporcionado'));

        $request->validate([
            'termino' => 'required|string|max:255',
        ]);

        try {
            $termino = trim($request->termino);

            $proveedores = Proveedor::where('nomProveedor', 'LIKE', '%' . $termino . '%')
                ->orWhere('idProveedor', 'LIKE', '%' . $termino . '%')    
                ->select('idProveedor', 'nomProveedor', 'desProveedor', 'telProveedor', 'emailProveedor', 'nitProveedor', 'estadoProveedor')
                ->take(10)
                ->get();

            if ($proveedores->isNotEmpty()) {
                Log::info('Proveedores encontrados: ' . $proveedores->toJson());
                return response()->json([
                    'success' => true,
                    'proveedores' => $proveedores->map(function ($proveedor) {
                        return [
                            'idProveedor' => $proveedor->idProveedor,
                            'nomProveedor' => htmlspecialchars($proveedor->nomProveedor, ENT_QUOTES, 'UTF-8'),
                            'desProveedor' => htmlspecialchars($proveedor->desProveedor ?? '', ENT_QUOTES, 'UTF-8'),
                            'telProveedor' => htmlspecialchars($proveedor->telProveedor ?? '', ENT_QUOTES, 'UTF-8'),
                            'emailProveedor' => htmlspecialchars($proveedor->emailProveedor ?? '', ENT_QUOTES, 'UTF-8'),
                            'nitProveedor' => htmlspecialchars($proveedor->nitProveedor ?? '', ENT_QUOTES, 'UTF-8'),
                            'estadoProveedor' => $proveedor->estadoProveedor,
                        ];
                    })->toArray(),
                ]);
            } else {
                Log::info('No se encontraron proveedores con término: ' . $termino);
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron proveedores que coincidan con la búsqueda.',
                    'proveedores' => [],
                ], 404);
            }
        } catch (\Exception $e) {
            Log::error('Error en buscarProveedor: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al buscar proveedores: ' . $e->getMessage(),
                'proveedores' => [],
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'nomProveedor' => 'required|string|max:255',
            'desProveedor' => 'nullable|string|max:500',
            'telProveedor' => 'nullable|string|max:20',
            'emailProveedor' => 'nullable|email|max:255',
            'nitProveedor' => 'nullable|string|max:50',
            'estadoProveedor' => 'required|boolean',
        ]);

        $proveedor = new Proveedor();
        $proveedor->nomProveedor = $request->nomProveedor;
        $proveedor->desProveedor = $request->desProveedor;
        $proveedor->telProveedor = $request->telProveedor;
        $proveedor->emailProveedor = $request->emailProveedor;
        $proveedor->nitProveedor = $request->nitProveedor;
        $proveedor->estadoProveedor = $request->estadoProveedor;
        $proveedor->save();

        return redirect()->route('proveedores.index')->with('success', 'Proveedor creado exitosamente.');
    }

    public function update(Request $request, $idProveedor)
    {
        $proveedor = Proveedor::findOrFail($idProveedor);

        $request->validate([
            'nomProveedor' => 'required|string|max:255',
            'desProveedor' => 'nullable|string|max:500',
            'telProveedor' => 'nullable|string|max:20',
            'emailProveedor' => 'nullable|email|max:255',
            'nitProveedor' => 'nullable|string|max:50',
            'estadoProveedor' => 'required|boolean',
        ]);

        $proveedor->nomProveedor = $request->nomProveedor;
        $proveedor->desProveedor = $request->desProveedor;
        $proveedor->telProveedor = $request->telProveedor;
        $proveedor->emailProveedor = $request->emailProveedor;
        $proveedor->nitProveedor = $request->nitProveedor;
        $proveedor->estadoProveedor = $request->estadoProveedor;
        $proveedor->save();

        return redirect()->route('proveedores.index')->with('success', 'Proveedor actualizado correctamente.');
    }

    public function destroy(Proveedor $proveedor)
    {
        try {
            $proveedor->delete();
            return redirect()->route('proveedores.index')->with('success', 'Proveedor eliminado exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al eliminar proveedor: ' . $e->getMessage());
            return redirect()->route('proveedores.index')->with('error', 'Error al eliminar el proveedor.');
        }
    }
}