
@extends('layouts.layout')

@section('content')
<div class="container mx-auto max-w-6xl px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Editar Información</h1>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg p-6">
        <form action="{{ route('cuenta.actualizar') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="mb-4">
                    <label for="nombre" class="block text-gray-600">Nombre</label>
                    <input type="text" id="nombre" name="nombre" value="{{ old('nombre', $usuario->nombre) }}" class="w-full p-2 border rounded-md" required>
                </div>
                <div class="mb-4">
                    <label for="email" class="block text-gray-600">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $usuario->email) }}" class="w-full p-2 border rounded-md" required>
                </div>
                <div class="mb-4">
                    <label for="celular" class="block text-gray-600">Celular</label>
                    <input type="text" id="celular" name="celular" value="{{ old('celular', $usuario->celular) }}" class="w-full p-2 border rounded-md" required>
                </div>
                <div class="mb-4">
                    <label for="fecha_nacimiento" class="block text-gray-600">Fecha de Nacimiento</label>
                    <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" value="{{ old('fecha_nacimiento', $usuario->fecha_nacimiento->format('Y-m-d')) }}" class="w-full p-2 border rounded-md" required>
                </div>
                <div class="mb-4">
                    <label for="numero_documento" class="block text-gray-600">Número de Documento</label>
                    <input type="text" id="numero_documento" name="numero_documento" value="{{ old('numero_documento', $usuario->numero_documento) }}" class="w-full p-2 border rounded-md" required>
                </div>
                <div class="mb-4">
                    <label for="idTipoDocumento" class="block text-gray-600">Tipo de Documento</label>
                    <select id="idTipoDocumento" name="idTipoDocumento" class="w-full p-2 border rounded-md" required>
                        <option value="">Seleccione un tipo</option>
                        @foreach ($tiposDocumento as $tipo)
                            <option value="{{ $tipo->idTipoDocumento }}" {{ old('idTipoDocumento', $usuario->idTipoDocumento) == $tipo->idTipoDocumento ? 'selected' : '' }}>{{ $tipo->nomTipoDocumento }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label for="idGenero" class="block text-gray-600">Género</label>
                    <select id="idGenero" name="idGenero" class="w-full p-2 border rounded-md" required>
                        <option value="">Seleccione un género</option>
                        @foreach ($generos as $genero)
                            <option value="{{ $genero->idGenero }}" {{ old('idGenero', $usuario->idGenero) == $genero->idGenero ? 'selected' : '' }}>{{ $genero->nomGenero }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label for="idPais" class="block text-gray-600">País</label>
                    <select id="idPais" name="idPais" class="w-full p-2 border rounded-md" required>
                        <option value="">Seleccione un país</option>
                        @foreach ($paises as $pais)
                            <option value="{{ $pais->idPais }}" {{ old('idPais', $paisInicial ? $paisInicial->idPais : '') == $pais->idPais ? 'selected' : '' }}>{{ $pais->nomPais }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label for="idDepartamento" class="block text-gray-600">Departamento</label>
                    <select id="idDepartamento" name="idDepartamento" class="w-full p-2 border rounded-md" required>
                        <option value="">Seleccione un departamento</option>
                        @foreach ($departamentos as $departamento)
                            <option value="{{ $departamento->idDepartamento }}" {{ old('idDepartamento', $departamentoInicial ? $departamentoInicial->idDepartamento : '') == $departamento->idDepartamento ? 'selected' : '' }}>{{ $departamento->nomDepartamento }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label for="idMunicipio" class="block text-gray-600">Municipio</label>
                    <select id="idMunicipio" name="idMunicipio" class="w-full p-2 border rounded-md" required>
                        <option value="">Seleccione un municipio</option>
                        @foreach ($municipios as $municipio)
                            <option value="{{ $municipio->idMunicipio }}" {{ old('idMunicipio', $usuario->idMunicipio) == $municipio->idMunicipio ? 'selected' : '' }}>{{ $municipio->nomMunicipio }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label for="direccion" class="block text-gray-600">Dirección</label>
                    <input type="text" id="direccion" name="direccion" value="{{ old('direccion', $usuario->direccion) }}" class="w-full p-2 border rounded-md" required>
                </div>
            </div>
            <div class="mt-6">
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">Guardar Cambios</button>
                <a href="{{ route('cuenta.perfil') }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-800 ml-2">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<script src="{{ asset('js/cuenta/editar.js') }}">
 
</script>
@endsection
