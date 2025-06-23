document.addEventListener('DOMContentLoaded', function () {
    const formularioProducto = document.getElementById('formularioProducto');
    const limpiarFormularioBtn = document.getElementById('limpiarFormulario');
    const inputImagen = document.getElementById('imagen');
    const imagenPrevia = document.getElementById('imagenPrevia');
    const inputBuscarProductoId = document.getElementById('buscar_producto');
    const mensajeBusqueda = document.getElementById('mensajeBusquedaProducto');
    const limpiarBusquedaBtn = document.getElementById('limpiarBusquedaProducto');

    // Editar producto
    document.querySelectorAll('.editar-producto').forEach(button => {
        button.addEventListener('click', function () {
            const producto = JSON.parse(this.getAttribute('data-producto'));
            formularioProducto.action = `/productos/${producto.idProducto}`;
            formularioProducto.querySelector('input[name="_method"]').value = 'PUT';
            document.getElementById('idProducto').value = producto.idProducto;
            document.getElementById('nomProducto').value = producto.nomProducto;
            document.getElementById('desProducto').value = producto.desProducto;
            document.getElementById('stockMinimo').value = producto.stockMinimo;
            document.getElementById('stockMaximo').value = producto.stockMaximo;
            document.getElementById('cantidadExistente').value = producto.cantidadExistente;
            document.getElementById('precioVenta').value = producto.precioVenta;
            document.getElementById('idCategoria').value = producto.idCategoria || '';
            document.getElementById('estadoProducto').value = producto.estadoProducto;
            if (producto.imagen) {
                imagenPrevia.src = `/storage/${producto.imagen}`;
                imagenPrevia.style.display = 'block';
            } else {
                imagenPrevia.style.display = 'none';
                imagenPrevia.src = '#';
            }
        });
    });

    // Limpiar formulario de producto
    limpiarFormularioBtn.addEventListener('click', function () {
        formularioProducto.reset();
        formularioProducto.action = "{{ route('productos.store') }}";
        formularioProducto.querySelector('input[name="_method"]').value = 'POST';
        document.getElementById('idProducto').value = '';
        document.getElementById('nomProducto').value = '';
        document.getElementById('desProducto').value = '';
        document.getElementById('stockMinimo').value = '';
        document.getElementById('stockMaximo').value = '';
        document.getElementById('cantidadExistente').value = '0';
        document.getElementById('precioVenta').value = '0';
        document.getElementById('idCategoria').value = '';
        document.getElementById('estadoProducto').value = '1';
        document.getElementById('imagen').value = '';
        imagenPrevia.style.display = 'none';
        imagenPrevia.src = '#';
        inputBuscarProductoId.value = '';
        mensajeBusqueda.classList.add('hidden');
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

    // Buscar producto en tiempo real
    inputBuscarProductoId.addEventListener('input', debounce(function () {
        const termino = inputBuscarProductoId.value.trim();
        console.log('Buscando producto:', termino);

        if (!termino) {
            mensajeBusqueda.classList.add('hidden');
            fetch('/productos/lista-json', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.productos) {
                    renderTablaProductos(data.productos);
                }
            })
            .catch(error => console.error('Error al recargar productos:', error));
            return;
        }

        const url = `/productos/buscarProducto?termino=${encodeURIComponent(termino)}`;
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
                renderTablaProductos([]);
                mensajeBusqueda.textContent = 'No hay productos asociados a este dato.';
                mensajeBusqueda.classList.remove('hidden');
                mensajeBusqueda.classList.add('text-red-500');
                return;
            }
            return response.json();
        })
        .then(data => {
            if (!data) return;
            console.log('Datos recibidos:', data);
            if (data.success && data.productos.length > 0) {
                renderTablaProductos(data.productos);
                mensajeBusqueda.classList.add('hidden');
            } else {
                renderTablaProductos([]);
                mensajeBusqueda.textContent = data.message || 'No se encontraron productos.';
                mensajeBusqueda.classList.remove('hidden');
                mensajeBusqueda.classList.add('text-red-500');
            }
        })
        .catch(error => {
            console.error('Error en la búsqueda de productos:', error);
            renderTablaProductos([]);
            mensajeBusqueda.textContent = 'Error al buscar productos.';
            mensajeBusqueda.classList.remove('hidden');
            mensajeBusqueda.classList.add('text-red-500');
        });
    }, 300));

    // Limpiar búsqueda de producto
    limpiarBusquedaBtn.addEventListener('click', function () {
        inputBuscarProductoId.value = '';
        mensajeBusqueda.classList.add('hidden');
        fetch('/productos/lista-json', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.productos) {
                renderTablaProductos(data.productos);
            }
        })
        .catch(error => console.error('Error al recargar productos:', error));
    });

    // Renderizar tabla de productos
    function renderTablaProductos(productos) {
        const tablaProductos = document.getElementById('tablaProductos');
        if (!tablaProductos) return;
        const tbody = tablaProductos.querySelector('tbody');
        tbody.innerHTML = '';
        if (productos.length > 0) {
            productos.forEach(producto => {
                const tr = document.createElement('tr');
                tr.className = 'border-t hover:bg-blue-50';
                tr.innerHTML = `
                    <td class="px-4 py-3">${producto.idProducto}</td>
                    <td class="px-4 py-3">${producto.nomProducto || ''}</td>
                    <td class="px-4 py-3">${producto.desProducto || ''}</td>
                    <td class="px-4 py-3">${producto.stockMinimo || ''}</td>
                    <td class="px-4 py-3">${producto.stockMaximo || ''}</td>
                    <td class="px-4 py-3">${producto.cantidadExistente || '0'}</td>
                    <td class="px-4 py-3">${producto.precioVenta || '0'}</td>
                    <td class="px-4 py-3">${producto.nomCategoria || ''}</td>
                    <td class="px-4 py-3">${producto.estadoProducto ? 'Activo' : 'Inactivo'}</td>
                    <td class="px-4 py-3 flex space-x-2">
                        <button class="editar-producto bg-blue-500 text-white px-3 py-1 rounded-md hover:bg-blue-600 text-xs" data-producto='${JSON.stringify(producto)}'>Editar</button>
                        <form action="/productos/${producto.idProducto}" method="POST" class="inline">
                            <input type="hidden" name="_token" value="${document.querySelector('input[name="_token"]').value}">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-600 text-xs" onclick="return confirm('¿Está seguro de eliminar este producto?')">Eliminar</button>
                        </form>
                    </td>
                `;
                tbody.appendChild(tr);
            });

            tbody.querySelectorAll('.editar-producto').forEach(button => {
                button.addEventListener('click', function () {
                    const producto = JSON.parse(this.getAttribute('data-producto'));
                    formularioProducto.action = `/productos/${producto.idProducto}`;
                    formularioProducto.querySelector('input[name="_method"]').value = 'PUT';
                    document.getElementById('idProducto').value = producto.idProducto;
                    document.getElementById('nomProducto').value = producto.nomProducto;
                    document.getElementById('desProducto').value = producto.desProducto;
                    document.getElementById('stockMinimo').value = producto.stockMinimo;
                    document.getElementById('stockMaximo').value = producto.stockMaximo;
                    document.getElementById('cantidadExistente').value = producto.cantidadExistente;
                    document.getElementById('precioVenta').value = producto.precioVenta;
                    document.getElementById('idCategoria').value = producto.idCategoria || '';
                    document.getElementById('estadoProducto').value = producto.estadoProducto;
                    if (producto.imagen) {
                        imagenPrevia.src = producto.imagen.startsWith('http') ? producto.imagen : `/storage/${producto.imagen}`;
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
                    <td colspan="10" class="px-4 py-3 text-center text-red-500">No hay productos para mostrar.</td>
                </tr>
            `;
        }
    }
});