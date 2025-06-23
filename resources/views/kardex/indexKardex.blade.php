@extends('layouts.layout')

@section('content')
<div class="container mx-auto max-w-4xl px-4 py-8">
    <h1 class="text-3xl md:text-4xl font-bold text-gray-800 text-center mb-8">Kardex de Productos</h1>

    <!-- Product Selection -->
    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <form id="productForm" action="{{ route('kardex.index') }}" method="GET">
            <div class="mb-4">
                <label for="idProducto" class="block text-sm font-medium text-gray-700 mb-1">Seleccionar Producto</label>
                <select id="idProducto" name="idProducto" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" onchange="this.form.submit()">
                    <option value="">Seleccione un producto</option>
                    @foreach ($productos as $productoItem)
                        <option value="{{ $productoItem->idProducto }}" {{ $productoItem->idProducto == $producto?->idProducto ? 'selected' : '' }}>
                            {{ $productoItem->nomProducto }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    <!-- Product Information and Kardex Movements -->
    @if ($producto)
        <!-- Product Information -->
        <div class="bg-white shadow-md rounded-lg p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Información del Producto</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p><strong>Nombre:</strong> {{ $producto->nomProducto }}</p>
                    <p><strong>Código:</strong> {{ $producto->idProducto }}</p>
                </div>
                <div>
                    <p><strong>Stock Actual:</strong> {{ $producto->cantidadExistente }}</p>
                    <p><strong>Precio de Venta:</strong> {{ number_format($producto->precioVenta, 2) }}</p>
                </div>
            </div>
        </div>

        <!-- Success Message -->
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-md mb-6">
                {{ session('success') }}
            </div>
        @endif

        <!-- Kardex Movements Table -->
        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Movimientos del Kardex</h2>
            @if ($movimientos->isEmpty())
                <p class="text-gray-500">No hay movimientos registrados para este producto.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Costo Unitario</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Costo Total</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio Venta</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($movimientos as $movimiento)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $movimiento->fechaMovimiento }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $movimiento->tipoMovimiento }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $movimiento->cantidadMovimiento }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($movimiento->costoUnitario, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($movimiento->costoTotal, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($movimiento->precioVentaActualizado, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @else
        <div class="bg-white shadow-md rounded-lg p-6">
            <p class="text-gray-500">Seleccione un producto para ver sus detalles y movimientos.</p>
        </div>
    @endif

    <!-- Create Movement Button -->
    <div class="mt-6">
        <a href="{{ route('kardex.create') }}" class="inline-block bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Registrar Nuevo Movimiento</a>
    </div>
</div>

@endsection