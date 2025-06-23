<?php

namespace App\Http\Controllers\kardex;

use App\Models\Kardex;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\ProductoProveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller as BaseController;

class KardexController extends BaseController
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
        $productos = Producto::where('estadoProducto', true)->get();
        $producto = null;
        $movimientos = collect();

        if ($request->has('idProducto') && $request->idProducto) {
            $producto = Producto::findOrFail($request->idProducto);
            $movimientos = Kardex::where('idProducto', $request->idProducto)
                ->orderBy('fechaMovimiento', 'desc')
                ->get();
        }

        return view('kardex.indexKardex', compact('productos', 'producto', 'movimientos'));
    }

    public function create()
    {
        $productos = Producto::where('estadoProducto', true)->get();
        $proveedores = Proveedor::where('estadoProveedor', true)->get();
        return view('kardex.kardex', compact('productos', 'proveedores'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'idProducto' => 'required|exists:producto,idProducto',
            'idProveedor' => 'required|exists:proveedor,idProveedor',
            'tipoMovimiento' => 'required|in:Entrada',
            'cantidadMovimiento' => 'required|integer|min:1',
            'costoUnitario' => 'required|numeric|min:0',
            'fechaMovimiento' => 'required|date',
        ]);

        DB::beginTransaction();
        try {
            $producto = Producto::findOrFail($request->idProducto);
            $proveedor = Proveedor::findOrFail($request->idProveedor);
            $tipoMovimiento = $request->tipoMovimiento;
            $cantidadMovimiento = $request->cantidadMovimiento;
            $costoUnitario = $request->costoUnitario;
            $fechaMovimiento = $request->fechaMovimiento;

            // Validar stock máximo
            if ($producto->stockMaximo && ($producto->cantidadExistente + $cantidadMovimiento > $producto->stockMaximo)) {
                Log::debug('Stock máximo excedido', [
                    'idProducto' => $producto->idProducto,
                    'cantidadExistente' => $producto->cantidadExistente,
                    'cantidadMovimiento' => $cantidadMovimiento,
                    'stockMaximo' => $producto->stockMaximo,
                ]);
                session()->flash('error', "La entrada excede el stock máximo de {$producto->stockMaximo} unidades. Stock actual: {$producto->cantidadExistente}.");
                return redirect()->back()->withInput();
            }

            // Crear registro en kardex
            $kardex = new Kardex();
            $kardex->tipoMovimiento = $tipoMovimiento;
            $kardex->cantidadMovimiento = $cantidadMovimiento;
            $kardex->fechaMovimiento = $fechaMovimiento;
            $kardex->costoUnitario = $costoUnitario; // Usar el costo unitario ingresado directamente
            $kardex->costoTotal = $cantidadMovimiento * $costoUnitario;
            $kardex->idProducto = $producto->idProducto;

            // Actualizar cantidadExistente en producto
            $producto->cantidadExistente += $cantidadMovimiento;

            // Calcular costo unitario promedio ponderado para precioVenta
            $ultimoMovimiento = Kardex::where('idProducto', $producto->idProducto)
                ->orderBy('fechaMovimiento', 'desc')
                ->first();
            $costoUnitarioPromedio = $costoUnitario; // Valor por defecto
            if ($ultimoMovimiento) {
                $valorAnterior = $ultimoMovimiento->costoUnitario * ($producto->cantidadExistente - $cantidadMovimiento);
                $valorNuevo = $cantidadMovimiento * $costoUnitario;
                $costoUnitarioPromedio = ($valorAnterior + $valorNuevo) / $producto->cantidadExistente;
            }

            // Actualizar precioVenta (margen de ganancia del 30%)
            $kardex->precioVentaActualizado = $costoUnitarioPromedio * 1.3;
            $producto->precioVenta = $kardex->precioVentaActualizado;

            // Crear registro en productoProveedor
            $productoProveedor = new ProductoProveedor();
            $productoProveedor->fechaRegistro = $fechaMovimiento;
            $productoProveedor->precioUnitario = $costoUnitario; // Usar el costo unitario ingresado
            $productoProveedor->cantidad = $cantidadMovimiento;
            $productoProveedor->precioTotal = $cantidadMovimiento * $costoUnitario;
            $productoProveedor->idProducto = $producto->idProducto;
            $productoProveedor->idProveedor = $proveedor->idProveedor;

            // Guardar los cambios
            $kardex->save();
            $producto->save();
            $productoProveedor->save();

            DB::commit();
            Log::debug('Movimiento registrado correctamente', ['idProducto' => $producto->idProducto]);
            session()->flash('success', 'Movimiento registrado correctamente.');
            return redirect()->route('kardex.index', ['idProducto' => $producto->idProducto]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al registrar el movimiento', ['error' => $e->getMessage()]);
            session()->flash('error', 'Error al registrar el movimiento: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }
}