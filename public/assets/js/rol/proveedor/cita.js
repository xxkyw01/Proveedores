
 src="https://cdn.jsdelivr.net/npm/sweetalert2@11";
src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js";
 src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js";
 src="https://cdn.jsdelivr.net/npm/sweetalert2@11";





    let currentStep = 1;

    document.addEventListener("DOMContentLoaded", function () {    
        const totalSteps = 5;
        const steps = document.querySelectorAll(".step");

        // Permitir hacer clic en los pasos anteriores para navegar rápidamente

        document.querySelectorAll(".step-item").forEach((item, index) => {
            item.addEventListener("click", function () {
                const pasoClickeado = index + 1;

                // No hacer nada si estás en el mismo paso
                if (pasoClickeado === currentStep) return;

                // Solo permitir avanzar si ya completaste el paso 5
                if (pasoClickeado > currentStep && currentStep < totalSteps) return;

                currentStep = pasoClickeado;
                showStep(currentStep);
                actualizarWizard(currentStep);
            });
        });


        // Hacer Formulario de "Registro" con múltiples pasos aissitente de pasos 
        function actualizarWizard(step) {
        for (let i = 1; i <= totalSteps; i++) {
            const stepElement = document.getElementById(`step-indicator-${i}`);
            if (!stepElement) continue;

            const circle = stepElement.querySelector('.circle');

            if (i < step) {
                stepElement.classList.add("completed");
                stepElement.classList.remove("active");
                circle.textContent = "✔";
            } else if (i === step) {
                stepElement.classList.add("active");
                stepElement.classList.remove("completed");
                circle.textContent = i;
            } else {
                stepElement.classList.remove("active", "completed");
                circle.textContent = i;
            }
        }
    }

    function showStep(step) {
        const steps = document.querySelectorAll(".step");
        steps.forEach((element, index) => {
            if (index === step - 1) {
                element.classList.remove("d-none");
            } else {
                element.classList.add("d-none");
            }
        });

        

        actualizarWizard(step);
    }

        // Siguiente paso con validaciones
        document.querySelectorAll(".next-step").forEach(button => {
            button.addEventListener("click", function () {
                if (!validarCamposPaso(currentStep)) return;
                if (currentStep === 3 && !validarDuplicadosHorarioYAnden()) return;
                if (currentStep === 3 && !validarHorariosSeleccionadosDisponibles()) return;
                if (currentStep === 3 && !validarCamposMacheteros()) return;


                if (currentStep < totalSteps) {
                    currentStep++;
                    if (currentStep === 5) llenarResumenCompleto();
                    showStep(currentStep);

                }
            });
        });

        // Paso anterior
        document.querySelectorAll(".prev-step").forEach(button => {
            button.addEventListener("click", function () {
                if (currentStep > 1) {
                    currentStep--;
                    showStep(currentStep);
                }
            });
        });

        // Cargar sucursales por ciudad
        document.getElementById("ciudad").addEventListener("change", function () {
            fetch(`/proveedor/obtener-sucursales/${this.value}`)
                .then(res => res.json())
                .then(data => {
                    let sucursalSelect = document.getElementById("sucursal");
                    sucursalSelect.innerHTML = "<option value=''>Seleccione...</option>";
                    data.forEach(s => {
                        sucursalSelect.innerHTML += `<option value="${s.id}">${s.nombre}</option>`;
                    });
                });
        });

        // Fecha mínima
        document.getElementById("fecha").setAttribute("min", new Date().toISOString().split("T")[0]);

        showStep(currentStep);
        actualizarWizard(currentStep);
    });

    // FUNCIONES GLOBALES

    //funcion donde se debe de considerar anden o hora (no puede existir si el proveedor elimino lagun vehiculo  datos fantasmas(old))
    function validarDuplicadosHorarioYAnden() {
    const combinaciones = new Set();
    let conflictos = [];
    let repetido = false;

    // Limpiar clases de error previas
    document.querySelectorAll(".anden, .hora").forEach(el => el.classList.remove("is-invalid"));

    // Buscar todos los bloques de vehículo
    const bloques = document.querySelectorAll(".vehiculo-block");

    bloques.forEach((bloque, index) => {
        const i = index + 1;

        const anden = document.getElementById(`anden_${i}`);
        const hora = document.getElementById(`hora_${i}`);

        // Validar si existen los elementos
        if (!anden || !hora) return;

        const clave = `${anden.value}-${hora.value}`;

        if (combinaciones.has(clave)) {
            conflictos.push(i);
            repetido = true;
        } else {
            combinaciones.add(clave);
        }
    });

    // Si hay conflicto, marcar y mostrar alerta
    if (repetido) {
        conflictos.forEach(i => {
            const anden = document.getElementById(`anden_${i}`);
            const hora = document.getElementById(`hora_${i}`);

            if (anden) anden.classList.add('is-invalid');
            if (hora) hora.classList.add('is-invalid');
        });

        Swal.fire({
            icon: 'warning',
            title: 'Duplicado detectado',
            text: 'No puedes seleccionar el mismo andén y la misma hora para más de un vehículo.',
            confirmButtonColor: '#ee7826'
        });
        return false;
    }
    return true;
}

function validarCamposPaso(paso) {
    const pasoActual = document.querySelector(`.step-${paso}`);
    const inputs = pasoActual.querySelectorAll("select, input");
    let esValido = true;

    inputs.forEach(input => {
        // Saltar si está oculto o si es un input de folio
        if (input.offsetParent === null || input.name?.startsWith("folio_factura_")) return;

        if (!input.value.trim()) {
            input.classList.add("is-invalid");
            esValido = false;
        } else {
            input.classList.remove("is-invalid");
        }
    });

    if (!esValido) {
        Swal.fire({
            icon: 'warning',
            title: 'Campos obligatorios',
            text: 'Por favor completa todos los campos antes de continuar.',
            confirmButtonColor: '#ee7826'
        });
    }

    return esValido;
}



    const tipoUsuario = "{{ AuthHelper::tipoUsuario() }}"; 

    // -- Agregar evento al botón de buscar solo si existe
    const botonBuscarProveedor = document.getElementById("buscarProveedor");
    if (botonBuscarProveedor) {
        botonBuscarProveedor.addEventListener("click", buscarProveedor);
    }

    function actualizarCampo(idCampo, valor) {
        const campo = document.getElementById(idCampo);
        if (campo) {
            campo.value = valor;
        }
    }

    // -- Agregar evento de input en el campo idUser
    let timerBuscarProveedor;
    document.getElementById("idUser").addEventListener("input", function () {
        clearTimeout(timerBuscarProveedor);

        const codigo = this.value.trim();

        if (codigo.length >= 6) {
            timerBuscarProveedor = setTimeout(() => {
                buscarProveedor();
            }, 600);
        }
    });

    // -- Al cargar la página automáticamente buscar proveedor si ya tiene ID cargado
    document.addEventListener("DOMContentLoaded", function() {
        const idUserInput = document.getElementById("idUser");

        if (tipoUsuario === 'proveedor' && idUserInput && idUserInput.value.trim() !== '') {
            buscarProveedor();
        }
    });

// -- FUNCION PRINCIPAL buscarProveedor
function buscarProveedor() {
    const codigoProveedor = document.getElementById("idUser").value.trim();

    if (!codigoProveedor) {
        Swal.fire({
            icon: 'warning',
            title: 'Campo requerido',
            text: 'Ingrese un código de proveedor válido.',
            confirmButtonColor: '#ee7826'
        });
        return;
    }

    fetch(`/proveedor/datos/${codigoProveedor}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                Swal.fire("Proveedor no encontrado", "", "warning");
                actualizarCampo("labelNombreProveedor", "No disponible");
                actualizarCampo("labelCelular", "No disponible");
                actualizarCampo("labelCorreo", "No disponible");
                actualizarCampo("labelNombreContacto", "No disponible");
                actualizarCampo("labelCelularContacto", "No disponible");
                actualizarCampo("labelCorreoContacto", "No disponible");
            } else {
                actualizarCampo("labelNombreProveedor", data.Nombre_Proveedor || "No disponible");
                actualizarCampo("labelCelular", data.Celular || "No registrado");
                actualizarCampo("labelCorreo", data.Correo_Electronico || "No registrado");
                actualizarCampo("labelNombreContacto", data.Nombre_Contacto || "No disponible");
                actualizarCampo("labelCelularContacto", data.Celular_Contacto || "No registrado");
                actualizarCampo("labelCorreoContacto", data.Correo_Contacto || "No registrado");

                // Desplazar suavemente hacia los datos cargados
                document.getElementById("labelNombreProveedor").scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        })
        .catch(error => {
            console.error("Error al obtener datos del proveedor:", error);
            Swal.fire("Error al consultar el proveedor", "", "error");
        });
}


document.addEventListener("DOMContentLoaded", function () {
    let ordenCompraInput = document.getElementById("ordenCompraInput");
    let ordenCompraList = document.getElementById("ordenCompraList");
    let selectedOrdenesContainer = document.getElementById("selectedOrdenes");
    let resumenOrdenes = document.getElementById("resumen_ordenes");

    // Cargar órdenes de compra al seleccionar un proveedor y un input para teclar el folio 
    function cargarOrdenes(codigoProveedor) {
    const sucursalId = document.getElementById("sucursal").value;

    if (!codigoProveedor || !sucursalId) {
        console.warn("Proveedor o sucursal no definidos para cargar órdenes.");
        return;
    }

    fetch("/proveedor/ordenes", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
        },
        body: JSON.stringify({
            codigoProveedor: codigoProveedor,
            entidad_id: sucursalId
        })
    })
    .then(response => {
        if (!response.ok) throw new Error("Error al obtener datos del servidor");
        return response.json();
    })
    .then(data => {
        selectedOrdenes = []; 
        ordenCompraList.innerHTML = "";

        data.forEach(orden => {
            let listItem = document.createElement("li");
            listItem.innerHTML = `
                <label class="dropdown-item">
                    <input type="checkbox" class="form-check-input me-2" value="${orden.NumeroOrdenCompra}">
                    ${orden.NumeroOrdenCompra}
                </label>
            `;
            ordenCompraList.appendChild(listItem);
        });

        ordenCompraList.querySelectorAll("input[type='checkbox']").forEach(checkbox => {
            checkbox.addEventListener("change", function () {
                if (this.checked) {
                    selectedOrdenes.push(this.value);
                } else {
                    selectedOrdenes = selectedOrdenes.filter(item => item !== this.value);
                }
                actualizarSeleccion();
            });
        });
    })
    .catch(error => {
        console.error("Error al obtener órdenes de compra:", error);
        resumenOrdenes.textContent = "Error al cargar órdenes.";
    });
}

    function verificarYcargarOrdenes() {
    const codigoProveedor = document.getElementById("idUser").value.trim();
    const sucursalId = document.getElementById("sucursal").value;

    if (codigoProveedor !== "" && sucursalId !== "") {
        cargarOrdenes(codigoProveedor);
    }
    }
    function actualizarSeleccion() {
        selectedOrdenesContainer.innerHTML = "";
        ordenCompraInput.value = JSON.stringify(selectedOrdenes);
        resumenOrdenes.textContent = selectedOrdenes.length > 0 ? selectedOrdenes.join(", ") : "No seleccionadas";

        const foliosContainer = document.getElementById("foliosContainer");
    foliosContainer.innerHTML = "";

    selectedOrdenes.forEach(orden => {
        const div = document.createElement("div");
        div.classList.add("mb-2");
        div.innerHTML = `
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <label class="form-label m-0" style="min-width: 160px;">Orden de Compra ${orden}:</label>
            <input type="text" class="form-control flex-grow-1" name="folio_factura_${orden}" placeholder="Escribe el folio de factura" style="max-width: 300px;">
        </div>
    `;
        foliosContainer.appendChild(div);
    });

        selectedOrdenes.forEach(orden => {
            let tag = document.createElement("span");
            tag.classList.add("selected-tag");
            tag.innerHTML = `OC #${orden} <span class="remove-tag" data-id="${orden}">&times;</span>`;
            selectedOrdenesContainer.appendChild(tag);
        });

        document.querySelectorAll(".remove-tag").forEach(btn => {
            btn.addEventListener("click", function () {
                let id = this.getAttribute("data-id");
                selectedOrdenes = selectedOrdenes.filter(item => item !== id);
                const checkbox = ordenCompraList.querySelector(`input[value='${id}']`);
                if (checkbox) checkbox.checked = false;
                actualizarSeleccion();
            });
        });
    }
    document.getElementById("idUser").addEventListener("change", verificarYcargarOrdenes);
    document.getElementById("sucursal").addEventListener("change", verificarYcargarOrdenes);
});

    //nueva funcion para contendor agregar vehiculo 
    let contadorVehiculos = 1;
    
    // Agrega formulario dinámico para cada vehículo
    function agregarFormularioVehiculo() {
    const container = document.getElementById("vehiculosContainer");
    const indiceActual = contadorVehiculos; 

    const div = document.createElement("div");
    div.classList.add("vehiculo-block", "mb-4", "vehiculo-slide-in");
    div.setAttribute("data-id", indiceActual);

    div.innerHTML = `
        <h5>Datos del Vehículo de Entrega ${indiceActual}</h5>
        <button type="button" class="btn-close btnEliminarVehiculo position-absolute top-0 end-0 m-2" data-index="${indiceActual}" aria-label="Eliminar" title="Eliminar vehículo"></button>

        <div class="mb-3">
            <label for="transporte_${indiceActual}" class="form-label">Tipo de Vehiculo</label>
            <select class="form-select custom-select transporte" data-index="${indiceActual}" id="transporte_${indiceActual}" required>
                <option value="">Seleccione...</option>
                @foreach($transportes as $t)
                    <option value="{{ $t->id }}">{{ $t->tipo }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="anden_${indiceActual}" class="form-label">Seleccionar Andén</label>
            <select class="form-select custom-select anden" data-index="${indiceActual}" id="anden_${indiceActual}" required>
                <option value="">Seleccione...</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="hora_${indiceActual}" class="form-label">Horario Disponible</label>
            <select class="form-select custom-select hora" id="hora_${indiceActual}" required>
                <option value="">Seleccione...</option>
            </select>
        </div>

        <!-- ¿Llevará Estibador? -->
<div class="mb-3">
    <label class="form-label d-block">Extras: </label>

    <div class="form-check form-check-inline">
        <input class="form-check-input lleva-macheteros" type="radio" name="lleva_macheteros_${indiceActual}" id="lleva_macheteros_si_${indiceActual}" value="1">
        <label class="form-check-label" for="lleva_macheteros_si_${indiceActual}">Estibador</label>
    </div>

    <div class="form-check form-check-inline">
        <input class="form-check-input lleva-macheteros" type="radio" name="lleva_macheteros_${indiceActual}" id="lleva_macheteros_no_${indiceActual}" value="0">
        <label class="form-check-label" for="lleva_macheteros_no_${indiceActual}">Maniobra</label>
    </div>

    <div class="form-check form-check-inline">
        <input class="form-check-input lleva-macheteros" type="radio" name="lleva_macheteros_${indiceActual}" id="lleva_macheteros_na_${indiceActual}" value="na">
        <label class="form-check-label" for="lleva_macheteros_na_${indiceActual}">Ninguno </label>
    </div>
</div>

<!-- Grupo SI: Cantidad macheteros -->
<div class="mb-3 campo-macheteros-si d-none" id="campo_macheteros_si_${indiceActual}">
    <label for="descripcion_${indiceActual}" class="form-label">¿Cuántos estibadores llegarán?</label>
    <input type="number" class="form-control descripcion" name="descripcion_${indiceActual}" id="descripcion_${indiceActual}" min="1" step="1" placeholder="Ej. 2">
</div>

<!-- Grupo NO: Monto maniobra -->
<div class="mb-3 campo-macheteros-no d-none" id="campo_macheteros_no_${indiceActual}">
    <label class="form-label" for="monto_maniobra_${indiceActual}">Monto total de maniobra ($)</label>
    <div class="input-group">
        <span class="input-group-text">$</span>
        <input type="number" step="0.01" min="0" class="form-control monto-maniobra" name="monto_maniobra_${indiceActual}" id="monto_maniobra_${indiceActual}" placeholder="Ej. 250.00">
    </div>
</div>
`;

    container.appendChild(div);
    verificarFormularioVehiculoCompleto(indiceActual);


    const macheteroSelect = document.getElementById(`lleva_macheteros_${indiceActual}`);
    if (macheteroSelect) {
        macheteroSelect.addEventListener("change", function () {
            const campo = document.getElementById(`campo_macheteros_${indiceActual}`);
            if (campo) {
                if (this.value === "Sí") {
                    campo.classList.remove("d-none");
                } else {
                    campo.classList.add("d-none");
                    campo.querySelector("textarea").value = "";
                    campo.querySelector("input").value = "";
                }
            }
        });
    }

    document.querySelectorAll(`input[name="lleva_macheteros_${indiceActual}"]`).forEach(radio => {
    radio.addEventListener("change", function () {
        const campoSi = document.getElementById(`campo_macheteros_si_${indiceActual}`);
        const campoNo = document.getElementById(`campo_macheteros_no_${indiceActual}`);

        if (this.value === "1") {
            campoSi.classList.remove("d-none");
            campoNo.classList.add("d-none");
            document.getElementById(`monto_maniobra_${indiceActual}`).value = "";
        } else if (this.value === "0") {
            campoSi.classList.add("d-none");
            campoNo.classList.remove("d-none");
            document.getElementById(`descripcion_${indiceActual}`).value = "";
        } else {
            // "Ninguna de las anteriores"
            campoSi.classList.add("d-none");
            campoNo.classList.add("d-none");
            document.getElementById(`descripcion_${indiceActual}`).value = "";
            document.getElementById(`monto_maniobra_${indiceActual}`).value = "";
        }
    });
});



document.addEventListener('DOMContentLoaded', function () {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});



    asignarEventosCambioTransporteYAnden(indiceActual);
    cargarAndenes(indiceActual);

    contadorVehiculos++;
    document.getElementById("btnAgregarVehiculo").style.display = "none";

    }

    // Eliminar vehículo individualmente con validación de mínimo 1
    document.addEventListener("click", function(e) {
        if (e.target.classList.contains("btnEliminarVehiculo") || e.target.closest(".btnEliminarVehiculo")) {
            const totalVehiculos = document.querySelectorAll(".vehiculo-block").length;
            if (totalVehiculos <= 1) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No permitido',
                    text: 'Debes tener al menos un vehículo en la cita.',
                    confirmButtonColor: '#ee7826'
                });
                return;
            }

            const index = e.target.dataset.index || e.target.closest(".btnEliminarVehiculo").dataset.index;
            eliminarVehiculo(index);
        }
    });

    function verificarFormularioVehiculoCompleto(index) {
    const transporte = document.getElementById(`transporte_${index}`);
    const anden = document.getElementById(`anden_${index}`);
    const hora = document.getElementById(`hora_${index}`);
    const btnAgregar = document.getElementById("btnAgregarVehiculo");

    function validarCampos() {
        const transporteValido = transporte && transporte.value.trim() !== "";
        const andenValido = anden && anden.value.trim() !== "";
        const horaValida = hora && hora.value.trim() !== "";

        // Habilita el botón si todo está completo
        if (transporteValido && andenValido && horaValida) {
            btnAgregar.disabled = false;
            btnAgregar.style.display = 'inline-block';
        } else {
            btnAgregar.disabled = true;
            btnAgregar.style.display = 'none';
        }
    }

    // Validar al cambiar los selects
    if (transporte) transporte.addEventListener("change", validarCampos);
    if (anden) anden.addEventListener("change", validarCampos);
    if (hora) hora.addEventListener("change", validarCampos);
    }


    function eliminarVehiculo(index) {
        const div = document.querySelector(`.vehiculo-block[data-id="${index}"]`);
        if (div) {
            div.remove();
            reordenarTitulosVehiculos();
        }
    }

    function convertirMinutos(horaStr) {
    const [h, m] = horaStr.split(":").map(Number);
    return h * 60 + m;    
    }

    const transporteDuracionMinutos = {
        2: 30,   // Camioneta
        3: 150,  // Tráiler
        5: 90    // Rabón
    };
 

    // Reordena títulos y atributos
    function reordenarTitulosVehiculos() {
        const bloques = document.querySelectorAll(".vehiculo-block");
        let nuevoIndice = 1;
    
        bloques.forEach(block => {
            block.setAttribute("data-id", nuevoIndice);
    
            const titulo = block.querySelector("h5");
            if (titulo) titulo.textContent = `Vehículo ${nuevoIndice}`;
    
            const transporte = block.querySelector("[id^='transporte_']");
            const anden = block.querySelector("[id^='anden_']");
            const hora = block.querySelector("[id^='hora_']");
            const descripcion = block.querySelector("textarea");
    
            if (transporte) {
                transporte.id = `transporte_${nuevoIndice}`;
                transporte.setAttribute("data-index", nuevoIndice);
            }
            if (anden) {
                anden.id = `anden_${nuevoIndice}`;
                anden.setAttribute("data-index", nuevoIndice);
            }
            if (hora) hora.id = `hora_${nuevoIndice}`;
            if (descripcion) descripcion.name = `descripcion_${nuevoIndice}`;
    
            nuevoIndice++;
        });
    
        contadorVehiculos = nuevoIndice;
    }

    function validarCamposMacheteros() {
    let valido = true;

    document.querySelectorAll(".vehiculo-block").forEach(block => {
        const index = block.getAttribute("data-id");
        const macheteroSi = document.getElementById(`lleva_macheteros_si_${index}`);
        const macheteroNo = document.getElementById(`lleva_macheteros_no_${index}`);
        const campoCantidad = document.getElementById(`descripcion_${index}`);
        const campoMonto = document.getElementById(`monto_maniobra_${index}`);

        // Limpiar estados previos
        campoCantidad?.classList.remove("is-invalid");
        campoMonto?.classList.remove("is-invalid");

        if (macheteroSi?.checked) {
            const valor = campoCantidad.value.trim();
            if (!/^[1-9]\d*$/.test(valor)) { // solo enteros > 0
                campoCantidad.classList.add("is-invalid");
                valido = false;
            }
        }

        if (macheteroNo?.checked) {
            const valor = parseFloat(campoMonto.value.trim());
            if (isNaN(valor) || valor < 1) {
                campoMonto.classList.add("is-invalid");
                valido = false;
            }
        }
    });

     if (!valido) {
        Swal.fire({
            icon: 'warning',
            title: 'Campos inválidos',
            text: 'Verifica que los Estibador sean números enteros positivos o el monto mínimo sea $1.',
            confirmButtonColor: '#ee7826'
        });
    }

    return valido;
}

    //Verificar que cada vehiculo tenga un horario seleecionado(no vacio) 
    function validarHorariosSeleccionadosDisponibles() {
    let validos = true;
    
    document.querySelectorAll(".hora").forEach(hora => {
        if (!hora.value) {
            hora.classList.add("is-invalid");
            validos = false;
        } else {
            hora.classList.remove("is-invalid");
        }
    });

    if (!validos) {
        Swal.fire({
            icon: 'warning',
            title: 'Faltan horarios',
            text: 'Todos los vehículos deben tener un horario seleccionado.',
            confirmButtonColor: '#ee7826',
        });
    }

    return validos;
}

    // Cargar andenes desde backend
    function cargarAndenes(index) {
        const sucursalId = document.getElementById("sucursal").value;
        if (!sucursalId) return;
    
        fetch(`/proveedor/obtener-andenes/${sucursalId}`)
            .then(res => res.json())
            .then(data => {
                const andenSelect = document.getElementById(`anden_${index}`);
                andenSelect.innerHTML = `<option value="">Seleccione...</option>`;
                data.forEach(a => {
                    andenSelect.innerHTML += `<option value="${a.id}">${a.nombre}</option>`;
                });
            });
    }

    function asignarEventosCambioTransporteYAnden(index) {
    const transporte = document.getElementById(`transporte_${index}`);
    const anden = document.getElementById(`anden_${index}`);
    const hora = document.getElementById(`hora_${index}`);

    if (transporte && anden) {
        const cargarHorarios = () => {
            const fecha = document.getElementById("fecha").value;
            const sucursalId = document.getElementById("sucursal").value;
            const transporteId = transporte.value;
            const andenId = anden.value;

            if (!fecha || !sucursalId || !transporteId || !andenId) return;

            fetch("/proveedor/citas/disponibilidad", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    sucursal_id: sucursalId,
                    fecha: fecha,
                    transporte_id: transporteId,
                    anden_id: andenId
                })
            })
            .then(res => res.json())
            .then(data => {
                const horariosFiltrados = data.filter(item => {
                    if (item.estado !== 'Disponible') return false;

                    const horaActual = item.horario;
                    const andenActual = andenId;
                    const duracionActual = transporteDuracionMinutos[parseInt(transporteId)];

                    let esSolapado = false;

                    document.querySelectorAll(".vehiculo-block").forEach(block => {
                        const id = block.getAttribute("data-id");
                        if (parseInt(id) === index) return;

                        const otroAnden = document.getElementById(`anden_${id}`)?.value;
                        const otraHora = document.getElementById(`hora_${id}`)?.value;
                        const otroTransporte = document.getElementById(`transporte_${id}`)?.value;

                        if (otroAnden === andenActual && otraHora && otroTransporte) {
                            const duracionOtra = transporteDuracionMinutos[parseInt(otroTransporte)];

                            const inicioA = convertirMinutos(otraHora);
                            const finA = inicioA + duracionOtra;
                            const inicioB = convertirMinutos(horaActual);
                            const finB = inicioB + duracionActual;

                            if ((inicioB < finA) && (inicioA < finB)) {
                                esSolapado = true;
                            }
                        }
                    });

                    return !esSolapado;
                });

                hora.innerHTML = '<option value="">Seleccione el horario</option>';
                horariosFiltrados.forEach(item => {
                    const horaRaw = item.horario.split(":");
                    const hora12 = `${(horaRaw[0] % 12) || 12}:${horaRaw[1]} ${horaRaw[0] >= 12 ? 'PM' : 'AM'}`;
                    hora.innerHTML += `<option value="${item.horario}">${hora12}</option>`;
                });

                if (hora.options.length === 1) {
                    hora.innerHTML = '<option value="">Sin horarios disponibles</option>';
                }
            });
        };

        // Escucha cambios
        transporte.addEventListener("change", cargarHorarios);
        anden.addEventListener("change", cargarHorarios);
    }
}

 
    flatpickr("#fecha", {
        dateFormat: "Y-m-d",
        minDate: "today",
        locale: {
            firstDayOfWeek: 0,
            weekdays: {
                shorthand: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
                longhand: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
            },
            months: {
                shorthand: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
                longhand: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio','Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
            }
        },
        disable: [
            function(date) {
            // Deshabilitar domingos
            return (date.getDay() === 0);
            }
        ]
    });

    document.getElementById("fecha").addEventListener("change", function () {
        let fechaSeleccionada = this.value;
        let sucursalId = document.getElementById("sucursal").value;

        if (!sucursalId) {
            Swal.fire({
                icon: 'warning',
                title: 'Sucursal no seleccionada',
                text: 'Por favor, selecciona primero una sucursal antes de elegir la fecha.',
                confirmButtonColor: '#ee7826'
            });
            this.value = "";
            return;
        }

        //  Eliminar todos los formularios de vehículos
        document.getElementById("vehiculosContainer").innerHTML = "";

        //  Reiniciar contador
        contadorVehiculos = 1;

        //  Agregar uno nuevo por defecto
        agregarFormularioVehiculo();

        //  Validar automáticamente el primer formulario
        verificarFormularioVehiculoCompleto(1);
    });
  


// Cargar serie de OC al seleccionar sucursal
let serieOrdenCompra = "";
document.getElementById("sucursal").addEventListener("change", function () {
    let sucursalId = this.value;

    // Cargar la serie OC (ej: ZO, ZC...)
    fetch(`/proveedor/serie-oc/${sucursalId}`)
        .then(response => response.json())
        .then(data => {
            serieOrdenCompra = data.serie_oc;
            console.log("Serie OC de esta sucursal:", serieOrdenCompra);
        });
});

    // Guardar cita
    function convertirHoraFormatoSQL(hora12) {
        const [horaMin, meridiano] = hora12.trim().split(' ');
        let [hora, minutos] = horaMin.split(':');
        hora = parseInt(hora);
        if (meridiano === 'PM' && hora < 12) hora += 12;
        if (meridiano === 'AM' && hora === 12) hora = 0;
        return `${hora.toString().padStart(2, '0')}:${minutos}:00`;
    }
    
    document.getElementById("btnGuardarCita").addEventListener("click", function () {
        let selectedOrdenes = JSON.parse(document.getElementById("ordenCompraInput").value || "[]");

        if (!validarHorariosSeleccionadosDisponibles()) return;

        let foliosFactura = {};
        selectedOrdenes.forEach(orden => {
            const input = document.querySelector(`[name='folio_factura_${orden}']`);
            if (input) {
                foliosFactura[orden] = input.value.trim();
            }
        });
    
        const numVehiculos = document.querySelectorAll(".vehiculo-block").length;
        let vehiculos = [];
    
        //  PRE-CHEQUEO: limpiar valores de hora que ya no existen
        for (let i = 1; i <= numVehiculos; i++) {
            const horaSelect = document.getElementById(`hora_${i}`);
            const horaVal = horaSelect.value;
    
            const optionExists = [...horaSelect.options].some(opt => opt.value === horaVal);
            if (!optionExists) {
                horaSelect.value = "";
            }
        }
    
        for (let i = 1; i <= numVehiculos; i++) {
        const andenVal = document.getElementById(`anden_${i}`).value;
        const horaVal = document.getElementById(`hora_${i}`).value;
        const transporteVal = document.getElementById(`transporte_${i}`).value;
        const descripcionVal = document.querySelector(`[name='descripcion_${i}']`)?.value || "";
        const llevaMacheterosVal = document.getElementById(`lleva_macheteros_${i}`)?.value === "Sí" ? 1 : 0;
        const montoVal = parseFloat(document.getElementById(`monto_maniobra_${i}`)?.value) || null;

        vehiculos.push({
            anden_id: andenVal,
            transporte_id: transporteVal,
            hora: convertirHoraFormatoSQL(horaVal),
            descripcion: descripcionVal,
            lleva_macheteros: llevaMacheterosVal,
            monto_maniobra: montoVal
        });
    }
    
        const data = {
            sucursal_id: document.getElementById("sucursal").value,
            fecha: document.getElementById("fecha").value,
            idUser: document.getElementById("idUser").value,
            proveedor_id: document.getElementById("idUser").value,
            //orden_compra: JSON.parse(document.getElementById("ordenCompraInput").value || "[]"),
            orden_compra:selectedOrdenes,
            folios_factura: foliosFactura,
            vehiculos: vehiculos
        };

        // Mostrar datos en consola para depuración
        console.log("🚛 Datos a enviar:", data);

    
        fetch('/proveedor/citas', {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(response => {
            console.log("RESPUESTA DEL BACK:", response);
            if (response.success) {
                Swal.fire("Éxito", response.message, "success");
            } else {
                Swal.fire("Error", response.message, "error");
            }
        })
        .catch(error => {
            console.error("Error al guardar cita:", error);
            Swal.fire("Error", "No se pudo guardar la cita", "error");
        });
    });

    function llenarResumenCompleto() {
        console.log("Resumen completado"); 
    
        // 1 Datos Generales
        document.getElementById("resumen_idUser").textContent = document.getElementById("idUser").value || "N/A";
        // Ciudad
        let ciudadSelect = document.getElementById("ciudad");
        document.getElementById("resumen_ciudad").textContent = ciudadSelect.options[ciudadSelect.selectedIndex]?.text || "N/A";
        // Sucursal
        let sucursalSelect = document.getElementById("sucursal");
        document.getElementById("resumen_sucursal").textContent = sucursalSelect.options[sucursalSelect.selectedIndex]?.text || "N/A";
        // Fecha
        const fechaCruda = document.getElementById("fecha").value;
        if (fechaCruda) {
            const fechaObj = new Date(fechaCruda + "T00:00:00");
            const fechaFormateada = new Intl.DateTimeFormat('es-MX', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            }).format(fechaObj);
            document.getElementById("resumen_fecha").textContent = fechaFormateada;
        } else {
            document.getElementById("resumen_fecha").textContent = "N/A";
        }
    
        // Celular y Correo
        document.getElementById("resumen_celular").textContent = document.getElementById("labelCelular").value || "N/A";
        document.getElementById("resumen_correo").textContent = document.getElementById("labelCorreo").value || "N/A";
    
        // Vehículos
        const tablaResumen = document.getElementById("tablaVehiculosResumen");
        tablaResumen.innerHTML = ""; 
        const numVehiculos = document.querySelectorAll(".vehiculo-block").length;
    
        for (let i = 1; i <= numVehiculos; i++) {
        const andenSelect = document.getElementById(`anden_${i}`);
        const transporteSelect = document.getElementById(`transporte_${i}`);
        const horaSelect = document.getElementById(`hora_${i}`);
        const macheteroSi = document.getElementById(`lleva_macheteros_si_${i}`);
        const macheteroNo = document.getElementById(`lleva_macheteros_no_${i}`);
        
        const macheteroText = macheteroSi?.checked ? 
            (document.getElementById(`descripcion_${i}`)?.value || "Sin dato") : "No aplica";

        const montoVal = document.getElementById(`monto_maniobra_${i}`)?.value;
        const montoText = macheteroNo?.checked && montoVal ?
            `$${parseFloat(montoVal).toFixed(2)}` : "No aplica";

        const andenText = andenSelect?.options[andenSelect.selectedIndex]?.text || "N/A";
        const transporteText = transporteSelect?.options[transporteSelect.selectedIndex]?.text || "N/A";

        const horaRaw = horaSelect?.value || "N/A";
        let horaFormateada = horaRaw;
        if (horaRaw.includes(":")) {
            const [h, m] = horaRaw.split(":");
            const horaNum = parseInt(h);
            const ampm = horaNum >= 12 ? 'PM' : 'AM';
            const hora12 = ((horaNum % 12) || 12);
            horaFormateada = `${hora12}:${m.padStart(2, '0')} ${ampm}`;
        }

    tablaResumen.innerHTML += `
        <tr>
            <td>${i}</td>
            <td>${andenText}</td>
            <td>${transporteText}</td>
            <td>${horaFormateada}</td>
            <td>${macheteroText}</td>
            <td>${montoText}</td>
        </tr>
    `;
}    
        // Tabla de ordenes con folio
        const resumenFolios = document.getElementById("tablaFoliosResumen");
        resumenFolios.innerHTML = "";
        selectedOrdenes.forEach((orden, index) => {
            const input = document.querySelector(`[name='folio_factura_${orden}']`);
            const folio = input ? input.value : "Sin folio";
            resumenFolios.innerHTML += `
                <tr>
                    <td>${index + 1}</td>
                    <td>${orden}</td>
                    <td>${folio}</td>
                </tr>
            `;
        });
    }
    
    //Script para activar inputs y enviar alerta
    document.querySelectorAll('.campo-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const input = document.getElementById(`input_${this.value}`);
            if (this.checked) {
                input.classList.remove('d-none');
                input.required = true;
            } else {
                input.classList.add('d-none');
                input.required = false;
                input.value = '';
            }
        });
    });

    const etiquetasCampos = {
        "proveedor": "Nombre del Proveedor",
        "celular": "Número de Celular",
        "correo": "Correo Electrónico",
        "contacto": "Nombre del Contacto",
        "celular_contacto": "Celular del Contacto",
        "correo_contacto": "Correo del Contacto"
        };

    const regexValidaciones = {
    proveedor: /^[a-zA-ZÁÉÍÓÚáéíóúñÑ\s]{3,50}$/, 
    celular: /^[0-9]{10,12}$/,
    correo: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
    contacto: /^[a-zA-ZÁÉÍÓÚáéíóúñÑ\s]{3,50}$/,
    celular_contacto: /^[0-9]{10,12}$/,
    correo_contacto: /^[^\s@]+@[^\s@]+\.[^\s@]+$/
    };

    // Luego, la función ya no necesita redefinirlo
    function validarCampo(campo, valor) {

        return regexValidaciones[campo]?.test(valor) ?? true;
    }

    function validarCamposActualizacionModal() {
    const errores = [];
    let esValido = true;

    document.querySelectorAll('.campo-checkbox:checked').forEach(cb => {
        const campo = cb.value; 
        const input = document.getElementById(`input_${campo}`);
        const valor = input.value.trim();
        const etiqueta = etiquetasCampos[campo];

        input.classList.remove('is-invalid');

        if (!valor) {
            errores.push(`• ${etiqueta}: Este campo no puede estar vacío.`);
            input.classList.add('is-invalid');
            esValido = false;
            return;
        }

        if (!validarCampo(etiqueta, valor)) {
            let msg = "";

            switch (etiqueta) {
                case "Número de Celular":
                case "Celular del Contacto":
                    msg = "Debe contener solo números y tener entre 10 y 12 dígitos.";
                    break;
                case "Correo Electrónico":
                case "Correo del Contacto":
                    msg = "Debe tener un formato válido, por ejemplo: correo@dominio.com.";
                    break;
                case "Nombre del Proveedor":
                case "Nombre del Contacto":
                    msg = "Solo letras y espacios, de 3 a 50 caracteres.";
                    break;
                default:
                    msg = "Formato no válido.";
            }
            errores.push(`• ${etiqueta}: ${msg}`);
            input.classList.add('is-invalid');
            esValido = false;
        }
    });

    if (!esValido) {
        Swal.fire({
            title: "Datos inválidos",
            html: errores.join("<br>"),
            icon: "warning"
        });
    }
    return esValido;
}

function enviarSolicitudActualizacion() {
    // VALIDACIÓN antes de enviar solicitud de cambio de datos 
    if (!validarCamposActualizacionModal()) return;
    const camposSeleccionados = [];
    document.querySelectorAll('.campo-checkbox:checked').forEach(cb => {
        const campo = cb.value;
        const nuevoValor = document.getElementById(`input_${campo}`).value.trim();
        if (nuevoValor) {
            camposSeleccionados.push(`${campo}: ${nuevoValor}`);
        }
    });

    if (camposSeleccionados.length === 0) {
        Swal.fire("Advertencia", "Debes seleccionar al menos un campo e ingresar el nuevo valor.", "warning");
        return;
    }

    const codigo = document.getElementById("idUser").value;

    fetch("/proveedor/enviar-solicitud", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            codigoProveedor: codigo,
            campos: camposSeleccionados
        })
    })
    .then(res => res.json())
    .then(response => {
        if (response.success) {
            Swal.fire("Enviado", "Tu solicitud ha sido enviada por correo electrónico.", "success").then(() => {
                location.reload();
            });
        } else {
            Swal.fire("Error", "Hubo un problema al enviar la solicitud.", "error");
        }

        // Cerrar modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('modalActualizarDatos'));
        modal.hide();

        // Resetear formulario
        document.getElementById("formSolicitudActualizacion").reset();
        document.querySelectorAll('.campo-input').forEach(i => i.classList.add('d-none'));
    })
    .catch(error => {
        console.error("Error al enviar correo:", error);
        Swal.fire("Error", "No se pudo enviar la solicitud", "error");
    });
}
