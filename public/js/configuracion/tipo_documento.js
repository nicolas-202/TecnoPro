document.addEventListener('DOMContentLoaded', function () {
    const formularioTipoDocumento = document.getElementById('formularioTipoDocumento');
    const limpiarTodoBtn = document.getElementById('limpiarTodo');
    const inputBuscarTipoDocumento = document.getElementById('buscar_tipo_documento');
    const mensajeBusquedaTipoDocumento = document.getElementById('mensajeBusquedaTipoDocumento');
    const tbodyTiposDocumento = document.getElementById('tbodyTiposDocumento');
    const limpiarBusquedaTipoDocumentoBtn = document.getElementById('limpiarBusquedaTipoDocumento');

    // Función para asignar eventos a los botones de editar
    function asignarEventosEditar() {
        document.querySelectorAll('.editar-tipo-documento').forEach(button => {
            console.log('Asignando evento al botón:', button);
            button.removeEventListener('click', editarTipoDocumentoHandler);
            button.addEventListener('click', editarTipoDocumentoHandler);
        });
    }

    // Handler para editar tipo de documento
    function editarTipoDocumentoHandler() {
        try {
            const dataTipoDocumento = this.getAttribute('data-tipo-documento').trim();
            console.log('Valor crudo de data-tipo-documento:', dataTipoDocumento);
            console.log('Longitud de data-tipo-documento:', dataTipoDocumento.length);
            const tipoDocumento = JSON.parse(dataTipoDocumento);
            console.log('Editando tipo de documento:', tipoDocumento);
            formularioTipoDocumento.action = `/tipos_documento/${tipoDocumento.idTipoDocumento}`;
            formularioTipoDocumento.querySelector('input[name="_method"]').value = 'PUT';
            document.getElementById('idTipoDocumento').value = tipoDocumento.idTipoDocumento;
            document.getElementById('nomTipoDocumento').value = tipoDocumento.nomTipoDocumento || '';
            document.getElementById('desTipoDocumento').value = tipoDocumento.desTipoDocumento || '';
            document.getElementById('nomeTipoDocumento').value = tipoDocumento.nomeTipoDocumento || '';
            document.getElementById('estadoTipoDocumento').value = tipoDocumento.estadoTipoDocumento ? '1' : '0';
        } catch (error) {
            console.error('Error al parsear data-tipo-documento:', error);
            alert('Error al cargar los datos del tipo de documento para edición: ' + error.message);
        }
    }

    // Inicializar eventos de editar al cargar la página
    asignarEventosEditar();

    // Limpiar formulario de tipo de documento
    limpiarTodoBtn.addEventListener('click', function () {
        formularioTipoDocumento.reset();
        formularioTipoDocumento.action = "{{ route('tipos_documento.store') }}";
        formularioTipoDocumento.querySelector('input[name="_method"]').value = 'POST';
        document.getElementById('idTipoDocumento').value = '';
        document.getElementById('nomTipoDocumento').value = '';
        document.getElementById('desTipoDocumento').value = '';
        document.getElementById('nomeTipoDocumento').value = '';
        document.getElementById('estadoTipoDocumento').value = '1';
    });

    // Limpiar formulario de búsqueda y recargar lista
    limpiarBusquedaTipoDocumentoBtn.addEventListener('click', function () {
        inputBuscarTipoDocumento.value = '';
        mensajeBusquedaTipoDocumento.classList.add('hidden');
        fetch('/tipos_documento', {
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
            console.log('Respuesta del servidor para /tipos_documento:', data);
            tbodyTiposDocumento.innerHTML = '';
            mensajeBusquedaTipoDocumento.classList.add('hidden');
            if (data.success && Array.isArray(data.tiposDocumento) && data.tiposDocumento.length > 0) {
                data.tiposDocumento.forEach(tipoDocumento => {
                    const tr = document.createElement('tr');
                    tr.className = 'border-t hover:bg-blue-50';
                    tr.innerHTML = `
                        <td class="px-4 py-3">${tipoDocumento.idTipoDocumento}</td>
                        <td class="px-4 py-3">${tipoDocumento.nomTipoDocumento}</td>
                        <td class="px-4 py-3">${tipoDocumento.desTipoDocumento || 'Sin descripción'}</td>
                        <td class="px-4 py-3">${tipoDocumento.nomeTipoDocumento || 'Sin nomenclatura'}</td>
                        <td class="px-4 py-3">${tipoDocumento.estadoTipoDocumento ? 'Activo' : 'Inactivo'}</td>
                        <td class="px-4 py-3 flex space-x-2">
                            <button class="editar-tipo-documento bg-blue-500 text-white px-3 py-1 rounded-md hover:bg-blue-600 text-xs" data-tipo-documento='${JSON.stringify(tipoDocumento)}'>Editar</button>
                            <form action="/tipos_documento/${tipoDocumento.idTipoDocumento}" method="POST" class="inline">
                                <input type="hidden" name="_token" value="${document.querySelector('input[name="_token"]').value}">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-600 text-xs" onclick="return confirm('¿Está seguro de eliminar este tipo de documento?')">Eliminar</button>
                            </form>
                        </td>
                    `;
                    tbodyTiposDocumento.appendChild(tr);
                });
                asignarEventosEditar();
            } else {
                tbodyTiposDocumento.innerHTML = `
                    <tr>
                        <td colspan="6" class="px-4 py-3 text-center text-gray-500">No se encontraron tipos de documento.</td>
                    </tr>
                `;
                mensajeBusquedaTipoDocumento.textContent = data.message || 'No se encontraron tipos de documento.';
                mensajeBusquedaTipoDocumento.classList.remove('hidden');
                mensajeBusquedaTipoDocumento.classList.add('text-gray-500');
            }
        })
        .catch(error => {
            console.error('Error al recargar tipos de documento:', error);
            mensajeBusquedaTipoDocumento.textContent = 'Error al recargar la lista de tipos de documento: ' + error.message;
            mensajeBusquedaTipoDocumento.classList.remove('hidden');
            mensajeBusquedaTipoDocumento.classList.add('text-red-500');
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

    // Buscar tipo de documento en tiempo real
    inputBuscarTipoDocumento.addEventListener('input', debounce(function () {
        const termino = inputBuscarTipoDocumento.value.trim();
        if (!termino) {
            limpiarBusquedaTipoDocumentoBtn.click();
            return;
        }
        const url = `/tipos_documento/buscarTipoDocumento?termino=${encodeURIComponent(termino)}`;
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
            tbodyTiposDocumento.innerHTML = '';
            mensajeBusquedaTipoDocumento.classList.add('hidden');
            if (data.success && Array.isArray(data.tiposDocumento) && data.tiposDocumento.length > 0) {
                data.tiposDocumento.forEach(tipoDocumento => {
                    const tr = document.createElement('tr');
                    tr.className = 'border-t hover:bg-blue-50';
                    tr.innerHTML = `
                        <td class="px-4 py-3">${tipoDocumento.idTipoDocumento}</td>
                        <td class="px-4 py-3">${tipoDocumento.nomTipoDocumento}</td>
                        <td class="px-4 py-3">${tipoDocumento.desTipoDocumento || 'Sin descripción'}</td>
                        <td class="px-4 py-3">${tipoDocumento.nomeTipoDocumento || 'Sin nomenclatura'}</td>
                        <td class="px-4 py-3">${tipoDocumento.estadoTipoDocumento ? 'Activo' : 'Inactivo'}</td>
                        <td class="px-4 py-3 flex space-x-2">
                            <button class="editar-tipo-documento bg-blue-500 text-white px-3 py-1 rounded-md hover:bg-blue-600 text-xs" data-tipo-documento='${JSON.stringify(tipoDocumento)}'>Editar</button>
                            <form action="/tipos_documento/${tipoDocumento.idTipoDocumento}" method="POST" class="inline">
                                <input type="hidden" name="_token" value="${document.querySelector('input[name="_token"]').value}">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-600 text-xs" onclick="return confirm('¿Está seguro de eliminar este tipo de documento?')">Eliminar</button>
                            </form>
                        </td>
                    `;
                    tbodyTiposDocumento.appendChild(tr);
                });
                asignarEventosEditar();
            } else {
                tbodyTiposDocumento.innerHTML = `
                    <tr>
                        <td colspan="6" class="px-4 py-3 text-center text-gray-500">No se encontraron tipos de documento.</td>
                    </tr>
                `;
                mensajeBusquedaTipoDocumento.textContent = data.message || 'No se encontraron tipos de documento.';
                mensajeBusquedaTipoDocumento.classList.remove('hidden');
                mensajeBusquedaTipoDocumento.classList.add('text-red-500');
            }
        })
        .catch(error => {
            console.error('Error en la búsqueda de tipos de documento:', error);
            mensajeBusquedaTipoDocumento.textContent = 'Error al buscar tipos de documento: ' + error.message;
            mensajeBusquedaTipoDocumento.classList.remove('hidden');
            mensajeBusquedaTipoDocumento.classList.add('text-red-500');
            tbodyTiposDocumento.innerHTML = `
                <tr>
                    <td colspan="6" class="px-4 py-3 text-center text-gray-500">Error al buscar tipos de documento.</td>
                </tr>
            `;
        });
    }, 300));
});