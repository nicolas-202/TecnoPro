@extends('layouts.layout')

@section('content')
<div class="container mx-auto max-w-6xl px-4 py-8">
    <h1 class="text-3xl md:text-4xl font-bold text-gray-800 text-center mb-8">Productos</h1>

    <!-- Tabla de Productos y Búsqueda de Productos -->
    <div class="bg-white shadow-md rounded-lg mb-8">
        <div class="bg-blue-600 text-white text-lg font-semibold px-6 py-4 rounded-t-lg">Lista de Productos</div>
        <div class="p-6">
            <!-- Formulario de Búsqueda de Productos -->
            <div class="mb-6">
                <form id="formularioBusquedaProducto" method="GET" class="flex flex-col gap-4">
                    @csrf
                    <div class="flex-1 relative">
                        <label for="buscar_producto" class="block text-sm font-medium text-gray-700 mb-1">Buscar producto</label>
                        <input type="text" id="buscar_producto" name="termino" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Ingrese ID o nombre del producto" autocomplete="off">
                        <p id="mensajeBusquedaProducto" class="mt-2 text-sm text-gray-500 hidden"></p>
                    </div>
                    <div>
                        <button type="button" id="limpiarBusquedaProducto" class="bg-gray-500 text-white px-6 py-2 rounded-md hover:bg-gray-600 text-sm font-medium">Limpiar Búsqueda</button>
                    </div>
                </form>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-700" id="tablaProductos">
                    <thead class="bg-gray-100 text-xs uppercase text-gray-700">
                        <tr>
                            <th class="px-4 py-3">ID</th>
                            <th class="px-4 py-3">Nombre</th>
                            <th class="px-4 py-3">Descripción</th>
                            <th class="px-4 py-3">Stock Mínimo</th>
                            <th class="px-4 py-3">Stock Máximo</th>
                            <th class="px-4 py-3">Cantidad Existente</th>
                            <th class="px-4 py-3">Precio Venta</th>
                            <th class="px-4 py-3">Categoría</th>
                            <th class="px-4 py-3">Estado</th>
                            <th class="px-4 py-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyProductos">
                        @forelse ($productos as $producto)
                            <tr class="border-t hover:bg-blue-50">
                                <td class="px-4 py-3">{{ $producto->idProducto }}</td>
                                <td class="px-4 py-3">{{ $producto->nomProducto }}</td>
                                <td class="px-4 py-3">{{ $producto->desProducto }}</td>
                                <td class="px-4 py-3">{{ $producto->stockMinimo }}</td>
                                <td class="px-4 py-3">{{ $producto->stockMaximo }}</td>
                                <td class="px-4 py-3">{{ $producto->cantidadExistente }}</td>
                                <td class="px-4 py-3">{{ $producto->precioVenta }}</td>
                                <td class="px-4 py-3">{{ $producto->categoria->nomCategoria }}</td>
                                <td class="px-4 py-3">{{ $producto->estadoProducto ? 'Activo' : 'Inactivo' }}</td>
                                <td class="px-4 py-3 flex space-x-2">
                                    <button class="editar-producto bg-blue-500 text-white px-3 py-1 rounded-md hover:bg-blue-600 text-xs" data-producto='{{ json_encode(["idProducto" => $producto->idProducto, "nomProducto" => $producto->nomProducto, "desProducto" => $producto->desProducto, "stockMinimo" => $producto->stockMinimo, "stockMaximo" => $producto->stockMaximo, "cantidadExistente" => $producto->cantidadExistente, "precioVenta" => $producto->precioVenta, "idCategoria" => $producto->idCategoria, "estadoProducto" => $producto->estadoProducto, "imagen" => $producto->imagen]) }}'>Editar</button>
                                    <form action="{{ route('productos.destroy', $producto->idProducto) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-600 text-xs" onclick="return confirm('¿Está seguro de eliminar este producto?')">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="px-4 py-3 text-center text-gray-500">No hay productos registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4" id="paginacion">
                {{ $productos->links('pagination::tailwind') }}
            </div>
        </div>
    </div>

    <!-- Formulario de Producto -->
    <div class="bg-white shadow-md rounded-lg">
        <div class="bg-blue-600 text-white text-lg font-semibold px-6 py-4 rounded-t-lg">Formulario de Producto</div>
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
            <form id="formularioProducto" action="{{ route('productos.store') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @csrf
                @method('POST')
                <div>
                    <label for="idProducto" class="block text-sm font-medium text-gray-700 mb-1">ID Producto</label>
                    <input type="text" id="idProducto" name="idProducto" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm bg-gray-100 cursor-not-allowed" readonly>
                </div>
                <div>
                    <label for="nomProducto" class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                    <input type="text" id="nomProducto" name="nomProducto" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required value="{{ old('nomProducto') }}">
                    @error('nomProducto')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="desProducto" class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                    <textarea id="desProducto" name="desProducto" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>{{ old('desProducto') }}</textarea>
                    @error('desProducto')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="stockMinimo" class="block text-sm font-medium text-gray-700 mb-1">Stock Mínimo</label>
                    <input type="number" id="stockMinimo" name="stockMinimo" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required value="{{ old('stockMinimo') }}">
                    @error('stockMinimo')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="stockMaximo" class="block text-sm font-medium text-gray-700 mb-1">Stock Máximo</label>
                    <input type="number" id="stockMaximo" name="stockMaximo" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required value="{{ old('stockMaximo') }}">
                    @error('stockMaximo')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="cantidadExistente" class="block text-sm font-medium text-gray-700 mb-1">Cantidad Existente</label>
                    <input type="number" id="cantidadExistente" name="cantidadExistente" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm bg-gray-100 cursor-not-allowed" readonly value="0">
                </div>
                <div>
                    <label for="precioVenta" class="block text-sm font-medium text-gray-700 mb-1">Precio Venta</label>
                    <input type="number" id="precioVenta" name="precioVenta" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm bg-gray-100 cursor-not-allowed" readonly value="0">
                </div>
                <div>
                    <label for="idCategoria" class="block text-sm font-medium text-gray-700 mb-1">Categoría</label>
                    <select id="idCategoria" name="idCategoria" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="">Seleccione una categoría</option>
                        @foreach ($categorias as $categoria)
                            <option value="{{ $categoria->idCategoria }}" {{ old('idCategoria') == $categoria->idCategoria ? 'selected' : '' }}>{{ $categoria->nomCategoria }}</option>
                        @endforeach
                    </select>
                    @error('idCategoria')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="estadoProducto" class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                    <select id="estadoProducto" name="estadoProducto" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="1" {{ old('estadoProducto') == '1' ? 'selected' : '' }}>Activo</option>
                        <option value="0" {{ old('estadoProducto') == '0' ? 'selected' : '' }}>Inactivo</option>
                    </select>
                    @error('estadoProducto')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="imagen" class="block text-sm font-medium text-gray-700 mb-1">Foto del Producto</label>
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

<link href="{{ asset('css/productos.css') }}" rel="stylesheet">
<script src="{{ asset('js/configuracion/producto.js') }}"></script>
@endsection