@extends('layouts.layout')

@section('content')
<div class="container mx-auto max-w-6xl px-4 py-8">
    <h1 class="text-3xl md:text-4xl font-bold text-gray-800 text-center mb-8">Departamentos</h1>

    <!-- Tabla de Departamentos y Búsqueda de Departamentos -->
    <div class="bg-white shadow-md rounded-lg mb-8">
        <div class="bg-blue-600 text-white text-lg font-semibold px-6 py-4 rounded-t-lg">Lista de Departamentos</div>
        <div class="p-6">
            <!-- Formulario de Búsqueda de Departamentos -->
            <div class="mb-6">
                <form id="formularioBusquedaDepartamento" method="GET" class="flex flex-col gap-4">
                    @csrf
                    <div class="flex-1 relative">
                        <label for="buscar_departamento" class="block text-sm font-medium text-gray-700 mb-1">Buscar departamento</label>
                        <input type="text" id="buscar_departamento" name="termino" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Ingrese ID o nombre del departamento" autocomplete="off">
                        <p id="mensajeBusquedaDepartamento" class="mt-2 text-sm text-gray-500 hidden"></p>
                    </div>
                    <div>
                        <button type="button" id="limpiarBusquedaDepartamento" class="bg-gray-500 text-white px-6 py-2 rounded-md hover:bg-gray-600 text-sm font-medium">Limpiar Búsqueda</button>
                    </div>
                </form>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-700" id="tablaDepartamentos">
                    <thead class="bg-gray-100 text-xs uppercase text-gray-700">
                        <tr>
                            <th class="px-4 py-3">ID</th>
                            <th class="px-4 py-3">Nombre Departamento</th>
                            <th class="px-4 py-3">Descripción</th>
                            <th class="px-4 py-3">Nomenclatura</th>
                            <th class="px-4 py-3">País</th>
                            <th class="px-4 py-3">Estado</th>
                            <th class="px-4 py-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyDepartamentos">
                        @forelse ($departamentos as $departamento)
                            <tr class="border-t hover:bg-blue-50">
                                <td class="px-4 py-3">{{ $departamento->idDepartamento }}</td>
                                <td class="px-4 py-3">{{ $departamento->nomDepartamento }}</td>
                                <td class="px-4 py-3">{{ $departamento->desDepartamento ?? 'Sin descripción' }}</td>
                                <td class="px-4 py-3">{{ $departamento->nomeDepartamento ?? 'Sin nomenclatura' }}</td>
                                <td class="px-4 py-3">{{ $departamento->pais->nomPais ?? 'Sin país' }}</td>
                                <td class="px-4 py-3">{{ $departamento->estadoDepartamento ? 'Activo' : 'Inactivo' }}</td>
                                <td class="px-4 py-3 flex space-x-2">
                                    <button class="editar-departamento bg-blue-500 text-white px-3 py-1 rounded-md hover:bg-blue-600 text-xs" data-departamento='{{ json_encode([
                                        "idDepartamento" => $departamento->idDepartamento,
                                        "nomDepartamento" => $departamento->nomDepartamento,
                                        "desDepartamento" => $departamento->desDepartamento,
                                        "nomeDepartamento" => $departamento->nomeDepartamento,
                                        "estadoDepartamento" => $departamento->estadoDepartamento,
                                        "idPais" => $departamento->idPais
                                    ], JSON_HEX_QUOT | JSON_HEX_TAG | JSON_UNESCAPED_UNICODE) }}'>Editar</button>
                                    <form action="{{ route('departamentos.destroy', $departamento->idDepartamento) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-600 text-xs" onclick="return confirm('¿Está seguro de eliminar este departamento?')">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-3 text-center text-gray-500">No hay departamentos registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Formulario de Departamento -->
    <div class="bg-white shadow-md rounded-lg">
        <div class="bg-blue-600 text-white text-lg font-semibold px-6 py-4 rounded-t-lg">Formulario de Departamento</div>
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

            <form id="formularioDepartamento" action="{{ route('departamentos.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @csrf
                <input type="hidden" name="_method" id="methodDepartamento" value="POST">
                <div>
                    <label for="idDepartamento" class="block text-sm font-medium text-gray-700 mb-1">ID Departamento</label>
                    <input type="text" id="idDepartamento" name="idDepartamento" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm bg-gray-100 cursor-not-allowed" readonly>
                </div>
                <div>
                    <label for="nomDepartamento" class="block text-sm font-medium text-gray-700 mb-1">Nombre Departamento</label>
                    <input type="text" id="nomDepartamento" name="nomDepartamento" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required value="{{ old('nomDepartamento') }}">
                    @error('nomDepartamento')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="desDepartamento" class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                    <textarea id="desDepartamento" name="desDepartamento" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('desDepartamento') }}</textarea>
                    @error('desDepartamento')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="nomeDepartamento" class="block text-sm font-medium text-gray-700 mb-1">Nomenclatura</label>
                    <input type="text" id="nomeDepartamento" name="nomeDepartamento" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="{{ old('nomeDepartamento') }}">
                    @error('nomeDepartamento')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="idPais" class="block text-sm font-medium text-gray-700 mb-1">País</label>
                    <select id="idPais" name="idPais" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="" disabled {{ old('idPais') ? '' : 'selected' }}>Seleccione un país</option>
                        @foreach ($paises as $pais)
                            <option value="{{ $pais->idPais }}" {{ old('idPais') == $pais->idPais ? 'selected' : '' }}>{{ $pais->nomPais }}</option>
                        @endforeach
                    </select>
                    @error('idPais')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="estadoDepartamento" class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                    <select id="estadoDepartamento" name="estadoDepartamento" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="1" {{ old('estadoDepartamento') == '1' ? 'selected' : '' }}>Activo</option>
                        <option value="0" {{ old('estadoDepartamento') == '0' ? 'selected' : '' }}>Inactivo</option>
                    </select>
                    @error('estadoDepartamento')
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
            departamentosIndex: "{{ route('departamentos.index') }}",
            buscarDepartamento: "{{ route('departamentos.buscarDepartamento') }}",
            departamentosStore: "{{ route('departamentos.store') }}",
            departamentosUpdate: "{{ route('departamentos.update', ':idDepartamento') }}",
            departamentosDestroy: "{{ route('departamentos.destroy', ':idDepartamento') }}"
        }
    };
</script>
<script src="{{ asset('js/configuracion/departamento.js') }}"></script>
@endsection