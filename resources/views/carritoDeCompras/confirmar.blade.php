
@extends('layouts.layout')

@section('content')
<div class="container mx-auto max-w-6xl px-4 py-8">
    <h1 class="text-2xl md:text-3xl font-bold text-gray-800 text-center mb-6">Confirmar Pedido</h1>

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
        <!-- Dirección de envío -->
        <div class="w-full md:w-1/2 bg-white shadow-md rounded-lg p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Dirección de Envío</h2>
            <div id="direccion-envio">
                <p><strong>País:</strong> {{ $usuario->pais ?? 'No especificado' }}</p>
                <p><strong>Departamento:</strong> {{ $usuario->departamento ?? 'No especificado' }}</p>
                <p><strong>Municipio:</strong> 
                    @php
                        $municipio = $usuario->municipio ?? 'No especificado';
                        if (is_string($municipio) && json_decode($municipio, true)) {
                            $municipioData = json_decode($municipio, true);
                            $municipio = $municipioData['nomMunicipio'] ?? 'No especificado';
                        } elseif (is_object($municipio) && isset($municipio->nomMunicipio)) {
                            $municipio = $municipio->nomMunicipio;
                        }
                        echo $municipio;
                    @endphp
                </p>
                <p><strong>Dirección:</strong> {{ $usuario->direccion ?? 'No especificado' }}</p>
            </div>
            <button id="modificar-direccion" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 mt-4">Modificar Dirección</button>
            <form id="form-direccion" class="hidden mt-4" action="{{ route('carrito.actualizarDireccion') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="idPais" class="block text-gray-600">País</label>
                    <select id="idPais" name="idPais" class="w-full p-2 border rounded-md" required>
                        <option value="">Seleccione un país</option>
                        @foreach ($paises as $pais)
                            <option value="{{ $pais->idPais }}" {{ $usuario->pais == $pais->nomPais ? 'selected' : '' }}>{{ $pais->nomPais }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label for="idDepartamento" class="block text-gray-600">Departamento</label>
                    <select id="idDepartamento" name="idDepartamento" class="w-full p-2 border rounded-md" required>
                        <option value="">Seleccione un departamento</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="idMunicipio" class="block text-gray-600">Municipio</label>
                    <select id="idMunicipio" name="idMunicipio" class="w-full p-2 border rounded-md" required>
                        <option value="">Seleccione un municipio</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="direccion" class="block text-gray-600">Dirección</label>
                    <input type="text" id="direccion" name="direccion" value="{{ $usuario->direccion ?? '' }}" class="w-full p-2 border rounded-md" required>
                </div>
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">Guardar Dirección</button>
                <button type="button" id="cancelar-direccion" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 ml-2">Cancelar</button>
            </form>
        </div>

        <!-- Resumen del pedido -->
        <div class="w-full md:w-1/2 bg-white shadow-md rounded-lg p-6 sticky top-4">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Resumen del Pedido</h2>
            <div id="resumen-items" class="mb-4">
                @foreach ($carrito as $idProducto => $item)
                    <div class="flex justify-between text-gray-600 text-sm mb-2">
                        <span>{{ $item['nombre'] }} (x{{ $item['cantidad'] }})</span>
                        <span>S/ {{ number_format($item['cantidad'] * $item['precioUnitario'], 2) }}</span>
                    </div>
                @endforeach
            </div>
            <div class="flex justify-between text-lg font-bold text-gray-800">
                <span>Total:</span>
                <span id="total-pedido">S/ {{ number_format($total, 2) }}</span>
            </div>
            <div class="relative mt-4">
                <button id="realizar-compra" class="block bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 text-center w-full">Seleccionar Forma de Pago</button>
                <div id="dropdown-formas-pago" class="hidden absolute z-10 bg-white shadow-md rounded-md w-full mt-2">
                    @forelse ($formasPago as $formaPago)
                        <button class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 select-forma-pago" data-id="{{ $formaPago->idFormaPago }}">{{ $formaPago->nomFormaPago }}</button>
                    @empty
                        <div class="px-4 py-2 text-sm text-gray-500">No hay formas de pago disponibles.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para datos de tarjeta -->
    <div id="modal-tarjeta" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Datos de la Tarjeta</h2>
            <form id="form-tarjeta">
                <input type="hidden" id="idFormaPago" name="idFormaPago">
                <div class="mb-4">
                    <label for="numero-tarjeta" class="block text-gray-600">Número de Tarjeta</label>
                    <input type="text" id="numero-tarjeta" name="numero_tarjeta" class="w-full p-2 border rounded-md" placeholder="1234 5678 9012 3456" required>
                </div>
                <div class="mb-4">
                    <label for="nombre-titular" class="block text-gray-600">Nombre del Titular</label>
                    <input type="text" id="nombre-titular" name="nombre_titular" class="w-full p-2 border rounded-md" required>
                </div>
                <div class="flex gap-4 mb-4">
                    <div>
                        <label for="fecha-expiracion" class="block text-gray-600">Fecha de Expiración</label>
                        <input type="text" id="fecha-expiracion" name="fecha_expiracion" class="w-full p-2 border rounded-md" placeholder="MM/AA" required>
                    </div>
                    <div>
                        <label for="cvv" class="block text-gray-600">CVV</label>
                        <input type="text" id="cvv" name="cvv" class="w-full p-2 border rounded-md" placeholder="123" required>
                    </div>
                </div>
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">Confirmar Pago</button>
                <button type="button" id="cancelar-tarjeta" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-800 ml-2">Cancelar</button>
            </form>
        </div>
    </div>
</div>

<link href="{{ asset('css/carrito.css') }}" rel="stylesheet">
<script src="{{ asset('js/carrito/confirmar.js') }}"></script>
@endsection
