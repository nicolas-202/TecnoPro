document.addEventListener('DOMContentLoaded', function () {
    const formularioCategoria = document.getElementById('formularioCategoria');
    const limpiarTodoBtn = document.getElementById('limpiarTodo');
    const inputBuscarCategoria = document.getElementById('buscar_categoria');
    const mensajeBusquedaCategoria = document.getElementById('mensajeBusquedaCategoria');
    const tbodyCategorias = document.getElementById('tbodyCategorias');
    const limpiarBusquedaCategoriaBtn = document.getElementById('limpiarBusquedaCategoria');

    // Función para asignar eventos a los botones de editar
    function asignarEventosEditar() {
        document.querySelectorAll('.editar-categoria').forEach(button => {
            button.removeEventListener('click', editarCategoriaHandler); // Evitar duplicados
            button.addEventListener('click', editarCategoriaHandler);
        });
    }

    // Handler para editar categoría
    function editarCategoriaHandler() {
        const categoria = JSON.parse(this.getAttribute('data-categoria'));
        formularioCategoria.action = `/categorias/${categoria.idCategoria}`;
        formularioCategoria.querySelector('input[name="_method"]').value = 'PUT';
        document.getElementById('idCategoria').value = categoria.idCategoria;
        document.getElementById('nomCategoria').value = categoria.nomCategoria || '';
        document.getElementById('desCategoria').value = categoria.desCategoria || '';
        document.getElementById('nomeCategoria').value = categoria.nomeCategoria || '';
        document.getElementById('estadoCategoria').value = categoria.estadoCategoria ? '1' : '0';
    }

    // Inicializar eventos de editar al cargar la página
    asignarEventosEditar();

    // Limpiar formulario de categoría
    limpiarTodoBtn.addEventListener('click', function () {
        formularioCategoria.reset();
        formularioCategoria.action = "{{ route('categorias.store') }}";
        formularioCategoria.querySelector('input[name="_method"]').value = 'POST';
        document.getElementById('idCategoria').value = '';
        document.getElementById('nomCategoria').value = '';
        document.getElementById('desCategoria').value = '';
        document.getElementById('nomeCategoria').value = '';
        document.getElementById('estadoCategoria').value = '1';
    });

    // Limpiar formulario de búsqueda de categoría y recargar lista
   limpiarBusquedaCategoriaBtn.addEventListener('click', function () {
    inputBuscarCategoria.value = '';
    mensajeBusquedaCategoria.classList.add('hidden');
    fetch('/categorias', {
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
        console.log('Respuesta del servidor para /categorias:', data); // Agrega esto para depurar
        tbodyCategorias.innerHTML = '';
        mensajeBusquedaCategoria.classList.add('hidden');
        if (data.success && Array.isArray(data.categorias) && data.categorias.length > 0) {
            data.categorias.forEach(categoria => {
                const tr = document.createElement('tr');
                tr.className = 'border-t hover:bg-blue-50';
                tr.innerHTML = `
                    <td class="px-4 py-3">${categoria.idCategoria}</td>
                    <td class="px-4 py-3">${categoria.nomCategoria}</td>
                    <td class="px-4 py-3">${categoria.desCategoria || 'Sin descripción'}</td>
                    <td class="px-4 py-3">${categoria.nomeCategoria || 'Sin nomenclatura'}</td>
                    <td class="px-4 py-3">${categoria.estadoCategoria ? 'Activo' : 'Inactivo'}</td>
                    <td class="px-4 py-3 flex space-x-2">
                        <button class="editar-categoria bg-blue-500 text-white px-3 py-1 rounded-md hover:bg-blue-600 text-xs" data-categoria='${JSON.stringify(categoria)}'>Editar</button>
                        <form action="/categorias/${categoria.idCategoria}" method="POST" class="inline">
                            <input type="hidden" name="_token" value="${document.querySelector('input[name="_token"]').value}">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-600 text-xs" onclick="return confirm('¿Está seguro de eliminar esta categoría?')">Eliminar</button>
                        </form>
                    </td>
                `;
                tbodyCategorias.appendChild(tr);
            });
            asignarEventosEditar();
        } else {
            tbodyCategorias.innerHTML = `
                <tr>
                    <td colspan="6" class="px-4 py-3 text-center text-gray-500">No se encontraron categorías.</td>
                </tr>
            `;
            mensajeBusquedaCategoria.textContent = data.message || 'No se encontraron categorías.';
            mensajeBusquedaCategoria.classList.remove('hidden');
            mensajeBusquedaCategoria.classList.add('text-gray-500');
        }
    })
    .catch(error => {
        console.error('Error al recargar categorías:', error);
        mensajeBusquedaCategoria.textContent = 'Error al recargar la lista de categorías: ' + error.message;
        mensajeBusquedaCategoria.classList.remove('hidden');
        mensajeBusquedaCategoria.classList.add('text-red-500');
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

   inputBuscarCategoria.addEventListener('input', debounce(function () {
    const termino = inputBuscarCategoria.value.trim();
    if (!termino) {
        limpiarBusquedaCategoriaBtn.click();
        return;
    }
    const url = `/categorias/buscarCategoria?termino=${encodeURIComponent(termino)}`;
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
        console.log('Respuesta del servidor:', data); // Agrega esto para depurar
        tbodyCategorias.innerHTML = '';
        mensajeBusquedaCategoria.classList.add('hidden');
        if (data.success && Array.isArray(data.categorias) && data.categorias.length > 0) {
            data.categorias.forEach(categoria => {
                const tr = document.createElement('tr');
                tr.className = 'border-t hover:bg-blue-50';
                tr.innerHTML = `
                    <td class="px-4 py-3">${categoria.idCategoria}</td>
                    <td class="px-4 py-3">${categoria.nomCategoria}</td>
                    <td class="px-4 py-3">${categoria.desCategoria || 'Sin descripción'}</td>
                    <td class="px-4 py-3">${categoria.nomeCategoria || 'Sin nomenclatura'}</td>
                    <td class="px-4 py-3">${categoria.estadoCategoria ? 'Activo' : 'Inactivo'}</td>
                    <td class="px-4 py-3 flex space-x-2">
                        <button class="editar-categoria bg-blue-500 text-white px-3 py-1 rounded-md hover:bg-blue-600 text-xs" data-categoria='${JSON.stringify(categoria)}'>Editar</button>
                        <form action="/categorias/${categoria.idCategoria}" method="POST" class="inline">
                            <input type="hidden" name="_token" value="${document.querySelector('input[name="_token"]').value}">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-600 text-xs" onclick="return confirm('¿Está seguro de eliminar esta categoría?')">Eliminar</button>
                        </form>
                    </td>
                `;
                tbodyCategorias.appendChild(tr);
            });
            asignarEventosEditar();
        } else {
            tbodyCategorias.innerHTML = `
                <tr>
                    <td colspan="6" class="px-4 py-3 text-center text-gray-500">No se encontraron categorías.</td>
                </tr>
            `;
            mensajeBusquedaCategoria.textContent = data.message || 'No se encontraron categorías.';
            mensajeBusquedaCategoria.classList.remove('hidden');
            mensajeBusquedaCategoria.classList.add('text-red-500');
        }
    })
    .catch(error => {
        console.error('Error en la búsqueda de categorías:', error);
        mensajeBusquedaCategoria.textContent = 'Error al buscar categorías: ' + error.message;
        mensajeBusquedaCategoria.classList.remove('hidden');
        mensajeBusquedaCategoria.classList.add('text-red-500');
        tbodyCategorias.innerHTML = `
            <tr>
                <td colspan="6" class="px-4 py-3 text-center text-gray-500">Error al buscar categorías.</td>
            </tr>
        `;
    });
}, 300));
});