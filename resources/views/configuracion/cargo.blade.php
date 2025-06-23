@extends('layouts.layout')

@section('content')
<div class="container mx-auto max-w-6xl px-4 py-8">
    <h1 class="text-3xl md:text-4xl font-bold text-gray-800 text-center mb-8">Cargos</h1>

    <!-- Tabla de Cargos y Búsqueda de Cargos -->
    <div class="bg-white shadow-md rounded-lg mb-8">
        <div class="bg-blue-600 text-white text-lg font-semibold px-6 py-4 rounded-t-lg">Lista de Cargos</div>
        <div class="p-6">
            <!-- Formulario de Búsqueda de Cargos -->
            <div class="mb-6">
                <form id="formularioBusquedaCargo" method="GET" class="flex flex-col gap-4">
                    @csrf
                    <div class="flex-1 relative">
                        <label for="buscar_cargo" class="block text-sm font-medium text-gray-700 mb-1">Buscar cargo</label>
                        <input type="text" id="buscar_cargo" name="termino" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Ingrese ID o nombre del cargo" autocomplete="off">
                        <p id="mensajeBusquedaCargo" class="mt-2 text-sm text-gray-500 hidden"></p>
                    </div>
                    <div>
                        <button type="button" id="limpiarBusquedaCargo" class="bg-gray-500 text-white px-6 py-2 rounded-md hover:bg-gray-600 text-sm font-medium">Limpiar Búsqueda</button>
                    </div>
                </form>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-700" id="tablaCargos">
                    <thead class="bg-gray-100 text-xs uppercase text-gray-700">
                        <tr>
                            <th class="px-4 py-3">ID</th>
                            <th class="px-4 py-3">Nombre Cargo</th>
                            <th class="px-4 py-3">Descripción</th>
                            <th class="px-4 py-3">Nomenclatura</th>
                            <th class="px-4 py-3">Estado</th>
                            <th class="px-4 py-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyCargos">
                        @forelse ($cargos as $cargo)
                            <tr class="border-t hover:bg-blue-50">
                                <td class="px-4 py-3">{{ $cargo->idCargo }}</td>
                                <td class="px-4 py-3">{{ $cargo->nomCargo }}</td>
                                <td class="px-4 py-3">{{ $cargo->desCargo ?? 'Sin descripción' }}</td>
                                <td class="px-4 py-3">{{ $cargo->nomeCargo ?? 'Sin nomenclatura' }}</td>
                                <td class="px-4 py-3">{{ $cargo->estadoCargo ? 'Activo' : 'Inactivo' }}</td>
                                <td class="px-4 py-3 flex space-x-2">
                                    <button class="editar-cargo bg-blue-500 text-white px-3 py-1 rounded-md hover:bg-blue-600 text-xs" data-cargo='{{ json_encode(["idCargo" => $cargo->idCargo, "nomCargo" => $cargo->nomCargo, "desCargo" => $cargo->desCargo, "nomeCargo" => $cargo->nomeCargo, "estadoCargo" => $cargo->estadoCargo]) }}'>Editar</button>
                                    <form action="{{ route('cargos.destroy', $cargo->idCargo) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-600 text-xs" onclick="return confirm('¿Está seguro de eliminar este cargo?')">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-3 text-center text-gray-500">No hay cargos registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Formulario de Cargo -->
    <div class="bg-white shadow-md rounded-lg">
        <div class="bg-blue-600 text-white text-lg font-semibold px-6 py-4 rounded-t-lg">Formulario de Cargo</div>
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

            <form id="formularioCargo" action="{{ route('cargos.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @csrf
                @method('POST')
                <div>
                    <label for="idCargo" class="block text-sm font-medium text-gray-700 mb-1">ID Cargo</label>
                    <input type="text" id="idCargo" name="idCargo" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm bg-gray-100 cursor-not-allowed" readonly>
                </div>
                <div>
                    <label for="nomCargo" class="block text-sm font-medium text-gray-700 mb-1">Nombre Cargo</label>
                    <input type="text" id="nomCargo" name="nomCargo" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required value="{{ old('nomCargo') }}">
                    @error('nomCargo')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="desCargo" class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                    <textarea id="desCargo" name="desCargo" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('desCargo') }}</textarea>
                    @error('desCargo')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="nomeCargo" class="block text-sm font-medium text-gray-700 mb-1">Nomenclatura</label>
                    <input type="text" id="nomeCargo" name="nomeCargo" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="{{ old('nomeCargo') }}">
                    @error('nomeCargo')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
               <div>
                    <label for="estadoCargo" class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                    <select id="estadoCargo" name="estadoCargo" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="1" {{ old('estadoCargo') == '1' ? 'selected' : '' }}>Activo</option>
                        <option value="0" {{ old('estadoCargo') == '0' ? 'selected' : '' }}>Inactivo</option>
                    </select>
                    @error('estadoCargo')
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

<script src="{{ asset('js/configuracion/cargo.js') }}"></script>
@endsection