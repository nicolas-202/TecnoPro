@extends('layouts.layout')

@section('content')
<div class="container mx-auto max-w-6xl px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-800 text-center mb-6">¡Compra Exitosa!</h1>
    <p class="text-center text-gray-600 mb-4">Gracias por tu compra. Tu pedido ha sido procesado correctamente.</p>
    <div class="text-center">
        <a href="{{ route('home') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Volver al inicio</a>
    </div>
</div>
@endsection