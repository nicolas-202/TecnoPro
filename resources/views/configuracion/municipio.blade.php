@extends('layouts.layout')

@section('content')
<div class="container mx-auto max-w-6xl px-4 py-8">
    <h1 class="text-3xl md:text-4xl font-bold text-gray-800 text-center mb-8">Municipios</h1>

    <!-- Tabla de Municipios y Búsqueda de Municipios -->
    <div class="bg-white shadow-md rounded-lg mb-8">
        <div class="bg-blue-600 text-white text-lg font-semibold px-6 py-4 rounded-t-lg">Lista de Municipios</div>
        <div class="p-6">
            <!-- Formulario de Búsqueda de Municipios -->
            <div class="mb-6">
                <form id="formularioBusquedaMunicipio" method="GET" class="flex flex-col gap-4">
                    @csrf
                    <div class="flex-1 relative">
                        <label for="buscar_municipio" class="block text-sm font-medium text-gray-700 mb-1">Buscar municipio</label>
                        <input type="text" id="buscar_municipio" name="termino" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Ingrese ID o nombre del municipio" autocomplete="off">
                        <p id="mensajeBusquedaMunicipio" class="mt-2 text-sm text-gray-500 hidden"></p>
                    </div>
                    <div>
                        <button type="button" id="limpiarBusquedaMunicipio" class="bg-gray-500 text-white px-6 py-2 rounded-md hover:bg-gray-600 text-sm font-medium">Limpiar Búsqueda</button>
                    </div>
                </form>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-700" id="tablaMunicipios">
                    <thead class="bg-gray-100 text-xs uppercase text-gray-700">
                        <tr>
                            <th class="px-4 py-3">ID</th>
                            <th class="px-4 py-3">Nombre Municipio</th>
                            <th class="px-4 py-3">Descripción</th>
                            <th class="px-4 py-3">Nomenclatura</th>
                            <th class="px-4 py-3">Departamento</th>
                            <th class="px-4 py-3">Estado</th>
                            <th class="px-4 py-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyMunicipios">
                        @forelse ($municipios as $municipio)
                            <tr class="border-t hover:bg-blue-50">
                                <td class="px-4 py-3">{{ $municipio->idMunicipio }}</td>
                                <td class="px-4 py-3">{{ $municipio->nomMunicipio }}</td>
                                <td class="px-4 py-3">{{ $municipio->desMunicipio ?? 'Sin descripción' }}</td>
                                <td class="px-4 py-3">{{ $municipio->nomeMunicipio ?? 'Sin nomenclatura' }}</td>
                                <td class="px-4 py-3">{{ $municipio->departamento->nomDepartamento ?? 'Sin departamento' }}</td>
                                <td class="px-4 py-3">{{ $municipio->estadoMunicipio ? 'Activo' : 'Inactivo' }}</td>
                                <td class="px-4 py-3 flex space-x-2">
                                    <button class="editar-municipio bg-blue-500 text-white px-3 py-1 rounded-md hover:bg-blue-600 text-xs" data-municipio='{{ json_encode([
                                        "idMunicipio" => $municipio->idMunicipio,
                                        "nomMunicipio" => $municipio->nomMunicipio,
                                        "desMunicipio" => $municipio->desMunicipio,
                                        "nomeMunicipio" => $municipio->nomeMunicipio,
                                        "estadoMunicipio" => $municipio->estadoMunicipio,
                                        "idDepartamento" => $municipio->idDepartamento
                                    ], JSON_HEX_QUOT | JSON_HEX_TAG | JSON_UNESCAPED_UNICODE) }}'>Editar</button>
                                    <form action="{{ route('municipios.destroy', $municipio->idMunicipio) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-600 text-xs" onclick="return confirm('¿Está seguro de eliminar este municipio?')">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-3 text-center text-gray-500">No hay municipios registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Formulario de Municipio -->
    <div class="bg-white shadow-md rounded-lg">
        <div class="bg-blue-600 text-white text-lg font-semibold px-6 py-4 rounded-t-lg">Formulario de Municipio</div>
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

            <form id="formularioMunicipio" action="{{ route('municipios.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @csrf
                <input type="hidden" name="_method" id="methodMunicipio" value="POST">
                <div>
                    <label for="idMunicipio" class="block text-sm font-medium text-gray-700 mb-1">ID Municipio</label>
                    <input type="text" id="idMunicipio" name="idMunicipio" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm bg-gray-100 cursor-not-allowed" readonly>
                </div>
                <div>
                    <label for="nomMunicipio" class="block text-sm font-medium text-gray-700 mb-1">Nombre Municipio</label>
                    <input type="text" id="nomMunicipio" name="nomMunicipio" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required value="{{ old('nomMunicipio') }}">
                    @error('nomMunicipio')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="desMunicipio" class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                    <textarea id="desMunicipio" name="desMunicipio" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('desMunicipio') }}</textarea>
                    @error('desMunicipio')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="nomeMunicipio" class="block text-sm font-medium text-gray-700 mb-1">Nomenclatura</label>
                    <input type="text" id="nomeMunicipio" name="nomeMunicipio" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="{{ old('nomeMunicipio') }}">
                    @error('nomeMunicipio')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="idDepartamento" class="block text-sm font-medium text-gray-700 mb-1">Departamento</label>
                    <select id="idDepartamento" name="idDepartamento" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="" disabled {{ old('idDepartamento') ? '' : 'selected' }}>Seleccione un departamento</option>
                        @foreach ($departamentos as $departamento)
                            <option value="{{ $departamento->idDepartamento }}" {{ old('idDepartamento') == $departamento->idDepartamento ? 'selected' : '' }}>{{ $departamento->nomDepartamento }}</option>
                        @endforeach
                    </select>
                    @error('idDepartamento')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="estadoMunicipio" class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                    <select id="estadoMunicipio" name="estadoMunicipio" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="1" {{ old('estadoMunicipio') == '1' ? 'selected' : '' }}>Activo</option>
                        <option value="0" {{ old('estadoMunicipio') == '0' ? 'selected' : '' }}>Inactivo</option>
                    </select>
                    @error('estadoMunicipio')
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
            municipiosIndex: "{{ route('municipios.index') }}",
            buscarMunicipio: "{{ route('municipios.buscarMunicipio') }}",
            municipiosStore: "{{ route('municipios.store') }}",
            municipiosUpdate: "{{ route('municipios.update', ':idMunicipio') }}",
            municipiosDestroy: "{{ route('municipios.destroy', ':idMunicipio') }}"
        }
    };
</script>
<script src="{{ asset('js/configuracion/municipio.js') }}"></script>
@endsection