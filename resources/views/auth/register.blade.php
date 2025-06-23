
<script>

    document.addEventListener('DOMContentLoaded', function () {
        const paisSelect = document.getElementById('idPais');
        const departamentoSelect = document.getElementById('idDepartamento');
        const municipioSelect = document.getElementById('idMunicipio');

        paisSelect.addEventListener('change', function () {
            const paisId = this.value;
            departamentoSelect.innerHTML = '<option value="">Cargando...</option>';
            municipioSelect.innerHTML = '<option value="">Seleccione un municipio</option>';

            fetch(`/departamentos/${paisId}`)
                .then(response => response.json())
                .then(data => {
                    departamentoSelect.innerHTML = '<option value="">Seleccione un departamento</option>';
                    data.forEach(depto => {
                        departamentoSelect.innerHTML += `<option value="${depto.idDepartamento}">${depto.nomDepartamento}</option>`;
                    });
                });
        });

        departamentoSelect.addEventListener('change', function () {
            const departamentoId = this.value;
            municipioSelect.innerHTML = '<option value="">Cargando...</option>';

            fetch(`/municipios/${departamentoId}`)
                .then(response => response.json())
                .then(data => {
                    municipioSelect.innerHTML = '<option value="">Seleccione un municipio</option>';
                    data.forEach(muni => {
                        municipioSelect.innerHTML += `<option value="${muni.idMunicipio}">${muni.nomMunicipio}</option>`;
                    });
                });
        });
    });
</script>

<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="nombre" :value="__('Nombre')" />
            <x-text-input id="nombre" class="block mt-1 w-full" type="text" name="nombre" :value="old('nombre')" required autofocus autocomplete="nombre" />
            <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Contraseña')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirma tu contraseña')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Celular -->
        <div class="mt-4">
            <x-input-label for="celular" :value="__('Celular')" />
            <x-text-input id="celular" class="block mt-1 w-full" type="text" name="celular" :value="old('celular')" required />
            <x-input-error :messages="$errors->get('celular')" class="mt-2" />
        </div>
        <!-- Fecha de Nacimiento -->
        <div class="mt-4">
            <x-input-label for="fecha_nacimiento" :value="__('Fecha de Nacimiento')" />
            <x-text-input id="fecha_nacimiento" class="block mt-1 w-full" type="date" name="fecha_nacimiento" :value="old('fecha_nacimiento')" required />
            <x-input-error :messages="$errors->get('fecha_nacimiento')" class="mt-2" />
        </div>

        <!-- Número de Documento -->
        <div class="mt-4">
            <x-input-label for="numero_documento" :value="__('Número de Documento')" />
            <x-text-input id="numero_documento" class="block mt-1 w-full" type="text" name="numero_documento" :value="old('numero_documento')" required />
            <x-input-error :messages="$errors->get('numero_documento')" class="mt-2" />
        </div>
        <!-- Dirección -->
        <div class="mt-4">
            <x-input-label for="direccion" :value="__('Dirección')" />
            <x-text-input id="direccion" class="block mt-1 w-full" type="text" name="direccion" :value="old('direccion')" required />
            <x-input-error :messages="$errors->get('direccion')" class="mt-2" />
        </div>
        <!-- Género -->
        <div class="mt-4">
            <x-input-label for="idGenero" :value="__('Género')" />
            <select id="idGenero" name="idGenero" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required>
                <option value="">Seleccione un género</option>
                @foreach ($generos as $genero)
                    <option value="{{ $genero->idGenero }}">{{ $genero->nomGenero }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('idGenero')" class="mt-2" />
        </div>
        <!-- Tipo de Documento -->
        <div class="mt-4">
            <x-input-label for="idTipoDocumento" :value="__('Tipo de Documento')" />
            <select id="idTipoDocumento" name="idTipoDocumento" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required>
                <option value="">Seleccione un tipo</option>
                @foreach ($tiposDocumento as $tipo)
                    <option value="{{ $tipo->idTipoDocumento }}">{{ $tipo->nomTipoDocumento }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('idTipoDocumento')" class="mt-2" />
        </div>
        <!-- Municipio -->
        
            
            <!-- País -->
        <div class="mt-4">
            <x-input-label for="idPais" :value="__('País')" />
            <select id="idPais" name="idPais" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required>
                <option value="">Seleccione un país</option>
                @foreach ($paises as $pais)
                    <option value="{{ $pais->idPais }}">{{ $pais->nomPais }}</option>
                @endforeach
            </select>
        </div>

        <!-- Departamento -->
        <div class="mt-4">
            <x-input-label for="idDepartamento" :value="__('Departamento')" />
            <select id="idDepartamento" name="idDepartamento" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required>
                <option value="">Seleccione un departamento</option>
            </select>
        </div>

        <!-- Municipio -->
        <div class="mt-4">
            <x-input-label for="idMunicipio" :value="__('Municipio')" />
            <select id="idMunicipio" name="idMunicipio" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required>
                <option value="">Seleccione un municipio</option>
            </select>
        </div>


        
        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('¿Ya estas registrado?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Registrarse') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
