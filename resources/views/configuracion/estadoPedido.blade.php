@extends('layouts.layout')

@section('content')
<div class="container mx-auto max-w-6xl px-4 py-8">
    <h1 class="text-3xl md:text-4xl font-bold text-gray-800 text-center mb-8">Estados de Pedido</h1>

    <!-- Tabla de Estados y Búsqueda de Estados -->
    <div class="bg-white shadow-md rounded-lg mb-8">
        <div class="bg-blue-600 text-white text-lg font-semibold px-6 py-4 rounded-t-lg">Lista de Estados</div>
        <div class="p-6">
            <!-- Formulario de Búsqueda de Estados -->
            <div class="mb-6">
                <form id="formularioBusquedaEstado" method="GET" class="flex flex-col gap-4">
                    @csrf
                    <div class="flex-1 relative">
                        <label for="buscar_estado" class="block text-sm font-medium text-gray-700 mb-1">Buscar estado</label>
                        <input type="text" id="buscar_estado" name="termino" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Ingrese ID o nombre del estado" autocomplete="off">
                        <p id="mensajeBusquedaEstado" class="mt-2 text-sm text-gray-500 hidden"></p>
                    </div>
                    <div>
                        <button type="button" id="limpiarBusquedaEstado" class="bg-gray-500 text-white px-6 py-2 rounded-md hover:bg-gray-600 text-sm font-medium">Limpiar Búsqueda</button>
                    </div>
                </form>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-700" id="tablaEstados">
                    <thead class="bg-gray-100 text-xs uppercase text-gray-700">
                        <tr>
                            <th class="px-4 py-3">ID</th>
                            <th class="px-4 py-3">Nombre Estado</th>
                            <th class="px-4 py-3">Descripción</th>
                            <th class="px-4 py-3">Nomenclatura</th>
                            <th class="px-4 py-3">Estado</th>
                            <th class="px-4 py-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyEstados">
                        @forelse ($estados as $estado)
                            <tr class="border-t hover:bg-blue-50">
                                <td class="px-4 py-3">{{ $estado->idEstadoPedido }}</td>
                                <td class="px-4 py-3">{{ $estado->nomEstadoPedido }}</td>
                                <td class="px-4 py-3">{{ $estado->desEstadoPedido ?? 'Sin descripción' }}</td>
                                <td class="px-4 py-3">{{ $estado->nomeEstadoPedido ?? 'Sin nomenclatura' }}</td>
                                <td class="px-4 py-3">{{ $estado->estadoEstadoPedido ? 'Activo' : 'Inactivo' }}</td>
                                <td class="px-4 py-3 flex space-x-2">
                                    <button class="editar-estado bg-blue-500 text-white px-3 py-1 rounded-md hover:bg-blue-600 text-xs" data-estado='{{ json_encode([
                                        "idEstadoPedido" => $estado->idEstadoPedido,
                                        "nomEstadoPedido" => $estado->nomEstadoPedido,
                                        "desEstadoPedido" => $estado->desEstadoPedido,
                                        "nomeEstadoPedido" => $estado->nomeEstadoPedido,
                                        "estadoEstadoPedido" => $estado->estadoEstadoPedido
                                    ], JSON_HEX_QUOT | JSON_HEX_TAG | JSON_UNESCAPED_UNICODE) }}'>Editar</button>
                                    <form action="{{ route('estados_pedido.destroy', $estado->idEstadoPedido) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-600 text-xs" onclick="return confirm('¿Está seguro de eliminar este estado?')">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-3 text-center text-gray-500">No hay estados registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Formulario de Estado -->
    <div class="bg-white shadow-md rounded-lg">
        <div class="bg-blue-600 text-white text-lg font-semibold px-6 py-4 rounded-t-lg">Formulario de Estado</div>
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

            <form id="formularioEstado" action="{{ route('estados_pedido.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @csrf
                <input type="hidden" name="_method" id="methodEstado" value="POST">
                <div>
                    <label for="idEstadoPedido" class="block text-sm font-medium text-gray-700 mb-1">ID Estado</label>
                    <input type="text" id="idEstadoPedido" name="idEstadoPedido" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm bg-gray-100 cursor-not-allowed" readonly>
                </div>
                <div>
                    <label for="nomEstadoPedido" class="block text-sm font-medium text-gray-700 mb-1">Nombre Estado</label>
                    <input type="text" id="nomEstadoPedido" name="nomEstadoPedido" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required value="{{ old('nomEstadoPedido') }}">
                    @error('nomEstadoPedido')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="desEstadoPedido" class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                    <textarea id="desEstadoPedido" name="desEstadoPedido" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('desEstadoPedido') }}</textarea>
                    @error('desEstadoPedido')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="nomeEstadoPedido" class="block text-sm font-medium text-gray-700 mb-1">Nomenclatura</label>
                    <input type="text" id="nomeEstadoPedido" name="nomeEstadoPedido" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="{{ old('nomeEstadoPedido') }}">
                    @error('nomeEstadoPedido')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="estadoEstadoPedido" class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                    <select id="estadoEstadoPedido" name="estadoEstadoPedido" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="1" {{ old('estadoEstadoPedido') == '1' ? 'selected' : '' }}>Activo</option>
                        <option value="0" {{ old('estadoEstadoPedido') == '0' ? 'selected' : '' }}>Inactivo</option>
                    </select>
                    @error('estadoEstadoPedido')
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
            estadosIndex: "{{ route('estados_pedido.index') }}",
            buscarEstado: "{{ route('estados_pedido.buscarEstado') }}",
            estadosStore: "{{ route('estados_pedido.store') }}",
            estadosUpdate: "{{ route('estados_pedido.update', ':idEstadoPedido') }}",
            estadosDestroy: "{{ route('estados_pedido.destroy', ':idEstadoPedido') }}"
        }
    };
</script>
<script src="{{ asset('js/configuracion/estadoPedido.js') }}"></script>
@endsection