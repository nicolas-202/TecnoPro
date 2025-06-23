   document.getElementById('idPais').addEventListener('change', function() {
        const idPais = this.value;
        const departamentoSelect = document.getElementById('idDepartamento');
        const municipioSelect = document.getElementById('idMunicipio');

        // Limpiar departamentos y municipios
        departamentoSelect.innerHTML = '<option value="">Seleccione un departamento</option>';
        municipioSelect.innerHTML = '<option value="">Seleccione un municipio</option>';

        if (idPais) {
            fetch(`/departamentos/${idPais}`)
                .then(response => response.json())
                .then(data => {
                    data.forEach(departamento => {
                        const option = document.createElement('option');
                        option.value = departamento.idDepartamento;
                        option.text = departamento.nomDepartamento;
                        departamentoSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error al cargar departamentos:', error));
        }
    });

    document.getElementById('idDepartamento').addEventListener('change', function() {
        const idDepartamento = this.value;
        const municipioSelect = document.getElementById('idMunicipio');

        // Limpiar municipios
        municipioSelect.innerHTML = '<option value="">Seleccione un municipio</option>';

        if (idDepartamento) {
            fetch(`/municipios/${idDepartamento}`)
                .then(response => response.json())
                .then(data => {
                    data.forEach(municipio => {
                        const option = document.createElement('option');
                        option.value = municipio.idMunicipio;
                        option.text = municipio.nomMunicipio;
                        municipioSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error al cargar municipios:', error));
        }
    });