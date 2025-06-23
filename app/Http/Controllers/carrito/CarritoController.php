<?php

namespace App\Http\Controllers\carrito;

use Illuminate\Routing\Controller;
use App\Models\Producto;
use App\Models\Pedido;
use App\Models\PedidoProducto;
use App\Models\User;
use App\Models\Pais;
use App\Models\Departamento;
use App\Models\Municipio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Models\Usuario;
use App\Models\Kardex;
use App\Models\EstadoPedido;
use App\Models\FormaPago;

class CarritoController extends Controller
{
   public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('carritoDeCompras.carrito');
    }

    public function getStock($idProducto)
    {
        $producto = Producto::findOrFail($idProducto);
        return response()->json(['cantidadExistente' => $producto->cantidadExistente]);
    }

    public function storeCarrito(Request $request)
    {
        $request->validate([
            'carrito' => 'required|array',
            'carrito.*.idProducto' => 'required|exists:producto,idProducto',
            'carrito.*.cantidad' => 'required|integer|min:1',
            'carrito.*.precioVenta' => 'required|numeric|min:0',
            'carrito.*.nomProducto' => 'required|string',
        ]);

        $carrito = $request->carrito;
        $carritoValido = [];

        foreach ($carrito as $item) {
            $producto = Producto::find($item['idProducto']);
            if ($producto && $producto->estadoProducto && $producto->cantidadExistente >= $item['cantidad']) {
                $carritoValido[$item['idProducto']] = [
                    'cantidad' => $item['cantidad'],
                    'precioUnitario' => $producto->precioVenta,
                    'nombre' => $producto->nomProducto,
                    'imagen' => $item['imagen'] ?? null,
                ];
            } else {
                Log::warning("Producto inválido o sin stock: idProducto = {$item['idProducto']}");
            }
        }

        if (empty($carritoValido)) {
            return response()->json(['success' => false, 'message' => 'No hay productos válidos en el carrito.']);
        }

        session(['carrito' => $carritoValido]);
        Log::debug('Carrito almacenado en sesión:', [$carritoValido]);

        return response()->json(['success' => true, 'redirect' => route('carrito.confirmar')]);
    }

    public function confirmar(Request $request)
    {
        if ($request->isMethod('get')) {
            $carrito = session('carrito', []);
            $total = 0;
            foreach ($carrito as $item) {
                $total += $item['cantidad'] * $item['precioUnitario'];
            }

            $usuario = Auth::user();
            // Parsear municipio si es un JSON
            if (is_string($usuario->municipio) && json_decode($usuario->municipio, true)) {
                $municipioData = json_decode($usuario->municipio, true);
                $usuario->municipio = $municipioData['nomMunicipio'] ?? 'No especificado';
            }

            $paises = Pais::all();
            $formasPago = FormaPago::where('estadoFormaPago', 1)->get(); // Load active payment methods
            return view('carritoDeCompras.confirmar', compact('carrito', 'total', 'usuario', 'paises', 'formasPago'));
        }

        if ($request->isMethod('post')) {
            try {
                $request->validate([
                    'carrito' => 'required|array',
                    'carrito.*.idProducto' => 'required|exists:producto,idProducto',
                    'carrito.*.cantidad' => 'required|integer|min:1',
                ]);

                $carrito = $request->carrito;
                $carritoValido = [];

                foreach ($carrito as $item) {
                    $producto = Producto::find($item['idProducto']);
                    if ($producto && $producto->estadoProducto && $producto->cantidadExistente >= $item['cantidad']) {
                        $carritoValido[$item['idProducto']] = [
                            'cantidad' => $item['cantidad'],
                            'precioUnitario' => $producto->precioVenta,
                            'nombre' => $producto->nomProducto,
                            'imagen' => $item['imagen'] ?? null,
                        ];
                    } else {
                        $nombreProducto = $producto && isset($producto->nomProducto) ? $producto->nomProducto : "ID {$item['idProducto']}";
                        Log::warning("Producto inválido o sin stock: idProducto = {$item['idProducto']}, nombre = {$nombreProducto}");
                        return response()->json(['success' => false, 'message' => "Producto {$nombreProducto} no disponible o sin stock."], 422);
                    }
                }

                if (empty($carritoValido)) {
                    return response()->json(['success' => false, 'message' => 'No hay productos válidos en el carrito.'], 422);
                }

                session(['carrito' => $carritoValido]);
                Log::debug('Carrito confirmado:', [$carritoValido]);

                return response()->json(['success' => true, 'redirect' => route('carrito.confirmar')]);
            } catch (\Exception $e) {
                Log::error('Error in confirmar: ' . $e->getMessage());
                return response()->json(['success' => false, 'message' => 'Server error: ' . $e->getMessage()], 500);
            }
        }
    }

    public function actualizarDireccion(Request $request)
    {
        try {
            Log::debug('Datos recibidos en actualizarDireccion:', $request->all());
            Log::debug('Usuario autenticado:', ['user_id' => Auth::id(), 'autenticado' => Auth::check()]);

            $request->validate([
                'idPais' => 'required|exists:pais,idPais',
                'idDepartamento' => 'required|exists:departamento,idDepartamento',
                'idMunicipio' => 'required|exists:municipio,idMunicipio',
                'direccion' => 'required|string|max:255',
            ]);

            $pais = Pais::findOrFail($request->idPais);
            $departamento = Departamento::findOrFail($request->idDepartamento);
            $municipio = Municipio::findOrFail($request->idMunicipio);

            $usuario = Usuario::findOrFail(Auth::id());
            Log::debug('Usuario encontrado:', ['user' => $usuario->toArray()]);

            $usuario->direccion = $request->direccion;
            $usuario->idMunicipio = $request->idMunicipio;
            $usuario->save();

            Log::debug('Dirección actualizada para usuario:', ['user_id' => $usuario->user_id]);

            return redirect()->route('carrito.confirmar')->with('success', 'Dirección actualizada correctamente.');
        } catch (\Exception $e) {
            Log::error('Error en actualizarDireccion: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Error al actualizar la dirección: ' . $e->getMessage()], 500);
        }
    }

    public function procesarCompra(Request $request)
    {
        try {
            Log::debug('Datos recibidos en procesarCompra:', $request->all());
            Log::debug('Contenido de la sesión carrito:', ['carrito' => session('carrito', [])]);

            $request->validate([
                'idFormaPago' => 'required|exists:formapago,idFormaPago',
                'numero_tarjeta' => 'required|string|size:16',
                'nombre_titular' => 'required|string|max:255',
                'fecha_expiracion' => 'required|string|regex:/^[0-1][0-9]\/[0-9]{2}$/',
                'cvv' => 'required|string|size:3',
            ]);

            $carrito = session('carrito', []);
            if (empty($carrito)) {
                Log::warning('Carrito vacío en procesarCompra', ['session_id' => session()->getId()]);
                return response()->json(['success' => false, 'message' => 'El carrito está vacío.']);
            }

            $estadoPedido = EstadoPedido::find(1);
            if (!$estadoPedido) {
                Log::error('Estado de pedido no encontrado: idEstadoPedido = 1');
                return response()->json(['success' => false, 'message' => 'Estado de pedido no configurado. Contacta al administrador.']);
            }

            $formaPago = FormaPago::findOrFail($request->idFormaPago);
            if (!$formaPago->estadoFormaPago) {
                Log::warning('Forma de pago inactiva seleccionada:', ['idFormaPago' => $request->idFormaPago]);
                return response()->json(['success' => false, 'message' => 'La forma de pago seleccionada no está disponible.']);
            }

            DB::beginTransaction();

            $total = 0;
            foreach ($carrito as $item) {
                $total += $item['cantidad'] * $item['precioUnitario'];
            }

            $pedido = Pedido::create([
                'fechaPedido' => now(),
                'totalPedido' => $total,
                'informacionPedido' => json_encode([
                    'numero_tarjeta' => substr($request->numero_tarjeta, -4),
                    'nombre_titular' => $request->nombre_titular,
                    'fecha_expiracion' => $request->fecha_expiracion,
                    'cvv' => '***',
                ]),
                'idEstadoPedido' => $estadoPedido->idEstadoPedido,
                'idUsuario' => Auth::id(),
                'idFormaPago' => $request->idFormaPago,
            ]);

            foreach ($carrito as $idProducto => $item) {
                $producto = Producto::findOrFail($idProducto);
                if ($producto->cantidadExistente < $item['cantidad']) {
                    DB::rollBack();
                    Log::error('Stock insuficiente para producto:', ['idProducto' => $idProducto]);
                    return response()->json(['success' => false, 'message' => "No hay suficiente stock para el producto: {$item['nombre']}"]);
                }

                PedidoProducto::create([
                    'cantidadProducto' => $item['cantidad'],
                    'precioProducto' => $item['precioUnitario'],
                    'totalProducto' => $item['cantidad'] * $item['precioUnitario'],
                    'idPedido' => $pedido->idPedido,
                    'idProducto' => $idProducto,
                ]);

                Kardex::create([
                    'tipoMovimiento' => 'salida',
                    'cantidadMovimiento' => $item['cantidad'],
                    'fechaMovimiento' => now(),
                    'costoUnitario' => $producto->costoUnitario ?? $item['precioUnitario'],
                    'costoTotal' => ($producto->costoUnitario ?? $item['precioUnitario']) * $item['cantidad'],
                    'precioVentaActualizado' => $item['precioUnitario'],
                    'idProducto' => $idProducto,
                ]);

                $producto->cantidadExistente -= $item['cantidad'];
                $producto->save();
            }

            session()->forget('carrito');
            DB::commit();

            Log::debug('Compra procesada exitosamente:', ['idPedido' => $pedido->idPedido, 'idFormaPago' => $request->idFormaPago]);

            if (!Route::has('carrito.exito')) {
                Log::error('Ruta carrito.exito no definida');
                return response()->json(['success' => false, 'message' => 'Ruta de éxito no configurada.']);
            }

            return response()->json(['success' => true, 'redirect' => route('carrito.exito')]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al procesar compra: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Error al procesar la compra: ' . $e->getMessage()]);
        }
    }

    public function exito()
    {
        return view('carritoDeCompras.exito');
    }
}