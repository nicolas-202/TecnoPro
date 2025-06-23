document.addEventListener('DOMContentLoaded', function () {
    const formularioDepartamento = document.getElementById('formularioDepartamento');
    const limpiarTodoBtn = document.getElementById('limpiarTodo');
    const inputBuscarDepartamento = document.getElementById('buscar_departamento');
    const mensajeBusquedaDepartamento = document.getElementById('mensajeBusquedaDepartamento');
    const tbodyDepartamentos = document.getElementById('tbodyDepartamentos');
    const limpiarBusquedaDepartamentoBtn = document.getElementById('limpiarBusquedaDepartamento');
    const selectPais = document.getElementById('idPais');

    // Función para asignar eventos a los botones de editar
    function asignarEventosEditar() {
        document.querySelectorAll('.editar-departamento').forEach(button => {
            console.log('Asignando evento al botón:', button);
            button.removeEventListener('click', editarDepartamentoHandler);
            button.addEventListener('click', editarDepartamentoHandler);
        });
    }

    // Handler para editar departamento
    function editarDepartamentoHandler() {
        try {
            const dataDepartamento = this.getAttribute('data-departamento').trim();
            console.log('Valor crudo de data-departamento:', dataDepartamento);
            console.log('Longitud de data-departamento:', dataDepartamento.length);
            const departamento = JSON.parse(dataDepartamento);
            console.log('Editando departamento:', departamento);
            formularioDepartamento.action = `/departamentos/${departamento.idDepartamento}`;
            formularioDepartamento.querySelector('input[name="_method"]').value = 'PUT';
            document.getElementById('idDepartamento').value = departamento.idDepartamento;
            document.getElementById('nomDepartamento').value = departamento.nomDepartamento || '';
            document.getElementById('desDepartamento').value = departamento.desDepartamento || '';
            document.getElementById('nomeDepartamento').value = departamento.nomeDepartamento || '';
            document.getElementById('estadoDepartamento').value = departamento.estadoDepartamento ? '1' : '0';
            document.getElementById('idPais').value = departamento.idPais || '';
        } catch (error) {
            console.error('Error al parsear data-departamento:', error);
            alert('Error al cargar los datos del departamento para edición: ' + error.message);
        }
    }

    // Inicializar eventos de editar al cargar la página
    asignarEventosEditar();

    // Limpiar formulario de departamento
    limpiarTodoBtn.addEventListener('click', function () {
        formularioDepartamento.reset();
        formularioDepartamento.action = "{{ route('departamentos.store') }}";
        formularioDepartamento.querySelector('input[name="_method"]').value = 'POST';
        document.getElementById('idDepartamento').value = '';
        document.getElementById('nomDepartamento').value = '';
        document.getElementById('desDepartamento').value = '';
        document.getElementById('nomeDepartamento').value = '';
        document.getElementById('estadoDepartamento').value = '1';
        document.getElementById('idPais').value = '';
    });

    // Limpiar formulario de búsqueda de departamento y recargar lista
    limpiarBusquedaDepartamentoBtn.addEventListener('click', function () {
        inputBuscarDepartamento.value = '';
        mensajeBusquedaDepartamento.classList.add('hidden');
        fetch('/departamentos', {
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
            console.log('Respuesta del servidor para /departamentos:', data);
            tbodyDepartamentos.innerHTML = '';
            mensajeBusquedaDepartamento.classList.add('hidden');
            if (data.success && Array.isArray(data.departamentos) && data.departamentos.length > 0) {
                data.departamentos.forEach(departamento => {
                    const tr = document.createElement('tr');
                    tr.className = 'border-t hover:bg-blue-50';
                    tr.innerHTML = `
                        <td class="px-4 py-3">${departamento.idDepartamento}</td>
                        <td class="px-4 py-3">${departamento.nomDepartamento}</td>
                        <td class="px-4 py-3">${departamento.desDepartamento || 'Sin descripción'}</td>
                        <td class="px-4 py-3">${departamento.nomeDepartamento || 'Sin nomenclatura'}</td>
                        <td class="px-4 py-3">${departamento.nomPais || 'Sin país'}</td>
                        <td class="px-4 py-3">${departamento.estadoDepartamento ? 'Activo' : 'Inactivo'}</td>
                        <td class="px-4 py-3 flex space-x-2">
                            <button class="editar-departamento bg-blue-500 text-white px-3 py-1 rounded-md hover:bg-blue-600 text-xs" data-departamento='${JSON.stringify(departamento)}'>Editar</button>
                            <form action="/departamentos/${departamento.idDepartamento}" method="POST" class="inline">
                                <input type="hidden" name="_token" value="${document.querySelector('input[name="_token"]').value}">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-600 text-xs" onclick="return confirm('¿Está seguro de eliminar este departamento?')">Eliminar</button>
                            </form>
                        </td>
                    `;
                    tbodyDepartamentos.appendChild(tr);
                });
                asignarEventosEditar();
            } else {
                tbodyDepartamentos.innerHTML = `
                    <tr>
                        <td colspan="7" class="px-4 py-3 text-center text-gray-500">No se encontraron departamentos.</td>
                    </tr>
                `;
                mensajeBusquedaDepartamento.textContent = data.message || 'No se encontraron departamentos.';
                mensajeBusquedaDepartamento.classList.remove('hidden');
                mensajeBusquedaDepartamento.classList.add('text-gray-500');
            }
        })
        .catch(error => {
            console.error('Error al recargar departamentos:', error);
            mensajeBusquedaDepartamento.textContent = 'Error al recargar la lista de departamentos: ' + error.message;
            mensajeBusquedaDepartamento.classList.remove('hidden');
            mensajeBusquedaDepartamento.classList.add('text-red-500');
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

    // Buscar departamento en tiempo real
    inputBuscarDepartamento.addEventListener('input', debounce(function () {
        const termino = inputBuscarDepartamento.value.trim();
        if (!termino) {
            limpiarBusquedaDepartamentoBtn.click();
            return;
        }
        const url = `/departamentos/buscarDepartamento?termino=${encodeURIComponent(termino)}`;
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
            tbodyDepartamentos.innerHTML = '';
            mensajeBusquedaDepartamento.classList.add('hidden');
            if (data.success && Array.isArray(data.departamentos) && data.departamentos.length > 0) {
                data.departamentos.forEach(departamento => {
                    const tr = document.createElement('tr');
                    tr.className = 'border-t hover:bg-blue-50';
                    tr.innerHTML = `
                        <td class="px-4 py-3">${departamento.idDepartamento}</td>
                        <td class="px-4 py-3">${departamento.nomDepartamento}</td>
                        <td class="px-4 py-3">${departamento.desDepartamento || 'Sin descripción'}</td>
                        <td class="px-4 py-3">${departamento.nomeDepartamento || 'Sin nomenclatura'}</td>
                        <td class="px-4 py-3">${departamento.nomPais || 'Sin país'}</td>
                        <td class="px-4 py-3">${departamento.estadoDepartamento ? 'Activo' : 'Inactivo'}</td>
                        <td class="px-4 py-3 flex space-x-2">
                            <button class="editar-departamento bg-blue-500 text-white px-3 py-1 rounded-md hover:bg-blue-600 text-xs" data-departamento='${JSON.stringify(departamento)}'>Editar</button>
                            <form action="/departamentos/${departamento.idDepartamento}" method="POST" class="inline">
                                <input type="hidden" name="_token" value="${document.querySelector('input[name="_token"]').value}">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-600 text-xs" onclick="return confirm('¿Está seguro de eliminar este departamento?')">Eliminar</button>
                            </form>
                        </td>
                    `;
                    tbodyDepartamentos.appendChild(tr);
                });
                asignarEventosEditar();
            } else {
                tbodyDepartamentos.innerHTML = `
                    <tr>
                        <td colspan="7" class="px-4 py-3 text-center text-gray-500">No se encontraron departamentos.</td>
                    </tr>
                `;
                mensajeBusquedaDepartamento.textContent = data.message || 'No se encontraron departamentos.';
                mensajeBusquedaDepartamento.classList.remove('hidden');
                mensajeBusquedaDepartamento.classList.add('text-red-500');
            }
        })
        .catch(error => {
            console.error('Error en la búsqueda de departamentos:', error);
            mensajeBusquedaDepartamento.textContent = 'Error al buscar departamentos: ' + error.message;
            mensajeBusquedaDepartamento.classList.remove('hidden');
            mensajeBusquedaDepartamento.classList.add('text-red-500');
            tbodyDepartamentos.innerHTML = `
                <tr>
                    <td colspan="7" class="px-4 py-3 text-center text-gray-500">Error al buscar departamentos.</td>
                </tr>
            `;
        });
    }, 300));
});