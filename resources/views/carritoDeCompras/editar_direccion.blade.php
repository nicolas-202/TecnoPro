@extends('layouts.layout')

@section('content')
    <div class="container mx-auto px-4 py-8 max-w-lg">
        <h1 class="text-2xl font-bold text-gray-800 mb-6 text-center">Editar Dirección de Envío</h1>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('carrito.actualizar_direccion') }}" method="POST" class="bg-white shadow-md rounded-lg p-6">
            @csrf

            <div class="mb-4">
                <label for="idPais" class="block text-gray-600 text-sm font-semibold mb-2">País</label>
                <select id="idPais" name="idPais" class="w-full p-2 border rounded-md text-gray-600" required>
                    <option value="">Selecciona un país</option>
                    @foreach ($paises as $pais)
                        <option value="{{ $pais->idPais }}" {{ $usuario->idMunicipio && $usuario->municipio->departamento->pais->idPais == $pais->idPais ? 'selected' : '' }}>
                            {{ $pais->nombrePais }}
                        </option>
                    @endforeach
                </select>
                @error('idPais')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4">
                <label for="idDepartamento" class="block text-gray-600 text-sm font-semibold mb-2">Departamento</label>
                <select id="idDepartamento" name="idDepartamento" class="w-full p-2 border rounded-md text-gray-600" required>
                    <option value="">Selecciona un departamento</option>
                    <!-- Populate dynamically via JavaScript -->
                </select>
                @error('idDepartamento')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4">
                <label for="idMunicipio" class="block text-gray-600 text-sm font-semibold mb-2">Municipio</label>
                <select id="idMunicipio" name="idMunicipio" class="w-full p-2 border rounded-md text-gray-600" required>
                    <option value="">Selecciona un municipio</option>
                    <!-- Populate dynamically via JavaScript -->
                </select>
                @error('idMunicipio')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4">
                <label for="direccion" class="block text-gray-600 text-sm font-semibold mb-2">Dirección</label>
                <input type="text" id="direccion" name="direccion" value="{{ old('direccion', $usuario->direccion ?? '') }}" class="w-full p-2 border rounded-md text-gray-600" required>
                @error('direccion')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                @enderror
            </div>

            <div class="text-center">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Actualizar Dirección</button>
                <a href="{{ route('confirmar') }}" class="ml-4 text-gray-600 hover:underline">Cancelar</a>
            </div>
        </form>
    </div>

    <script>
         href="{{ route('asset(carrito.editar_direccion.js)') }}"
    </script>
@endsectionErrorException
