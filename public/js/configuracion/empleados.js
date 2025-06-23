document.addEventListener('DOMContentLoaded', function () {
    const formularioEmpleado = document.getElementById('formularioEmpleado');
    const limpiarFormularioBtn = document.getElementById('limpiarFormulario');
    const inputImagen = document.getElementById('imagen');
    const imagenPrevia = document.getElementById('imagenPrevia');
    const inputBuscarEmpleadoId=document.getElementById('buscar_empleado');
    const inputBuscarUserId = document.getElementById('buscar_user_id');
    const mensajeBusqueda = document.getElementById('mensajeBusqueda');
    const resultadosBusqueda = document.getElementById('resultadosBusqueda');
    const listaUsuarios = document.getElementById('listaUsuarios');
    const limpiarBusquedaBtn = document.getElementById('limpiarBusquedaEmpleado');

    // Editar empleado
    document.querySelectorAll('.editar-empleado').forEach(button => {
        button.addEventListener('click', function () {
            const empleado = JSON.parse(this.getAttribute('data-empleado'));
            formularioEmpleado.action = `/empleados/${empleado.idEmpleado}`;
            formularioEmpleado.querySelector('input[name="_method"]').value = 'PUT';
            document.getElementById('idEmpleado').value = empleado.idEmpleado;
            document.getElementById('user_id').value = empleado.user_id;
            document.getElementById('nombre').value = empleado.nombre;
            document.getElementById('numero_documento').value = empleado.numero_documento;
            document.getElementById('idCargo').value = empleado.idCargo || '';
            document.getElementById('estadoEmpleado').value = empleado.estadoEmpleado;
            if (empleado.imagen) {
                imagenPrevia.src = `/storage/${empleado.imagen}`;
                imagenPrevia.style.display = 'block';
            }
        });
    });

    // Limpiar formulario de empleado
    limpiarFormularioBtn.addEventListener('click', function () {
        // Restablecer el formulario
        formularioEmpleado.reset();
        formularioEmpleado.action = "{{ route('empleados.store') }}";
        formularioEmpleado.querySelector('input[name="_method"]').value = 'POST';

        // Limpiar campos manualmente (incluidos readonly)
        document.getElementById('idEmpleado').value = '';
        document.getElementById('user_id').value = '';
        document.getElementById('nombre').value = '';
        document.getElementById('numero_documento').value = '';
        document.getElementById('idCargo').value = '';
        document.getElementById('estadoEmpleado').value = '1'; // Valor por defecto
        document.getElementById('imagen').value = '';
        console.log('Botón Limpiar Formulario clicado');
        // Ocultar vista previa de la imagen
        imagenPrevia.style.display = 'none';
        imagenPrevia.src = '#';

        // Limpiar campo de búsqueda y resultados
        inputBuscarUserId.value = '';
        mensajeBusqueda.classList.add('hidden');
        resultadosBusqueda.classList.add('hidden');
        listaUsuarios.innerHTML = '';
    });

 

    // Vista previa de la imagen
    inputImagen.addEventListener('change', function (event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                imagenPrevia.src = e.target.result;
                imagenPrevia.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });

    // Función de debounce para retrasar la búsqueda
    const debounce = (func, delay) => {
        let timeoutId;
        return (...args) => {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => func.apply(null, args), delay);
        };
    };

    // Buscar usuario en tiempo real
    inputBuscarUserId.addEventListener('input', debounce(function () {
        const userId = inputBuscarUserId.value.trim();
        console.log('Buscando user_id:', userId);

        // Limpiar resultados si el campo está vacío
        if (!userId) {
            document.getElementById('user_id').value = '';
            document.getElementById('nombre').value = '';
            document.getElementById('numero_documento').value = '';
            mensajeBusqueda.classList.add('hidden');
            resultadosBusqueda.classList.add('hidden');
            listaUsuarios.innerHTML = '';
            console.log('Campo vacío, limpiando resultados');
            return;
        }

        // Enviar solicitud AJAX
        const url = `/empleados/buscarUsuario?buscar_user_id=${encodeURIComponent(userId)}`;
        console.log('URL de la solicitud:', url);
        fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
            },
        })
            .then(response => {
                console.log('Estado de la respuesta:', response.status);
                return response.json();
            })
        .then(data => {
                console.log('Datos recibidos:', data);
            listaUsuarios.innerHTML = '';
            mensajeBusqueda.classList.add('hidden');
            resultadosBusqueda.classList.add('hidden');

            if (data.success && data.usuarios.length > 0) {
                    // Mostrar lista de usuarios
                data.usuarios.forEach(usuario => {
                    const li = document.createElement('li');
                    li.className = 'px-4 py-2 hover:bg-blue-100 cursor-pointer';
                    li.innerHTML = `
                        <div class="text-sm font-medium text-gray-900">${usuario.user_id}</div>
                        <div class="text-xs text-gray-500">${usuario.nombre || 'Sin nombre'}</div>
                        <div class="text-xs text-gray-500">${usuario.numero_documento || 'Sin documento'}</div>
                    `;
                    li.addEventListener('click', () => {
                            // Llenar formulario al seleccionar un usuario
                        document.getElementById('user_id').value = usuario.user_id;
                        document.getElementById('nombre').value = usuario.nombre || '';
                        document.getElementById('numero_documento').value = usuario.numero_documento || '';
                        inputBuscarUserId.value = usuario.user_id;
                        resultadosBusqueda.classList.add('hidden');
                        listaUsuarios.innerHTML = '';
                        mensajeBusqueda.classList.add('hidden');
                    });
                    listaUsuarios.appendChild(li);
                });
                resultadosBusqueda.classList.remove('hidden');
            } else {
                    // Mostrar mensaje si no hay resultados
                mensajeBusqueda.textContent = data.message || 'No se encontraron usuarios.';
                mensajeBusqueda.classList.remove('hidden');
                mensajeBusqueda.classList.add('text-red-500');
            }
        })
        .catch(error => {
            console.error('Error en la búsqueda:', error);
            mensajeBusqueda.textContent = 'Error al buscar usuarios.';
            mensajeBusqueda.classList.remove('hidden');
            mensajeBusqueda.classList.add('text-red-500');
            resultadosBusqueda.classList.add('hidden');
            listaUsuarios.innerHTML = '';
        });
    }, 300));

    function renderTablaEmpleados(empleados) {
    const tablaEmpleados = document.getElementById('tablaEmpleados');
    if (!tablaEmpleados) return;
    const tbody = tablaEmpleados.querySelector('tbody');
    tbody.innerHTML = '';
    if (empleados.length > 0) {
        empleados.forEach(empleado => {
            // Soporta ambos formatos: con user anidado o campos planos
            const nombre = empleado.user ? empleado.user.nombre : empleado.nombre;
            const numero_documento = empleado.user ? empleado.user.numero_documento : empleado.numero_documento;
            const idEmpleado = empleado.idEmpleado;
            const estadoEmpleado = empleado.estadoEmpleado ? 'Activo' : 'Inactivo';
            const dataEmpleado = empleado.user
                ? JSON.stringify({
                    idEmpleado: empleado.idEmpleado,
                    user_id: empleado.user_id,
                    nombre: empleado.user.nombre,
                    numero_documento: empleado.user.numero_documento,
                    idCargo: empleado.idCargo,
                    estadoEmpleado: empleado.estadoEmpleado,
                    imagen: empleado.imagen
                })
                : JSON.stringify(empleado);

            const tr = document.createElement('tr');
            tr.className = 'border-t hover:bg-blue-50';
            tr.innerHTML = `
                <td class="px-4 py-3">${idEmpleado}</td>
                <td class="px-4 py-3">${nombre || ''}</td>
                <td class="px-4 py-3">${numero_documento || ''}</td>
                <td class="px-4 py-3">${estadoEmpleado}</td>
                <td class="px-4 py-3 flex space-x-2">
                    <button class="editar-empleado bg-blue-500 text-white px-3 py-1 rounded-md hover:bg-blue-600 text-xs" data-empleado='${dataEmpleado}'>Editar</button>
                    <form action="/empleados/${idEmpleado}" method="POST" class="inline">
                        <input type="hidden" name="_token" value="${document.querySelector('input[name="_token"]').value}">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-600 text-xs" onclick="return confirm('¿Está seguro de eliminar este empleado?')">Eliminar</button>
                    </form>
                </td>
            `;
            tbody.appendChild(tr);
        });

        // Reasignar eventos a los nuevos botones de editar
        tbody.querySelectorAll('.editar-empleado').forEach(button => {
            button.addEventListener('click', function () {
                const empleado = JSON.parse(this.getAttribute('data-empleado'));
                formularioEmpleado.action = `/empleados/${empleado.idEmpleado}`;
                formularioEmpleado.querySelector('input[name="_method"]').value = 'PUT';
                document.getElementById('idEmpleado').value = empleado.idEmpleado;
                document.getElementById('user_id').value = empleado.user_id;
                document.getElementById('nombre').value = empleado.nombre;
                document.getElementById('numero_documento').value = empleado.numero_documento;
                document.getElementById('idCargo').value = empleado.idCargo || '';
                document.getElementById('estadoEmpleado').value = empleado.estadoEmpleado;
                if (empleado.imagen) {
                    imagenPrevia.src = empleado.imagen.startsWith('http') ? empleado.imagen : `/storage/${empleado.imagen}`;
                    imagenPrevia.style.display = 'block';
                } else {
                    imagenPrevia.style.display = 'none';
                    imagenPrevia.src = '#';
                }
            });
        });

    } else {
        tbody.innerHTML = `
            <tr>
                <td colspan="5" class="px-4 py-3 text-center text-red-500">No hay empleados para mostrar.</td>
            </tr>
        `;
    }
}
    limpiarBusquedaBtn.addEventListener('click', function () {
    inputBuscarEmpleadoId.value = '';
    mensajeBusqueda.classList.add('hidden');
    // Recargar los datos iniciales de la tabla empleados usando AJAX
    fetch('/empleados/lista-json', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
            'X-Requested-With': 'XMLHttpRequest'
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.empleados) {
            renderTablaEmpleados(data.empleados);
        }
    })
    .catch(error => {
        console.error('Error al recargar empleados:', error);
    });
});

    // Buscar empleado en tiempo real
    inputBuscarEmpleadoId.addEventListener('input', debounce(function () {
        const empleadoId = inputBuscarEmpleadoId.value.trim();
        console.log('Buscando empleado:', empleadoId);

        // Enviar solicitud AJAX para buscar empleados
        const url = `/empleados/buscarEmpleado?termino=${encodeURIComponent(empleadoId)}`;
        fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
            },
        })
        .then(response => {
            console.log('Estado de la respuesta:', response.status);
            if (!response.ok) {
                // Mostrar mensaje en la tabla si no hay empleados asociados
                renderTablaEmpleados([]);
                mensajeBusqueda.textContent = 'No hay empleados asociados a este dato.';
                mensajeBusqueda.classList.remove('hidden');
                mensajeBusqueda.classList.add('text-red-500');
                return;
            }
            return response.json();
        })
        .then(data => {
            if (!data) return;
            console.log('Datos recibidos:', data);
            if (data.success && data.empleados.length > 0) {
                renderTablaEmpleados(data.empleados);
                mensajeBusqueda.classList.add('hidden');
            } else {
                renderTablaEmpleados([]);
                mensajeBusqueda.textContent = data.message || 'No se encontraron empleados.';
                mensajeBusqueda.classList.remove('hidden');
                mensajeBusqueda.classList.add('text-red-500');
            }
        })
        .catch(error => {
            console.error('Error en la búsqueda de empleados:', error);
            renderTablaEmpleados([]);
            mensajeBusqueda.textContent = 'Error al buscar empleados.';
            mensajeBusqueda.classList.remove('hidden');
            mensajeBusqueda.classList.add('text-red-500');
        });
    }, 300));
   
 
    // Ocultar resultados al hacer clic fuera del contenedor
    document.addEventListener('click', function (event) {
        if (!resultadosBusqueda.contains(event.target) && event.target !== inputBuscarUserId) {
            resultadosBusqueda.classList.add('hidden');
            listaUsuarios.innerHTML = '';
        }
    });
    

});