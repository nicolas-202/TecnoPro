@extends('layouts.layout')

@section('content')
<div class="container mx-auto max-w-4xl px-4 py-8">
    <h1 class="text-3xl md:text-4xl font-bold text-gray-800 text-center mb-8">Registrar Movimiento en Kardex (Entrada)</h1>

    <!-- Mensaje de éxito o error -->
    <!-- Debugging: Verificar si hay mensajes en la sesión -->
    @if (session('error'))
        <div class="bg-red-500 text-white px-4 py-3 rounded-md mb-4 font-semibold">
            {{ session('error') }}
        </div>
    @endif
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-md mb-4">
            {{ session('success') }}
        </div>
    @endif

    <!-- Formulario -->
    <div class="bg-white shadow-md rounded-lg p-6">
        <form action="{{ route('kardex.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="idProducto" class="block text-sm font-medium text-gray-700 mb-1">Producto</label>
                <select id="idProducto" name="idProducto" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                    <option value="">Seleccione un producto</option>
                    @foreach ($productos as $producto)
                        <option value="{{ $producto->idProducto }}" {{ old('idProducto') == $producto->idProducto ? 'selected' : '' }}>
                            {{ $producto->nomProducto }}
                        </option>
                    @endforeach
                </select>
                @error('idProducto')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="idProveedor" class="block text-sm font-medium text-gray-700 mb-1">Proveedor</label>
                <select id="idProveedor" name="idProveedor" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                    <option value="">Seleccione un proveedor</option>
                    @foreach ($proveedores as $proveedor)
                        <option value="{{ $proveedor->idProveedor }}" {{ old('idProveedor') == $proveedor->idProveedor ? 'selected' : '' }}>
                            {{ $proveedor->nomProveedor }}
                        </option>
                    @endforeach
                </select>
                @error('idProveedor')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <input type="hidden" name="tipoMovimiento" value="Entrada">
            </div>

            <div class="mb-4">
                <label for="cantidadMovimiento" class="block text-sm font-medium text-gray-700 mb-1">Cantidad</label>
                <input type="number" id="cantidadMovimiento" name="cantidadMovimiento" value="{{ old('cantidadMovimiento') }}" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" min="1" required>
                @error('cantidadMovimiento')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="costoUnitario" class="block text-sm font-medium text-gray-700 mb-1">Costo Unitario</label>
                <input type="number" id="costoUnitario" name="costoUnitario" value="{{ old('costoUnitario') }}" step="0.01" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                @error('costoUnitario')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="fechaMovimiento" class="block text-sm font-medium text-gray-700 mb-1">Fecha del Movimiento</label>
                <input type="date" id="fechaMovimiento" name="fechaMovimiento" value="{{ old('fechaMovimiento', now()->format('Y-m-d')) }}" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                @error('fechaMovimiento')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 w-full">Registrar Movimiento</button>
        </form>
    </div>
</div>
@endsection