 document.addEventListener('DOMContentLoaded', function() {
        console.log('Script cargado. Buscando botones con clase devolucion-btn...');
        const buttons = document.querySelectorAll('.devolucion-btn');
        console.log(`Encontrados ${buttons.length} botones de devolución`);

        buttons.forEach((button, index) => {
            console.log(`Botón ${index + 1}:`, {
                id: button.getAttribute('data-id'),
                nombre: button.getAttribute('data-nombre'),
                cantidad: button.getAttribute('data-cantidad')
            });
            button.addEventListener('click', function() {
                console.log('Botón de devolución clickeado:', {
                    id: this.getAttribute('data-id'),
                    nombre: this.getAttribute('data-nombre'),
                    cantidad: this.getAttribute('data-cantidad')
                });
                const id = this.getAttribute('data-id');
                const nombre = this.getAttribute('data-nombre');
                const cantidad = parseInt(this.getAttribute('data-cantidad'), 10);
                openDevolucionModal(id, nombre, cantidad);
            });
        });
    });

    function openDevolucionModal(id, nombre, cantidad) {
        console.log('Abriendo modal con datos:', { id, nombre, cantidad });
        const modal = document.getElementById('devolucionModal');
        const modalTitle = document.getElementById('modalTitle');
        const idInput = document.getElementById('idPedidoProducto');
        const cantidadInput = document.getElementById('cantidad');
        const cantidadMaxText = document.getElementById('cantidadMax');

        if (modal && modalTitle && idInput && cantidadInput && cantidadMaxText) {
            modal.classList.remove('hidden');
            modalTitle.textContent = `Solicitar Devolución: ${nombre}`;
            idInput.value = id;
            cantidadInput.max = cantidad;
            cantidadInput.value = 1;
            cantidadMaxText.textContent = `Máximo: ${cantidad} unidad${cantidad > 1 ? 'es' : ''}`;
        } else {
            console.error('Error: Elementos del modal no encontrados', {
                modal: !!modal,
                modalTitle: !!modalTitle,
                idInput: !!idInput,
                cantidadInput: !!cantidadInput,
                cantidadMaxText: !!cantidadMaxText
            });
        }
    }

    function closeDevolucionModal() {
        console.log('Cerrando modal');
        const modal = document.getElementById('devolucionModal');
        const form = document.getElementById('devolucionForm');
        if (modal && form) {
            modal.classList.add('hidden');
            form.reset();
            document.getElementById('idPedidoProducto').value = '';
            document.getElementById('cantidadMax').textContent = '';
        } else {
            console.error('Error: Elementos del modal no encontrados', {
                modal: !!modal,
                form: !!form
            });
        }
    }

    // Cerrar modal al hacer clic fuera
    const modal = document.getElementById('devolucionModal');
    if (modal) {
        modal.addEventListener('click', function(event) {
            if (event.target === this) {
                console.log('Clic fuera del modal, cerrando...');
                closeDevolucionModal();
            }
        });
    } else {
        console.error('Modal no encontrado');
    }