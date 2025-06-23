@extends('layouts.layout')

@section('content')
<div class="container mx-auto max-w-6xl px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Detalles de la Solicitud #{{ $solicitud->idSolDevReem ?? 'N/A' }}</h1>

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

    <!-- Información de la Solicitud -->
    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Información de la Solicitud</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-gray-600 font-semibold">ID Solicitud:</p>
                <p>{{ $solicitud->idSolDevReem ?? 'No especificado' }}</p>
            </div>
            <div>
                <p class="text-gray-600 font-semibold">ID Pedido:</p>
                <p>
                    @if ($solicitud->pedidoProducto && $solicitud->pedidoProducto->pedido)
                        <a href="{{ route('admin.detalle-pedido-solicitud', $solicitud->pedidoProducto->pedido->idPedido) }}"
                           class="text-blue-600 hover:underline">
                            {{ $solicitud->pedidoProducto->pedido->idPedido }}
                        </a>
                    @else
                        No especificado
                    @endif
                </p>
            </div>
            <div>
                <p class="text-gray-600 font-semibold">Producto:</p>
                <p>{{ $solicitud->pedidoProducto->producto->nomProducto ?? 'No especificado' }}</p>
            </div>
            <div>
                <p class="text-gray-600 font-semibold">Fecha:</p>
                <p>{{ \Carbon\Carbon::parse($solicitud->fechaSolDevReem)->format('d/m/Y') ?? 'No especificado' }}</p>
            </div>
            <div>
                <p class="text-gray-600 font-semibold">Estado Actual:</p>
                <p>{{ $solicitud->estadoSolicitud->nomEstadoSolDevReem ?? 'No especificado' }}</p>
            </div>
            <div class="col-span-2">
                <p class="text-gray-600 font-semibold">Motivo (Comentario del Usuario):</p>
                <p>{{ $solicitud->comentarioSolDevReem ?? 'No especificado' }}</p>
            </div>
            <div class="col-span-2">
                <p class="text-gray-600 font-semibold">Respuesta Administrativa:</p>
                <p>{{ $solicitud->respuestaSolDevReem ?? 'Sin respuesta' }}</p>
            </div>
        </div>
    </div>

    <!-- Actualizar Estado de la Solicitud -->
    <div class="bg-white shadow-md rounded-lg p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Actualizar Estado de la Solicitud</h2>
        @if ($solicitud->idSolDevReem)
            <form action="{{ route('admin.actualizar-estado-solicitud', $solicitud->idSolDevReem) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label for="idEstadoSolDevReem" class="block text-gray-700 font-semibold mb-2">Seleccionar Nuevo Estado</label>
                    <select name="idEstadoSolDevReem" id="idEstadoSolDevReem" class="w-full border-2 rounded-md p-2 focus:ring focus:ring-blue-200">
                        @foreach ($estados as $estado)
                            <option value="{{ $estado->idEstadoSolDevReem }}"
                                    {{ $solicitud->idEstadoSolDevReem == $estado->idEstadoSolDevReem ? 'selected' : '' }}>
                                {{ $estado->nomEstadoSolDevReem }}
                            </option>
                        @endforeach
                    </select>
                    @error('idEstadoSolDevReem')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="respuestaSolDevReem" class="block text-gray-700 font-semibold mb-2">Respuesta Administrativa</label>
                    <textarea name="respuestaSolDevReem" id="respuestaSolDevReem" class="w-full border-2 rounded-md p-2 focus:ring focus:ring-blue-200"
                              rows="5">{{ old('respuestaSolDevReem', $solicitud->respuestaSolDevReem) }}</textarea>
                    @error('respuestaSolDevReem')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:ring focus:ring-blue-200 min-w-[120px]">
                    Guardar Cambios
                </button>
            </form>
        @else
            <p class="text-red-500">No se puede actualizar la solicitud porque el ID es inválido.</p>
        @endif
    </div>

    <!-- Volver -->
    <div class="mt-6">
        <a href="{{ route('admin.solicitudes') }}" class="text-blue-600 hover:underline">Volver a Gestión de Solicitudes</a>
    </div>
</div>
<link href="{{ asset('css/admin-solicitudes.css') }}" rel="stylesheet">
@endsection
