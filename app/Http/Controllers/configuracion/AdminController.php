<?php

namespace App\Http\Controllers\configuracion;

use App\Models\Pedido;
use App\Models\EstadoPedido;
use App\Models\EstadoSolDevReem;
use App\Models\SolicitudDevolucionReembolso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Routing\Controller as BaseController;

class AdminController extends BaseController
{
  public function __construct()
    {
        $this->middleware('auth'); // Añade middleware de admin si existe
    }

    public function pedidos()
{
    try {
        // Buscar el estado "Procesando" por nombre
        $estadoProcesando = EstadoPedido::where('nomEstadoPedido', 'Procesando')->first();

        $pedidosNoRevisados = Pedido::with('estadoPedido')
            ->when($estadoProcesando, function ($query) use ($estadoProcesando) {
                $query->where('idEstadoPedido', $estadoProcesando->idEstadoPedido);
            })
            ->orderBy('fechaPedido', 'asc')
            ->take(10)
            ->get();

        $pedidosRevisados = Pedido::with('estadoPedido')
            ->when($estadoProcesando, function ($query) use ($estadoProcesando) {
                $query->where('idEstadoPedido', '!=', $estadoProcesando->idEstadoPedido);
            })
            ->orderBy('fechaPedido', 'desc')
            ->take(10)
            ->get();

        $searchResults = null;

        Log::info('Pedidos administrativos cargados:', [
            'no_revisados_count' => $pedidosNoRevisados->count(),
            'revisados_count' => $pedidosRevisados->count()
        ]);

        return view('configuracion.admin-pedidos', compact('pedidosNoRevisados', 'pedidosRevisados', 'searchResults'));
    } catch (\Exception $e) {
        Log::error('Error al cargar pedidos administrativos: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
        return redirect()->back()->with('error', 'Error al cargar los pedidos.');
    }
}

    public function pedidosSearch(Request $request)
{
    try {
        $query = Pedido::with('estadoPedido');

        if ($request->filled('idPedido')) {
            $query->where('idPedido', $request->idPedido);
        }

        if ($request->filled('idUsuario')) {
            $query->where('idUsuario', $request->idUsuario);
        }

        $searchResults = $query->orderBy('fechaPedido', 'desc')->get();

        // Buscar el estado "Procesando" por nombre
        $estadoProcesando = EstadoPedido::where('nomEstadoPedido', 'Procesando')->first();

        $pedidosNoRevisados = Pedido::with('estadoPedido')
            ->when($estadoProcesando, function ($query) use ($estadoProcesando) {
                $query->where('idEstadoPedido', $estadoProcesando->idEstadoPedido);
            })
            ->orderBy('fechaPedido', 'asc')
            ->take(10)
            ->get();

        $pedidosRevisados = Pedido::with('estadoPedido')
            ->when($estadoProcesando, function ($query) use ($estadoProcesando) {
                $query->where('idEstadoPedido', '!=', $estadoProcesando->idEstadoPedido);
            })
            ->orderBy('fechaPedido', 'desc')
            ->take(10)
            ->get();

        Log::info('Búsqueda de pedidos administrativos:', [
            'params' => $request->all(),
            'results_count' => $searchResults->count()
        ]);

        return view('configuracion.admin-pedidos', compact('pedidosNoRevisados', 'pedidosRevisados', 'searchResults'));
    } catch (\Exception $e) {
        Log::error('Error al buscar pedidos administrativos: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
        return redirect()->back()->with('error', 'Error al buscar los pedidos.');
    }
}

    public function detallePedidoAdmin($idPedido)
    {
        try {
            $pedido = Pedido::with(['estadoPedido', 'usuario.municipio'])->findOrFail($idPedido);
            $estados = EstadoPedido::all();

            Log::info('Detalles del pedido cargados:', [
                'idPedido' => $idPedido,
                'idUsuario' => $pedido->idUsuario,
                'estado' => $pedido->estadoPedido->nomEstadoPedido ?? 'No especificado',
                'nomMunicipio' => $pedido->usuario->municipio->nomMunicipio ?? 'No especificado',
                'direccion' => $pedido->usuario->direccion ?? 'No especificado'
            ]);

            return view('configuracion.detalle_pedido_admin', compact('pedido', 'estados'));
        } catch (\Exception $e) {
            Log::error('Error al cargar detalles del pedido: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->route('admin.pedidos')->with('error', 'Error al cargar los detalles del pedido.');
        }
    }

    public function actualizarEstadoPedido(Request $request, $idPedido)
    {
        try {
            $request->validate([
                'idEstadoPedido' => 'required|exists:estadopedido,idEstadoPedido',
            ]);

            $pedido = Pedido::findOrFail($idPedido);
            $pedido->idEstadoPedido = $request->idEstadoPedido;
            $pedido->save();

            Log::info('Estado del pedido actualizado:', [
                'idPedido' => $idPedido,
                'nuevo_estado' => $request->idEstadoPedido
            ]);

            return redirect()->route('admin.detalle-pedido', $idPedido)->with('success', 'Estado del pedido actualizado correctamente.');
        } catch (\Exception $e) {
            Log::error('Error al actualizar estado del pedido: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'Error al actualizar el estado del pedido.');
        }
    }

    public function solicitudes()
{
    try {
        // Buscar el estado "Procesando" por nombre
        $estadoProcesando = EstadoSolDevReem::where('nomEstadoSolDevReem', 'Procesando')->first();

        $solicitudesNoRevisadas = SolicitudDevolucionReembolso::with(['pedidoProducto.pedido', 'pedidoProducto.producto', 'estadoSolicitud'])
            ->when($estadoProcesando, function ($query) use ($estadoProcesando) {
                $query->where('idEstadoSolDevReem', $estadoProcesando->idEstadoSolDevReem);
            })
            ->orderBy('fechaSolDevReem', 'asc')
            ->take(10)
            ->get();

        $solicitudesRevisadas = SolicitudDevolucionReembolso::with(['pedidoProducto.pedido', 'pedidoProducto.producto', 'estadoSolicitud'])
            ->when($estadoProcesando, function ($query) use ($estadoProcesando) {
                $query->where('idEstadoSolDevReem', '!=', $estadoProcesando->idEstadoSolDevReem);
            })
            ->orderBy('fechaSolDevReem', 'desc')
            ->take(10)
            ->get();

        $searchResults = null;

        Log::info('Solicitudes administrativas cargadas:', [
            'no_revisadas_count' => $solicitudesNoRevisadas->count(),
            'no_revisadas_ids' => $solicitudesNoRevisadas->pluck('idSolDevReem')->toArray(),
            'revisadas_count' => $solicitudesRevisadas->count(),
            'revisadas_ids' => $solicitudesRevisadas->pluck('idSolDevReem')->toArray()
        ]);

        return view('configuracion.admin-solicitudes', compact('solicitudesNoRevisadas', 'solicitudesRevisadas', 'searchResults'));
    } catch (\Exception $e) {
        Log::error('Error al cargar solicitudes administrativas: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
        return redirect()->back()->with('error', 'Error al cargar las solicitudes.');
    }
}
    public function solicitudesSearch(Request $request)
{
    try {
        $query = SolicitudDevolucionReembolso::with(['pedidoProducto.pedido', 'pedidoProducto.producto', 'estadoSolicitud']);

        if ($request->filled('idUsuario')) {
            $query->whereHas('pedidoProducto.pedido', function ($q) use ($request) {
                $q->where('idUsuario', $request->idUsuario);
            });
        }

        $searchResults = $query->orderBy('fechaSolDevReem', 'desc')->get();

        // Buscar el estado "Procesando" por nombre
        $estadoProcesando = EstadoSolDevReem::where('nomEstadoSolDevReem', 'Procesando')->first();

        $solicitudesNoRevisadas = SolicitudDevolucionReembolso::with(['pedidoProducto.pedido', 'pedidoProducto.producto', 'estadoSolicitud'])
            ->when($estadoProcesando, function ($query) use ($estadoProcesando) {
                $query->where('idEstadoSolDevReem', $estadoProcesando->idEstadoSolDevReem);
            })
            ->orderBy('fechaSolDevReem', 'asc')
            ->take(10)
            ->get();

        $solicitudesRevisadas = SolicitudDevolucionReembolso::with(['pedidoProducto.pedido', 'pedidoProducto.producto', 'estadoSolicitud'])
            ->when($estadoProcesando, function ($query) use ($estadoProcesando) {
                $query->where('idEstadoSolDevReem', '!=', $estadoProcesando->idEstadoSolDevReem);
            })
            ->orderBy('fechaSolDevReem', 'desc')
            ->take(10)
            ->get();

        Log::info('Búsqueda de solicitudes administrativas:', [
            'params' => $request->all(),
            'results_count' => $searchResults->count(),
            'results_ids' => $searchResults->pluck('idSolDevReem')->toArray(),
            'no_revisadas_count' => $solicitudesNoRevisadas->count(),
            'no_revisadas_ids' => $solicitudesNoRevisadas->pluck('idSolDevReem')->toArray(),
            'revisadas_count' => $solicitudesRevisadas->count(),
            'revisadas_ids' => $solicitudesRevisadas->pluck('idSolDevReem')->toArray()
        ]);

        return view('configuracion.admin-solicitudes', compact('solicitudesNoRevisadas', 'solicitudesRevisadas', 'searchResults'));
    } catch (\Exception $e) {
        Log::error('Error al buscar solicitudes administrativas: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
        return redirect()->back()->with('error', 'Error al buscar las solicitudes.');
    }
}
    public function detallePedidoSolicitud($idPedido)
    {
        try {
            $pedido = Pedido::with(['estadoPedido', 'usuario.municipio'])->findOrFail($idPedido);

            Log::info('Detalles del pedido para solicitud cargados:', [
                'idPedido' => $idPedido,
                'idUsuario' => $pedido->idUsuario,
                'estado' => $pedido->estadoPedido->nomEstadoPedido ?? 'No especificado',
                'nomMunicipio' => $pedido->usuario->municipio->nomMunicipio ?? 'No especificado',
                'direccion' => $pedido->usuario->direccion ?? 'No especificado'
            ]);

            return view('configuracion.detalle_pedido_solicitud', compact('pedido'));
        } catch (\Exception $e) {
            Log::error('Error al cargar detalles del pedido para solicitud: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->route('admin.solicitudes')->with('error', 'Error al cargar los detalles del pedido.');
        }
    }

    public function detalleSolicitudAdmin($idSolDevReem)
    {
        try {
            $solicitud = SolicitudDevolucionReembolso::with(['pedidoProducto.pedido', 'pedidoProducto.producto', 'estadoSolicitud'])
                ->findOrFail($idSolDevReem);
            $estados = EstadoSolDevReem::all();

            Log::info('Detalles de la solicitud cargados:', [
                'idSolDevReem' => $idSolDevReem,
                'idPedidoProducto' => $solicitud->idPedidoProducto,
                'idPedido' => $solicitud->pedidoProducto->pedido->idPedido ?? 'No especificado',
                'estado' => $solicitud->estadoSolicitud->nomEstadoSolDevReem ?? 'No especificado'
            ]);

            return view('configuracion.detalle_solicitud_admin', compact('solicitud', 'estados'));
        } catch (\Exception $e) {
            Log::error('Error al cargar detalles de la solicitud: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->route('admin.solicitudes')->with('error', 'Error al cargar los detalles de la solicitud.');
        }
    }

    public function actualizarEstadoSolicitud(Request $request, $idSolDevReem)
    {
        try {
            $request->validate([
                'idEstadoSolDevReem' => 'required|exists:estadosoldevreem,idEstadoSolDevReem',
                'respuestaSolDevReem' => 'nullable|string|max:1000',
            ]);

            $solicitud = SolicitudDevolucionReembolso::findOrFail($idSolDevReem);
            $solicitud->idEstadoSolDevReem = $request->idEstadoSolDevReem;
            $solicitud->respuestaSolDevReem = $request->respuestaSolDevReem;
            $solicitud->save();

            Log::info('Estado de la solicitud actualizado:', [
                'idSolDevReem' => $idSolDevReem,
                'nuevo_estado' => $request->idEstadoSolDevReem,
                'respuesta' => $request->respuestaSolDevReem ?? 'Sin respuesta'
            ]);

            return redirect()->route('admin.detalle-solicitud', $idSolDevReem)->with('success', 'Estado de la solicitud actualizado correctamente.');
        } catch (\Exception $e) {
            Log::error('Error al actualizar estado de la solicitud: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'Error al actualizar el estado de la solicitud.');
        }
    }
}
