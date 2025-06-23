@extends('layouts.layout')

@section('content')
<div class="container mx-auto max-w-6xl px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Detalles del Pedido #{{ $pedido->idPedido }}</h1>

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

    <!-- Información del Pedido -->
    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Información del Pedido</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-gray-600 font-semibold">ID Pedido:</p>
                <p>{{ $pedido->idPedido }}</p>
            </div>
            <div>
                <p class="text-gray-600 font-semibold">ID Usuario:</p>
                <p>{{ $pedido->idUsuario }} ({{ $pedido->usuario->nomUsuario ?? 'No especificado' }})</p>
            </div>
            <div>
                <p class="text-gray-600 font-semibold">Dirección de Entrega:</p>
                <p>{{ $pedido->usuario->municipio->nomMunicipio ?? 'No especificado' }}, {{ $pedido->usuario->direccion ?? 'No especificado' }}</p>
            </div>
            <div>
                <p class="text-gray-600 font-semibold">Precio Total:</p>
                <p>${{ number_format($pedido->totalPedido, 2, '.', '') }}</p>
            </div>
            <div>
                <p class="text-gray-600 font-semibold">Fecha:</p>
                <p>{{ \Carbon\Carbon::parse($pedido->fechaPedido)->format('d/m/Y') }}</p>
            </div>
            <div>
                <p class="text-gray-600 font-semibold">Estado Actual:</p>
                <p>{{ $pedido->estadoPedido->nomEstadoPedido ?? 'No especificado' }}</p>
            </div>
        </div>
    </div>

    <!-- Actualizar Estado del Pedido -->
    <div class="bg-white shadow-md rounded-lg p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Actualizar Estado del Pedido</h2>
        <form action="{{ route('admin.actualizar-estado-pedido', $pedido->idPedido) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label for="idEstadoPedido" class="block text-gray-700 font-semibold mb-2">Seleccionar Nuevo Estado</label>
                <select name="idEstadoPedido" id="idEstadoPedido" class="w-full border-2 rounded-md p-2 focus:ring focus:ring-blue-200">
                    @foreach ($estados as $estado)
                        <option value="{{ $estado->idEstadoPedido }}"
                                {{ $pedido->idEstadoPedido == $estado->idEstadoPedido ? 'selected' : '' }}>
                            {{ $estado->nomEstadoPedido }}
                        </option>
                    @endforeach
                </select>
                @error('idEstadoPedido')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:ring focus:ring-blue-200 min-w-[120px]">
                Guardar Cambios
            </button>
        </form>
    </div>

    <!-- Volver -->
    <div class="mt-6">
        <a href="{{ route('admin.pedidos') }}" class="text-blue-600 hover:underline">Volver a Gestión de Pedidos</a>
    </div>
</div>
<link href="{{ asset('css/admin-solicitudes.css') }}" rel="stylesheet">
@endsection
