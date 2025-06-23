@extends('layouts.layout')

@section('content')
<div class="container mx-auto max-w-6xl px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Mis Solicitudes de Devolución/Reembolso</h1>

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

    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <!-- Lista de Solicitudes -->
        @if ($solicitudes->isNotEmpty())
            <!-- Tabla para pantallas medianas y grandes -->
            <div class="hidden md:block">
                <table class="w-full table-auto border-collapse">
                    <thead>
                        <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                            <th class="py-3 px-6 text-left">ID Solicitud</th>
                            <th class="py-3 px-6 text-left">Producto</th>
                            <th class="py-3 px-6 text-left">ID Pedido</th>
                            <th class="py-3 px-6 text-center">Fecha</th>
                            <th class="py-3 px-6 text-center">Estado</th>
                            <th class="py-3 px-6 text-left">Motivo</th>
                            <th class="py-3 px-6 text-left">Respuesta</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm font-light">
                        @foreach ($solicitudes as $solicitud)
                            <tr class="border-b border-gray-200 hover:bg-gray-100">
                                <td class="py-3 px-6 text-left">{{ $solicitud->idSolDevReem }}</td>
                                <td class="py-3 px-6 text-left">
                                    {{ $solicitud->pedidoProducto->producto->nomProducto ?? 'No disponible' }}
                                </td>
                                <td class="py-3 px-6 text-left">
                                    <a href="{{ route('cuenta.detalle-pedido', $solicitud->pedidoProducto->pedido->idPedido) }}"
                                       class="text-blue-600 hover:underline">
                                        {{ $solicitud->pedidoProducto->pedido->idPedido }}
                                    </a>
                                </td>
                                <td class="py-3 px-6 text-center">
                                    {{ \Carbon\Carbon::parse($solicitud->fechaSolDevReem)->format('d/m/Y') }}
                                </td>
                                <td class="py-3 px-6 text-center">
                                    {{ $solicitud->estadoSolicitud->nomEstadoSolDevReem ?? 'No especificado' }}
                                </td>
                                <td class="py-3 px-6 text-left">
                                    {{ $solicitud->comentarioSolDevReem ?? 'Sin motivo' }}
                                </td>
                                <td class="py-3 px-6 text-left">
                                    {{ $solicitud->respuestaSolDevReem ?? 'Sin respuesta' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Tarjetas para pantallas pequeñas -->
            <div class="block md:hidden">
                @foreach ($solicitudes as $solicitud)
                    <div class="border border-gray-200 rounded-lg p-4 mb-4 bg-white shadow-sm hover:shadow-md transition-shadow">
                        <div class="grid grid-cols-1 gap-2">
                            <div>
                                <p class="text-gray-600 font-semibold">ID Solicitud:</p>
                                <p>{{ $solicitud->idSolDevReem }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600 font-semibold">Producto:</p>
                                <p>{{ $solicitud->pedidoProducto->producto->nomProducto ?? 'No disponible' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600 font-semibold">ID Pedido:</p>
                                <p>
                                    <a href="{{ route('cuenta.detalle-pedido', $solicitud->pedidoProducto->pedido->idPedido) }}"
                                       class="text-blue-600 hover:underline">
                                        {{ $solicitud->pedidoProducto->pedido->idPedido }}
                                    </a>
                                </p>
                            </div>
                            <div>
                                <p class="text-gray-600 font-semibold">Fecha:</p>
                                <p>{{ \Carbon\Carbon::parse($solicitud->fechaSolDevReem)->format('d/m/Y') }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600 font-semibold">Estado:</p>
                                <p>{{ $solicitud->estadoSolicitud->nomEstadoSolDevReem ?? 'No especificado' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600 font-semibold">Motivo:</p>
                                <p>{{ $solicitud->comentarioSolDevReem ?? 'Sin motivo' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600 font-semibold">Respuesta:</p>
                                <p>{{ $solicitud->respuestaSolDevReem ?? 'Sin respuesta' }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-600 text-center">No tienes solicitudes de devolución/reembolso registradas.</p>
        @endif

        <!-- Botón Volver -->
        <div class="mt-6 text-center md:text-left">
            <a href="{{ route('cuenta.pedidos') }}"
               class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors focus:ring focus:ring-blue-200">
                Volver a Pedidos
            </a>
        </div>
    </div>
</div>

<link href="{{ asset('css/admin-pedidos.css') }}" rel="stylesheet">

@endsection
