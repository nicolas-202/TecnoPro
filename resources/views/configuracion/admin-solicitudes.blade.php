
@extends('layouts.layout')

@section('content')
<div class="container mx-auto max-w-6xl px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Gestión de Solicitudes de Devolución/Reembolso</h1>

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6" role="alert">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <!-- Formulario de Búsqueda -->
    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Buscar Solicitudes</h2>
        <form action="{{ route('admin.solicitudes.search') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="idUsuario" class="block text-gray-700 font-semibold mb-2">ID Usuario</label>
                <input type="number" name="idUsuario" id="idUsuario" class="w-full border-2 rounded-md p-2 focus:ring focus:ring-blue-200"
                       value="{{ request('idUsuario') }}">
            </div>
            <div class="flex items-end">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:ring focus:ring-blue-200 min-w-[120px]">
                    Buscar
                </button>
            </div>
        </form>
        @if (!is_null($searchResults))
            @if ($searchResults->isNotEmpty())
                <div class="mt-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Resultados de Búsqueda</h3>
                    <table class="w-full table-auto border-collapse">
                        <thead>
                            <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                                <th class="py-3 px-6 text-left">ID Solicitud</th>
                                <th class="py-3 px-6 text-left">ID Pedido</th>
                                <th class="py-3 px-6 text-left">Producto</th>
                                <th class="py-3 px-6 text-left">Fecha</th>
                                <th class="py-3 px-6 text-center">Estado</th>
                                <th class="py-3 px-6 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 text-sm font-light">
                            @foreach ($searchResults as $solicitud)
                                <tr class="border-b border-gray-200 hover:bg-gray-100">
                                    <td class="py-3 px-6 text-left">{{ $solicitud->idSolDevReem ?? 'N/A' }}</td>
                                    <td class="py-3 px-6 text-left">
                                        @if ($solicitud->pedidoProducto && $solicitud->pedidoProducto->pedido)
                                            <a href="{{ route('admin.detalle-pedido-solicitud', $solicitud->pedidoProducto->pedido->idPedido) }}"
                                               class="text-blue-600 hover:underline">
                                                {{ $solicitud->pedidoProducto->pedido->idPedido }}
                                            </a>
                                        @else
                                            No especificado
                                        @endif
                                    </td>
                                    <td class="py-3 px-6 text-left">{{ $solicitud->pedidoProducto->producto->nomProducto ?? 'No especificado' }}</td>
                                    <td class="py-3 px-6 text-left">
                                        {{ \Carbon\Carbon::parse($solicitud->fechaSolDevReem)->format('d/m/Y') }}
                                    </td>
                                    <td class="py-3 px-6 text-center">{{ $solicitud->estadoSolicitud->nomEstadoSolDevReem ?? 'No especificado' }}</td>
                                    <td class="py-3 px-6 text-center">
                                        @if ($solicitud->idSolDevReem)
                                            <a href="{{ route('admin.detalle-solicitud', $solicitud->idSolDevReem) }}"
                                               class="text-blue-600 hover:underline">Ver Detalles</a>
                                        @else
                                            <span class="text-red-500">ID Solicitud inválido</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-600 text-center mt-4">No se encontraron solicitudes.</p>
            @endif
        @endif
    </div>

    <!-- 10 Primeras Solicitudes No Revisadas -->
    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">10 Primeras Solicitudes No Revisadas</h2>
        @if (!empty($solicitudesNoRevisadas) && $solicitudesNoRevisadas->isNotEmpty())
            <div class="hidden md:block">
                <table class="w-full table-auto border-collapse">
                    <thead>
                        <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                            <th class="py-3 px-6 text-left">ID Solicitud</th>
                            <th class="py-3 px-6 text-left">ID Pedido</th>
                            <th class="py-3 px-6 text-left">Producto</th>
                            <th class="py-3 px-6 text-left">Fecha</th>
                            <th class="py-3 px-6 text-center">Estado</th>
                            <th class="py-3 px-6 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm font-light">
                        @foreach ($solicitudesNoRevisadas as $solicitud)
                            <tr class="border-b border-gray-200 hover:bg-gray-100">
                                <td class="py-3 px-6 text-left">{{ $solicitud->idSolDevReem ?? 'N/A' }}</td>
                                <td class="py-3 px-6 text-left">
                                    @if ($solicitud->pedidoProducto && $solicitud->pedidoProducto->pedido)
                                        <a href="{{ route('admin.detalle-pedido-solicitud', $solicitud->pedidoProducto->pedido->idPedido) }}"
                                           class="text-blue-600 hover:underline">
                                            {{ $solicitud->pedidoProducto->pedido->idPedido }}
                                        </a>
                                    @else
                                        No especificado
                                    @endif
                                </td>
                                <td class="py-3 px-6 text-left">{{ $solicitud->pedidoProducto->producto->nomProducto ?? 'No especificado' }}</td>
                                <td class="py-3 px-6 text-left">
                                    {{ \Carbon\Carbon::parse($solicitud->fechaSolDevReem)->format('d/m/Y') }}
                                </td>
                                <td class="py-3 px-6 text-center">{{ $solicitud->estadoSolicitud->nomEstadoSolDevReem ?? 'No especificado' }}</td>
                                <td class="py-3 px-6 text-center">
                                    @if ($solicitud->idSolDevReem)
                                        <a href="{{ route('admin.detalle-solicitud', $solicitud->idSolDevReem) }}"
                                           class="text-blue-600 hover:underline">Ver Detalles</a>
                                    @else
                                        <span class="text-red-500">ID Solicitud inválido</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="block md:hidden">
                @foreach ($solicitudesNoRevisadas as $solicitud)
                    <div class="border border-gray-200 rounded-lg p-4 mb-4 bg-white shadow-sm hover:shadow-md transition-shadow">
                        <div class="grid grid-cols-1 gap-2">
                            <div><p class="text-gray-600 font-semibold">ID Solicitud:</p><p>{{ $solicitud->idSolDevReem ?? 'N/A' }}</p></div>
                            <div><p class="text-gray-600 font-semibold">ID Pedido:</p>
                                @if ($solicitud->pedidoProducto && $solicitud->pedidoProducto->pedido)
                                    <a href="{{ route('admin.detalle-pedido-solicitud', $solicitud->pedidoProducto->pedido->idPedido) }}"
                                       class="text-blue-600 hover:underline">
                                        {{ $solicitud->pedidoProducto->pedido->idPedido }}
                                    </a>
                                @else
                                    No especificado
                                @endif
                            </div>
                            <div><p class="text-gray-600 font-semibold">Producto:</p><p>{{ $solicitud->pedidoProducto->producto->nomProducto ?? 'No especificado' }}</p></div>
                            <div><p class="text-gray-600 font-semibold">Fecha:</p><p>{{ \Carbon\Carbon::parse($solicitud->fechaSolDevReem)->format('d/m/Y') }}</p></div>
                            <div><p class="text-gray-600 font-semibold">Estado:</p><p>{{ $solicitud->estadoSolicitud->nomEstadoSolDevReem ?? 'No especificado' }}</p></div>
                            <div class="mt-2 text-center">
                                @if ($solicitud->idSolDevReem)
                                    <a href="{{ route('admin.detalle-solicitud', $solicitud->idSolDevReem) }}"
                                       class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 min-w-[120px]">Ver Detalles</a>
                                @else
                                    <span class="text-red-500">ID Solicitud inválido</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-600 text-center">No hay solicitudes no revisadas.</p>
        @endif
    </div>

    <!-- Últimas 10 Solicitudes Revisadas -->
    <div class="bg-white shadow-md rounded-lg p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Últimas 10 Solicitudes Revisadas</h2>
        @if (!empty($solicitudesRevisadas) && $solicitudesRevisadas->isNotEmpty())
            <div class="hidden md:block">
                <table class="w-full table-auto border-collapse">
                    <thead>
                        <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                            <th class="py-3 px-6 text-left">ID Solicitud</th>
                            <th class="py-3 px-6 text-left">ID Pedido</th>
                            <th class="py-3 px-6 text-left">Producto</th>
                            <th class="py-3 px-6 text-left">Fecha</th>
                            <th class="py-3 px-6 text-center">Estado</th>
                            <th class="py-3 px-6 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm font-light">
                        @foreach ($solicitudesRevisadas as $solicitud)
                            <tr class="border-b border-gray-200 hover:bg-gray-100">
                                <td class="py-3 px-6 text-left">{{ $solicitud->idSolDevReem ?? 'N/A' }}</td>
                                <td class="py-3 px-6 text-left">
                                    @if ($solicitud->pedidoProducto && $solicitud->pedidoProducto->pedido)
                                        <a href="{{ route('admin.detalle-pedido-solicitud', $solicitud->pedidoProducto->pedido->idPedido) }}"
                                           class="text-blue-600 hover:underline">
                                            {{ $solicitud->pedidoProducto->pedido->idPedido }}
                                        </a>
                                    @else
                                        No especificado
                                    @endif
                                </td>
                                <td class="py-3 px-6 text-left">{{ $solicitud->pedidoProducto->producto->nomProducto ?? 'No especificado' }}</td>
                                <td class="py-3 px-6 text-left">
                                    {{ \Carbon\Carbon::parse($solicitud->fechaSolDevReem)->format('d/m/Y') }}
                                </td>
                                <td class="py-3 px-6 text-center">{{ $solicitud->estadoSolicitud->nomEstadoSolDevReem ?? 'No especificado' }}</td>
                                <td class="py-3 px-6 text-center">
                                    @if ($solicitud->idSolDevReem)
                                        <a href="{{ route('admin.detalle-solicitud', $solicitud->idSolDevReem) }}"
                                           class="text-blue-600 hover:underline">Ver Detalles</a>
                                    @else
                                        <span class="text-red-500">ID Solicitud inválido</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="block md:hidden">
                @foreach ($solicitudesRevisadas as $solicitud)
                    <div class="border border-gray-200 rounded-lg p-4 mb-4 bg-white shadow-sm hover:shadow-md transition-shadow">
                        <div class="grid grid-cols-1 gap-2">
                            <div><p class="text-gray-600 font-semibold">ID Solicitud:</p><p>{{ $solicitud->idSolDevReem ?? 'N/A' }}</p></div>
                            <div><p class="text-gray-600 font-semibold">ID Pedido:</p>
                                @if ($solicitud->pedidoProducto && $solicitud->pedidoProducto->pedido)
                                    <a href="{{ route('admin.detalle-pedido-solicitud', $solicitud->pedidoProducto->pedido->idPedido) }}"
                                       class="text-blue-600 hover:underline">
                                        {{ $solicitud->pedidoProducto->pedido->idPedido }}
                                    </a>
                                @else
                                    No especificado
                                @endif
                            </div>
                            <div><p class="text-gray-600 font-semibold">Producto:</p><p>{{ $solicitud->pedidoProducto->producto->nomProducto ?? 'No especificado' }}</p></div>
                            <div><p class="text-gray-600 font-semibold">Fecha:</p><p>{{ \Carbon\Carbon::parse($solicitud->fechaSolDevReem)->format('d/m/Y') }}</p></div>
                            <div><p class="text-gray-600 font-semibold">Estado:</p><p>{{ $solicitud->estadoSolicitud->nomEstadoSolDevReem ?? 'No especificado' }}</p></div>
                            <div class="mt-2 text-center">
                                @if ($solicitud->idSolDevReem)
                                    <a href="{{ route('admin.detalle-solicitud', $solicitud->idSolDevReem) }}"
                                       class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 min-w-[120px]">Ver Detalles</a>
                                @else
                                    <span class="text-red-500">ID Solicitud inválido</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-600 text-center">No hay solicitudes revisadas.</p>
        @endif
    </div>
</div>
<link href="{{ asset('css/admin-solicitudes.css') }}" rel="stylesheet">
@endsection
