<?php
namespace App\Http\Controllers\usuario;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use App\Models\Genero;
use App\Models\TipoDocumento;
use App\Models\Municipio;
use App\Models\Pedido;
use App\Models\Departamento;
use App\Models\Pais;
use App\Models\SolicitudDevolucionReembolso;
use App\Models\PedidoProducto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Routing\Controller as BaseController;
class UsuarioController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function perfil()
    {
        $usuario = Auth::user();
        return view('cuenta.perfil', compact('usuario'));
    }

    public function editar()
    {
        $usuario = Auth::user();
        $generos = Genero::all();
        $tiposDocumento = TipoDocumento::all();
        $paises = Pais::all();

        // Cargar departamento y país iniciales según el municipio del usuario
        $departamentoInicial = null;
        $paisInicial = null;
        $departamentos = [];
        $municipios = [];

        if ($usuario->idMunicipio) {
            $municipio = Municipio::find($usuario->idMunicipio);
            if ($municipio) {
                $departamentoInicial = $municipio->departamento;
                $paisInicial = $departamentoInicial ? $departamentoInicial->pais : null;
                $departamentos = Departamento::where('idPais', $paisInicial ? $paisInicial->idPais : null)->get();
                $municipios = Municipio::where('idDepartamento', $departamentoInicial ? $departamentoInicial->idDepartamento : null)->get();
            }
        }

        return view('cuenta.editar', compact('usuario', 'generos', 'tiposDocumento', 'paises', 'departamentos', 'municipios', 'paisInicial', 'departamentoInicial'));
    }

    public function actualizar(Request $request)
    {
        try {
            Log::debug('Datos recibidos en actualizar:', $request->all());

            $request->validate([
                'nombre' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:usuario,email,' . Auth::id() . ',user_id',
                'celular' => 'required|string|max:15',
                'fecha_nacimiento' => 'required|date',
                'numero_documento' => 'required|string|max:20',
                'direccion' => 'required|string|max:255',
                'idGenero' => 'required|exists:genero,idGenero',
                'idTipoDocumento' => 'required|exists:tipodocumento,idTipoDocumento',
                'idMunicipio' => 'required|exists:municipio,idMunicipio',
            ]);

            $usuario = Usuario::findOrFail(Auth::id());
            $usuario->update([
                'nombre' => $request->nombre,
                'email' => $request->email,
                'celular' => $request->celular,
                'fecha_nacimiento' => $request->fecha_nacimiento,
                'numero_documento' => $request->numero_documento,
                'direccion' => $request->direccion,
                'idGenero' => $request->idGenero,
                'idTipoDocumento' => $request->idTipoDocumento,
                'idMunicipio' => $request->idMunicipio,
            ]);

            Log::debug('Información del usuario actualizada:', ['user_id' => $usuario->user_id]);

            return redirect()->route('cuenta.perfil')->with('success', 'Información actualizada correctamente.');
        } catch (\Exception $e) {
            Log::error('Error al actualizar información: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->withErrors(['error' => 'Error al actualizar la información: ' . $e->getMessage()]);
        }
    }

    public function pedidos()
    {
        $usuario = Auth::user();
        Log::debug('Usuario autenticado:', ['user_id' => $usuario ? $usuario->user_id : null]);
        if (!$usuario) {
            Log::error('No hay usuario autenticado');
            return redirect()->route('login')->withErrors(['error' => 'Debes iniciar sesión para ver tus pedidos.']);
        }
        $pedidos = $usuario->pedidos()->with('estadoPedido')->orderBy('fechaPedido', 'desc')->get();
        Log::debug('Pedidos cargados:', ['pedidos' => $pedidos->toArray()]);
        return view('cuenta.pedidos', compact('pedidos'));
    }
    public function detallePedido($idPedido)
{
    $pedido = Pedido::with(['estadoPedido', 'productos.producto'])->findOrFail($idPedido);
    if ($pedido->idUsuario !== Auth::id()) {
        abort(403, 'No autorizado');
    }
    Log::debug('Pedido cargado:', ['pedido' => $pedido->toArray(), 'productos' => $pedido->productos->toArray()]);
    return view('cuenta.detalle_pedido', compact('pedido'));
}
public function procesarDevolucion(Request $request)
{
    $request->validate([
        'idPedidoProducto' => 'required|exists:pedidoproducto,idPedidoProducto',
        'cantidad' => 'required|integer|min:1',
        'motivo' => 'required|string|max:1000',
    ]);

    try {
        $pedidoProducto = PedidoProducto::with('pedido')->findOrFail($request->idPedidoProducto);
        if ($pedidoProducto->pedido->idUsuario !== Auth::id()) {
            abort(403, 'No autorizado');
        }

        if ($request->cantidad > $pedidoProducto->cantidadProducto) {
            return back()->withErrors(['cantidad' => 'La cantidad a devolver excede la cantidad comprada.']);
        }

        $solicitud = SolicitudDevolucionReembolso::create([
            'fechaSolDevReem' => now()->toDateString(),
            'comentarioSolDevReem' => $request->motivo,
            'idEstadoSolDevReem' => 1, // Estado "Pendiente"
            'idPedidoProducto' => $request->idPedidoProducto,
        ]);

        $pedidoProducto->cantidadProducto -= $request->cantidad;
        $pedidoProducto->totalProducto = $pedidoProducto->cantidadProducto * $pedidoProducto->precioProducto;
        $pedidoProducto->save();

        $pedido = $pedidoProducto->pedido;
        $pedido->totalPedido = $pedido->productos->sum('totalProducto');
        $pedido->save();

        Log::info('Solicitud de devolución creada:', ['idSolDevReem' => $solicitud->idSolDevReem, 'idUsuario' => Auth::id()]);

        return redirect()->route('cuenta.pedidos')->with('success', 'Solicitud de devolución enviada correctamente.');
    } catch (\Exception $e) {
        Log::error('Error al procesar devolución: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
        return back()->withErrors(['error' => 'Error al procesar la solicitud: ' . $e->getMessage()]);
    }
}
 public function solicitudesDevolucion()
    {
        try {
            $solicitudes = SolicitudDevolucionReembolso::with(['pedidoProducto.pedido', 'pedidoProducto.producto', 'estadoSolicitud'])
                ->whereHas('pedidoProducto.pedido', function ($query) {
                    $query->where('idUsuario', Auth::id());
                })
                ->orderBy('fechaSolDevReem', 'desc')
                ->get();

            Log::info('Solicitudes de devolución cargadas:', ['idUsuario' => Auth::id(), 'count' => $solicitudes->count()]);

            return view('cuenta.solicitudes_devolucion', compact('solicitudes'));
        } catch (\Exception $e) {
            Log::error('Error al cargar solicitudes de devolución: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->route('cuenta.pedidos')->with('error', 'Error al cargar las solicitudes.');
        }
    }
}
