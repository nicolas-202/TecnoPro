
@extends('layouts.layout')

@section('content')
<div class="container mx-auto max-w-6xl px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Gestión de Pedidos</h1>

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
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Buscar Pedidos</h2>
        <form action="{{ route('admin.pedidos.search') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="idPedido" class="block text-gray-700 font-semibold mb-2">ID Pedido</label>
                <input type="number" name="idPedido" id="idPedido" class="w-full border-2 rounded-md p-2 focus:ring focus:ring-blue-200"
                       value="{{ request('idPedido') }}">
            </div>
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
                                <th class="py-3 px-6 text-left">ID Pedido</th>
                                <th class="py-3 px-6 text-left">ID Usuario</th>
                                <th class="py-3 px-6 text-left">Fecha</th>
                                <th class="py-3 px-6 text-right">Total</th>
                                <th class="py-3 px-6 text-center">Estado</th>
                                <th class="py-3 px-6 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 text-sm font-light">
                            @foreach ($searchResults as $pedido)
                                <tr class="border-b border-gray-200 hover:bg-gray-100">
                                    <td class="py-3 px-6 text-left">{{ $pedido->idPedido }}</td>
                                    <td class="py-3 px-6 text-left">{{ $pedido->idUsuario }}</td>
                                    <td class="py-3 px-6 text-left">
                                        {{ \Carbon\Carbon::parse($pedido->fechaPedido)->format('d/m/Y') }}
                                    </td>
                                    <td class="py-3 px-6 text-right">${{ number_format($pedido->totalPedido, 2, '.', '') }}</td>
                                    <td class="py-3 px-6 text-center">{{ $pedido->estadoPedido->nomEstadoPedido ?? 'No especificado' }}</td>
                                    <td class="py-3 px-6 text-center">
                                        <a href="{{ route('admin.detalle-pedido', $pedido->idPedido) }}" class="text-blue-600 hover:underline">Ver Detalles</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-600 text-center mt-4">No se encontraron pedidos.</p>
            @endif
        @endif
    </div>

    <!-- 10 Primeros Pedidos No Revisados -->
    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">10 Primeros Pedidos No Revisados</h2>
        @if (!empty($pedidosNoRevisados) && $pedidosNoRevisados->isNotEmpty())
            <div class="hidden md:block">
                <table class="w-full table-auto border-collapse">
                    <thead>
                        <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                            <th class="py-3 px-6 text-left">ID Pedido</th>
                            <th class="py-3 px-6 text-left">ID Usuario</th>
                            <th class="py-3 px-6 text-left">Fecha</th>
                            <th class="py-3 px-6 text-right">Total</th>
                            <th class="py-3 px-6 text-center">Estado</th>
                            <th class="py-3 px-6 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm font-light">
                        @foreach ($pedidosNoRevisados as $pedido)
                            <tr class="border-b border-gray-200 hover:bg-gray-100">
                                <td class="py-3 px-6 text-left">{{ $pedido->idPedido }}</td>
                                <td class="py-3 px-6 text-left">{{ $pedido->idUsuario }}</td>
                                <td class="py-3 px-6 text-left">
                                    {{ \Carbon\Carbon::parse($pedido->fechaPedido)->format('d/m/Y') }}
                                </td>
                                <td class="py-3 px-6 text-right">${{ number_format($pedido->totalPedido, 2, '.', '') }}</td>
                                <td class="py-3 px-6 text-center">{{ $pedido->estadoPedido->nomEstadoPedido ?? 'No especificado' }}</td>
                                <td class="py-3 px-6 text-center">
                                    <a href="{{ route('admin.detalle-pedido', $pedido->idPedido) }}" class="text-blue-600 hover:underline">Ver Detalles</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="block md:hidden">
                @foreach ($pedidosNoRevisados as $pedido)
                    <div class="border border-gray-200 rounded-lg p-4 mb-4 bg-white shadow-sm hover:shadow-md transition-shadow">
                        <div class="grid grid-cols-1 gap-2">
                            <div><p class="text-gray-600 font-semibold">ID Pedido:</p><p>{{ $pedido->idPedido }}</p></div>
                            <div><p class="text-gray-600 font-semibold">ID Usuario:</p><p>{{ $pedido->idUsuario }}</p></div>
                            <div><p class="text-gray-600 font-semibold">Fecha:</p><p>{{ \Carbon\Carbon::parse($pedido->fechaPedido)->format('d/m/Y') }}</p></div>
                            <div><p class="text-gray-600 font-semibold">Total:</p><p>${{ number_format($pedido->totalPedido, 2, '.', '') }}</p></div>
                            <div><p class="text-gray-600 font-semibold">Estado:</p><p>{{ $pedido->estadoPedido->nomEstadoPedido ?? 'No especificado' }}</p></div>
                            <div class="mt-2 text-center">
                                <a href="{{ route('admin.detalle-pedido', $pedido->idPedido) }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 min-w-[120px]">Ver Detalles</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-600 text-center">No hay pedidos no revisados.</p>
        @endif
    </div>

    <!-- Últimos 10 Pedidos Revisados -->
    <div class="bg-white shadow-md rounded-lg p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Últimos 10 Pedidos Revisados</h2>
        @if (!empty($pedidosRevisados) && $pedidosRevisados->isNotEmpty())
            <div class="hidden md:block">
                <table class="w-full table-auto border-collapse">
                    <thead>
                        <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                            <th class="py-3 px-6 text-left">ID Pedido</th>
                            <th class="py-3 px-6 text-left">ID Usuario</th>
                            <th class="py-3 px-6 text-left">Fecha</th>
                            <th class="py-3 px-6 text-right">Total</th>
                            <th class="py-3 px-6 text-center">Estado</th>
                            <th class="py-3 px-6 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm font-light">
                        @foreach ($pedidosRevisados as $pedido)
                            <tr class="border-b border-gray-200 hover:bg-gray-100">
                                <td class="py-3 px-6 text-left">{{ $pedido->idPedido }}</td>
                                <td class="py-3 px-6 text-left">{{ $pedido->idUsuario }}</td>
                                <td class="py-3 px-6 text-left">
                                    {{ \Carbon\Carbon::parse($pedido->fechaPedido)->format('d/m/Y') }}
                                </td>
                                <td class="py-3 px-6 text-right">${{ number_format($pedido->totalPedido, 2, '.', '') }}</td>
                                <td class="py-3 px-6 text-center">{{ $pedido->estadoPedido->nomEstadoPedido ?? 'No especificado' }}</td>
                                <td class="py-3 px-6 text-center">
                                    <a href="{{ route('admin.detalle-pedido', $pedido->idPedido) }}" class="text-blue-600 hover:underline">Ver Detalles</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="block md:hidden">
                @foreach ($pedidosRevisados as $pedido)
                    <div class="border border-gray-200 rounded-lg p-4 mb-4 bg-white shadow-sm hover:shadow-md transition-shadow">
                        <div class="grid grid-cols-1 gap-2">
                            <div><p class="text-gray-600 font-semibold">ID Pedido:</p><p>{{ $pedido->idPedido }}</p></div>
                            <div><p class="text-gray-600 font-semibold">ID Usuario:</p><p>{{ $pedido->idUsuario }}</p></div>
                            <div><p class="text-gray-600 font-semibold">Fecha:</p><p>{{ \Carbon\Carbon::parse($pedido->fechaPedido)->format('d/m/Y') }}</p></div>
                            <div><p class="text-gray-600 font-semibold">Total:</p><p>${{ number_format($pedido->totalPedido, 2, '.', '') }}</p></div>
                            <div><p class="text-gray-600 font-semibold">Estado:</p><p>{{ $pedido->estadoPedido->nomEstadoPedido ?? 'No especificado' }}</p></div>
                            <div class="mt-2 text-center">
                                <a href="{{ route('admin.detalle-pedido', $pedido->idPedido) }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 min-w-[120px]">Ver Detalles</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-600 text-center">No hay pedidos revisados.</p>
        @endif
    </div>
</div>
<link href="{{ asset('css/admin-solicitudes.css') }}" rel="stylesheet">
@endsection
