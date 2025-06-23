
document.addEventListener('DOMContentLoaded', function () {
    const formularioFormaPago = document.getElementById('formularioFormaPago');
    const limpiarTodoBtn = document.getElementById('limpiarTodo');
    const inputBuscarFormaPago = document.getElementById('buscar_forma_pago');
    const mensajeBusquedaFormaPago = document.getElementById('mensajeBusquedaFormaPago');
    const tbodyFormasPago = document.getElementById('tbodyFormasPago');
    const limpiarBusquedaFormaPagoBtn = document.getElementById('limpiarBusquedaFormaPago');

    // Función para asignar eventos a los botones de editar
    function asignarEventosEditar() {
        document.querySelectorAll('.editar-forma-pago').forEach(button => {
            console.log('Asignando evento al botón:', button);
            button.removeEventListener('click', editarFormaPagoHandler);
            button.addEventListener('click', editarFormaPagoHandler);
        });
    }

    // Handler para editar forma de pago
    function editarFormaPagoHandler() {
        try {
            const dataFormaPago = this.getAttribute('data-forma-pago').trim();
            console.log('Valor crudo de data-forma-pago:', dataFormaPago);
            console.log('Longitud de data-forma-pago:', dataFormaPago.length);
            const formaPago = JSON.parse(dataFormaPago);
            console.log('Editando forma de pago:', formaPago);
            formularioFormaPago.action = `/formas-pago/${formaPago.idFormaPago}`;
            formularioFormaPago.querySelector('input[name="_method"]').value = 'PUT';
            document.getElementById('idFormaPago').value = formaPago.idFormaPago;
            document.getElementById('nomFormaPago').value = formaPago.nomFormaPago || '';
            document.getElementById('desFormaPago').value = formaPago.desFormaPago || '';
            document.getElementById('nomeFormaPago').value = formaPago.nomeFormaPago || '';
            document.getElementById('estadoFormaPago').value = formaPago.estadoFormaPago ? '1' : '0';
        } catch (error) {
            console.error('Error al parsear data-forma-pago:', error);
            alert('Error al cargar los datos de la forma de pago para edición: ' + error.message);
        }
    }

    // Inicializar eventos de editar al cargar la página
    asignarEventosEditar();

    // Limpiar formulario de forma de pago
    limpiarTodoBtn.addEventListener('click', function () {
        formularioFormaPago.reset();
        formularioFormaPago.action = "{{ route('formas-pago.store') }}";
        formularioFormaPago.querySelector('input[name="_method"]').value = 'POST';
        document.getElementById('idFormaPago').value = '';
        document.getElementById('nomFormaPago').value = '';
        document.getElementById('desFormaPago').value = '';
        document.getElementById('nomeFormaPago').value = '';
        document.getElementById('estadoFormaPago').value = '1';
    });

    // Limpiar formulario de búsqueda y recargar lista
    limpiarBusquedaFormaPagoBtn.addEventListener('click', function () {
        inputBuscarFormaPago.value = '';
        mensajeBusquedaFormaPago.classList.add('hidden');
        fetch('/formas-pago', {
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
            console.log('Respuesta del servidor para /formas-pago:', data);
            tbodyFormasPago.innerHTML = '';
            mensajeBusquedaFormaPago.classList.add('hidden');
            if (data.success && Array.isArray(data.formasPago) && data.formasPago.length > 0) {
                data.formasPago.forEach(formaPago => {
                    const tr = document.createElement('tr');
                    tr.className = 'border-t hover:bg-blue-50';
                    tr.innerHTML = `
                        <td class="px-4 py-3">${formaPago.idFormaPago}</td>
                        <td class="px-4 py-3">${formaPago.nomFormaPago}</td>
                        <td class="px-4 py-3">${formaPago.desFormaPago || 'Sin descripción'}</td>
                        <td class="px-4 py-3">${formaPago.nomeFormaPago || 'Sin nomenclatura'}</td>
                        <td class="px-4 py-3">${formaPago.estadoFormaPago ? 'Activo' : 'Inactivo'}</td>
                        <td class="px-4 py-3 flex space-x-2">
                            <button class="editar-forma-pago bg-blue-500 text-white px-3 py-1 rounded-md hover:bg-blue-600 text-xs" data-forma-pago='${JSON.stringify(formaPago)}'>Editar</button>
                            <form action="/formas-pago/${formaPago.idFormaPago}" method="POST" class="inline">
                                <input type="hidden" name="_token" value="${document.querySelector('input[name="_token"]').value}">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-600 text-xs" onclick="return confirm('¿Está seguro de eliminar esta forma de pago?')">Eliminar</button>
                            </form>
                        </td>
                    `;
                    tbodyFormasPago.appendChild(tr);
                });
                asignarEventosEditar();
            } else {
                tbodyFormasPago.innerHTML = `
                    <tr>
                        <td colspan="6" class="px-4 py-3 text-center text-gray-500">No se encontraron formas de pago.</td>
                    </tr>
                `;
                mensajeBusquedaFormaPago.textContent = data.message || 'No se encontraron formas de pago.';
                mensajeBusquedaFormaPago.classList.remove('hidden');
                mensajeBusquedaFormaPago.classList.add('text-gray-500');
            }
        })
        .catch(error => {
            console.error('Error al recargar formas de pago:', error);
            mensajeBusquedaFormaPago.textContent = 'Error al recargar la lista de formas de pago: ' + error.message;
            mensajeBusquedaFormaPago.classList.remove('hidden');
            mensajeBusquedaFormaPago.classList.add('text-red-500');
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

    // Buscar forma de pago en tiempo real
    inputBuscarFormaPago.addEventListener('input', debounce(function () {
        const termino = inputBuscarFormaPago.value.trim();
        if (!termino) {
            limpiarBusquedaFormaPagoBtn.click();
            return;
        }
        const url = `/formas-pago/buscarFormaPago?termino=${encodeURIComponent(termino)}`;
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
            tbodyFormasPago.innerHTML = '';
            mensajeBusquedaFormaPago.classList.add('hidden');
            if (data.success && Array.isArray(data.formasPago) && data.formasPago.length > 0) {
                data.formasPago.forEach(formaPago => {
                    const tr = document.createElement('tr');
                    tr.className = 'border-t hover:bg-blue-50';
                    tr.innerHTML = `
                        <td class="px-4 py-3">${formaPago.idFormaPago}</td>
                        <td class="px-4 py-3">${formaPago.nomFormaPago}</td>
                        <td class="px-4 py-3">${formaPago.desFormaPago || 'Sin descripción'}</td>
                        <td class="px-4 py-3">${formaPago.nomeFormaPago || 'Sin nomenclatura'}</td>
                        <td class="px-4 py-3">${formaPago.estadoFormaPago ? 'Activo' : 'Inactivo'}</td>
                        <td class="px-4 py-3 flex space-x-2">
                            <button class="editar-forma-pago bg-blue-500 text-white px-3 py-1 rounded-md hover:bg-blue-600 text-xs" data-forma-pago='${JSON.stringify(formaPago)}'>Editar</button>
                            <form action="/formas-pago/${formaPago.idFormaPago}" method="POST" class="inline">
                                <input type="hidden" name="_token" value="${document.querySelector('input[name="_token"]').value}">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-600 text-xs" onclick="return confirm('¿Está seguro de eliminar esta forma de pago?')">Eliminar</button>
                            </form>
                        </td>
                    `;
                    tbodyFormasPago.appendChild(tr);
                });
                asignarEventosEditar();
            } else {
                tbodyFormasPago.innerHTML = `
                    <tr>
                        <td colspan="6" class="px-4 py-3 text-center text-gray-500">No se encontraron formas de pago.</td>
                    </tr>
                `;
                mensajeBusquedaFormaPago.textContent = data.message || 'No se encontraron formas de pago.';
                mensajeBusquedaFormaPago.classList.remove('hidden');
                mensajeBusquedaFormaPago.classList.add('text-red-500');
            }
        })
        .catch(error => {
            console.error('Error en la búsqueda de formas de pago:', error);
            mensajeBusquedaFormaPago.textContent = 'Error al buscar formas de pago: ' + error.message;
            mensajeBusquedaFormaPago.classList.remove('hidden');
            mensajeBusquedaFormaPago.classList.add('text-red-500');
            tbodyFormasPago.innerHTML = `
                <tr>
                    <td colspan="6" class="px-4 py-3 text-center text-gray-500">Error al buscar formas de pago.</td>
                </tr>
            `;
        });
    }, 300));
});
