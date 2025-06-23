
@extends('layouts.layout')

@section('content')
<div class="container mx-auto max-w-6xl px-4 py-8">
    <h1 class="text-3xl md:text-4xl font-bold text-gray-800 text-center mb-8">{{ $producto->nomProducto }}</h1>

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

    <!-- Detalle del Producto -->
    <div class="bg-white shadow-md rounded-lg p-6 flex flex-col md:flex-row gap-6">
        <div class="md:w-1/2">
            <img src="{{ $producto->imagen ? asset('storage/' . $producto->imagen) : asset('images/placeholder.jpg') }}" alt="{{ $producto->nomProducto }}" class="w-full h-96 object-contain rounded-md">
        </div>
        <div class="md:w-1/2">
            <h2 class="text-2xl font-semibold text-gray-800">{{ $producto->nomProducto }}</h2>
            <p class="text-gray-600 mt-2">$ {{ number_format($producto->precioVenta, 2) }}</p>
            <p class="text-gray-700 mt-4">{{ $producto->desProducto }}</p>
            <button class="agregar-carrito bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 mt-4" data-producto='{{ json_encode([
                "idProducto" => $producto->idProducto,
                "nomProducto" => $producto->nomProducto,
                "precioVenta" => $producto->precioVenta,
                "imagen" => $producto->imagen
            ]) }}'>Agregar al Carrito</button>
        </div>
    </div>

    <!-- Sección de Comentarios -->
    <div class="bg-white shadow-md rounded-lg mt-8 p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Comentarios</h2>

        @if (Auth::check())
            <!-- Formulario para agregar comentario -->
            <form id="formularioComentario" action="{{ route('comentarios.store', $producto->idProducto) }}" method="POST" class="mb-6">
                @csrf
                <div class="mb-4">
                    <label for="contenidoComentario" class="block text-sm font-medium text-gray-700 mb-1">Añadir Comentario</label>
                    <textarea id="contenidoComentario" name="contenidoComentario" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required></textarea>
                    @error('contenidoComentario')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Publicar Comentario</button>
            </form>
        @else
            <p class="text-gray-600 mb-4">Inicia sesión para dejar un comentario.</p>
        @endif

        <!-- Lista de Comentarios -->
        <div id="listaComentarios">
            @forelse ($comentarios as $comentario)
                <div class="border-t py-4 {{ Auth::check() && $comentario->idUsuario == Auth::user()->user_id ? 'bg-blue-50' : '' }}">
                    <div class="flex justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $comentario->user->nombre ?? 'Usuario' }}</p>
                            <p class="text-xs text-gray-500">
                                @if ($comentario->fechaComentario instanceof \Illuminate\Support\Carbon)
                                    {{ $comentario->fechaComentario->format('d/m/Y H:i') }}
                                @else
                                    {{ \Illuminate\Support\Carbon::parse($comentario->fechaComentario)->format('d/m/Y H:i') }}
                                @endif
                            </p>
                            <p class="text-gray-700 mt-2">{{ $comentario->contenidoComentario }}</p>
                        </div>
                        @if (Auth::check() && $comentario->idUsuario == Auth::user()->user_id)
                            <div class="flex space-x-2">
                                <form action="{{ route('comentarios.destroy', [$producto->idProducto, $comentario->idComentario]) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-600 text-xs" onclick="return confirm('¿Está seguro de eliminar este comentario?')">Eliminar</button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-gray-500">No hay comentarios para este producto.</p>
            @endforelse
        </div>
    </div>
</div>

<link href="{{ asset('css/producto_detalle.css') }}" rel="stylesheet">
<script src="{{ asset('js/catalogo/detalle_producto.js') }}"></script>
@endsection
