@extends('layouts.movil')
@section('title', 'Cita No Programada')
@section('content')
    @include('includes.scripts.SweetAlert2')
    @include('includes.scripts.flatpickr')
    @include('includes.scripts.bootstrap')

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <x-sidebar />

    <!-- Enlace a la hoja de estilos -->
    <link rel="stylesheet" href="{{ asset('assets/css/rol/almacen/cita-no-programada.css') }}">
    <!-- Bootstrap JS Bundle (incluye Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous">
    </script>

    <div class="container-fluid con-sidebar">
        <div class="container">
            <form id="citaForm" method="POST" action="{{ route('almacen.citas.store') }}">
                @csrf
                <h4 class="titulo-paso"><i class="fas fa-user"></i> Ingresa el Codigo del proveedor</h4>
                @php
                    use App\Helpers\AuthHelper;
                    $tipoUsuario = AuthHelper::tipoUsuario();
                    $codigoProveedor = session('Proveedor.CardCode', '');
                @endphp

                <div class="mb-3">
                    <label for="idUser" class="form-label">Código del Proveedor:</label>
                    <div class="input-group">
                        <input type="text" id="idUser" name="idUser" class="form-control custom-input"
                            value="{{ old('idUser', $tipoUsuario === 'proveedor' ? $codigoProveedor : '') }}"
                            {{ $tipoUsuario === 'proveedor' ? 'readonly' : '' }} required>
                        @if ($tipoUsuario !== 'proveedor')
                            <button type="button" id="buscarProveedor" class="btn btn-primary">Buscar <i
                                    class="fas fa-search"></i></button>
                        @endif
                    </div>
                </div>
                <!-- Información del Proveedor -->
                <div class="form-floating mb-3">
                    <input type="text" class="form-control bg-light" id="labelNombreProveedor"
                        placeholder="Nombre del Proveedor" readonly disabled>
                    <label for="labelNombreProveedor">Nombre del Proveedor</label>
                </div>

                <h4 class="titulo-paso"><i class="fas fa-map-marker-alt"></i> Selecciona Ciudad y Sucursal</h4>

                <div class="mb-3">
                    <label for="ciudad" class="form-label">Ciudad</label>
                    <select id="ciudad" name="ciudad" class="form-select custom-select" required>
                        <option value="">Seleccione...</option>
                        @foreach ($ciudades as $ciudad)
                            <option value="{{ $ciudad->id }}">{{ $ciudad->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="sucursal" class="form-label">Sucursal</label>
                    <select id="sucursal" name="sucursal_id" class="form-select custom-select" required>
                        <option value="">Seleccione...</option>
                    </select>
                </div>

                <h4 class="titulo-paso"><i class="fas fa-calendar-alt"></i> Seleccionar La fecha y hora</h4>
                <!-- Fecha -->
                <div class="mb-3">
                    <label for="fecha" class="form-label">Fecha</label>
                    <input type="text" id="fecha" name="fecha" class="form-control custom-input" required>
                </div>
                <!-- Contenedor dinámico de vehículos -->
                <div id="vehiculosContainer"></div>

                <!-- Botón para agregar más vehículos -->
                <div class="text-end mb-3">
                    <button type="button" class="btn btn-outline-success" id="btnAgregarVehiculo" style="display: none;"
                        onclick="agregarFormularioVehiculo()">
                        <i class="fas fa-plus"></i> Agregar otro vehículo
                    </button>
                </div>

                <h4 class="titulo-paso"><i class="fas fa-book"></i> Detalles de la Cita</h4>

                <label class="form-label">Órdenes de Compra Abiertas:</label>
                <div class="custom-multiselect">
                    <div class="selected-items-container" id="selectedOrdenes"> </div>
                    <button class="btn btn-light border dropdown-toggle w-100 text-start" type="button"
                        id="dropdownOrdenes" data-bs-toggle="dropdown" aria-expanded="false">
                        Seleccione órdenes de compra
                    </button>

                    <ul class="dropdown-menu w-100" id="ordenCompraList" style="max-height: 200px; overflow-y: auto;">

                        <!-- Opciones carga dinamicamente -->
                    </ul>
                </div>

                <input type="hidden" name="ordenCompra[]" id="ordenCompraInput">


                <label for="motivo_evento" class="form-label">Motivo de cita no programada</label>
                <textarea class="form-control" name="motivo_evento" id="motivo_evento" rows="3" required></textarea>

                <div class="mb-3">
                    <label for="evidencias" class="form-label">Evidencias (puedes subir varias imágenes)</label>
                    <input type="file" class="form-control" name="evidencias[]" id="evidencias" multiple
                        accept="image/*,application/pdf">
                </div>

                <!-- <button onclick="window.print()" class="btn btn-outline-dark"><i class="fas fa-print"></i> Imprimir PDF</button> --->
                <button type="button" class="btn btn-orange w-45" id="btnGuardarCita">Capturar Cita</button>
            </form>

        </div> {{-- Cierra row --}}
    </div> {{-- Cierra container-fluid --}}

    <script>
        let currentStep = 1;

        document.addEventListener("DOMContentLoaded", function() {
            const totalSteps = 5;
            const steps = document.querySelectorAll(".step");

            // Permitir hacer clic en los pasos anteriores para navegar rápidamente
            document.querySelectorAll(".step-item").forEach((item, index) => {
                item.addEventListener("click", function() {
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

            // Cargar sucursales por ciudad
            document.getElementById("ciudad").addEventListener("change", function() {
                fetch(`/proveedor/obtener-sucursales/${this.value}`)
                    .then(res => res.json())
                    .then(data => {
                        let sucursalSelect = document.getElementById("sucursal");
                        sucursalSelect.innerHTML = "<option value=''>Seleccione...</option>";
                        data.forEach(s => {
                            sucursalSelect.innerHTML +=
                                `<option value="${s.id}">${s.nombre}</option>`;
                        });
                    });
            });

            // Fecha mínima
            document.getElementById("fecha").setAttribute("min", new Date().toISOString().split("T")[0]);

            showStep(currentStep);
            actualizarWizard(currentStep);
        });

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
    </script>

    <script>
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
        document.getElementById("idUser").addEventListener("input", function() {
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

                    } else {
                        actualizarCampo("labelNombreProveedor", data.Nombre_Proveedor || "No disponible");

                        // Desplazar suavemente hacia los datos cargados
                        document.getElementById("labelNombreProveedor").scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                })
                .catch(error => {
                    console.error("Error al obtener datos del proveedor:", error);
                    Swal.fire("Error al consultar el proveedor", "", "error");
                });
        }
    </script>


    <script>
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
                @foreach ($transportes as $t)
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
            <label class="form-label">Horario Disponible</label>
            <div id="horas_${indiceActual}" class="d-flex flex-wrap gap-2 horas-container" data-index="${indiceActual}">
                <div class="text-muted">Seleccione andén/transporte/fecha…</div>
            </div>
            <input type="hidden" id="hora_${indiceActual}" class="hora" required>
            </div>
                `;

            container.appendChild(div);
            verificarFormularioVehiculoCompleto(indiceActual);

            document.addEventListener('DOMContentLoaded', function() {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.map(function(tooltipTriggerEl) {
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

        window.transporteDuracionMinutos = {
            2: 30, // Camioneta
            3: 150, // Tráiler
            5: 90, // Rabón
            8: 60 // Apartado interno (ajusta si debe ser 30/45/60)
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

            fetch(`/almacen/obtener-andenes/${sucursalId}`)
                .then(res => res.json())
                .then(data => {
                    const andenSelect = document.getElementById(`anden_${index}`);
                    andenSelect.innerHTML = `<option value="">Seleccione...</option>`;
                    data.forEach(a => {
                        andenSelect.innerHTML += `<option value="${a.id}">${a.nombre}</option>`;
                    });
                });
        }

        //Obligar al usuario seleccionar almenos una orden de compra.
        function validarOrdenesCompraSeleccionadas() {
            const ordenes = JSON.parse(document.getElementById("ordenCompraInput").value || "[]");
            const contenedor = document.getElementById("contenedorOrdenesCompra");

            if (ordenes.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Orden de Compra requerida',
                    text: 'Debes seleccionar al menos una orden de compra antes de continuar.',
                    confirmButtonColor: '#ee7826'
                });

                // Solo intenta modificar clases si el contenedor existe
                if (contenedor) {
                    contenedor.classList.add("border", "border-danger", "rounded");
                }
                return false;
            }

            if (contenedor) {
                contenedor.classList.remove("border", "border-danger", "rounded");
            }
            return true;
        }

        function formatearHora12(h24) {
            const base = (h24 || '').split('.')[0];
            const [H, M] = base.split(':').map(Number);
            const ampm = H >= 12 ? 'PM' : 'AM';
            const h12 = (H % 12) || 12;
            return `${h12}:${String(M).padStart(2,'0')} ${ampm}`;
        }

        function pintarHorasPills(index, lista) {
            const wrap = document.getElementById(`horas_${index}`);
            const hidden = document.getElementById(`hora_${index}`);
            wrap.innerHTML = '';

            const disponibles = (lista || []).filter(h => (h.estado || h.Estado) === 'Disponible');
            if (!disponibles.length) {
                wrap.innerHTML = `<div class="text-muted">Sin horarios disponibles.</div>`;
                hidden.value = '';
                return;
            }

            disponibles.forEach(h => {
                const hora24 = h.horario || h.Hora;
                const id = `v${index}_${hora24.replaceAll(':','_')}`;
                const label = formatearHora12(hora24);

                wrap.insertAdjacentHTML('beforeend', `
      <input type="checkbox" class="btn-check hora-check" id="${id}"
             data-index="${index}" value="${hora24}" autocomplete="off">
      <label class="btn btn-outline-secondary" for="${id}">${label}</label>
    `);
            });

            // comportamiento radio-like: solo 1 seleccionado por vehículo
            wrap.querySelectorAll('.hora-check').forEach(chk => {
                chk.addEventListener('change', (e) => {
                    const me = e.target;
                    const i = me.dataset.index;
                    const todos = wrap.querySelectorAll('.hora-check');
                    if (me.checked) {
                        todos.forEach(x => {
                            if (x !== me) x.checked = false;
                        });
                        hidden.value = me.value; // guarda la hora 24h
                    } else {
                        hidden.value = '';
                    }
                });
            });
        }

        function convertirMinutos(horaStr) {
            const base = (horaStr || '').split('.')[0]; // quita .000000
            const [h, m] = base.split(':').map(Number);
            return h * 60 + (m || 0);
        }

        function asignarEventosCambioTransporteYAnden(index) {
            const transporte = document.getElementById(`transporte_${index}`);
            const anden = document.getElementById(`anden_${index}`);
            const hiddenHora = document.getElementById(`hora_${index}`);
            const horasWrap = document.getElementById(`horas_${index}`);

            if (!transporte || !anden || !hiddenHora || !horasWrap) return;

            function limpiarSeleccion() {
                hiddenHora.value = '';
                horasWrap.innerHTML = `<div class="text-muted">Seleccione andén / transporte / fecha…</div>`;
            }

            function filtrarPorSolapes(listaCruda, miDuracionMin, miAndenId) {
                const ocupaciones = [];
                document.querySelectorAll('.vehiculo-block').forEach(block => {
                    const id = Number(block.getAttribute('data-id'));
                    if (id === index) return;

                    const andenOtro = document.getElementById(`anden_${id}`)?.value;
                    const horaOtro = document.getElementById(`hora_${id}`)?.value; // 24h
                    const transOtro = document.getElementById(`transporte_${id}`)?.value;

                    if (!andenOtro || !horaOtro || !transOtro) return;
                    if (String(andenOtro) !== String(miAndenId)) return;

                    const durOtra = Number(window.transporteDuracionMinutos[transOtro]) || 60;
                    const iniA = convertirMinutos(horaOtro);
                    const finA = iniA + durOtra;
                    ocupaciones.push([iniA, finA]);
                });

                return (listaCruda || []).filter(item => {
                    const estado = item.estado || item.Estado || 'Disponible';
                    if (estado !== 'Disponible') return false;

                    const hora24 = item.horario || item.Hora; // "HH:MM(:SS)"
                    const iniB = convertirMinutos(hora24);
                    const finB = iniB + miDuracionMin;

                    return !ocupaciones.some(([iniA, finA]) => (iniB < finA) && (iniA < finB));
                });
            }

            async function cargarHorarios() {
                const fecha = document.getElementById('fecha')?.value;
                const sucursalId = document.getElementById('sucursal')?.value;
                const transporteId = transporte.value;
                const andenId = anden.value;

                if (!fecha || !sucursalId || !transporteId || !andenId) {
                    limpiarSeleccion();
                    return;
                }

                horasWrap.innerHTML = `<div class="text-muted">Consultando disponibilidad…</div>`;
                hiddenHora.value = '';

                try {
                    const res = await fetch('/almacen/citas/disponibilidad', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            sucursal_id: sucursalId,
                            fecha: fecha,
                            transporte_id: transporteId,
                            anden_id: andenId
                        })
                    });

                    const data = await res.json();
                    const lista = Array.isArray(data) ? data : (data.horarios || []);

                    const miDuracionMin = Number(window.transporteDuracionMinutos[transporteId]) || 60;
                    const filtrados = filtrarPorSolapes(lista, miDuracionMin, andenId);

                    pintarHorasPills(index, filtrados);

                    if (!filtrados.length) {
                        horasWrap.innerHTML =
                            `<div class="text-muted">Sin horarios disponibles para estos filtros.</div>`;
                    }
                } catch (err) {
                    console.error(err);
                    horasWrap.innerHTML = `<div class="text-danger">Error al consultar disponibilidad.</div>`;
                }
            }

            // Disparadores
            transporte.addEventListener('change', cargarHorarios);
            anden.addEventListener('change', cargarHorarios);

            // Si ya hay fecha seleccionada cuando se crea el bloque, intenta cargar
            if (document.getElementById('fecha')?.value) {
                cargarHorarios();
            }
        }
    </script>

    <!--Carga automaticamente los ordenes de compras abiertos-->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const ordenCompraInput = document.getElementById("ordenCompraInput");
            const ordenCompraList = document.getElementById("ordenCompraList");
            const selectedOrdenesContainer = document.getElementById("selectedOrdenes");
            const dropdownOrdenes = document.getElementById("dropdownOrdenes");
            // OJO: puede que no exista; protegemos su uso
            const resumenOrdenes = document.getElementById("resumen_ordenes");

            // *** IMPORTANTE: declarar el arreglo ***
            let selectedOrdenes = [];

            function setBotonTexto() {
                if (!dropdownOrdenes) return;
                dropdownOrdenes.textContent = selectedOrdenes.length ?
                    `OC seleccionadas (${selectedOrdenes.length})` :
                    "Seleccione órdenes de compra";
            }

            function actualizarSeleccion() {
                // chips
                selectedOrdenesContainer.innerHTML = "";
                selectedOrdenes.forEach(oc => {
                    const tag = document.createElement("span");
                    tag.className = "selected-tag";
                    tag.innerHTML = `OC #${oc} <span class="remove-tag" data-id="${oc}">&times;</span>`;
                    selectedOrdenesContainer.appendChild(tag);
                });

                // input hidden para enviar al backend
                ordenCompraInput.value = JSON.stringify(selectedOrdenes);

                // texto del botón y (si existe) resumen
                setBotonTexto();
                if (resumenOrdenes) {
                    resumenOrdenes.textContent = selectedOrdenes.length ?
                        selectedOrdenes.join(", ") :
                        "No seleccionadas";
                }

                // borrar con la x
                selectedOrdenesContainer.querySelectorAll(".remove-tag").forEach(btn => {
                    btn.addEventListener("click", () => {
                        const id = btn.getAttribute("data-id");
                        selectedOrdenes = selectedOrdenes.filter(x => x !== id);
                        const chk = ordenCompraList.querySelector(
                            `input[type="checkbox"][value="${id}"]`);
                        if (chk) chk.checked = false;
                        actualizarSeleccion();
                    });
                });
            }

            function cargarOrdenes(codigoProveedor) {
                const sucursalId = document.getElementById("sucursal").value;
                if (!codigoProveedor || !sucursalId) return;

                fetch("/proveedor/ordenes", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            codigoProveedor,
                            entidad_id: sucursalId
                        })
                    })
                    .then(r => r.json())
                    .then(data => {
                        selectedOrdenes = [];
                        ordenCompraList.innerHTML = "";
                        setBotonTexto();

                        data.forEach(orden => {
                            const li = document.createElement("li");
                            li.innerHTML = `
          <label class="dropdown-item">
            <input type="checkbox" class="form-check-input me-2" value="${orden.NumeroOrdenCompra}">
            ${orden.NumeroOrdenCompra}
          </label>`;
                            ordenCompraList.appendChild(li);
                        });

                        ordenCompraList.querySelectorAll('input[type="checkbox"]').forEach(chk => {
                            chk.addEventListener("change", function() {
                                const val = this.value;
                                if (this.checked) {
                                    if (!selectedOrdenes.includes(val)) selectedOrdenes.push(
                                        val);
                                } else {
                                    selectedOrdenes = selectedOrdenes.filter(x => x !== val);
                                }
                                actualizarSeleccion();
                            });
                        });
                    })
                    .catch(err => {
                        console.error("Error al obtener órdenes de compra:", err);
                        if (resumenOrdenes) resumenOrdenes.textContent = "Error al cargar órdenes.";
                    });
            }

            function verificarYcargarOrdenes() {
                const codigoProveedor = document.getElementById("idUser").value.trim();
                const sucursalId = document.getElementById("sucursal").value;
                if (codigoProveedor && sucursalId) cargarOrdenes(codigoProveedor);
            }

            // eventos que disparan la carga
            document.getElementById("idUser").addEventListener("change", verificarYcargarOrdenes);
            document.getElementById("sucursal").addEventListener("change", verificarYcargarOrdenes);

            // estado inicial del botón
            setBotonTexto();
        });
    </script>



    <script>
        flatpickr("#fecha", {
            altInput: true,
            altFormat: "F j, Y",
            dateFormat: "Y-m-d",
            locale: {
                firstDayOfWeek: 0,
                weekdays: {
                    shorthand: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
                    longhand: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
                },
                months: {
                    shorthand: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
                    longhand: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto',
                        'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
                    ],
                }
            },
            disable: [
                function(date) {
                    // Deshabilitar domingos
                    return (date.getDay() === 0);
                }
            ]
        });
    </script>

    <script>
        document.getElementById("fecha").addEventListener("change", function() {
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

            document.getElementById("vehiculosContainer").innerHTML =
                ""; 
            contadorVehiculos = 1; //  Reiniciar contador
            agregarFormularioVehiculo(); //  Agregar uno nuevo por defecto
            verificarFormularioVehiculoCompleto(1); //  Validar automáticamente el primer formulario
        });
    </script>

    <script>
        // Guardar cita

        function aHoraSQLDesde24(h) {
            const base = (h || '').split('.')[0];
            let [H = '00', M = '00', S = '00'] = base.split(':');
            return `${String(H).padStart(2,'0')}:${String(M).padStart(2,'0')}:${String(S||'00').padStart(2,'0')}`;
        }

        document.getElementById("btnGuardarCita").addEventListener("click", function() {
            if (!validarHorariosSeleccionadosDisponibles()) return;
            const numVehiculos = document.querySelectorAll(".vehiculo-block").length;
            let vehiculos = [];

            for (let i = 1; i <= numVehiculos; i++) {
                const hidden = document.getElementById(`hora_${i}`); 
                const wrap = document.getElementById(`horas_${i}`); 
                const horaVal = hidden?.value || "";

                if (horaVal && wrap) {
                    const exists = !!wrap.querySelector(`.hora-check[value="${horaVal}"]`);
                    if (!exists) hidden.value = ""; 
                }
            }

            for (let i = 1; i <= numVehiculos; i++) {
                const andenVal = document.getElementById(`anden_${i}`).value;
                const horaVal = document.getElementById(`hora_${i}`).value;
                const transporteVal = document.getElementById(`transporte_${i}`).value;
                const descripcionVal = document.querySelector(`[name='descripcion_${i}']`)?.value || "";

                vehiculos.push({
                    anden_id: andenVal,
                    transporte_id: transporteVal,
                    hora: aHoraSQLDesde24(horaVal),
                    descripcion: descripcionVal,
                });
            }

            const formData = new FormData();

            formData.append("sucursal_id", document.getElementById("sucursal").value);
            formData.append("fecha", document.getElementById("fecha").value);
            formData.append("idUser", document.getElementById("idUser").value);
            formData.append("proveedor_id", document.getElementById("idUser").value);
            formData.append("motivo_evento", document.getElementById("motivo_evento")
                .value); // asegúrate que este input existe

            vehiculos.forEach((vehiculo, index) => {
                formData.append(`vehiculos[${index}][anden_id]`, vehiculo.anden_id);
                formData.append(`vehiculos[${index}][transporte_id]`, vehiculo.transporte_id);
                formData.append(`vehiculos[${index}][hora]`, vehiculo.hora);
                formData.append(`vehiculos[${index}][descripcion]`, vehiculo.descripcion);
            });

            const files = document.getElementById("evidencias").files;
            for (let i = 0; i < files.length; i++) {
                formData.append("evidencias[]", files[i]);
            }

            fetch('/almacen/citas', {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                })
                .then(async res => {
                    const contentType = res.headers.get("content-type");
                    if (!res.ok) {
                        const errorText = await res.text();
                        throw new Error(`HTTP ${res.status}: ${errorText.slice(0, 200)}`);
                    }
                    if (contentType && contentType.includes("application/json")) {
                        return res.json();
                    } else {
                        throw new Error("Respuesta no es JSON");
                    }
                })
                .then(response => {
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
    </script>

@endsection
