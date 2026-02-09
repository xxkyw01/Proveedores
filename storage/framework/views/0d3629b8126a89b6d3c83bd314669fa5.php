<?php $__env->startSection('title', 'Registrar Cita'); ?>
<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('includes.scripts.SweetAlert2', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('includes.scripts.flatpickr', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('includes.scripts.bootstrap', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <!-- Enlace a la hoja de estilos -->
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/rol/proveedor/cita.css')); ?>">
    <!-- Bootstrap JS Bundle (incluye Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

    <!-- Session Rol -->
    <?php if(!session()->has('Proveedor') && !(session('Usuario') && in_array(session('Usuario.IdRol'), [1, 3, 5]))): ?>
        <script>
            window.location.href = "/";
        </script>
    <?php endif; ?>

        <?php if (isset($component)) { $__componentOriginal2880b66d47486b4bfeaf519598a469d6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2880b66d47486b4bfeaf519598a469d6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.sidebar','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('sidebar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2880b66d47486b4bfeaf519598a469d6)): ?>
<?php $attributes = $__attributesOriginal2880b66d47486b4bfeaf519598a469d6; ?>
<?php unset($__attributesOriginal2880b66d47486b4bfeaf519598a469d6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2880b66d47486b4bfeaf519598a469d6)): ?>
<?php $component = $__componentOriginal2880b66d47486b4bfeaf519598a469d6; ?>
<?php unset($__componentOriginal2880b66d47486b4bfeaf519598a469d6); ?>
<?php endif; ?> 
        
    <div class="container-fluid con-sidebar">
    <div class="row justify-content-center">

    <div class="container">
        <!-- Barra de progreso -->
        <div class="wizard-steps mb-4">
            <div class="step-item" id="step-indicator-1">
                <div class="circle">1</div>
                <div class="label">Contacto</div>
            </div>
            <div class="line"></div>
            <div class="step-item" id="step-indicator-2">
                <div class="circle">2</div>
                <div class="label">Ubicación</div>
            </div>
            <div class="line"></div>
            <div class="step-item" id="step-indicator-3">
                <div class="circle">3</div>
                <div class="label">Disponibilidad</div>
            </div>
            <div class="line"></div>
            <div class="step-item" id="step-indicator-4">
                <div class="circle">4</div>
                <div class="label">Orden Compra</div>
            </div>
            <div class="line"></div>
            <div class="step-item" id="step-indicator-5">
                <div class="circle">5</div>
                <div class="label">Resumen</div>
            </div>
        </div>

        <form id="citaForm" method="POST" action="<?php echo e(route('proveedor.citas.store')); ?>">
            <?php echo csrf_field(); ?>

            <div class="step step-1 paso-card">
                <h4 class="titulo-paso"><i class="fas fa-user"></i> Paso 1: Información de Contacto</h4>

                <?php
                    use App\Helpers\AuthHelper;
                    $tipoUsuario = AuthHelper::tipoUsuario();
                    $codigoProveedor = session('Proveedor.CardCode', '');
                ?>

                <div class="mb-3">
                    <label for="idUser" class="form-label">Código del Proveedor:</label>
                    <div class="input-group">
                        <input type="text" id="idUser" name="idUser" class="form-control custom-input"
                            value="<?php echo e(old('idUser', $tipoUsuario === 'proveedor' ? $codigoProveedor : '')); ?>"
                            <?php echo e($tipoUsuario === 'proveedor' ? 'readonly' : ''); ?> required>
                        <?php if($tipoUsuario !== 'proveedor'): ?>
                            <button type="button" id="buscarProveedor" class="btn btn-primary">Buscar <i
                                    class="fas fa-search"></i></button>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Información del Proveedor -->
                <div class="form-floating mb-3">
                    <input type="text" class="form-control bg-light" id="labelNombreProveedor"
                        placeholder="Nombre del Proveedor" readonly disabled>
                    <label for="labelNombreProveedor">Nombre del Proveedor</label>
                </div>

                <div class="form-floating mb-3">
                    <input type="text" class="form-control bg-light" id="labelCelular" placeholder="Número de Celular"
                        readonly disabled>
                    <label for="labelCelular">Número de Celular</label>
                </div>

                <div class="form-floating mb-3">
                    <input type="email" class="form-control bg-light" id="labelCorreo" placeholder="Correo Electrónico"
                        readonly disabled>
                    <label for="labelCorreo">Correo Electrónico</label>
                </div>

                <!-- Subtítulo de Referencia de Contacto -->
                <h6>Referencia de Contacto</h6>

                <div class="form-floating mb-3">
                    <input type="text" class="form-control bg-light" id="labelNombreContacto"
                        placeholder="Nombre del Contacto" readonly disabled>
                    <label for="labelNombreContacto">Nombre del Contacto</label>
                </div>

                <div class="form-floating mb-3">
                    <input type="text" class="form-control bg-light" id="labelCelularContacto"
                        placeholder="Celular del Contacto" readonly disabled>
                    <label for="labelCelularContacto">Número de Celular del Contacto</label>
                </div>

                <div class="form-floating mb-3">
                    <input type="email" class="form-control bg-light" id="labelCorreoContacto"
                        placeholder="Correo del Contacto" readonly disabled>
                    <label for="labelCorreoContacto">Correo Electrónico del Contacto</label>
                </div>

                <!-- Botones en una misma fila -->
                <div class="d-flex justify-content-between mt-4">
                    <!-- Botón izquierdo -->
                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal"
                        data-bs-target="#modalActualizarDatos">
                        Solicitar actualización de datos <i class="fas fa-edit ms-1"></i>
                    </button>

                    <!-- Botón derecho -->
                    <button type="button" class="btn btn-primary next-step">
                        Siguiente <i class="fas fa-arrow-right"></i>
                    </button>
                </div>

            </div>

            <!-- PASO 2: Seleccionar Ciudad y Transporte -->
            <div class="step step-2 d-none paso-card">
                <h4 class="titulo-paso"><i class="fas fa-map-marker-alt"></i> Paso 2: Seleccionar Ubicación y Transporte
                </h4>
                <div class="mb-3">
                    <label for="ciudad" class="form-label">Ciudad</label>
                    <select id="ciudad" name="ciudad" class="form-select custom-select" required>
                        <option value="">Seleccione...</option>
                        <?php $__currentLoopData = $ciudades; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ciudad): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($ciudad->id); ?>"><?php echo e($ciudad->nombre); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="sucursal" class="form-label">Sucursal</label>
                    <select id="sucursal" name="sucursal_id" class="form-select custom-select" required>
                        <option value="">Seleccione...</option>
                    </select>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-secondary w-45 prev-step"><i class="fas fa-arrow-left"></i>
                        Atrás</button>
                    <button type="button" class="btn btn-primary w-45 next-step">Siguiente <i
                            class="fas fa-arrow-right"></i></button>
                </div>
            </div>

            <!-- PASO 3: Seleccionar Fecha, Andén, Vehículos, Transporte y Horarios -->
            <div class="step step-3 d-none paso-card ">
                <h4 class="titulo-paso"><i class="fas fa-calendar-alt"></i> Paso 3: Seleccionar disponibilidad</h4>

                <!-- Fecha -->
                <div class="mb-3">
                    <label for="fecha" class="form-label">Fecha</label>
                    <input type="text" id="fecha" name="fecha" class="form-control custom-input" required>
                </div>
                <!-- Contenedor dinámico de vehículos -->
                <div id="vehiculosContainer"></div>

                <!-- Botón para agregar más vehículos -->
                <div class="text-end mb-3">
                    <button type="button" class="btn btn-outline-success" id="btnAgregarVehiculo"
                        style="display: none;" onclick="agregarFormularioVehiculo()">
                        <i class="fas fa-plus"></i> Agregar otro vehículo
                    </button>
                </div>

                <!-- Footer del contenedor  -->
                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-secondary w-45 prev-step"><i class="fas fa-arrow-left"></i>
                        Atrás</button>
                    <button type="button" class="btn btn-primary w-45 next-step">Siguiente <i
                            class="fas fa-arrow-right"></i></button>
                </div>

            </div>

            <!-- PASO 4: Datos de Orden de Compra -->
            <div class="step step-4 d-none paso-card">
                <h4 class="titulo-paso"><i class="fas fa-user"></i> Paso 4: Datos de Orden de Compra</h4>

                <div class="mb-3">
                    <label class="form-label">Órdenes de Compra Abiertas:</label>
                    <div class="custom-multiselect">
                        <div class="selected-items-container" id="selectedOrdenes"></div>
                        <button class="btn btn-light border dropdown-toggle w-100 text-start" type="button"
                            id="dropdownOrdenes" data-bs-toggle="dropdown" aria-expanded="false">
                            Seleccione órdenes de compra
                        </button>
                        <ul class="dropdown-menu w-100" id="ordenCompraList"
                            style="max-height: 200px; overflow-y: auto;">

                            <!-- Opciones carga dinamicamente -->
                        </ul>
                    </div>
                    <input type="hidden" name="ordenCompra[]" id="ordenCompraInput">
                </div>

                <div id="foliosContainer" class="mt-3">
                    <!-- Aquí se agregan dinámicamente los campos de folio por OC -->
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-secondary w-45 prev-step"><i class="fas fa-arrow-left"></i>
                        Atrás</button>
                    <button type="button" class="btn btn-primary w-45 next-step">Siguiente <i
                            class="fas fa-arrow-right"></i></button>
                </div>

            </div>

            <!-- PASO 5: Confirmación -->
            <div class="step step-5 d-none paso-card">

                <div class="orden-doc border p-4 shadow bg-white mt-4 rounded resumen-nuevo">
                    <div class="text-center mb-4">
                        <h4 class="fw-bold text-orange text-uppercase">Resumen de Cita de Entrega</h4>
                        <p class="text-muted">Documento generado automáticamente</p>
                    </div>

                    <!-- Sección: Datos Generales -->
                    <div class="row g-3 resumen-section">
                        <div class="col-6"><strong>Proveedor:</strong> <span id="resumen_idUser"></span></div>
                        <div class="col-6"><strong>Ciudad:</strong> <span id="resumen_ciudad"></span></div>
                        <div class="col-6"><strong>Sucursal:</strong> <span id="resumen_sucursal"></span></div>
                        <div class="col-6"><strong>Fecha:</strong> <span id="resumen_fecha"></span></div>
                        <div class="col-6"><strong></strong> <span id="resumen_hora"></span></div>
                        <div class="col-6"><strong>Celular:</strong> <span id="resumen_celular"></span></div>
                        <div class="col-12"><strong>Correo:</strong> <span id="resumen_correo"></span></div>
                        <div class="col-12"><strong>Órdenes de Compra:</strong> <span id="resumen_ordenes"></span></div>
                    </div>

                    <!-- Vehículos -->
                    <div class="titulo-seccion">Vehículos Agendados</div>
                    <table class="tabla-resumen">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Andén</th>
                                <th>Tipo de Transporte</th>
                                <th>Hora</th>
                                <th>Estibador</th>
                                <th>Monto Maniobra ($)</th>
                            </tr>
                        </thead>

                        <tbody id="tablaVehiculosResumen">
                            <!-- Se llena desde JS -->
                        </tbody>
                    </table>

                    <div class="titulo-seccion">Orden de compra con folio</div>
                    <table class="tabla-resumen">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Orden de Compra</th>
                                <th>Folio</th>
                            </tr>
                        </thead>
                        <tbody id="tablaFoliosResumen">
                            <!-- Aquí agregas por JS el folio -->
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-secondary w-45 prev-step"><i class="fas fa-arrow-left"></i>
                        Atrás</button>
                    <!-- <button onclick="window.print()" class="btn btn-outline-dark"><i class="fas fa-print"></i> Imprimir PDF</button> --->
                    <button type="button" class="btn btn-success w-45" id="btnGuardarCita">Programar Cita</button>
                </div>
            </div>
        </form>
    </div>


    
        </div> 
    </div> 


    <!-- Modal Solicitar Actualización -->
    <div class="modal fade" id="modalActualizarDatos" tabindex="-1" aria-labelledby="modalActualizarDatosLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
            <div class="modal-content border border-warning">
                <div class="modal-header bg-light">
                    <h5 class="modal-title text-orange" id="modalActualizarDatosLabel"><i class="fas fa-edit"></i>
                        Solicitar Actualización de Datos</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <form id="formSolicitudActualizacion">
                        <p>Selecciona los datos que deseas actualizar:</p>
                        <?php
                            $campos = [
                                'Nombre del Proveedor' => 'proveedor',
                                'Número de Celular' => 'celular',
                                'Correo Electrónico' => 'correo',
                                'Nombre del Contacto' => 'contacto',
                                'Celular del Contacto' => 'celular_contacto',
                                'Correo del Contacto' => 'correo_contacto',
                            ];
                        ?>

                        <?php $__currentLoopData = $campos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label => $id): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="form-check mb-2">
                                <input class="form-check-input campo-checkbox" type="checkbox"
                                    value="<?php echo e($id); ?>" id="check_<?php echo e($id); ?>">
                                <label class="form-check-label"
                                    for="check_<?php echo e($id); ?>"><?php echo e($label); ?></label>
                                <input type="text" class="form-control mt-2 d-none campo-input"
                                    id="input_<?php echo e($id); ?>"
                                    placeholder="Ingrese nuevo  para <?php echo e($label); ?>">
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-warning"
                        onclick="enviarSolicitudActualizacion()">Solicitar</button>
                </div>
            </div>
        </div>
    </div>


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

            // Siguiente paso con validaciones
            document.querySelectorAll(".next-step").forEach(button => {
                button.addEventListener("click", function() {
                    if (!validarCamposPaso(currentStep)) return;
                    if (currentStep === 3 && !validarDuplicadosHorarioYAnden()) return;
                    if (currentStep === 3 && !validarHorariosSeleccionadosDisponibles()) return;
                    if (currentStep === 3 && !validarCamposMacheteros()) return;
                    if (currentStep === 4 && !validarOrdenesCompraSeleccionadas()) return;

                    if (currentStep < totalSteps) {
                        currentStep++;
                        if (currentStep === 5) llenarResumenCompleto();
                        showStep(currentStep);

                    }
                });
            });

            // Paso anterior
            document.querySelectorAll(".prev-step").forEach(button => {
                button.addEventListener("click", function() {
                    if (currentStep > 1) {
                        currentStep--;
                        showStep(currentStep);
                    }
                });
            });

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
        const tipoUsuario = "<?php echo e(AuthHelper::tipoUsuario()); ?>";
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

    <!--Carga automaticamente los ordenes de compras abiertos-->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
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
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute(
                                "content")
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
                            checkbox.addEventListener("change", function() {
                                if (this.checked) {
                                    selectedOrdenes.push(this.value);
                                } else {
                                    selectedOrdenes = selectedOrdenes.filter(item => item !==
                                        this.value);
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
                resumenOrdenes.textContent = selectedOrdenes.length > 0 ? selectedOrdenes.join(", ") :
                    "No seleccionadas";

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
                    tag.innerHTML =
                        `OC #${orden} <span class="remove-tag" data-id="${orden}">&times;</span>`;
                    selectedOrdenesContainer.appendChild(tag);
                });

                document.querySelectorAll(".remove-tag").forEach(btn => {
                    btn.addEventListener("click", function() {
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
    </script>

    <script>
        const PRECIO_POR_CAJA = <?php echo json_encode($precioCaja); ?>;
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
                <?php $__currentLoopData = $transportes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($t->id); ?>"><?php echo e($t->tipo); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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

                <!-- Grupo NO: Calcular monto de maniobra automáticamente -->
                <div class="mb-3 campo-macheteros-no d-none" id="campo_macheteros_no_${indiceActual}">
                    <label for="cajas_${indiceActual}" class="form-label">¿Cuántas cajas entregará?</label>
                    <input type="number" class="form-control cajas-input" name="cajas_${indiceActual}" id="cajas_${indiceActual}" min="1" step="1" placeholder="Ej. 20" data-index="${indiceActual}">
                    <small id="monto_label_${indiceActual}" class="text-muted d-block mt-1">Monto estimado: $0.00</small>

                    <small class="text-muted mt-1 d-block">
                        El sistema calculará el monto total con base en el precio por caja. Precio actual: <strong id="precio_caja_label_${indiceActual}">$0.00</strong>
                    </small>

                    <input type="hidden" name="precio_por_caja_${indiceActual}" id="precio_por_caja_${indiceActual}" value="0.00">
                    <input type="hidden" name="monto_maniobra_${indiceActual}" id="monto_maniobra_${indiceActual}" value="0.00">
                </div>

                `;

            container.appendChild(div);
            verificarFormularioVehiculoCompleto(indiceActual);

            const macheteroSelect = document.getElementById(`lleva_macheteros_${indiceActual}`);
            if (macheteroSelect) {
                macheteroSelect.addEventListener("change", function() {
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

            // seleccionar maniobra se consulte por el precio x caja dependeidneod del proveedor 
            document.querySelectorAll(`input[name="lleva_macheteros_${indiceActual}"]`).forEach(radio => {
                radio.addEventListener("change", function() {
                    const campoSi = document.getElementById(`campo_macheteros_si_${indiceActual}`);
                    const campoNo = document.getElementById(`campo_macheteros_no_${indiceActual}`);

                    if (this.value === "1") {
                        campoSi.classList.remove("d-none");
                        campoNo.classList.add("d-none");
                    } else if (this.value === "0") {
                        campoSi.classList.add("d-none");
                        campoNo.classList.remove("d-none");

                        // Obtener el código del proveedor
                        const codigoProveedor = document.getElementById("idUser").value.trim();

                        if (!codigoProveedor) return;

                        fetch(`/proveedor/precio-caja/${codigoProveedor}`)
                            .then(res => res.json())
                            .then(data => {
                                const precio = parseFloat(data.precio);
                                if (!isNaN(precio)) {
                                    document.getElementById(`precio_por_caja_${indiceActual}`).value =
                                        precio.toFixed(2);
                                    document.getElementById(`precio_caja_label_${indiceActual}`)
                                        .innerText = `$${precio.toFixed(2)}`;

                                    const cajasInput = document.getElementById(`cajas_${indiceActual}`);
                                    const cajas = parseInt(cajasInput.value) || 0;
                                    const monto = (cajas * precio).toFixed(2);
                                    document.getElementById(`monto_maniobra_${indiceActual}`).value =
                                        monto;
                                    document.getElementById(`monto_label_${indiceActual}`).innerText =
                                        `Monto estimado: $${monto}`;

                                    // el cálculo se actualiza si el usuario escribe las cajas 
                                    cajasInput.addEventListener("input", function() {
                                        const cajas = parseInt(this.value) || 0;
                                        const precioActual = parseFloat(document.getElementById(
                                            `precio_por_caja_${indiceActual}`).value) || 0;
                                        const nuevoMonto = (cajas * precioActual).toFixed(2);
                                        document.getElementById(
                                                `monto_maniobra_${indiceActual}`).value =
                                            nuevoMonto;
                                        document.getElementById(`monto_label_${indiceActual}`)
                                            .innerText = `Monto estimado: $${nuevoMonto}`;
                                    });

                                }
                            });
                    } else {
                        campoSi.classList.add("d-none");
                        campoNo.classList.add("d-none");
                        document.getElementById(`descripcion_${indiceActual}`).value = "";
                        document.getElementById(`monto_maniobra_${indiceActual}`).value = "";
                    }
                });
            });

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

        const transporteDuracionMinutos = {
            2: 30, // Camioneta
            3: 150, // Tráiler
            5: 90 // Rabón
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

        //Validacion macheteros 
        function validarCamposMacheteros() {
            let valido = true;

            document.querySelectorAll(".vehiculo-block").forEach(block => {
                const index = block.getAttribute("data-id");
                const macheteroSi = document.getElementById(`lleva_macheteros_si_${index}`);
                const macheteroNo = document.getElementById(`lleva_macheteros_no_${index}`);

                const campoCantidad = document.getElementById(`descripcion_${index}`);
                const campoCajas = document.getElementById(`cajas_${index}`);
                const campoMonto = document.getElementById(`monto_maniobra_${index}`);

                // Limpiar errores previos
                campoCantidad?.classList.remove("is-invalid");
                campoCajas?.classList.remove("is-invalid");
                campoMonto?.classList.remove("is-invalid");

                if (macheteroSi?.checked) {
                    const valor = campoCantidad.value.trim();
                    if (!/^[1-9]\d*$/.test(valor)) {
                        campoCantidad.classList.add("is-invalid");
                        valido = false;
                    }
                }

                if (macheteroNo?.checked) {
                    const cajas = parseInt(campoCajas.value.trim()) || 0;
                    if (cajas < 1) {
                        campoCajas.classList.add("is-invalid");
                        valido = false;
                    }

                    // Calcular el monto automáticamente
                    const monto = (cajas * PRECIO_POR_CAJA).toFixed(2);
                    campoMonto.value = monto;
                }
            });

            if (!valido) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Campos inválidos',
                    text: 'Verifica que hayas ingresado estibadores válidos o una cantidad de cajas mayor a 0.',
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
                                    const otroTransporte = document.getElementById(
                                        `transporte_${id}`)?.value;

                                    if (otroAnden === andenActual && otraHora && otroTransporte) {
                                        const duracionOtra = transporteDuracionMinutos[parseInt(
                                            otroTransporte)];

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
                                const hora12 =
                                    `${(horaRaw[0] % 12) || 12}:${horaRaw[1]} ${horaRaw[0] >= 12 ? 'PM' : 'AM'}`;
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
    </script>

    <script>
        flatpickr("#fecha", {
            dateFormat: "Y-m-d",
            // establece fecha después de 2 días de hoy
            minDate: new Date().fp_incr(2), 
            //maxDate: new Date().fp_incr(7),
            locale: {
                firstDayOfWeek: 0,
                weekdays: {
                    shorthand: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
                    longhand: ['Domingo','Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
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
                   // return (date.getDay() === 0);
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
                ""; //  Eliminar todos los formularios de vehículos
            contadorVehiculos = 1; //  Reiniciar contador
            agregarFormularioVehiculo(); //  Agregar uno nuevo por defecto
            verificarFormularioVehiculoCompleto(1); //  Validar automáticamente el primer formulario
        });
    </script>

    <script>
        // Cargar serie de OC al seleccionar sucursal
        let serieOrdenCompra = "";
        document.getElementById("sucursal").addEventListener("change", function() {
            let sucursalId = this.value;

            // Cargar la serie OC (ej: ZO, ZC...)
            fetch(`/proveedor/serie-oc/${sucursalId}`)
                .then(response => response.json())
                .then(data => {
                    serieOrdenCompra = data.serie_oc;
                    console.log("Serie OC de esta sucursal:", serieOrdenCompra);
                });
        });
    </script>

    <script>
        // Guardar cita
        function convertirHoraFormatoSQL(hora12) {
            const [horaMin, meridiano] = hora12.trim().split(' ');
            let [hora, minutos] = horaMin.split(':');
            hora = parseInt(hora);
            if (meridiano === 'PM' && hora < 12) hora += 12;
            if (meridiano === 'AM' && hora === 12) hora = 0;
            return `${hora.toString().padStart(2, '0')}:${minutos}:00`;
        }

        document.getElementById("btnGuardarCita").addEventListener("click", function() {
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
                orden_compra: selectedOrdenes,
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
    </script>

    <script>
        function llenarResumenCompleto() {
            console.log("Resumen completado");

            // 1 Datos Generales
            document.getElementById("resumen_idUser").textContent = document.getElementById("idUser").value || "N/A";
            // Ciudad
            let ciudadSelect = document.getElementById("ciudad");
            document.getElementById("resumen_ciudad").textContent = ciudadSelect.options[ciudadSelect.selectedIndex]
                ?.text || "N/A";
            // Sucursal
            let sucursalSelect = document.getElementById("sucursal");
            document.getElementById("resumen_sucursal").textContent = sucursalSelect.options[sucursalSelect.selectedIndex]
                ?.text || "N/A";
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
                const macheteroValue = document.querySelector(`input[name="lleva_macheteros_${i}"]:checked`)?.value;


                //si hay macheteros 
                const macheteroText = macheteroSi?.checked ?
                    (document.getElementById(`descripcion_${i}`)?.value || "Sin dato") : "No aplica";

                // Monto total 
                let montoText = "No aplica";
                if (macheteroValue === "0") {
                    const cajas = parseInt(document.getElementById(`cajas_${i}`)?.value) || 0;
                    const precio = parseFloat(document.getElementById(`precio_por_caja_${i}`)?.value) || 0;
                    const monto = (cajas * precio).toFixed(2);
                    document.getElementById(`monto_maniobra_${i}`).value = monto;
                    document.getElementById(`monto_label_${i}`).innerText = `Monto estimado: $${monto}`;
                    montoText = `$${monto}`;
                }


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
    </script>

    <script>
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
            // Impostor 
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
                        Swal.fire("Enviado", "Tu solicitud ha sido enviada por correo electrónico.", "success").then(
                            () => {
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
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.movil', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\ygonzalez\Synology\Home\Escritorio\Proveedores\resources\views/pages/proveedor/cita.blade.php ENDPATH**/ ?>