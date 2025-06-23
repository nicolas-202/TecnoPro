document.addEventListener('DOMContentLoaded', function () {
    const modificarDireccionBtn = document.getElementById('modificar-direccion');
    const formDireccion = document.getElementById('form-direccion');
    const cancelarDireccionBtn = document.getElementById('cancelar-direccion');
    const realizarCompraBtn = document.getElementById('realizar-compra');
    const dropdownFormasPago = document.getElementById('dropdown-formas-pago');
    const modalTarjeta = document.getElementById('modal-tarjeta');
    const formTarjeta = document.getElementById('form-tarjeta');
    const cancelarTarjetaBtn = document.getElementById('cancelar-tarjeta');
    const paisSelect = document.getElementById('idPais');
    const departamentoSelect = document.getElementById('idDepartamento');
    const municipioSelect = document.getElementById('idMunicipio');

    // Initialize dropdowns if user has existing address
    function initializeDropdowns() {
        if (paisSelect.value) {
            fetch(`/departamentos/${paisSelect.value}`)
                .then(response => response.json())
                .then(departamentos => {
                    departamentoSelect.innerHTML = '<option value="">Seleccione un departamento</option>';
                    departamentos.forEach(depto => {
                        const option = document.createElement('option');
                        option.value = depto.idDepartamento;
                        option.textContent = depto.nomDepartamento;
                        const usuarioDepartamento = window.usuarioDepartamento || '';
                        if (depto.nomDepartamento === usuarioDepartamento) {
                            option.selected = true;
                        }
                        departamentoSelect.appendChild(option);
                    });
                    if (departamentoSelect.value) {
                        fetch(`/municipios/${departamentoSelect.value}`)
                            .then(response => response.json())
                            .then(municipios => {
                                municipioSelect.innerHTML = '<option value="">Seleccione un municipio</option>';
                                municipios.forEach(muni => {
                                    const option = document.createElement('option');
                                    option.value = muni.idMunicipio;
                                    option.textContent = muni.nomMunicipio;
                                    const usuarioMunicipio = window.usuarioMunicipio || '';
                                    if (muni.nomMunicipio === usuarioMunicipio) {
                                        option.selected = true;
                                    }
                                    municipioSelect.appendChild(option);
                                });
                            });
                    }
                });
        }
    }

    // Populate departamentos when pais changes
    paisSelect.addEventListener('change', () => {
        const idPais = paisSelect.value;
        departamentoSelect.innerHTML = '<option value="">Seleccione un departamento</option>';
        municipioSelect.innerHTML = '<option value="">Seleccione un municipio</option>';
        if (idPais) {
            fetch(`/departamentos/${idPais}`)
                .then(response => response.json())
                .then(departamentos => {
                    departamentos.forEach(depto => {
                        const option = document.createElement('option');
                        option.value = depto.idDepartamento;
                        option.textContent = depto.nomDepartamento;
                        departamentoSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error fetching departamentos:', error));
        }
    });

    // Populate municipios when departamento changes
    departamentoSelect.addEventListener('change', () => {
        const idDepartamento = departamentoSelect.value;
        municipioSelect.innerHTML = '<option value="">Seleccione un municipio</option>';
        if (idDepartamento) {
            fetch(`/municipios/${idDepartamento}`)
                .then(response => response.json())
                .then(municipios => {
                    municipios.forEach(muni => {
                        const option = document.createElement('option');
                        option.value = muni.idMunicipio;
                        option.textContent = muni.nomMunicipio;
                        municipioSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error fetching municipios:', error));
        }
    });

    // Toggle address form
    modificarDireccionBtn.addEventListener('click', () => {
        formDireccion.classList.toggle('hidden');
        modificarDireccionBtn.classList.toggle('hidden');
        initializeDropdowns();
    });

    cancelarDireccionBtn.addEventListener('click', () => {
        formDireccion.classList.add('hidden');
        modificarDireccionBtn.classList.remove('hidden');
    });

    // Toggle payment methods dropdown
    realizarCompraBtn.addEventListener('click', () => {
        dropdownFormasPago.classList.toggle('hidden');
    });

    // Handle payment method selection
    document.querySelectorAll('.select-forma-pago').forEach(button => {
        button.addEventListener('click', () => {
            const idFormaPago = button.getAttribute('data-id');
            document.getElementById('idFormaPago').value = idFormaPago;
            dropdownFormasPago.classList.add('hidden');
            modalTarjeta.classList.remove('hidden');
        });
    });

    // Close modal
    cancelarTarjetaBtn.addEventListener('click', () => {
        modalTarjeta.classList.add('hidden');
        formTarjeta.reset();
    });

    // Handle card form submission
    formTarjeta.addEventListener('submit', (e) => {
        e.preventDefault();
        const formData = new FormData(formTarjeta);
        const data = {
            idFormaPago: formData.get('idFormaPago'),
            numero_tarjeta: formData.get('numero_tarjeta').replace(/\s/g, ''),
            nombre_titular: formData.get('nombre_titular'),
            fecha_expiracion: formData.get('fecha_expiracion'),
            cvv: formData.get('cvv'),
        };

        fetch('/carrito/procesar-compra', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    localStorage.removeItem('carrito');
                    window.location.href = data.redirect;
                } else {
                    alert(data.message || 'Error al procesar la compra.');
                }
            })
            .catch(error => {
                console.error('Error al procesar compra:', error);
                alert('Error al procesar la compra. Intenta de nuevo.');
            });
    });

    // Initialize dropdowns on page load
    initializeDropdowns();

    // Close dropdown when clicking outside
    document.addEventListener('click', (e) => {
        if (!realizarCompraBtn.contains(e.target) && !dropdownFormasPago.contains(e.target)) {
            dropdownFormasPago.classList.add('hidden');
        }
    });
});
