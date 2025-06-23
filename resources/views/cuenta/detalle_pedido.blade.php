
@extends('layouts.layout')

@section('content')
<div class="container mx-auto max-w-6xl px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Detalles del Pedido #{{ $pedido->idPedido ?? 'N/A' }}</h1>

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

    <div class="bg-white shadow-md rounded-lg p-6">
        <!-- Encabezado del pedido -->
        <div class="mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Información del Pedido</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <p class="text-gray-600 font-semibold">ID Pedido:</p>
                    <p>{{ $pedido->idPedido ?? 'No disponible' }}</p>
                </div>
                <div>
                    <p class="text-gray-600 font-semibold">Fecha:</p>
                    <p>
                        @if ($pedido->fechaPedido)
                            {{ $pedido->fechaPedido instanceof \Illuminate\Support\Carbon
                                ? $pedido->fechaPedido->format('d/m/Y')
                                : \Carbon\Carbon::parse($pedido->fechaPedido)->format('d/m/Y') }}
                        @else
                            No disponible
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-gray-600 font-semibold">Total:</p>
                    <p>${{ isset($pedido->totalPedido) ? number_format($pedido->totalPedido, 2, '.', '') : '0.00' }}</p>
                </div>
                <div>
                    <p class="text-gray-600 font-semibold">Estado:</p>
                    <p>{{ $pedido->estadoPedido->nomEstadoPedido ?? 'No especificado' }}</p>
                </div>
            </div>
        </div>

        <!-- Productos del pedido -->
        <div class="mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Productos</h2>

            @if ($pedido->productos && $pedido->productos->isNotEmpty())
                <!-- Tabla para pantallas medianas y grandes (md y superiores) -->
                <div class="hidden md:block">
                    <table class="w-full table-auto border-collapse">
                        <thead>
                            <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                                <th class="py-3 px-6 text-left">Producto</th>
                                <th class="py-3 px-6 text-center">Cantidad</th>
                                <th class="py-3 px-6 text-right">Precio Unitario</th>
                                <th class="py-3 px-6 text-right">Total</th>
                                <th class="py-3 px-6 text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 text-sm font-light">
                            @foreach ($pedido->productos as $producto)
                                <tr class="border-b border-gray-200 hover:bg-gray-100">
                                    <td class="py-3 px-6 text-left">
                                        {{ $producto->producto && $producto->producto->nomProducto ? $producto->producto->nomProducto : 'Producto no disponible' }}
                                    </td>
                                    <td class="py-3 px-6 text-center">{{ $producto->cantidadProducto ?? '0' }}</td>
                                    <td class="py-3 px-6 text-right">${{ $producto->precioProducto ? number_format($producto->precioProducto, 2, '.', '') : '0.00' }}</td>
                                    <td class="py-3 px-6 text-right">${{ $producto->totalProducto ? number_format($producto->totalProducto, 2, '.', '') : '0.00' }}</td>
                                    <td class="py-3 px-6 text-center">
                                        <button
                                            type="button"
                                            class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:ring focus:ring-blue-200 devolucion-btn"
                                            data-id="{{ $producto->idPedidoProducto }}"
                                            data-nombre="{{ $producto->producto && $producto->producto->nomProducto ? htmlspecialchars($producto->producto->nomProducto, ENT_QUOTES, 'UTF-8') : 'Producto' }}"
                                            data-cantidad="{{ $producto->cantidadProducto ?? 0 }}"
                                        >
                                            Devolución/Reembolso
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Tarjetas para pantallas pequeñas (sm y menores) -->
                <div class="block md:hidden">
                    @foreach ($pedido->productos as $producto)
                        <div class="border border-gray-200 rounded-lg p-4 mb-4 bg-white shadow-sm hover:shadow-md transition-shadow">
                            <div class="grid grid-cols-1 gap-2">
                                <div>
                                    <p class="text-gray-600 font-semibold">Producto:</p>
                                    <p>{{ $producto->producto && $producto->producto->nomProducto ? $producto->producto->nomProducto : 'Producto no disponible' }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600 font-semibold">Cantidad:</p>
                                    <p>{{ $producto->cantidadProducto ?? '0' }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600 font-semibold">Precio Unitario:</p>
                                    <p>${{ $producto->precioProducto ? number_format($producto->precioProducto, 2, '.', '') : '0.00' }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600 font-semibold">Total:</p>
                                    <p>${{ $producto->totalProducto ? number_format($producto->totalProducto, 2, '.', '') : '0.00' }}</p>
                                </div>
                                <div class="mt-2 text-center">
                                    <button
                                        type="button"
                                        class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:ring focus:ring-blue-200 devolucion-btn"
                                        data-id="{{ $producto->idPedidoProducto }}"
                                        data-nombre="{{ $producto->producto && $producto->producto->nomProducto ? htmlspecialchars($producto->producto->nomProducto, ENT_QUOTES, 'UTF-8') : 'Producto' }}"
                                        data-cantidad="{{ $producto->cantidadProducto ?? 0 }}"
                                    >
                                        Devolución/Reembolso
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Modal de Devolución -->
                <div id="devolucionModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden z-50">
                    <div class="bg-white rounded-lg p-6 w-full max-w-md">
                        <h2 id="modalTitle" class="text-xl font-semibold text-gray-800 mb-4">Solicitar Devolución</h2>
                        <form id="devolucionForm" action="{{ route('cuenta.procesar-devolucion') }}" method="POST">
                            @csrf
                            <input type="hidden" name="idPedidoProducto" id="idPedidoProducto" value="">
                            <div class="mb-4">
                                <label for="cantidad" class="block text-gray-700 font-semibold mb-2">Cantidad a devolver:</label>
                                <input type="number" name="cantidad" id="cantidad" min="1" class="w-full border rounded-md p-2 focus:ring focus:ring-blue-200" required>
                                <p id="cantidadMax" class="text-sm text-gray-500 mt-1"></p>
                                @error('cantidad')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="motivo" class="block text-gray-700 font-semibold mb-2">Motivo de la devolución:</label>
                                <textarea name="motivo" id="motivo" class="w-full border rounded-md p-2 focus:ring focus:ring-blue-200" rows="4" required></textarea>
                                @error('motivo')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="flex justify-end gap-4">
                                <button type="button" onclick="closeDevolucionModal()" class="bg-gray-300 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-400 focus:ring focus:ring-gray-200">Cancelar</button>
                                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:ring focus:ring-blue-200">Enviar Solicitud</button>
                            </div>
                        </form>
                    </div>
                </div>
            @else
                <p class="text-gray-600 text-center">No hay productos asociados a este pedido.</p>
            @endif
        </div>

        <!-- Botón Volver -->
        <div class="mt-6 text-center md:text-left">
            <a href="{{ route('cuenta.pedidos') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors focus:ring focus:ring-blue-200">Volver a Pedidos</a>
        </div>
    </div>
</div>


<script src="{{ asset('js/cuenta/detalle_pedido.js') }}">
   
</script>
<link href="{{ asset('css/admin-pedidos.css') }}" rel="stylesheet">
@endsection
