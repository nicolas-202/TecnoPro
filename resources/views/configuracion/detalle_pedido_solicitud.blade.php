
@extends('layouts.layout')

@section('content')
<div class="container mx-auto max-w-6xl px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Detalles del Pedido #{{ $pedido->idPedido }}</h1>

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

    <!-- Volver -->
    <div class="mt-6">
        <a href="{{ route('admin.solicitudes') }}" class="text-blue-600 hover:underline">Volver a Gestión de Solicitudes</a>
    </div>
</div>
<link href="{{ asset('css/admin-solicitudes.css') }}" rel="stylesheet">
@endsection
