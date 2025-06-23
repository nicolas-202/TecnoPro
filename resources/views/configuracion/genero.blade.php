@extends('layouts.layout')

@section('content')
<div class="container mx-auto max-w-6xl px-4 py-8">
    <h1 class="text-3xl md:text-4xl font-bold text-gray-800 text-center mb-8">Géneros</h1>

    <!-- Tabla de Géneros y Búsqueda de Géneros -->
    <div class="bg-white shadow-md rounded-lg mb-8">
        <div class="bg-blue-600 text-white text-lg font-semibold px-6 py-4 rounded-t-lg">Lista de Géneros</div>
        <div class="p-6">
            <!-- Formulario de Búsqueda de Géneros -->
            <div class="mb-6">
                <form id="formularioBusquedaGenero" method="GET" class="flex flex-col gap-4">
                    @csrf
                    <div class="flex-1 relative">
                        <label for="buscar_genero" class="block text-sm font-medium text-gray-700 mb-1">Buscar género</label>
                        <input type="text" id="buscar_genero" name="termino" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Ingrese ID o nombre del género" autocomplete="off">
                        <p id="mensajeBusquedaGenero" class="mt-2 text-sm text-gray-500 hidden"></p>
                    </div>
                    <div>
                        <button type="button" id="limpiarBusquedaGenero" class="bg-gray-500 text-white px-6 py-2 rounded-md hover:bg-gray-600 text-sm font-medium">Limpiar Búsqueda</button>
                    </div>
                </form>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-700" id="tablaGeneros">
                    <thead class="bg-gray-100 text-xs uppercase text-gray-700">
                        <tr>
                            <th class="px-4 py-3">ID</th>
                            <th class="px-4 py-3">Nombre Género</th>
                            <th class="px-4 py-3">Descripción</th>
                            <th class="px-4 py-3">Nomenclatura</th>
                            <th class="px-4 py-3">Estado</th>
                            <th class="px-4 py-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyGeneros">
                        @forelse ($generos as $genero)
                            <tr class="border-t hover:bg-blue-50">
                                <td class="px-4 py-3">{{ $genero->idGenero }}</td>
                                <td class="px-4 py-3">{{ $genero->nomGenero }}</td>
                                <td class="px-4 py-3">{{ $genero->desGenero ?? 'Sin descripción' }}</td>
                                <td class="px-4 py-3">{{ $genero->nomeGenero ?? 'Sin nomenclatura' }}</td>
                                <td class="px-4 py-3">{{ $genero->estadoGenero ? 'Activo' : 'Inactivo' }}</td>
                                <td class="px-4 py-3 flex space-x-2">
                                    <button class="editar-genero bg-blue-500 text-white px-3 py-1 rounded-md hover:bg-blue-600 text-xs" data-genero='{{ json_encode(["idGenero" => $genero->idGenero, "nomGenero" => $genero->nomGenero, "desGenero" => $genero->desGenero, "nomeGenero" => $genero->nomeGenero, "estadoGenero" => $genero->estadoGenero], JSON_HEX_QUOT | JSON_HEX_TAG | JSON_UNESCAPED_UNICODE) }}'>Editar</button>
                                    <form action="{{ route('generos.destroy', $genero->idGenero) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-600 text-xs" onclick="return confirm('¿Está seguro de eliminar este género?')">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-3 text-center text-gray-500">No hay géneros registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Formulario de Género -->
    <div class="bg-white shadow-md rounded-lg">
        <div class="bg-blue-600 text-white text-lg font-semibold px-6 py-4 rounded-t-lg">Formulario de Género</div>
        <div class="p-6">
            <!-- Mostrar errores de validación -->
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-md mb-4">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Mostrar mensaje de éxito -->
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

            <form id="formularioGenero" action="{{ route('generos.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @csrf
                <input type="hidden" name="_method" id="methodGenero" value="POST">
                <div>
                    <label for="idGenero" class="block text-sm font-medium text-gray-700 mb-1">ID Género</label>
                    <input type="text" id="idGenero" name="idGenero" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm bg-gray-100 cursor-not-allowed" readonly>
                </div>
                <div>
                    <label for="nomGenero" class="block text-sm font-medium text-gray-700 mb-1">Nombre Género</label>
                    <input type="text" id="nomGenero" name="nomGenero" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required value="{{ old('nomGenero') }}">
                    @error('nomGenero')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="desGenero" class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                    <textarea id="desGenero" name="desGenero" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('desGenero') }}</textarea>
                    @error('desGenero')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="nomeGenero" class="block text-sm font-medium text-gray-700 mb-1">Nomenclatura</label>
                    <input type="text" id="nomeGenero" name="nomeGenero" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="{{ old('nomeGenero') }}">
                    @error('nomeGenero')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="estadoGenero" class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                    <select id="estadoGenero" name="estadoGenero" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="1" {{ old('estadoGenero') == '1' ? 'selected' : '' }}>Activo</option>
                        <option value="0" {{ old('estadoGenero') == '0' ? 'selected' : '' }}>Inactivo</option>
                    </select>
                    @error('estadoGenero')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div class="md:col-span-2 flex space-x-4">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 text-sm font-medium">Guardar</button>
                    <button type="button" id="limpiarTodo" class="bg-gray-500 text-white px-6 py-2 rounded-md hover:bg-gray-600 text-sm font-medium">Limpiar Todo</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    window.appConfig = {
        urls: {
            generosIndex: "{{ route('generos.index') }}",
            buscarGenero: "{{ route('generos.buscarGenero') }}",
            generosStore: "{{ route('generos.store') }}",
            generosUpdate: "{{ route('generos.update', ':idGenero') }}",
            generosDestroy: "{{ route('generos.destroy', ':idGenero') }}"
        }
    };
</script>
<script src="{{ asset('js/configuracion/genero.js') }}"></script>
@endsection