@extends('layouts.layout')

@section('content')
<div class="container mx-auto max-w-6xl px-4 py-8">
    <h1 class="text-2xl md:text-3xl font-bold text-gray-800 text-center mb-6">Catálogo de Productos</h1>

    <!-- Category Filter Dropdown -->
    <div class="mb-6">
        <label for="categoryFilter" class="text-gray-700 font-semibold">Filtrar por Categoría:</label>
        <select id="categoryFilter" name="idCategoria" class="ml-2 p-2 border rounded-md text-gray-600">
            <option value="">Todas las categorías</option>
            @foreach ($categorias as $categoria)
                <option value="{{ $categoria->idCategoria }}" {{ request('idCategoria') == $categoria->idCategoria ? 'selected' : '' }}>
                    {{ $categoria->nomCategoria }}
                </option>
            @endforeach
        </select>
    </div>

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

    <!-- Catálogo de Productos -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @forelse ($productos as $producto)
            <div class="bg-white shadow-md rounded-lg overflow-hidden transform transition hover:scale-105">
                <a href="{{ route('catalogo.show', $producto->idProducto) }}">
                    <img src="{{ $producto->imagen ? asset('storage/' . $producto->imagen) : asset('images/placeholder.jpg') }}" alt="{{ $producto->nomProducto }}" class="w-full h-40 object-cover">
                </a>
                <div class="p-3">
                    <h2 class="text-base font-semibold text-gray-800 truncate">{{ $producto->nomProducto }}</h2>
                    <p class="text-gray-600 text-sm mt-1">$ {{ number_format($producto->precioVenta, 2) }}</p>
                    <button class="agregar-carrito bg-blue-600 text-white px-3 py-1 rounded-md hover:bg-blue-700 w-full mt-2 text-sm" data-producto='{{ json_encode([
                        "idProducto" => $producto->idProducto,
                        "nomProducto" => $producto->nomProducto,
                        "precioVenta" => $producto->precioVenta,
                        "imagen" => $producto->imagen
                    ]) }}'>Agregar al Carrito</button>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center text-gray-500">No hay productos disponibles.</div>
        @endforelse
    </div>

    <!-- Paginación -->
    <div class="mt-6">
        {{ $productos->appends(['idCategoria' => request('idCategoria')])->links('pagination::tailwind') }}
    </div>
</div>

<link href="{{ asset('css/catalogo.css') }}" rel="stylesheet">
<script src="{{ asset('js/catalogo/catalogo.js') }}"></script>
@endsection
