@extends('layouts.layout')

@section('content')
<div class="container mx-auto max-w-6xl px-4 py-8">
    <h1 class="text-3xl md:text-4xl font-bold text-gray-800 text-center mb-8">Categorías</h1>

    <!-- Tabla de Categorías y Búsqueda de Categorías -->
    <div class="bg-white shadow-md rounded-lg mb-8">
        <div class="bg-blue-600 text-white text-lg font-semibold px-6 py-4 rounded-t-lg">Lista de Categorías</div>
        <div class="p-6">
            <!-- Formulario de Búsqueda de Categorías -->
            <div class="mb-6">
                <form id="formularioBusquedaCategoria" method="GET" class="flex flex-col gap-4">
                    @csrf
                    <div class="flex-1 relative">
                        <label for="buscar_categoria" class="block text-sm font-medium text-gray-700 mb-1">Buscar categoría</label>
                        <input type="text" id="buscar_categoria" name="termino" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Ingrese ID o nombre de la categoría" autocomplete="off">
                        <p id="mensajeBusquedaCategoria" class="mt-2 text-sm text-gray-500 hidden"></p>
                    </div>
                    <div>
                        <button type="button" id="limpiarBusquedaCategoria" class="bg-gray-500 text-white px-6 py-2 rounded-md hover:bg-gray-600 text-sm font-medium">Limpiar Búsqueda</button>
                    </div>
                </form>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-700" id="tablaCategorias">
                    <thead class="bg-gray-100 text-xs uppercase text-gray-700">
                        <tr>
                            <th class="px-4 py-3">ID</th>
                            <th class="px-4 py-3">Nombre Categoría</th>
                            <th class="px-4 py-3">Descripción</th>
                            <th class="px-4 py-3">Nomenclatura</th>
                            <th class="px-4 py-3">Estado</th>
                            <th class="px-4 py-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyCategorias">
                        @forelse ($categorias as $categoria)
                            <tr class="border-t hover:bg-blue-50">
                                <td class="px-4 py-3">{{ $categoria->idCategoria }}</td>
                                <td class="px-4 py-3">{{ $categoria->nomCategoria }}</td>
                                <td class="px-4 py-3">{{ $categoria->desCategoria ?? 'Sin descripción' }}</td>
                                <td class="px-4 py-3">{{ $categoria->nomeCategoria ?? 'Sin nomenclatura' }}</td>
                                <td class="px-4 py-3">{{ $categoria->estadoCategoria ? 'Activo' : 'Inactivo' }}</td>
                                <td class="px-4 py-3 flex space-x-2">
                                    <button class="editar-categoria bg-blue-500 text-white px-3 py-1 rounded-md hover:bg-blue-600 text-xs" data-categoria='{{ json_encode(["idCategoria" => $categoria->idCategoria, "nomCategoria" => $categoria->nomCategoria, "desCategoria" => $categoria->desCategoria, "nomeCategoria" => $categoria->nomeCategoria, "estadoCategoria" => $categoria->estadoCategoria]) }}'>Editar</button>
                                    <form action="{{ route('categorias.destroy', $categoria->idCategoria) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-600 text-xs" onclick="return confirm('¿Está seguro de eliminar esta categoría?')">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-3 text-center text-gray-500">No hay categorías registradas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Formulario de Categoría -->
    <div class="bg-white shadow-md rounded-lg">
        <div class="bg-blue-600 text-white text-lg font-semibold px-6 py-4 rounded-t-lg">Formulario de Categoría</div>
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

            <form id="formularioCategoria" action="{{ route('categorias.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @csrf
                @method('POST')
                <div>
                    <label for="idCategoria" class="block text-sm font-medium text-gray-700 mb-1">Código</label>
                    <input type="text" id="idCategoria" name="idCategoria" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm bg-gray-100 cursor-not-allowed" readonly>
                </div>
                <div>
                    <label for="nomCategoria" class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                    <input type="text" id="nomCategoria" name="nomCategoria" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required value="{{ old('nomCategoria') }}">
                    @error('nomCategoria')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="desCategoria" class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                    <textarea id="desCategoria" name="desCategoria" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('desCategoria') }}</textarea>
                    @error('desCategoria')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="nomeCategoria" class="block text-sm font-medium text-gray-700 mb-1">Nomenclatura</label>
                    <input type="text" id="nomeCategoria" name="nomeCategoria" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="{{ old('nomeCategoria') }}">
                    @error('nomeCategoria')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="estadoCategoria" class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                    <select id="estadoCategoria" name="estadoCategoria" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="1" {{ old('estadoCategoria') == '1' ? 'selected' : '' }}>Activo</option>
                        <option value="0" {{ old('estadoCategoria') == '0' ? 'selected' : '' }}>Inactivo</option>
                    </select>
                    @error('estadoCategoria')
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

<script src="{{ asset('js/configuracion/categoria.js') }}"></script>
@endsection