
document.addEventListener('DOMContentLoaded', function () {
    const agregarCarritoButtons = document.querySelectorAll('.agregar-carrito');
    const categoryFilter = document.getElementById('categoryFilter');

    // Carrito functionality
    agregarCarritoButtons.forEach(button => {
        button.addEventListener('click', function () {
            const producto = JSON.parse(this.getAttribute('data-producto'));
            let carrito = JSON.parse(localStorage.getItem('carrito') || '[]');

            // Verificar si el producto ya está en el carrito
            const productoExistente = carrito.find(item => item.idProducto === producto.idProducto);
            if (productoExistente) {
                // Validar stock antes de incrementar
                fetch(`/carrito/stock/${producto.idProducto}`)
                    .then(response => response.json())
                    .then(data => {
                        if (productoExistente.cantidad + 1 > data.cantidadExistente) {
                            alert(`No hay suficiente stock. Máximo disponible: ${data.cantidadExistente}`);
                        } else {
                            productoExistente.cantidad += 1;
                            localStorage.setItem('carrito', JSON.stringify(carrito));
                            alert('Cantidad actualizada en el carrito!');
                        }
                    })
                    .catch(error => {
                        console.error('Error al verificar stock:', error);
                        alert('Error al verificar el stock del producto.');
                    });
            } else {
                // Validar stock antes de agregar
                fetch(`/carrito/stock/${producto.idProducto}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.cantidadExistente < 1) {
                            alert('No hay stock disponible para este producto.');
                        } else {
                            carrito.push({
                                idProducto: producto.idProducto,
                                nomProducto: producto.nomProducto,
                                precioVenta: producto.precioVenta,
                                cantidad: 1,
                                imagen: producto.imagen || null
                            });
                            localStorage.setItem('carrito', JSON.stringify(carrito));
                            alert('Producto agregado al carrito!');
                        }
                    })
                    .catch(error => {
                        console.error('Error al verificar stock:', error);
                        alert('Error al verificar el stock del producto.');
                    });
            }
        });
    });

    // Category filter functionality
    if (categoryFilter) {
        categoryFilter.addEventListener('change', function () {
            const selectedCategory = this.value;
            const url = new URL(window.location);
            if (selectedCategory) {
                url.searchParams.set('idCategoria', selectedCategory);
            } else {
                url.searchParams.delete('idCategoria');
            }
            window.location.href = url.toString();
        });
    }
});
