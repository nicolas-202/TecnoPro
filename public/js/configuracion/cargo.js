document.addEventListener('DOMContentLoaded', function () {
    const formularioCargo = document.getElementById('formularioCargo');
    const limpiarTodoBtn = document.getElementById('limpiarTodo');
    const inputBuscarCargo = document.getElementById('buscar_cargo');
    const mensajeBusquedaCargo = document.getElementById('mensajeBusquedaCargo');
    const tbodyCargos = document.getElementById('tbodyCargos');
    const limpiarBusquedaCargoBtn = document.getElementById('limpiarBusquedaCargo');

    // Función para asignar eventos a los botones de editar
    function asignarEventosEditar() {
        document.querySelectorAll('.editar-cargo').forEach(button => {
            button.removeEventListener('click', editarCargoHandler); // Evitar duplicados
            button.addEventListener('click', editarCargoHandler);
        });
    }

    // Handler para editar cargo
    function editarCargoHandler() {
        const cargo = JSON.parse(this.getAttribute('data-cargo'));
        formularioCargo.action = `/cargos/${cargo.idCargo}`;
        formularioCargo.querySelector('input[name="_method"]').value = 'PUT';
        document.getElementById('idCargo').value = cargo.idCargo;
        document.getElementById('nomCargo').value = cargo.nomCargo || '';
        document.getElementById('desCargo').value = cargo.desCargo || '';
        document.getElementById('nomeCargo').value = cargo.nomeCargo || '';
        document.getElementById('estadoCargo').value = cargo.estadoCargo ? '1' : '0';
    }

    // Inicializar eventos de editar al cargar la página
    asignarEventosEditar();

    // Limpiar formulario de cargo
    limpiarTodoBtn.addEventListener('click', function () {
        formularioCargo.reset();
        formularioCargo.action = "{{ route('cargos.store') }}";
        formularioCargo.querySelector('input[name="_method"]').value = 'POST';
        document.getElementById('idCargo').value = '';
        document.getElementById('nomCargo').value = '';
        document.getElementById('desCargo').value = '';
        document.getElementById('nomeCargo').value = '';
        document.getElementById('estadoCargo').value = '1';
    });

   limpiarBusquedaCargoBtn.addEventListener('click', function () {
    inputBuscarCargo.value = '';
    mensajeBusquedaCargo.classList.add('hidden');
    fetch('/cargos', {
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
        return response.json(); // Cambia response.text() a response.json()
    })
    .then(data => {
        tbodyCargos.innerHTML = ''; // Limpia la tabla
        mensajeBusquedaCargo.classList.add('hidden');
        if (data.success && data.cargos.length > 0) {
            data.cargos.forEach(cargo => {
                const tr = document.createElement('tr');
                tr.className = 'border-t hover:bg-blue-50';
                tr.innerHTML = `
                    <td class="px-4 py-3">${cargo.idCargo}</td>
                    <td class="px-4 py-3">${cargo.nomCargo}</td>
                    <td class="px-4 py-3">${cargo.desCargo || 'Sin descripción'}</td>
                    <td class="px-4 py-3">${cargo.nomeCargo || 'Sin nomenclatura'}</td>
                    <td class="px-4 py-3">${cargo.estadoCargo ? 'Activo' : 'Inactivo'}</td>
                    <td class="px-4 py-3 flex space-x-2">
                        <button class="editar-cargo bg-blue-500 text-white px-3 py-1 rounded-md hover:bg-blue-600 text-xs" data-cargo='${JSON.stringify(cargo)}'>Editar</button>
                        <form action="/cargos/${cargo.idCargo}" method="POST" class="inline">
                            <input type="hidden" name="_token" value="${document.querySelector('input[name="_token"]').value}">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-600 text-xs" onclick="return confirm('¿Está seguro de eliminar este cargo?')">Eliminar</button>
                        </form>
                    </td>
                `;
                tbodyCargos.appendChild(tr);
            });
            asignarEventosEditar();
        } else {
            tbodyCargos.innerHTML = `
                <tr>
                    <td colspan="6" class="px-4 py-3 text-center text-gray-500">No se encontraron cargos.</td>
                </tr>
            `;
        }
    })
    .catch(error => {
        console.error('Error al recargar cargos:', error);
        mensajeBusquedaCargo.textContent = 'Error al recargar la lista de cargos: ' + error.message;
        mensajeBusquedaCargo.classList.remove('hidden');
        mensajeBusquedaCargo.classList.add('text-red-500');
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

    // Buscar cargo en tiempo real
    inputBuscarCargo.addEventListener('input', debounce(function () {
        const termino = inputBuscarCargo.value.trim();
        if (!termino) {
            limpiarBusquedaCargoBtn.click();
            return;
        }
        const url = `/cargos/buscarCargo?termino=${encodeURIComponent(termino)}`;
        fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
            },
        })
        .then(response => response.json())
        .then(data => {
            tbodyCargos.innerHTML = '';
            mensajeBusquedaCargo.classList.add('hidden');
            if (data.success && data.cargos.length > 0) {
                data.cargos.forEach(cargo => {
                    const tr = document.createElement('tr');
                    tr.className = 'border-t hover:bg-blue-50';
                    tr.innerHTML = `
                        <td class="px-4 py-3">${cargo.idCargo}</td>
                        <td class="px-4 py-3">${cargo.nomCargo}</td>
                        <td class="px-4 py-3">${cargo.desCargo || 'Sin descripción'}</td>
                        <td class="px-4 py-3">${cargo.nomeCargo || 'Sin nomenclatura'}</td>
                        <td class="px-4 py-3">${cargo.estadoCargo ? 'Activo' : 'Inactivo'}</td>
                        <td class="px-4 py-3 flex space-x-2">
                            <button class="editar-cargo bg-blue-500 text-white px-3 py-1 rounded-md hover:bg-blue-600 text-xs" data-cargo='${JSON.stringify(cargo)}'>Editar</button>
                            <form action="/cargos/${cargo.idCargo}" method="POST" class="inline">
                                <input type="hidden" name="_token" value="${document.querySelector('input[name="_token"]').value}">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-600 text-xs" onclick="return confirm('¿Está seguro de eliminar este cargo?')">Eliminar</button>
                            </form>
                        </td>
                    `;
                    tbodyCargos.appendChild(tr);
                });
                asignarEventosEditar();
            } else {
                tbodyCargos.innerHTML = `
                    <tr>
                        <td colspan="6" class="px-4 py-3 text-center text-gray-500">No se encontraron cargos.</td>
                    </tr>
                `;
                mensajeBusquedaCargo.textContent = data.message || 'No se encontraron cargos.';
                mensajeBusquedaCargo.classList.remove('hidden');
                mensajeBusquedaCargo.classList.add('text-red-500');
            }
        })
        .catch(error => {
            console.error('Error en la búsqueda de cargos:', error);
            mensajeBusquedaCargo.textContent = 'Error al buscar cargos.';
            mensajeBusquedaCargo.classList.remove('hidden');
            mensajeBusquedaCargo.classList.add('text-red-500');
            tbodyCargos.innerHTML = `
                <tr>
                    <td colspan="6" class="px-4 py-3 text-center text-gray-500">Error al buscar cargos.</td>
                </tr>
            `;
        });
    }, 300));
});