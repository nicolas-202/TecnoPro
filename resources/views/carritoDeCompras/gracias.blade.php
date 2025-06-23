@extends('layouts.layout')

@section('content')
<div class="container mx-auto max-w-4xl px-4 py-8 text-center">
    <h1 class="text-3xl font-bold text-gray-800 mb-4">¡Gracias por tu compra!</h1>
    <p class="text-gray-600 mb-6">Tu pedido ha sido procesado correctamente. Recibirás una confirmación pronto.</p>
    <a href="{{ route('home') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Volver al Inicio</a>
</div>
@endsection