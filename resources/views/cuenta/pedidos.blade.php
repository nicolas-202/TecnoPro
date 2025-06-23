@extends('layouts.layout')

@section('content')
<div class="container mx-auto max-w-6xl px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Mis Pedidos</h1>

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
        <!-- Enlace para ver solicitudes de devolución/reembolso -->
        <div class="mb-4 text-right">
            <a
                href="{{ route('cuenta.solicitudes-devolucion') }}"
                class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:ring focus:ring-blue-200"
            >
                Ver Solicitudes de Devolución/Reembolso
            </a>
        </div>

        <!-- Lista de Pedidos -->
        @if ($pedidos->isNotEmpty())
            <!-- Tabla para pantallas medianas y grandes -->
            <div class="hidden md:block">
                <table class="w-full table-auto border-collapse">
                    <thead>
                        <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                            <th class="py-3 px-6 text-left">ID Pedido</th>
                            <th class="py-3 px-6 text-left">Fecha</th>
                            <th class="py-3 px-6 text-right">Total</th>
                            <th class="py-3 px-6 text-center">Estado</th>
                            <th class="py-3 px-6 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm font-light">
                        @foreach ($pedidos as $pedido)
                            <tr class="border-b border-gray-200 hover:bg-gray-100">
                                <td class="py-3 px-6 text-left">{{ $pedido->idPedido }}</td>
                                <td class="py-3 px-6 text-left">
                                    {{ $pedido->fechaPedido instanceof \Illuminate\Support\Carbon
                                        ? $pedido->fechaPedido->format('d/m/Y')
                                        : \Carbon\Carbon::parse($pedido->fechaPedido)->format('d/m/Y') }}
                                </td>
                                <td class="py-3 px-6 text-right">${{ number_format($pedido->totalPedido, 2, '.', '') }}</td>
                                <td class="py-3 px-6 text-center">{{ $pedido->estadoPedido->nomEstadoPedido ?? 'No especificado' }}</td>
                                <td class="py-3 px-6 text-center">
                                    <a href="{{ route('cuenta.detalle-pedido', $pedido->idPedido) }}" class="text-blue-600 hover:underline">Ver Detalles</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Tarjetas para pantallas pequeñas -->
            <div class="block md:hidden">
                @foreach ($pedidos as $pedido)
                    <div class="border border-gray-200 rounded-lg p-4 mb-4 bg-white shadow-sm hover:shadow-md transition-shadow">
                        <div class="grid grid-cols-1 gap-2">
                            <div>
                                <p class="text-gray-600 font-semibold">ID Pedido:</p>
                                <p>{{ $pedido->idPedido }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600 font-semibold">Fecha:</p>
                                <p>
                                    {{ $pedido->fechaPedido instanceof \Illuminate\Support\Carbon
                                        ? $pedido->fechaPedido->format('d/m/Y')
                                        : \Carbon\Carbon::parse($pedido->fechaPedido)->format('d/m/Y') }}
                                </p>
                            </div>
                            <div>
                                <p class="text-gray-600 font-semibold">Total:</p>
                                <p>${{ number_format($pedido->totalPedido, 2, '.', '') }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600 font-semibold">Estado:</p>
                                <p>{{ $pedido->estadoPedido->nomEstadoPedido ?? 'No especificado' }}</p>
                            </div>
                            <div class="mt-2 text-center">
                                <a href="{{ route('cuenta.detalle-pedido', $pedido->idPedido) }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Ver Detalles</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-600 text-center">No tienes pedidos registrados.</p>
        @endif
    </div>
</div>
<link href="{{ asset('css/admin-pedidos.css') }}" rel="stylesheet">
@endsection
