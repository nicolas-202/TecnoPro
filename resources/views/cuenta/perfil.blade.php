
@extends('layouts.layout')

@section('content')
<div class="container mx-auto max-w-6xl px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Mi Cuenta</h1>

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Información Personal</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <p><strong>ID de Usuario:</strong> {{ $usuario->user_id }}</p>
            <p><strong>Nombre:</strong> {{ $usuario->nombre }}</p>
            <p><strong>Email:</strong> {{ $usuario->email }}</p>
            <p><strong>Celular:</strong> {{ $usuario->celular }}</p>
            <p><strong>Fecha de Nacimiento:</strong> {{ $usuario->fecha_nacimiento->format('d/m/Y') }}</p>
            <p><strong>Número de Documento:</strong> {{ $usuario->numero_documento }}</p>
            <p><strong>Tipo de Documento:</strong> {{ $usuario->tipoDocumento->nomTipoDocumento ?? 'No especificado' }}</p>
            <p><strong>Género:</strong> {{ $usuario->genero->nomGenero ?? 'No especificado' }}</p>
            <p><strong>Dirección:</strong> {{ $usuario->direccion }}</p>
            <p><strong>Municipio:</strong> {{ $usuario->municipio->nomMunicipio ?? 'No especificado' }}</p>
        </div>
        <div class="mt-6 flex space-x-4">
            <a href="{{ route('cuenta.editar') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Editar Información</a>
            <a href="{{ route('cuenta.pedidos') }}" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">Tus Pedidos</a>
        </div>
    </div>
</div>
@endsection
