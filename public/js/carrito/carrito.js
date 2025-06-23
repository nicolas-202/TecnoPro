document.addEventListener('DOMContentLoaded', function () {
    const carritoProductos = document.getElementById('carrito-productos');
    const resumenItems = document.getElementById('resumen-items');
    const totalPedido = document.getElementById('total-pedido');
    const confirmarPedidoBtn = document.getElementById('confirmar-pedido');

    let carrito = JSON.parse(localStorage.getItem('carrito') || '[]');

    function actualizarCarrito() {
        carritoProductos.innerHTML = '';
        resumenItems.innerHTML = '';
        let total = 0;

        if (carrito.length === 0) {
            carritoProductos.innerHTML = '<div class="col-span-full text-center text-gray-500">El carrito está vacío.</div>';
            totalPedido.textContent = '$ 0.00';
            confirmarPedidoBtn.classList.add('opacity-50', 'cursor-not-allowed');
            confirmarPedidoBtn.removeAttribute('href');
            confirmarPedidoBtn.addEventListener('click', (e) => e.preventDefault());
            return;
        }

        confirmarPedidoBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        confirmarPedidoBtn.addEventListener('click', confirmarCarrito);

        carrito.forEach((producto, index) => {
            const productoDiv = document.createElement('div');
            productoDiv.className = 'bg-white shadow-md rounded-lg overflow-hidden';
            productoDiv.innerHTML = `
                <img src="${producto.imagen ? '/storage/' + producto.imagen : '/images/placeholder.jpg'}" alt="${producto.nomProducto}" class="w-full h-24 object-cover">
                <div class="p-3">
                    <h2 class="text-sm font-semibold text-gray-800 truncate">${producto.nomProducto}</h2>
                    <p class="text-gray-600 text-xs mt-1">$ ${producto.precioVenta.toFixed(2)}</p>
                    <div class="flex items-center mt-2">
                        <label for="cantidad-${index}" class="text-gray-600 text-xs mr-2">Cantidad:</label>
                        <input type="number" id="cantidad-${index}" value="${producto.cantidad}" min="1" class="w-12 p-1 border rounded-md text-gray-600 text-xs">
                    </div>
                    <button class="eliminar-producto bg-red-600 text-white px-2 py-1 rounded-md hover:bg-red-700 w-full mt-2 text-xs" data-index="${index}">Eliminar</button>
                </div>
            `;
            carritoProductos.appendChild(productoDiv);

            const inputCantidad = productoDiv.querySelector(`#cantidad-${index}`);
            inputCantidad.addEventListener('change', () => {
                const nuevaCantidad = parseInt(inputCantidad.value);
                if (nuevaCantidad < 1) {
                    inputCantidad.value = 1;
                    return;
                }

                fetch(`/carrito/stock/${producto.idProducto}`)
                    .then(response => response.json())
                    .then(data => {
                        if (nuevaCantidad > data.cantidadExistente) {
                            alert(`No hay suficiente stock. Máximo disponible: ${data.cantidadExistente}`);
                            inputCantidad.value = producto.cantidad;
                        } else {
                            carrito[index].cantidad = nuevaCantidad;
                            localStorage.setItem('carrito', JSON.stringify(carrito));
                            actualizarCarrito();
                        }
                    })
                    .catch(error => {
                        console.error('Error al verificar stock:', error);
                        alert('Error al verificar el stock del producto.');
                        inputCantidad.value = producto.cantidad;
                    });
            });

            const eliminarBtn = productoDiv.querySelector('.eliminar-producto');
            eliminarBtn.addEventListener('click', () => {
                carrito.splice(index, 1);
                localStorage.setItem('carrito', JSON.stringify(carrito));
                actualizarCarrito();
            });

            const subtotal = producto.precioVenta * producto.cantidad;
            total += subtotal;
            const resumenItem = document.createElement('div');
            resumenItem.className = 'flex justify-between text-gray-600 text-sm mb-2';
            resumenItem.innerHTML = `
                <span>${producto.nomProducto} (x${producto.cantidad})</span>
                <span>$ ${subtotal.toFixed(2)}</span>
            `;
            resumenItems.appendChild(resumenItem);
        });

        totalPedido.textContent = `$ ${total.toFixed(2)}`;
    }

    function confirmarCarrito(e) {
        e.preventDefault();
        if (carrito.length === 0) {
            alert('El carrito está vacío.');
            return;
        }

        fetch('/carrito/confirmar', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ carrito })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = '/carrito/confirmar';
                } else {
                    alert(data.message || 'Error al confirmar el carrito.');
                }
            })
            .catch(error => {
                console.error('Error al confirmar carrito:', error);
                alert('Error al confirmar el carrito. Intenta de nuevo.');
            });
    }

    actualizarCarrito();
});