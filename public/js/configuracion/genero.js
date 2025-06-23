document.addEventListener('DOMContentLoaded', function () {
    const formularioGenero = document.getElementById('formularioGenero');
    const limpiarTodoBtn = document.getElementById('limpiarTodo');
    const inputBuscarGenero = document.getElementById('buscar_genero');
    const mensajeBusquedaGenero = document.getElementById('mensajeBusquedaGenero');
    const tbodyGeneros = document.getElementById('tbodyGeneros');
    const limpiarBusquedaGeneroBtn = document.getElementById('limpiarBusquedaGenero');

    // Función para asignar eventos a los botones de editar
    function asignarEventosEditar() {
        document.querySelectorAll('.editar-genero').forEach(button => {
            console.log('Asignando evento al botón:', button); // Depuración
            button.removeEventListener('click', editarGeneroHandler); // Evitar duplicados
            button.addEventListener('click', editarGeneroHandler);
        });
    }

    // Handler para editar género
    function editarGeneroHandler() {
        try {
            const dataGenero = this.getAttribute('data-genero').trim(); // Eliminar espacios
            console.log('Valor crudo de data-genero:', dataGenero); // Depuración
            console.log('Longitud de data-genero:', dataGenero.length); // Depuración
            const genero = JSON.parse(dataGenero);
            console.log('Editando género:', genero); // Depuración
            formularioGenero.action = `/generos/${genero.idGenero}`;
            formularioGenero.querySelector('input[name="_method"]').value = 'PUT';
            document.getElementById('idGenero').value = genero.idGenero;
            document.getElementById('nomGenero').value = genero.nomGenero || '';
            document.getElementById('desGenero').value = genero.desGenero || '';
            document.getElementById('nomeGenero').value = genero.nomeGenero || '';
            document.getElementById('estadoGenero').value = genero.estadoGenero ? '1' : '0';
        } catch (error) {
            console.error('Error al parsear data-genero:', error);
            alert('Error al cargar los datos del género para edición: ' + error.message);
        }
    }

    // Inicializar eventos de editar al cargar la página
    asignarEventosEditar();

    // Limpiar formulario de género
    limpiarTodoBtn.addEventListener('click', function () {
        formularioGenero.reset();
        formularioGenero.action = "{{ route('generos.store') }}";
        formularioGenero.querySelector('input[name="_method"]').value = 'POST';
        document.getElementById('idGenero').value = '';
        document.getElementById('nomGenero').value = '';
        document.getElementById('desGenero').value = '';
        document.getElementById('nomeGenero').value = '';
        document.getElementById('estadoGenero').value = '1';
    });

    // Limpiar formulario de búsqueda de género y recargar lista
    limpiarBusquedaGeneroBtn.addEventListener('click', function () {
        inputBuscarGenero.value = '';
        mensajeBusquedaGenero.classList.add('hidden');
        fetch('/generos', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
            },
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Respuesta del servidor para /generos:', data); // Depuración
            tbodyGeneros.innerHTML = '';
            mensajeBusquedaGenero.classList.add('hidden');
            if (data.success && Array.isArray(data.generos) && data.generos.length > 0) {
                data.generos.forEach(genero => {
                    const tr = document.createElement('tr');
                    tr.className = 'border-t hover:bg-blue-50';
                    tr.innerHTML = `
                        <td class="px-4 py-3">${genero.idGenero}</td>
                        <td class="px-4 py-3">${genero.nomGenero}</td>
                        <td class="px-4 py-3">${genero.desGenero || 'Sin descripción'}</td>
                        <td class="px-4 py-3">${genero.nomeGenero || 'Sin nomenclatura'}</td>
                        <td class="px-4 py-3">${genero.estadoGenero ? 'Activo' : 'Inactivo'}</td>
                        <td class="px-4 py-3 flex space-x-2">
                            <button class="editar-genero bg-blue-500 text-white px-3 py-1 rounded-md hover:bg-blue-600 text-xs" data-genero='${JSON.stringify(genero)}'>Editar</button>
                            <form action="/generos/${genero.idGenero}" method="POST" class="inline">
                                <input type="hidden" name="_token" value="${document.querySelector('input[name="_token"]').value}">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-600 text-xs" onclick="return confirm('¿Está seguro de eliminar este género?')">Eliminar</button>
                            </form>
                        </td>
                    `;
                    tbodyGeneros.appendChild(tr);
                });
                asignarEventosEditar();
            } else {
                tbodyGeneros.innerHTML = `
                    <tr>
                        <td colspan="6" class="px-4 py-3 text-center text-gray-500">No se encontraron géneros.</td>
                    </tr>
                `;
                mensajeBusquedaGenero.textContent = data.message || 'No se encontraron géneros.';
                mensajeBusquedaGenero.classList.remove('hidden');
                mensajeBusquedaGenero.classList.add('text-gray-500');
            }
        })
        .catch(error => {
            console.error('Error al recargar géneros:', error);
            mensajeBusquedaGenero.textContent = 'Error al recargar la lista de géneros: ' + error.message;
            mensajeBusquedaGenero.classList.remove('hidden');
            mensajeBusquedaGenero.classList.add('text-red-500');
        });
    });

    // Función de debounce
    const debounce = (func, delay) => {
        let timeoutId;
        return (...args) => {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => func.apply(null, args), delay);
        };
    };

    // Buscar género en tiempo real
    inputBuscarGenero.addEventListener('input', debounce(function () {
        const termino = inputBuscarGenero.value.trim();
        if (!termino) {
            limpiarBusquedaGeneroBtn.click();
            return;
        }
        const url = `/generos/buscarGenero?termino=${encodeURIComponent(termino)}`;
        fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
            },
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Respuesta del servidor:', data); // Depuración
            tbodyGeneros.innerHTML = '';
            mensajeBusquedaGenero.classList.add('hidden');
            if (data.success && Array.isArray(data.generos) && data.generos.length > 0) {
                data.generos.forEach(genero => {
                    const tr = document.createElement('tr');
                    tr.className = 'border-t hover:bg-blue-50';
                    tr.innerHTML = `
                        <td class="px-4 py-3">${genero.idGenero}</td>
                        <td class="px-4 py-3">${genero.nomGenero}</td>
                        <td class="px-4 py-3">${genero.desGenero || 'Sin descripción'}</td>
                        <td class="px-4 py-3">${genero.nomeGenero || 'Sin nomenclatura'}</td>
                        <td class="px-4 py-3">${genero.estadoGenero ? 'Activo' : 'Inactivo'}</td>
                        <td class="px-4 py-3 flex space-x-2">
                            <button class="editar-genero bg-blue-500 text-white px-3 py-1 rounded-md hover:bg-blue-600 text-xs" data-genero='${JSON.stringify(genero)}'>Editar</button>
                            <form action="/generos/${genero.idGenero}" method="POST" class="inline">
                                <input type="hidden" name="_token" value="${document.querySelector('input[name="_token"]').value}">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-600 text-xs" onclick="return confirm('¿Está seguro de eliminar este género?')">Eliminar</button>
                            </form>
                        </td>
                    `;
                    tbodyGeneros.appendChild(tr);
                });
                asignarEventosEditar();
            } else {
                tbodyGeneros.innerHTML = `
                    <tr>
                        <td colspan="6" class="px-4 py-3 text-center text-gray-500">No se encontraron géneros.</td>
                    </tr>
                `;
                mensajeBusquedaGenero.textContent = data.message || 'No se encontraron géneros.';
                mensajeBusquedaGenero.classList.remove('hidden');
                mensajeBusquedaGenero.classList.add('text-red-500');
            }
        })
        .catch(error => {
            console.error('Error en la búsqueda de géneros:', error);
            mensajeBusquedaGenero.textContent = 'Error al buscar géneros: ' + error.message;
            mensajeBusquedaGenero.classList.remove('hidden');
            mensajeBusquedaGenero.classList.add('text-red-500');
            tbodyGeneros.innerHTML = `
                <tr>
                    <td colspan="6" class="px-4 py-3 text-center text-gray-500">Error al buscar géneros.</td>
                </tr>
            `;
        });
    }, 300));
});