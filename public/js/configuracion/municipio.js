document.addEventListener('DOMContentLoaded', function () {
    const formularioMunicipio = document.getElementById('formularioMunicipio');
    const limpiarTodoBtn = document.getElementById('limpiarTodo');
    const inputBuscarMunicipio = document.getElementById('buscar_municipio');
    const mensajeBusquedaMunicipio = document.getElementById('mensajeBusquedaMunicipio');
    const tbodyMunicipios = document.getElementById('tbodyMunicipios');
    const limpiarBusquedaMunicipioBtn = document.getElementById('limpiarBusquedaMunicipio');
    const selectDepartamento = document.getElementById('idDepartamento');

    // Función para asignar eventos a los botones de editar
    function asignarEventosEditar() {
        document.querySelectorAll('.editar-municipio').forEach(button => {
            console.log('Asignando evento al botón:', button);
            button.removeEventListener('click', editarMunicipioHandler);
            button.addEventListener('click', editarMunicipioHandler);
        });
    }

    // Handler para editar municipio
    function editarMunicipioHandler() {
        try {
            const dataMunicipio = this.getAttribute('data-municipio').trim();
            console.log('Valor crudo de data-municipio:', dataMunicipio);
            console.log('Longitud de data-municipio:', dataMunicipio.length);
            const municipio = JSON.parse(dataMunicipio);
            console.log('Editando municipio:', municipio);
            formularioMunicipio.action = `/municipios/${municipio.idMunicipio}`;
            formularioMunicipio.querySelector('input[name="_method"]').value = 'PUT';
            document.getElementById('idMunicipio').value = municipio.idMunicipio;
            document.getElementById('nomMunicipio').value = municipio.nomMunicipio || '';
            document.getElementById('desMunicipio').value = municipio.desMunicipio || '';
            document.getElementById('nomeMunicipio').value = municipio.nomeMunicipio || '';
            document.getElementById('estadoMunicipio').value = municipio.estadoMunicipio ? '1' : '0';
            document.getElementById('idDepartamento').value = municipio.idDepartamento || '';
        } catch (error) {
            console.error('Error al parsear data-municipio:', error);
            alert('Error al cargar los datos del municipio para edición: ' + error.message);
        }
    }

    // Inicializar eventos de editar al cargar la página
    asignarEventosEditar();

    // Limpiar formulario de municipio
    limpiarTodoBtn.addEventListener('click', function () {
        formularioMunicipio.reset();
        formularioMunicipio.action = "{{ route('municipios.store') }}";
        formularioMunicipio.querySelector('input[name="_method"]').value = 'POST';
        document.getElementById('idMunicipio').value = '';
        document.getElementById('nomMunicipio').value = '';
        document.getElementById('desMunicipio').value = '';
        document.getElementById('nomeMunicipio').value = '';
        document.getElementById('estadoMunicipio').value = '1';
        document.getElementById('idDepartamento').value = '';
    });

    // Limpiar formulario de búsqueda de municipio y recargar lista
    limpiarBusquedaMunicipioBtn.addEventListener('click', function () {
        inputBuscarMunicipio.value = '';
        mensajeBusquedaMunicipio.classList.add('hidden');
        fetch('/municipios', {
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
            console.log('Respuesta del servidor para /municipios:', data);
            tbodyMunicipios.innerHTML = '';
            mensajeBusquedaMunicipio.classList.add('hidden');
            if (data.success && Array.isArray(data.municipios) && data.municipios.length > 0) {
                data.municipios.forEach(municipio => {
                    const tr = document.createElement('tr');
                    tr.className = 'border-t hover:bg-blue-50';
                    tr.innerHTML = `
                        <td class="px-4 py-3">${municipio.idMunicipio}</td>
                        <td class="px-4 py-3">${municipio.nomMunicipio}</td>
                        <td class="px-4 py-3">${municipio.desMunicipio || 'Sin descripción'}</td>
                        <td class="px-4 py-3">${municipio.nomeMunicipio || 'Sin nomenclatura'}</td>
                        <td class="px-4 py-3">${municipio.nomDepartamento || 'Sin departamento'}</td>
                        <td class="px-4 py-3">${municipio.estadoMunicipio ? 'Activo' : 'Inactivo'}</td>
                        <td class="px-4 py-3 flex space-x-2">
                            <button class="editar-municipio bg-blue-500 text-white px-3 py-1 rounded-md hover:bg-blue-600 text-xs" data-municipio='${JSON.stringify(municipio)}'>Editar</button>
                            <form action="/municipios/${municipio.idMunicipio}" method="POST" class="inline">
                                <input type="hidden" name="_token" value="${document.querySelector('input[name="_token"]').value}">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-600 text-xs" onclick="return confirm('¿Está seguro de eliminar este municipio?')">Eliminar</button>
                            </form>
                        </td>
                    `;
                    tbodyMunicipios.appendChild(tr);
                });
                asignarEventosEditar();
            } else {
                tbodyMunicipios.innerHTML = `
                    <tr>
                        <td colspan="7" class="px-4 py-3 text-center text-gray-500">No se encontraron municipios.</td>
                    </tr>
                `;
                mensajeBusquedaMunicipio.textContent = data.message || 'No se encontraron municipios.';
                mensajeBusquedaMunicipio.classList.remove('hidden');
                mensajeBusquedaMunicipio.classList.add('text-gray-500');
            }
        })
        .catch(error => {
            console.error('Error al recargar municipios:', error);
            mensajeBusquedaMunicipio.textContent = 'Error al recargar la lista de municipios: ' + error.message;
            mensajeBusquedaMunicipio.classList.remove('hidden');
            mensajeBusquedaMunicipio.classList.add('text-red-500');
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

    // Buscar municipio en tiempo real
    inputBuscarMunicipio.addEventListener('input', debounce(function () {
        const termino = inputBuscarMunicipio.value.trim();
        if (!termino) {
            limpiarBusquedaMunicipioBtn.click();
            return;
        }
        const url = `/municipios/buscarMunicipio?termino=${encodeURIComponent(termino)}`;
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
            tbodyMunicipios.innerHTML = '';
            mensajeBusquedaMunicipio.classList.add('hidden');
            if (data.success && Array.isArray(data.municipios) && data.municipios.length > 0) {
                data.municipios.forEach(municipio => {
                    const tr = document.createElement('tr');
                    tr.className = 'border-t hover:bg-blue-50';
                    tr.innerHTML = `
                        <td class="px-4 py-3">${municipio.idMunicipio}</td>
                        <td class="px-4 py-3">${municipio.nomMunicipio}</td>
                        <td class="px-4 py-3">${municipio.desMunicipio || 'Sin descripción'}</td>
                        <td class="px-4 py-3">${municipio.nomeMunicipio || 'Sin nomenclatura'}</td>
                        <td class="px-4 py-3">${municipio.nomDepartamento || 'Sin departamento'}</td>
                        <td class="px-4 py-3">${municipio.estadoMunicipio ? 'Activo' : 'Inactivo'}</td>
                        <td class="px-4 py-3 flex space-x-2">
                            <button class="editar-municipio bg-blue-500 text-white px-3 py-1 rounded-md hover:bg-blue-600 text-xs" data-municipio='${JSON.stringify(municipio)}'>Editar</button>
                            <form action="/municipios/${municipio.idMunicipio}" method="POST" class="inline">
                                <input type="hidden" name="_token" value="${document.querySelector('input[name="_token"]').value}">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-600 text-xs" onclick="return confirm('¿Está seguro de eliminar este municipio?')">Eliminar</button>
                            </form>
                        </td>
                    `;
                    tbodyMunicipios.appendChild(tr);
                });
                asignarEventosEditar();
            } else {
                tbodyMunicipios.innerHTML = `
                    <tr>
                        <td colspan="7" class="px-4 py-3 text-center text-gray-500">No se encontraron municipios.</td>
                    </tr>
                `;
                mensajeBusquedaMunicipio.textContent = data.message || 'No se encontraron municipios.';
                mensajeBusquedaMunicipio.classList.remove('hidden');
                mensajeBusquedaMunicipio.classList.add('text-red-500');
            }
        })
        .catch(error => {
            console.error('Error en la búsqueda de municipios:', error);
            mensajeBusquedaMunicipio.textContent = 'Error al buscar municipios: ' + error.message;
            mensajeBusquedaMunicipio.classList.remove('hidden');
            mensajeBusquedaMunicipio.classList.add('text-red-500');
            tbodyMunicipios.innerHTML = `
                <tr>
                    <td colspan="7" class="px-4 py-3 text-center text-gray-500">Error al buscar municipios.</td>
                </tr>
            `;
        });
    }, 300));
});