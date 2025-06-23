@extends('layouts.layout')

@section('content')
<div class="container mx-auto max-w-6xl px-4 py-8">
    <h1 class="text-3xl md:text-4xl font-bold text-gray-800 text-center mb-8">Gestión de Pedidos y Solicitudes de devolución y reembolso</h1>

    <div class="bg-white shadow-md rounded-lg p-6 flex justify-center space-x-4">
        <a href="{{ route('admin.pedidos') }}" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 text-sm font-medium">Gestionar pedidos</a>
        <a href="{{ route('admin.solicitudes') }}" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 text-sm font-medium">Gestionar solicitudes devolucion/reembolso</a>
    </div>
</div>
@endsection