@extends('layouts.layout')

@section('content')
<div class="container mx-auto max-w-4xl px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 text-center mb-8">Ingresar Datos de Pago</h1>

    @if (session('error'))
        <div class="bg-red-500 text-white px-4 py-3 rounded-md mb-4 font-semibold">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg p-6">
        <form action="{{ route('carrito.procesar_pago') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="numero_tarjeta" class="block text-sm font-medium text-gray-700 mb-1">Número de Tarjeta</label>
                <input type="text" id="numero_tarjeta" name="numero_tarjeta" value="{{ old('numero_tarjeta') }}" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" maxlength="16" placeholder="1234567890123456" required>
                @error('numero_tarjeta')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="nombre_titular" class="block text-sm font-medium text-gray-700 mb-1">Nombre del Titular</label>
                <input type="text" id="nombre_titular" name="nombre_titular" value="{{ old('nombre_titular') }}" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Juan Pérez" required>
                @error('nombre_titular')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4 flex space-x-4">
                <div class="w-1/2">
                    <label for="fecha_expiracion" class="block text-sm font-medium text-gray-700 mb-1">Fecha de Expiración</label>
                    <input type="text" id="fecha_expiracion" name="fecha_expiracion" value="{{ old('fecha_expiracion') }}" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="MM/AA" required>
                    @error('fecha_expiracion')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div class="w-1/2">
                    <label for="cvv" class="block text-sm font-medium text-gray-700 mb-1">CVV</label>
                    <input type="text" id="cvv" name="cvv" value="{{ old('cvv') }}" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" maxlength="3" placeholder="123" required>
                    @error('cvv')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 w-full">Confirmar Pago</button>
        </form>
    </div>
</div>
@endsection