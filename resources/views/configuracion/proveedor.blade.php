@extends('layouts.layout')

@section('content')
<div class="container mx-auto max-w-6xl px-4 py-8">
    <h1 class="text-3xl md:text-4xl font-bold text-gray-800 text-center mb-8">Proveedores</h1>

    <!-- Tabla de Proveedores y Búsqueda de Proveedores -->
    <div class="bg-white shadow-md rounded-lg mb-8">
        <div class="bg-blue-600 text-white text-lg font-semibold px-6 py-4 rounded-t-lg">Lista de Proveedores</div>
        <div class="p-6">
            <!-- Formulario de Búsqueda de Proveedores -->
            <div class="mb-6">
                <form id="formularioBusquedaProveedor" method="GET" class="flex flex-col gap-4">
                    @csrf
                    <div class="flex-1 relative">
                        <label for="buscar_proveedor" class="block text-sm font-medium text-gray-700 mb-1">Buscar proveedor</label>
                        <input type="text" id="buscar_proveedor" name="termino" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Ingrese ID, nombre, email o NIT del proveedor" autocomplete="off">
                        <p id="mensajeBusquedaProveedor" class="mt-2 text-sm text-gray-500 hidden"></p>
                    </div>
                    <div>
                        <button type="button" id="limpiarBusquedaProveedor" class="bg-gray-500 text-white px-6 py-2 rounded-md hover:bg-gray-600 text-sm font-medium">Limpiar Búsqueda</button>
                    </div>
                </form>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-700" id="tablaProveedores">
                    <thead class="bg-gray-100 text-xs uppercase text-gray-700">
                        <tr>
                            <th class="px-4 py-3">ID</th>
                            <th class="px-4 py-3">Nombre Proveedor</th>
                            <th class="px-4 py-3">Descripción</th>
                            <th class="px-4 py-3">Teléfono</th>
                            <th class="px-4 py-3">Email</th>
                            <th class="px-4 py-3">NIT</th>
                            <th class="px-4 py-3">Estado</th>
                            <th class="px-4 py-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyProveedores">
                        @forelse ($proveedores as $proveedor)
                            <tr class="border-t hover:bg-blue-50">
                                <td class="px-4 py-3">{{ $proveedor->idProveedor }}</td>
                                <td class="px-4 py-3">{{ $proveedor->nomProveedor }}</td>
                                <td class="px-4 py-3">{{ $proveedor->desProveedor ?? 'Sin descripción' }}</td>
                                <td class="px-4 py-3">{{ $proveedor->telProveedor ?? 'Sin teléfono' }}</td>
                                <td class="px-4 py-3">{{ $proveedor->emailProveedor ?? 'Sin email' }}</td>
                                <td class="px-4 py-3">{{ $proveedor->nitProveedor ?? 'Sin NIT' }}</td>
                                <td class="px-4 py-3">{{ $proveedor->estadoProveedor ? 'Activo' : 'Inactivo' }}</td>
                                <td class="px-4 py-3 flex space-x-2">
                                    <button class="editar-proveedor bg-blue-500 text-white px-3 py-1 rounded-md hover:bg-blue-600 text-xs" data-proveedor='{{ json_encode([
                                        "idProveedor" => $proveedor->idProveedor,
                                        "nomProveedor" => $proveedor->nomProveedor,
                                        "desProveedor" => $proveedor->desProveedor,
                                        "telProveedor" => $proveedor->telProveedor,
                                        "emailProveedor" => $proveedor->emailProveedor,
                                        "nitProveedor" => $proveedor->nitProveedor,
                                        "estadoProveedor" => $proveedor->estadoProveedor
                                    ], JSON_HEX_QUOT | JSON_HEX_TAG | JSON_UNESCAPED_UNICODE) }}'>Editar</button>
                                    <form action="{{ route('proveedores.destroy', $proveedor->idProveedor) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-600 text-xs" onclick="return confirm('¿Está seguro de eliminar este proveedor?')">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-3 text-center text-gray-500">No hay proveedores registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Formulario de Proveedor -->
    <div class="bg-white shadow-md rounded-lg">
        <div class="bg-blue-600 text-white text-lg font-semibold px-6 py-4 rounded-t-lg">Formulario de Proveedor</div>
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

            <form id="formularioProveedor" action="{{ route('proveedores.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @csrf
                <input type="hidden" name="_method" id="methodProveedor" value="POST">
                <div>
                    <label for="idProveedor" class="block text-sm font-medium text-gray-700 mb-1">ID Proveedor</label>
                    <input type="text" id="idProveedor" name="idProveedor" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm bg-gray-100 cursor-not-allowed" readonly>
                </div>
                <div>
                    <label for="nomProveedor" class="block text-sm font-medium text-gray-700 mb-1">Nombre Proveedor</label>
                    <input type="text" id="nomProveedor" name="nomProveedor" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required value="{{ old('nomProveedor') }}">
                    @error('nomProveedor')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="desProveedor" class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                    <textarea id="desProveedor" name="desProveedor" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('desProveedor') }}</textarea>
                    @error('desProveedor')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="telProveedor" class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                    <input type="text" id="telProveedor" name="telProveedor" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="{{ old('telProveedor') }}">
                    @error('telProveedor')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="emailProveedor" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" id="emailProveedor" name="emailProveedor" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="{{ old('emailProveedor') }}">
                    @error('emailProveedor')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="nitProveedor" class="block text-sm font-medium text-gray-700 mb-1">NIT</label>
                    <input type="text" id="nitProveedor" name="nitProveedor" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="{{ old('nitProveedor') }}">
                    @error('nitProveedor')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="estadoProveedor" class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                    <select id="estadoProveedor" name="estadoProveedor" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="1" {{ old('estadoProveedor') == '1' ? 'selected' : '' }}>Activo</option>
                        <option value="0" {{ old('estadoProveedor') == '0' ? 'selected' : '' }}>Inactivo</option>
                    </select>
                    @error('estadoProveedor')
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
            proveedoresIndex: "{{ route('proveedores.index') }}",
            buscarProveedor: "{{ route('proveedores.buscarProveedor') }}",
            proveedoresStore: "{{ route('proveedores.store') }}",
            proveedoresUpdate: "{{ route('proveedores.update', ':idProveedor') }}",
            proveedoresDestroy: "{{ route('proveedores.destroy', ':idProveedor') }}"
        }
    };
</script>
<script src="{{ asset('js/configuracion/proveedor.js') }}"></script>
@endsection