document.addEventListener('DOMContentLoaded', function () {
    const formularioPais = document.getElementById('formularioPais');
    const limpiarTodoBtn = document.getElementById('limpiarTodo');
    const inputBuscarPais = document.getElementById('buscar_pais');
    const mensajeBusquedaPais = document.getElementById('mensajeBusquedaPais');
    const tbodyPaises = document.getElementById('tbodyPaises');
    const limpiarBusquedaPaisBtn = document.getElementById('limpiarBusquedaPais');

    // Función para asignar eventos a los botones de editar
    function asignarEventosEditar() {
        document.querySelectorAll('.editar-pais').forEach(button => {
            console.log('Asignando evento al botón:', button);
            button.removeEventListener('click', editarPaisHandler);
            button.addEventListener('click', editarPaisHandler);
        });
    }

    // Handler para editar país
    function editarPaisHandler() {
        try {
            const dataPais = this.getAttribute('data-pais').trim();
            console.log('Valor crudo de data-pais:', dataPais);
            console.log('Longitud de data-pais:', dataPais.length);
            const pais = JSON.parse(dataPais);
            console.log('Editando país:', pais);
            formularioPais.action = `/paises/${pais.idPais}`;
            formularioPais.querySelector('input[name="_method"]').value = 'PUT';
            document.getElementById('idPais').value = pais.idPais;
            document.getElementById('nomPais').value = pais.nomPais || '';
            document.getElementById('desPais').value = pais.desPais || '';
            document.getElementById('nomePais').value = pais.nomePais || '';
            document.getElementById('estadoPais').value = pais.estadoPais ? '1' : '0';
        } catch (error) {
            console.error('Error al parsear data-pais:', error);
            alert('Error al cargar los datos del país para edición: ' + error.message);
        }
    }

    // Inicializar eventos de editar al cargar la página
    asignarEventosEditar();

    // Limpiar formulario de país
    limpiarTodoBtn.addEventListener('click', function () {
        formularioPais.reset();
        formularioPais.action = "{{ route('paises.store') }}";
        formularioPais.querySelector('input[name="_method"]').value = 'POST';
        document.getElementById('idPais').value = '';
        document.getElementById('nomPais').value = '';
        document.getElementById('desPais').value = '';
        document.getElementById('nomePais').value = '';
        document.getElementById('estadoPais').value = '1';
    });

    // Limpiar formulario de búsqueda de país y recargar lista
    limpiarBusquedaPaisBtn.addEventListener('click', function () {
        inputBuscarPais.value = '';
        mensajeBusquedaPais.classList.add('hidden');
        fetch('/paises', {
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
            console.log('Respuesta del servidor para /paises:', data);
            tbodyPaises.innerHTML = '';
            mensajeBusquedaPais.classList.add('hidden');
            if (data.success && Array.isArray(data.paises) && data.paises.length > 0) {
                data.paises.forEach(pais => {
                    const tr = document.createElement('tr');
                    tr.className = 'border-t hover:bg-blue-50';
                    tr.innerHTML = `
                        <td class="px-4 py-3">${pais.idPais}</td>
                        <td class="px-4 py-3">${pais.nomPais}</td>
                        <td class="px-4 py-3">${pais.desPais || 'Sin descripción'}</td>
                        <td class="px-4 py-3">${pais.nomePais || 'Sin nomenclatura'}</td>
                        <td class="px-4 py-3">${pais.estadoPais ? 'Activo' : 'Inactivo'}</td>
                        <td class="px-4 py-3 flex space-x-2">
                            <button class="editar-pais bg-blue-500 text-white px-3 py-1 rounded-md hover:bg-blue-600 text-xs" data-pais='${JSON.stringify(pais)}'>Editar</button>
                            <form action="/paises/${pais.idPais}" method="POST" class="inline">
                                <input type="hidden" name="_token" value="${document.querySelector('input[name="_token"]').value}">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-600 text-xs" onclick="return confirm('¿Está seguro de eliminar este país?')">Eliminar</button>
                            </form>
                        </td>
                    `;
                    tbodyPaises.appendChild(tr);
                });
                asignarEventosEditar();
            } else {
                tbodyPaises.innerHTML = `
                    <tr>
                        <td colspan="6" class="px-4 py-3 text-center text-gray-500">No se encontraron países.</td>
                    </tr>
                `;
                mensajeBusquedaPais.textContent = data.message || 'No se encontraron países.';
                mensajeBusquedaPais.classList.remove('hidden');
                mensajeBusquedaPais.classList.add('text-gray-500');
            }
        })
        .catch(error => {
            console.error('Error al recargar países:', error);
            mensajeBusquedaPais.textContent = 'Error al recargar la lista de países: ' + error.message;
            mensajeBusquedaPais.classList.remove('hidden');
            mensajeBusquedaPais.classList.add('text-red-500');
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

    // Buscar país en tiempo real
    inputBuscarPais.addEventListener('input', debounce(function () {
        const termino = inputBuscarPais.value.trim();
        if (!termino) {
            limpiarBusquedaPaisBtn.click();
            return;
        }
        const url = `/paises/buscarPais?termino=${encodeURIComponent(termino)}`;
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
            console.log('Respuesta del servidor:', data);
            tbodyPaises.innerHTML = '';
            mensajeBusquedaPais.classList.add('hidden');
            if (data.success && Array.isArray(data.paises) && data.paises.length > 0) {
                data.paises.forEach(pais => {
                    const tr = document.createElement('tr');
                    tr.className = 'border-t hover:bg-blue-50';
                    tr.innerHTML = `
                        <td class="px-4 py-3">${pais.idPais}</td>
                        <td class="px-4 py-3">${pais.nomPais}</td>
                        <td class="px-4 py-3">${pais.desPais || 'Sin descripción'}</td>
                        <td class="px-4 py-3">${pais.nomePais || 'Sin nomenclatura'}</td>
                        <td class="px-4 py-3">${pais.estadoPais ? 'Activo' : 'Inactivo'}</td>
                        <td class="px-4 py-3 flex space-x-2">
                            <button class="editar-pais bg-blue-500 text-white px-3 py-1 rounded-md hover:bg-blue-600 text-xs" data-pais='${JSON.stringify(pais)}'>Editar</button>
                            <form action="/paises/${pais.idPais}" method="POST" class="inline">
                                <input type="hidden" name="_token" value="${document.querySelector('input[name="_token"]').value}">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-600 text-xs" onclick="return confirm('¿Está seguro de eliminar este país?')">Eliminar</button>
                            </form>
                        </td>
                    `;
                    tbodyPaises.appendChild(tr);
                });
                asignarEventosEditar();
            } else {
                tbodyPaises.innerHTML = `
                    <tr>
                        <td colspan="6" class="px-4 py-3 text-center text-gray-500">No se encontraron países.</td>
                    </tr>
                `;
                mensajeBusquedaPais.textContent = data.message || 'No se encontraron países.';
                mensajeBusquedaPais.classList.remove('hidden');
                mensajeBusquedaPais.classList.add('text-red-500');
            }
        })
        .catch(error => {
            console.error('Error en la búsqueda de países:', error);
            mensajeBusquedaPais.textContent = 'Error al buscar países: ' + error.message;
            mensajeBusquedaPais.classList.remove('hidden');
            mensajeBusquedaPais.classList.add('text-red-500');
            tbodyPaises.innerHTML = `
                <tr>
                    <td colspan="6" class="px-4 py-3 text-center text-gray-500">Error al buscar países.</td>
                </tr>
            `;
        });
    }, 300));
});