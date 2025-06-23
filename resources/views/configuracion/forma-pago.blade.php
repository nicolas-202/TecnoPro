
@extends('layouts.layout')

@section('content')
<div class="container mx-auto max-w-6xl px-4 py-8">
    <h1 class="text-3xl md:text-4xl font-bold text-gray-800 text-center mb-8">Formas de Pago</h1>

    <!-- Tabla de Formas de Pago y Búsqueda -->
    <div class="bg-white shadow-md rounded-lg mb-8">
        <div class="bg-blue-600 text-white text-lg font-semibold px-6 py-4 rounded-t-lg">Lista de Formas de Pago</div>
        <div class="p-6">
            <!-- Formulario de Búsqueda -->
            <div class="mb-6">
                <form id="formularioBusquedaFormaPago" method="GET" class="flex flex-col gap-4">
                    @csrf
                    <div class="flex-1 relative">
                        <label for="buscar_forma_pago" class="block text-sm font-medium text-gray-700 mb-1">Buscar forma de pago</label>
                        <input type="text" id="buscar_forma_pago" name="termino" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Ingrese ID o nombre de la forma de pago" autocomplete="off">
                        <p id="mensajeBusquedaFormaPago" class="mt-2 text-sm text-gray-500 hidden"></p>
                    </div>
                    <div>
                        <button type="button" id="limpiarBusquedaFormaPago" class="bg-gray-500 text-white px-6 py-2 rounded-md hover:bg-gray-600 text-sm font-medium">Limpiar Búsqueda</button>
                    </div>
                </form>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-700" id="tablaFormasPago">
                    <thead class="bg-gray-100 text-xs uppercase text-gray-700">
                        <tr>
                            <th class="px-4 py-3">ID</th>
                            <th class="px-4 py-3">Nombre Forma de Pago</th>
                            <th class="px-4 py-3">Descripción</th>
                            <th class="px-4 py-3">Nomenclatura</th>
                            <th class="px-4 py-3">Estado</th>
                            <th class="px-4 py-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyFormasPago">
                        @forelse ($formasPago as $formaPago)
                            <tr class="border-t hover:bg-blue-50">
                                <td class="px-4 py-3">{{ $formaPago->idFormaPago }}</td>
                                <td class="px-4 py-3">{{ $formaPago->nomFormaPago }}</td>
                                <td class="px-4 py-3">{{ $formaPago->desFormaPago ?? 'Sin descripción' }}</td>
                                <td class="px-4 py-3">{{ $formaPago->nomeFormaPago ?? 'Sin nomenclatura' }}</td>
                                <td class="px-4 py-3">{{ $formaPago->estadoFormaPago ? 'Activo' : 'Inactivo' }}</td>
                                <td class="px-4 py-3 flex space-x-2">
                                    <button class="editar-forma-pago bg-blue-500 text-white px-3 py-1 rounded-md hover:bg-blue-600 text-xs" data-forma-pago='{{ json_encode(["idFormaPago" => $formaPago->idFormaPago, "nomFormaPago" => $formaPago->nomFormaPago, "desFormaPago" => $formaPago->desFormaPago, "nomeFormaPago" => $formaPago->nomeFormaPago, "estadoFormaPago" => $formaPago->estadoFormaPago], JSON_HEX_QUOT | JSON_HEX_TAG | JSON_UNESCAPED_UNICODE) }}'>Editar</button>
                                    <form action="{{ route('formas-pago.destroy', $formaPago->idFormaPago) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-600 text-xs" onclick="return confirm('¿Está seguro de eliminar esta forma de pago?')">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-3 text-center text-gray-500">No hay formas de pago registradas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Formulario de Forma de Pago -->
    <div class="bg-white shadow-md rounded-lg">
        <div class="bg-blue-600 text-white text-lg font-semibold px-6 py-4 rounded-t-lg">Formulario de Forma de Pago</div>
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

            <!-- Mostrar mensaje de éxito o error -->
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

            <form id="formularioFormaPago" action="{{ route('formas-pago.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @csrf
                <input type="hidden" name="_method" id="methodFormaPago" value="POST">
                <div>
                    <label for="idFormaPago" class="block text-sm font-medium text-gray-700 mb-1">ID Forma de Pago</label>
                    <input type="text" id="idFormaPago" name="idFormaPago" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm bg-gray-100 cursor-not-allowed" readonly>
                </div>
                <div>
                    <label for="nomFormaPago" class="block text-sm font-medium text-gray-700 mb-1">Nombre Forma de Pago</label>
                    <input type="text" id="nomFormaPago" name="nomFormaPago" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required value="{{ old('nomFormaPago') }}">
                    @error('nomFormaPago')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="desFormaPago" class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                    <textarea id="desFormaPago" name="desFormaPago" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('desFormaPago') }}</textarea>
                    @error('desFormaPago')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="nomeFormaPago" class="block text-sm font-medium text-gray-700 mb-1">Nomenclatura</label>
                    <input type="text" id="nomeFormaPago" name="nomeFormaPago" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="{{ old('nomeFormaPago') }}">
                    @error('nomeFormaPago')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="estadoFormaPago" class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                    <select id="estadoFormaPago" name="estadoFormaPago" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="1" {{ old('estadoFormaPago') == '1' ? 'selected' : '' }}>Activo</option>
                        <option value="0" {{ old('estadoFormaPago') == '0' ? 'selected' : '' }}>Inactivo</option>
                    </select>
                    @error('estadoFormaPago')
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
            formasPagoIndex: "{{ route('formas-pago.index') }}",
            buscarFormaPago: "{{ route('formas-pago.buscarFormaPago') }}",
            formasPagoStore: "{{ route('formas-pago.store') }}",
            formasPagoUpdate: "{{ route('formas-pago.update', ':idFormaPago') }}",
            formasPagoDestroy: "{{ route('formas-pago.destroy', ':idFormaPago') }}"
        }
    };
</script>
<script src="{{ asset('js/configuracion/forma-pago.js') }}"></script>
@endsection
