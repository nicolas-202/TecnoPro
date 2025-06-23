@extends('layouts.layout')

@section('content')
<div class="container mx-auto max-w-6xl px-4 py-8">
    <h1 class="text-3xl md:text-4xl font-bold text-gray-800 text-center mb-8">Tipos de Documento</h1>

    <!-- Tabla de Tipos de Documento y Búsqueda -->
    <div class="bg-white shadow-md rounded-lg mb-8">
        <div class="bg-blue-600 text-white text-lg font-semibold px-6 py-4 rounded-t-lg">Lista de Tipos de Documento</div>
        <div class="p-6">
            <!-- Formulario de Búsqueda -->
            <div class="mb-6">
                <form id="formularioBusquedaTipoDocumento" method="GET" class="flex flex-col gap-4">
                    @csrf
                    <div class="flex-1 relative">
                        <label for="buscar_tipo_documento" class="block text-sm font-medium text-gray-700 mb-1">Buscar tipo de documento</label>
                        <input type="text" id="buscar_tipo_documento" name="termino" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Ingrese ID o nombre del tipo de documento" autocomplete="off">
                        <p id="mensajeBusquedaTipoDocumento" class="mt-2 text-sm text-gray-500 hidden"></p>
                    </div>
                    <div>
                        <button type="button" id="limpiarBusquedaTipoDocumento" class="bg-gray-500 text-white px-6 py-2 rounded-md hover:bg-gray-600 text-sm font-medium">Limpiar Búsqueda</button>
                    </div>
                </form>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-700" id="tablaTiposDocumento">
                    <thead class="bg-gray-100 text-xs uppercase text-gray-700">
                        <tr>
                            <th class="px-4 py-3">ID</th>
                            <th class="px-4 py-3">Nombre Tipo Documento</th>
                            <th class="px-4 py-3">Descripción</th>
                            <th class="px-4 py-3">Nomenclatura</th>
                            <th class="px-4 py-3">Estado</th>
                            <th class="px-4 py-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyTiposDocumento">
                        @forelse ($tiposDocumento as $tipoDocumento)
                            <tr class="border-t hover:bg-blue-50">
                                <td class="px-4 py-3">{{ $tipoDocumento->idTipoDocumento }}</td>
                                <td class="px-4 py-3">{{ $tipoDocumento->nomTipoDocumento }}</td>
                                <td class="px-4 py-3">{{ $tipoDocumento->desTipoDocumento ?? 'Sin descripción' }}</td>
                                <td class="px-4 py-3">{{ $tipoDocumento->nomeTipoDocumento ?? 'Sin nomenclatura' }}</td>
                                <td class="px-4 py-3">{{ $tipoDocumento->estadoTipoDocumento ? 'Activo' : 'Inactivo' }}</td>
                                <td class="px-4 py-3 flex space-x-2">
                                    <button class="editar-tipo-documento bg-blue-500 text-white px-3 py-1 rounded-md hover:bg-blue-600 text-xs" data-tipo-documento='{{ json_encode(["idTipoDocumento" => $tipoDocumento->idTipoDocumento, "nomTipoDocumento" => $tipoDocumento->nomTipoDocumento, "desTipoDocumento" => $tipoDocumento->desTipoDocumento, "nomeTipoDocumento" => $tipoDocumento->nomeTipoDocumento, "estadoTipoDocumento" => $tipoDocumento->estadoTipoDocumento], JSON_HEX_QUOT | JSON_HEX_TAG | JSON_UNESCAPED_UNICODE) }}'>Editar</button>
                                    <form action="{{ route('tipos_documento.destroy', $tipoDocumento->idTipoDocumento) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-600 text-xs" onclick="return confirm('¿Está seguro de eliminar este tipo de documento?')">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-3 text-center text-gray-500">No hay tipos de documento registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Formulario de Tipo de Documento -->
    <div class="bg-white shadow-md rounded-lg">
        <div class="bg-blue-600 text-white text-lg font-semibold px-6 py-4 rounded-t-lg">Formulario de Tipo de Documento</div>
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

            <form id="formularioTipoDocumento" action="{{ route('tipos_documento.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @csrf
                <input type="hidden" name="_method" id="methodTipoDocumento" value="POST">
                <div>
                    <label for="idTipoDocumento" class="block text-sm font-medium text-gray-700 mb-1">ID Tipo Documento</label>
                    <input type="text" id="idTipoDocumento" name="idTipoDocumento" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm bg-gray-100 cursor-not-allowed" readonly>
                </div>
                <div>
                    <label for="nomTipoDocumento" class="block text-sm font-medium text-gray-700 mb-1">Nombre Tipo Documento</label>
                    <input type="text" id="nomTipoDocumento" name="nomTipoDocumento" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required value="{{ old('nomTipoDocumento') }}">
                    @error('nomTipoDocumento')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="desTipoDocumento" class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                    <textarea id="desTipoDocumento" name="desTipoDocumento" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('desTipoDocumento') }}</textarea>
                    @error('desTipoDocumento')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="nomeTipoDocumento" class="block text-sm font-medium text-gray-700 mb-1">Nomenclatura</label>
                    <input type="text" id="nomeTipoDocumento" name="nomeTipoDocumento" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="{{ old('nomeTipoDocumento') }}">
                    @error('nomeTipoDocumento')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="estadoTipoDocumento" class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                    <select id="estadoTipoDocumento" name="estadoTipoDocumento" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="1" {{ old('estadoTipoDocumento') == '1' ? 'selected' : '' }}>Activo</option>
                        <option value="0" {{ old('estadoTipoDocumento') == '0' ? 'selected' : '' }}>Inactivo</option>
                    </select>
                    @error('estadoTipoDocumento')
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
            tiposDocumentoIndex: "{{ route('tipos_documento.index') }}",
            buscarTipoDocumento: "{{ route('tipos_documento.buscarTipoDocumento') }}",
            tiposDocumentoStore: "{{ route('tipos_documento.store') }}",
            tiposDocumentoUpdate: "{{ route('tipos_documento.update', ':idTipoDocumento') }}",
            tiposDocumentoDestroy: "{{ route('tipos_documento.destroy', ':idTipoDocumento') }}"
        }
    };
</script>
<script src="{{ asset('js/configuracion/tipo_documento.js') }}"></script>
@endsection