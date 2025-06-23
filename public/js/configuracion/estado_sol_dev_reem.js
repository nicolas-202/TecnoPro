document.addEventListener('DOMContentLoaded', function () {
    const formularioEstado = document.getElementById('formularioEstado');
    const limpiarTodoBtn = document.getElementById('limpiarTodo');
    const inputBuscarEstado = document.getElementById('buscar_estado');
    const mensajeBusquedaEstado = document.getElementById('mensajeBusquedaEstado');
    const tbodyEstados = document.getElementById('tbodyEstados');
    const limpiarBusquedaEstadoBtn = document.getElementById('limpiarBusquedaEstado');

    // Función para asignar eventos a los botones de editar
    function asignarEventosEditar() {
        document.querySelectorAll('.editar-estado').forEach(button => {
            console.log('Asignando evento al botón:', button);
            button.removeEventListener('click', editarEstadoHandler);
            button.addEventListener('click', editarEstadoHandler);
        });
    }

    // Handler para editar estado
    function editarEstadoHandler() {
        try {
            const dataEstado = this.getAttribute('data-estado').trim();
            console.log('Valor crudo de data-estado:', dataEstado);
            console.log('Longitud de data-estado:', dataEstado.length);
            const estado = JSON.parse(dataEstado);
            console.log('Editando estado:', estado);
            formularioEstado.action = `/estados_solicitud/${estado.idEstadoSolDevReem}`;
            formularioEstado.querySelector('input[name="_method"]').value = 'PUT';
            document.getElementById('idEstadoSolDevReem').value = estado.idEstadoSolDevReem;
            document.getElementById('nomEstadoSolDevReem').value = estado.nomEstadoSolDevReem || '';
            document.getElementById('desEstadoSolDevReem').value = estado.desEstadoSolDevReem || '';
            document.getElementById('nomeEstadoSolDevReem').value = estado.nomeEstadoSolDevReem || '';
            document.getElementById('estadoEstadoSolDevReem').value = estado.estadoEstadoSolDevReem ? '1' : '0';
        } catch (error) {
            console.error('Error al parsear data-estado:', error);
            alert('Error al cargar los datos del estado para edición: ' + error.message);
        }
    }

    // Inicializar eventos de editar al cargar la página
    asignarEventosEditar();

    // Limpiar formulario de estado
    limpiarTodoBtn.addEventListener('click', function () {
        formularioEstado.reset();
        formularioEstado.action = "{{ route('estados_solicitud.store') }}";
        formularioEstado.querySelector('input[name="_method"]').value = 'POST';
        document.getElementById('idEstadoSolDevReem').value = '';
        document.getElementById('nomEstadoSolDevReem').value = '';
        document.getElementById('desEstadoSolDevReem').value = '';
        document.getElementById('nomeEstadoSolDevReem').value = '';
        document.getElementById('estadoEstadoSolDevReem').value = '1';
    });

    // Limpiar formulario de búsqueda de estado y recargar lista
    limpiarBusquedaEstadoBtn.addEventListener('click', function () {
        inputBuscarEstado.value = '';
        mensajeBusquedaEstado.classList.add('hidden');
        fetch('/estados_solicitud', {
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
            console.log('Respuesta del servidor para /estados_solicitud:', data);
            tbodyEstados.innerHTML = '';
            mensajeBusquedaEstado.classList.add('hidden');
            if (data.success && Array.isArray(data.estados) && data.estados.length > 0) {
                data.estados.forEach(estado => {
                    const tr = document.createElement('tr');
                    tr.className = 'border-t hover:bg-blue-50';
                    tr.innerHTML = `
                        <td class="px-4 py-3">${estado.idEstadoSolDevReem}</td>
                        <td class="px-4 py-3">${estado.nomEstadoSolDevReem}</td>
                        <td class="px-4 py-3">${estado.desEstadoSolDevReem || 'Sin descripción'}</td>
                        <td class="px-4 py-3">${estado.nomeEstadoSolDevReem || 'Sin nomenclatura'}</td>
                        <td class="px-4 py-3">${estado.estadoEstadoSolDevReem ? 'Activo' : 'Inactivo'}</td>
                        <td class="px-4 py-3 flex space-x-2">
                            <button class="editar-estado bg-blue-500 text-white px-3 py-1 rounded-md hover:bg-blue-600 text-xs" data-estado='${JSON.stringify(estado)}'>Editar</button>
                            <form action="/estados_solicitud/${estado.idEstadoSolDevReem}" method="POST" class="inline">
                                <input type="hidden" name="_token" value="${document.querySelector('input[name="_token"]').value}">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-600 text-xs" onclick="return confirm('¿Está seguro de eliminar este estado?')">Eliminar</button>
                            </form>
                        </td>
                    `;
                    tbodyEstados.appendChild(tr);
                });
                asignarEventosEditar();
            } else {
                tbodyEstados.innerHTML = `
                    <tr>
                        <td colspan="6" class="px-4 py-3 text-center text-gray-500">No se encontraron estados.</td>
                    </tr>
                `;
                mensajeBusquedaEstado.textContent = data.message || 'No se encontraron estados.';
                mensajeBusquedaEstado.classList.remove('hidden');
                mensajeBusquedaEstado.classList.add('text-gray-500');
            }
        })
        .catch(error => {
            console.error('Error al recargar estados:', error);
            mensajeBusquedaEstado.textContent = 'Error al recargar la lista de estados: ' + error.message;
            mensajeBusquedaEstado.classList.remove('hidden');
            mensajeBusquedaEstado.classList.add('text-red-500');
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

    // Buscar estado en tiempo real
    inputBuscarEstado.addEventListener('input', debounce(function () {
        const termino = inputBuscarEstado.value.trim();
        if (!termino) {
            limpiarBusquedaEstadoBtn.click();
            return;
        }
        const url = `/estados_solicitud/buscarEstado?termino=${encodeURIComponent(termino)}`;
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
            tbodyEstados.innerHTML = '';
            mensajeBusquedaEstado.classList.add('hidden');
            if (data.success && Array.isArray(data.estados) && data.estados.length > 0) {
                data.estados.forEach(estado => {
                    const tr = document.createElement('tr');
                    tr.className = 'border-t hover:bg-blue-50';
                    tr.innerHTML = `
                        <td class="px-4 py-3">${estado.idEstadoSolDevReem}</td>
                        <td class="px-4 py-3">${estado.nomEstadoSolDevReem}</td>
                        <td class="px-4 py-3">${estado.desEstadoSolDevReem || 'Sin descripción'}</td>
                        <td class="px-4 py-3">${estado.nomeEstadoSolDevReem || 'Sin nomenclatura'}</td>
                        <td class="px-4 py-3">${estado.estadoEstadoSolDevReem ? 'Activo' : 'Inactivo'}</td>
                        <td class="px-4 py-3 flex space-x-2">
                            <button class="editar-estado bg-blue-500 text-white px-3 py-1 rounded-md hover:bg-blue-600 text-xs" data-estado='${JSON.stringify(estado)}'>Editar</button>
                            <form action="/estados_solicitud/${estado.idEstadoSolDevReem}" method="POST" class="inline">
                                <input type="hidden" name="_token" value="${document.querySelector('input[name="_token"]').value}">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-600 text-xs" onclick="return confirm('¿Está seguro de eliminar este estado?')">Eliminar</button>
                            </form>
                        </td>
                    `;
                    tbodyEstados.appendChild(tr);
                });
                asignarEventosEditar();
            } else {
                tbodyEstados.innerHTML = `
                    <tr>
                        <td colspan="6" class="px-4 py-3 text-center text-gray-500">No se encontraron estados.</td>
                    </tr>
                `;
                mensajeBusquedaEstado.textContent = data.message || 'No se encontraron estados.';
                mensajeBusquedaEstado.classList.remove('hidden');
                mensajeBusquedaEstado.classList.add('text-red-500');
            }
        })
        .catch(error => {
            console.error('Error en la búsqueda de estados:', error);
            mensajeBusquedaEstado.textContent = 'Error al buscar estados: ' + error.message;
            mensajeBusquedaEstado.classList.remove('hidden');
            mensajeBusquedaEstado.classList.add('text-red-500');
            tbodyEstados.innerHTML = `
                <tr>
                    <td colspan="6" class="px-4 py-3 text-center text-gray-500">Error al buscar estados.</td>
                </tr>
            `;
        });
    }, 300));
});