@extends('layouts.layout')

@section('content')
<div class="container mx-auto max-w-6xl px-4 py-8">
    <h1 class="text-3xl md:text-4xl font-bold text-gray-800 text-center mb-8">Empleados</h1>

    <!-- Tabla de Empleados y Búsqueda de Empleados -->
    <div class="bg-white shadow-md rounded-lg mb-8">
        <div class="bg-blue-600 text-white text-lg font-semibold px-6 py-4 rounded-t-lg">Lista de Empleados</div>
        <div class="p-6">
            <!-- Formulario de Búsqueda de Empleados -->
            <div class="mb-6">
                <form id="formularioBusquedaEmpleado" method="GET" class="flex flex-col gap-4">
                    @csrf
                    <div class="flex-1 relative">
                        <label for="buscar_empleado" class="block text-sm font-medium text-gray-700 mb-1">Buscar empleado</label>
                        <input type="text" id="buscar_empleado" name="termino" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Ingrese ID o nombre del empleado" autocomplete="off">
                        <p id="mensajeBusquedaEmpleado" class="mt-2 text-sm text-gray-500 hidden"></p>
                    </div>
                    <div>
                        <button type="button" id="limpiarBusquedaEmpleado" class="bg-gray-500 text-white px-6 py-2 rounded-md hover:bg-gray-600 text-sm font-medium">Limpiar Búsqueda</button>
                    </div>
                </form>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-700" id="tablaEmpleados">
                    <thead class="bg-gray-100 text-xs uppercase text-gray-700">
                        <tr>
                            <th class="px-4 py-3">ID</th>
                            <th class="px-4 py-3">Nombre</th>
                            <th class="px-4 py-3">Número de Documento</th>
                            <th class="px-4 py-3">Estado</th>
                            <th class="px-4 py-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyEmpleados">
                        @forelse ($empleados as $empleado)
                            <tr class="border-t hover:bg-blue-50">
                                <td class="px-4 py-3">{{ $empleado->idEmpleado }}</td>
                                <td class="px-4 py-3">{{ $empleado->user->nombre }}</td>
                                <td class="px-4 py-3">{{ $empleado->user->numero_documento }}</td>
                                <td class="px-4 py-3">{{ $empleado->estadoEmpleado ? 'Activo' : 'Inactivo' }}</td>
                                <td class="px-4 py-3 flex space-x-2">
                                    <button class="editar-empleado bg-blue-500 text-white px-3 py-1 rounded-md hover:bg-blue-600 text-xs" data-empleado='{{ json_encode(["idEmpleado" => $empleado->idEmpleado, "user_id" => $empleado->user_id, "nombre" => $empleado->user->nombre, "numero_documento" => $empleado->user->numero_documento, "idCargo" => $empleado->idCargo, "estadoEmpleado" => $empleado->estadoEmpleado, "imagen" => $empleado->imagen]) }}'>Editar</button>
                                    <form action="{{ route('empleados.destroy', $empleado->idEmpleado) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-600 text-xs" onclick="return confirm('¿Está seguro de eliminar este empleado?')">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-3 text-center text-gray-500">No hay empleados registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4" id="paginacion">
                {{ $empleados->links('pagination::tailwind') }}
            </div>
        </div>
    </div>

    <!-- Formulario de Empleado y Búsqueda de Usuario -->
    <div class="bg-white shadow-md rounded-lg">
        <div class="bg-blue-600 text-white text-lg font-semibold px-6 py-4 rounded-t-lg">Formulario de Empleado</div>
        <div class="p-6">
            <!-- Formulario de Búsqueda de Usuario -->
            <div class="mb-6">
                <form id="formularioBusquedaUsuario" method="GET" class="flex flex-col gap-4">
                    @csrf
                    <div class="flex-1 relative">
                        <label for="buscar_user_id" class="block text-sm font-medium text-gray-700 mb-1">Buscar usuario</label>
                        <input type="text" id="buscar_user_id" name="buscar_user_id" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Ingrese el ID o Nombre del usuario" autocomplete="off">
                        <div id="resultadosBusqueda" class="absolute z-10 w-full bg-white border border-gray-300 rounded-md shadow-lg mt-1 max-h-60 overflow-y-auto hidden">
                            <ul id="listaUsuarios" class="divide-y divide-gray-200"></ul>
                        </div>
                        <p id="mensajeBusqueda" class="mt-2 text-sm text-gray-500 hidden"></p>
                    </div>
                </form>
            </div>

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
            <form id="formularioEmpleado" action="{{ route('empleados.store') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @csrf
                @method('POST')
                <div>
                    <label for="idEmpleado" class="block text-sm font-medium text-gray-700 mb-1">ID Empleado</label>
                    <input type="text" id="idEmpleado" name="idEmpleado" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm bg-gray-100 cursor-not-allowed" readonly>
                </div>
                <div>
                    <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">ID Usuario</label>
                    <input type="text" id="user_id" name="user_id" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm bg-gray-100 cursor-not-allowed" readonly required value="{{ old('user_id') }}">
                    @error('user_id')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                    <input type="text" id="nombre" name="nombre" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm bg-gray-100 cursor-not-allowed" readonly value="{{ old('nombre') }}">
                </div>
                <div>
                    <label for="numero_documento" class="block text-sm font-medium text-gray-700 mb-1">Número de Documento</label>
                    <input type="text" id="numero_documento" name="numero_documento" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm bg-gray-100 cursor-not-allowed" readonly value="{{ old('numero_documento') }}">
                </div>
                <div>
                    <label for="idCargo" class="block text-sm font-medium text-gray-700 mb-1">Cargo</label>
                    <select id="idCargo" name="idCargo" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="">Seleccione un cargo</option>
                        @foreach ($cargos as $cargo)
                            <option value="{{ $cargo->idCargo }}" {{ old('idCargo') == $cargo->idCargo ? 'selected' : '' }}>{{ $cargo->nomCargo }}</option>
                        @endforeach
                    </select>
                    @error('idCargo')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="estadoEmpleado" class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                    <select id="estadoEmpleado" name="estadoEmpleado" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="1" {{ old('estadoEmpleado') == '1' ? 'selected' : '' }}>Activo</option>
                        <option value="0" {{ old('estadoEmpleado') == '0' ? 'selected' : '' }}>Inactivo</option>
                    </select>
                    @error('estadoEmpleado')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="imagen" class="block text-sm font-medium text-gray-700 mb-1">Foto del Empleado</label>
                    <input type="file" id="imagen" name="imagen" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm" accept="image/*">
                    @error('imagen')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                    <div id="vistaPreviaImagen" class="mt-4">
                        <img id="imagenPrevia" src="#" alt="Vista previa" class="max-w-[200px] rounded-md border border-gray-200 hidden">
                    </div>
                </div>
                <div class="md:col-span-2 flex space-x-4">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 text-sm font-medium">Guardar</button>
                    <button type="button" id="limpiarFormulario" class="bg-gray-500 text-white px-6 py-2 rounded-md hover:bg-gray-600 text-sm font-medium">Limpiar Todo</button>
                </div>
            </form>
        </div>
    </div>
</div>

<link href="{{ asset('css/empleados.css') }}" rel="stylesheet">
<script src="{{ asset('js/configuracion/empleados.js') }}"></script>
@endsection