@extends('layouts.layout')

@section('content')
<div class="container mx-auto max-w-6xl px-4 py-8">
    <h1 class="text-2xl md:text-3xl font-bold text-gray-800 text-center mb-6">Carrito de Compras</h1>

    <!-- Mensaje de éxito o error -->
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-md mb-4">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-md mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="flex flex-col md:flex-row gap-8">
        <!-- Lista de productos en el carrito -->
        <div id="carrito-productos" class="w-full md:w-2/3 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
            <!-- Productos se llenarán dinámicamente con JavaScript -->
        </div>

        <!-- Resumen del pedido -->
        <div id="resumen-carrito" class="w-full md:w-1/3 bg-white shadow-md rounded-lg p-6 sticky top-4">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Resumen del Pedido</h2>
            <div id="resumen-items" class="mb-4">
                <!-- Items del resumen se llenarán dinámicamente con JavaScript -->
            </div>
            <div class="flex justify-between text-lg font-bold text-gray-800">
                <span>Total:</span>
                <span id="total-pedido">$ 0.00</span>
            </div>
            <a href="{{ route('carrito.confirmar') }}" id="confirmar-pedido" class="block bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 text-center mt-4">Confirmar Pedido</a>
        </div>
    </div>
</div>

<link href="{{ asset('css/carrito.css') }}" rel="stylesheet">
<script src="{{ asset('js/carrito/carrito.js') }}"></script>
@endsection
