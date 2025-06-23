@extends('layouts.layout')

@section('content')
<div class="container mx-auto max-w-6xl px-4 py-8">
    <h1 class="text-3xl md:text-4xl font-bold text-gray-800 text-center mb-8">Países</h1>

    <!-- Tabla de Países y Búsqueda de Países -->
    <div class="bg-white shadow-md rounded-lg mb-8">
        <div class="bg-blue-600 text-white text-lg font-semibold px-6 py-4 rounded-t-lg">Lista de Países</div>
        <div class="p-6">
            <!-- Formulario de Búsqueda de Países -->
            <div class="mb-6">
                <form id="formularioBusquedaPais" method="GET" class="flex flex-col gap-4">
                    @csrf
                    <div class="flex-1 relative">
                        <label for="buscar_pais" class="block text-sm font-medium text-gray-700 mb-1">Buscar país</label>
                        <input type="text" id="buscar_pais" name="termino" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Ingrese ID o nombre del país" autocomplete="off">
                        <p id="mensajeBusquedaPais" class="mt-2 text-sm text-gray-500 hidden"></p>
                    </div>
                    <div>
                        <button type="button" id="limpiarBusquedaPais" class="bg-gray-500 text-white px-6 py-2 rounded-md hover:bg-gray-600 text-sm font-medium">Limpiar Búsqueda</button>
                    </div>
                </form>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-700" id="tablaPaises">
                    <thead class="bg-gray-100 text-xs uppercase text-gray-700">
                        <tr>
                            <th class="px-4 py-3">ID</th>
                            <th class="px-4 py-3">Nombre País</th>
                            <th class="px-4 py-3">Descripción</th>
                            <th class="px-4 py-3">Nomenclatura</th>
                            <th class="px-4 py-3">Estado</th>
                            <th class="px-4 py-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyPaises">
                        @forelse ($paises as $pais)
                            <tr class="border-t hover:bg-blue-50">
                                <td class="px-4 py-3">{{ $pais->idPais }}</td>
                                <td class="px-4 py-3">{{ $pais->nomPais }}</td>
                                <td class="px-4 py-3">{{ $pais->desPais ?? 'Sin descripción' }}</td>
                                <td class="px-4 py-3">{{ $pais->nomePais ?? 'Sin nomenclatura' }}</td>
                                <td class="px-4 py-3">{{ $pais->estadoPais ? 'Activo' : 'Inactivo' }}</td>
                                <td class="px-4 py-3 flex space-x-2">
                                    <button class="editar-pais bg-blue-500 text-white px-3 py-1 rounded-md hover:bg-blue-600 text-xs" data-pais='{{ json_encode([
                                        "idPais" => $pais->idPais,
                                        "nomPais" => $pais->nomPais,
                                        "desPais" => $pais->desPais,
                                        "nomePais" => $pais->nomePais,
                                        "estadoPais" => $pais->estadoPais
                                    ], JSON_HEX_QUOT | JSON_HEX_TAG | JSON_UNESCAPED_UNICODE) }}'>Editar</button>
                                    <form action="{{ route('paises.destroy', $pais->idPais) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-600 text-xs" onclick="return confirm('¿Está seguro de eliminar este país?')">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-3 text-center text-gray-500">No hay países registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Formulario de País -->
    <div class="bg-white shadow-md rounded-lg">
        <div class="bg-blue-600 text-white text-lg font-semibold px-6 py-4 rounded-t-lg">Formulario de País</div>
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

            <form id="formularioPais" action="{{ route('paises.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @csrf
                <input type="hidden" name="_method" id="methodPais" value="POST">
                <div>
                    <label for="idPais" class="block text-sm font-medium text-gray-700 mb-1">ID País</label>
                    <input type="text" id="idPais" name="idPais" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm bg-gray-100 cursor-not-allowed" readonly>
                </div>
                <div>
                    <label for="nomPais" class="block text-sm font-medium text-gray-700 mb-1">Nombre País</label>
                    <input type="text" id="nomPais" name="nomPais" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required value="{{ old('nomPais') }}">
                    @error('nomPais')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="desPais" class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                    <textarea id="desPais" name="desPais" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('desPais') }}</textarea>
                    @error('desPais')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="nomePais" class="block text-sm font-medium text-gray-700 mb-1">Nomenclatura</label>
                    <input type="text" id="nomePais" name="nomePais" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="{{ old('nomePais') }}">
                    @error('nomePais')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="estadoPais" class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                    <select id="estadoPais" name="estadoPais" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="1" {{ old('estadoPais') == '1' ? 'selected' : '' }}>Activo</option>
                        <option value="0" {{ old('estadoPais') == '0' ? 'selected' : '' }}>Inactivo</option>
                    </select>
                    @error('estadoPais')
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
            paisesIndex: "{{ route('paises.index') }}",
            buscarPais: "{{ route('paises.buscarPais') }}",
            paisesStore: "{{ route('paises.store') }}",
            paisesUpdate: "{{ route('paises.update', ':idPais') }}",
            paisesDestroy: "{{ route('paises.destroy', ':idPais') }}"
        }
    };
</script>
<script src="{{ asset('js/configuracion/pais.js') }}"></script>
@endsection