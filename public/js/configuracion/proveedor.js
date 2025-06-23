document.addEventListener('DOMContentLoaded', function () {
    const formularioProveedor = document.getElementById('formularioProveedor');
    const limpiarTodoBtn = document.getElementById('limpiarTodo');
    const inputBuscarProveedor = document.getElementById('buscar_proveedor');
    const mensajeBusquedaProveedor = document.getElementById('mensajeBusquedaProveedor');
    const tbodyProveedores = document.getElementById('tbodyProveedores');
    const limpiarBusquedaProveedorBtn = document.getElementById('limpiarBusquedaProveedor');

    // Función para asignar eventos a los botones de editar
    function asignarEventosEditar() {
        document.querySelectorAll('.editar-proveedor').forEach(button => {
            console.log('Asignando evento al botón:', button);
            button.removeEventListener('click', editarProveedorHandler);
            button.addEventListener('click', editarProveedorHandler);
        });
    }

    // Handler para editar proveedor
    function editarProveedorHandler() {
        try {
            const dataProveedor = this.getAttribute('data-proveedor').trim();
            console.log('Valor crudo de data-proveedor:', dataProveedor);
            console.log('Longitud de data-proveedor:', dataProveedor.length);
            const proveedor = JSON.parse(dataProveedor);
            console.log('Editando proveedor:', proveedor);
            formularioProveedor.action = `/proveedores/${proveedor.idProveedor}`;
            formularioProveedor.querySelector('input[name="_method"]').value = 'PUT';
            document.getElementById('idProveedor').value = proveedor.idProveedor;
            document.getElementById('nomProveedor').value = proveedor.nomProveedor || '';
            document.getElementById('desProveedor').value = proveedor.desProveedor || '';
            document.getElementById('telProveedor').value = proveedor.telProveedor || '';
            document.getElementById('emailProveedor').value = proveedor.emailProveedor || '';
            document.getElementById('nitProveedor').value = proveedor.nitProveedor || '';
            document.getElementById('estadoProveedor').value = proveedor.estadoProveedor ? '1' : '0';
        } catch (error) {
            console.error('Error al parsear data-proveedor:', error);
            alert('Error al cargar los datos del proveedor para edición: ' + error.message);
        }
    }

    // Inicializar eventos de editar al cargar la página
    asignarEventosEditar();

    // Limpiar formulario de proveedor
    limpiarTodoBtn.addEventListener('click', function () {
        formularioProveedor.reset();
        formularioProveedor.action = "{{ route('proveedores.store') }}";
        formularioProveedor.querySelector('input[name="_method"]').value = 'POST';
        document.getElementById('idProveedor').value = '';
        document.getElementById('nomProveedor').value = '';
        document.getElementById('desProveedor').value = '';
        document.getElementById('telProveedor').value = '';
        document.getElementById('emailProveedor').value = '';
        document.getElementById('nitProveedor').value = '';
        document.getElementById('estadoProveedor').value = '1';
    });

    // Limpiar formulario de búsqueda de proveedor y recargar lista
    limpiarBusquedaProveedorBtn.addEventListener('click', function () {
        inputBuscarProveedor.value = '';
        mensajeBusquedaProveedor.classList.add('hidden');
        fetch('/proveedores', {
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
            console.log('Respuesta del servidor para /proveedores:', data);
            tbodyProveedores.innerHTML = '';
            mensajeBusquedaProveedor.classList.add('hidden');
            if (data.success && Array.isArray(data.proveedores) && data.proveedores.length > 0) {
                data.proveedores.forEach(proveedor => {
                    const tr = document.createElement('tr');
                    tr.className = 'border-t hover:bg-blue-50';
                    tr.innerHTML = `
                        <td class="px-4 py-3">${proveedor.idProveedor}</td>
                        <td class="px-4 py-3">${proveedor.nomProveedor}</td>
                        <td class="px-4 py-3">${proveedor.desProveedor || 'Sin descripción'}</td>
                        <td class="px-4 py-3">${proveedor.telProveedor || 'Sin teléfono'}</td>
                        <td class="px-4 py-3">${proveedor.emailProveedor || 'Sin email'}</td>
                        <td class="px-4 py-3">${proveedor.nitProveedor || 'Sin NIT'}</td>
                        <td class="px-4 py-3">${proveedor.estadoProveedor ? 'Activo' : 'Inactivo'}</td>
                        <td class="px-4 py-3 flex space-x-2">
                            <button class="editar-proveedor bg-blue-500 text-white px-3 py-1 rounded-md hover:bg-blue-600 text-xs" data-proveedor='${JSON.stringify(proveedor)}'>Editar</button>
                            <form action="/proveedores/${proveedor.idProveedor}" method="POST" class="inline">
                                <input type="hidden" name="_token" value="${document.querySelector('input[name="_token"]').value}">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-600 text-xs" onclick="return confirm('¿Está seguro de eliminar este proveedor?')">Eliminar</button>
                            </form>
                        </td>
                    `;
                    tbodyProveedores.appendChild(tr);
                });
                asignarEventosEditar();
            } else {
                tbodyProveedores.innerHTML = `
                    <tr>
                        <td colspan="8" class="px-4 py-3 text-center text-gray-500">No se encontraron proveedores.</td>
                    </tr>
                `;
                mensajeBusquedaProveedor.textContent = data.message || 'No se encontraron proveedores.';
                mensajeBusquedaProveedor.classList.remove('hidden');
                mensajeBusquedaProveedor.classList.add('text-gray-500');
            }
        })
        .catch(error => {
            console.error('Error al recargar proveedores:', error);
            mensajeBusquedaProveedor.textContent = 'Error al recargar la lista de proveedores: ' + error.message;
            mensajeBusquedaProveedor.classList.remove('hidden');
            mensajeBusquedaProveedor.classList.add('text-red-500');
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

    // Buscar proveedor en tiempo real
    inputBuscarProveedor.addEventListener('input', debounce(function () {
        const termino = inputBuscarProveedor.value.trim();
        if (!termino) {
            limpiarBusquedaProveedorBtn.click();
            return;
        }
        const url = `/proveedores/buscarProveedor?termino=${encodeURIComponent(termino)}`;
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
            tbodyProveedores.innerHTML = '';
            mensajeBusquedaProveedor.classList.add('hidden');
            if (data.success && Array.isArray(data.proveedores) && data.proveedores.length > 0) {
                data.proveedores.forEach(proveedor => {
                    const tr = document.createElement('tr');
                    tr.className = 'border-t hover:bg-blue-50';
                    tr.innerHTML = `
                        <td class="px-4 py-3">${proveedor.idProveedor}</td>
                        <td class="px-4 py-3">${proveedor.nomProveedor}</td>
                        <td class="px-4 py-3">${proveedor.desProveedor || 'Sin descripción'}</td>
                        <td class="px-4 py-3">${proveedor.telProveedor || 'Sin teléfono'}</td>
                        <td class="px-4 py-3">${proveedor.emailProveedor || 'Sin email'}</td>
                        <td class="px-4 py-3">${proveedor.nitProveedor || 'Sin NIT'}</td>
                        <td class="px-4 py-3">${proveedor.estadoProveedor ? 'Activo' : 'Inactivo'}</td>
                        <td class="px-4 py-3 flex space-x-2">
                            <button class="editar-proveedor bg-blue-500 text-white px-3 py-1 rounded-md hover:bg-blue-600 text-xs" data-proveedor='${JSON.stringify(proveedor)}'>Editar</button>
                            <form action="/proveedores/${proveedor.idProveedor}" method="POST" class="inline">
                                <input type="hidden" name="_token" value="${document.querySelector('input[name="_token"]').value}">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-600 text-xs" onclick="return confirm('¿Está seguro de eliminar este proveedor?')">Eliminar</button>
                            </form>
                        </td>
                    `;
                    tbodyProveedores.appendChild(tr);
                });
                asignarEventosEditar();
            } else {
                tbodyProveedores.innerHTML = `
                    <tr>
                        <td colspan="8" class="px-4 py-3 text-center text-gray-500">No se encontraron proveedores.</td>
                    </tr>
                `;
                mensajeBusquedaProveedor.textContent = data.message || 'No se encontraron proveedores.';
                mensajeBusquedaProveedor.classList.remove('hidden');
                mensajeBusquedaProveedor.classList.add('text-red-500');
            }
        })
        .catch(error => {
            console.error('Error en la búsqueda de proveedores:', error);
            mensajeBusquedaProveedor.textContent = 'Error al buscar proveedores: ' + error.message;
            mensajeBusquedaProveedor.classList.remove('hidden');
            mensajeBusquedaProveedor.classList.add('text-red-500');
            tbodyProveedores.innerHTML = `
                <tr>
                    <td colspan="8" class="px-4 py-3 text-center text-gray-500">Error al buscar proveedores.</td>
                </tr>
            `;
        });
    }, 300));
});