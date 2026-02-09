
<?php $__env->startSection('title', 'Confirmar Cita'); ?>
<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('includes.scripts.SweetAlert2@11', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('includes.scripts.Datatables', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('includes.scripts.fontAwesome', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('includes.scripts.bootstrap', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <!-- Enlace a la hoja de estilos -->
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/rol/almacen/confirmar_cita.css')); ?>">

    <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->

    <?php
        $rolId = session('Usuario.IdRol');
        $sucursalIdUsuario = session('Usuario.id_sucursal');
    ?>

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
            <!--multitablas  de pendientes , ayer , semana pasada y mes pasado --->
            <div class="container border rounded shadow-sm p-3"
                style="background-color: #fff; border-color: #ee7826 !important;">
                <!-- Tabla principal fija -->
                <h3 class="mb-3 bg-white p-2 rounded shadow-sm text-center fw-bold" style="color: #ee7826;">Citas Pendientes
                </h3>
                <div class="table-responsive-sm">
                    <table id="tablaPendientes" class="table table-bordered table-striped bg-white w-100">
                        <thead class="table-light">
                            <tr>
                                <th class="col-fecha">Fecha</th>
                                <th class="col-dias">Días Restantes</th>
                                <th class="col-proveedor">Proveedor</th>
                                <th class="col-orden">Orden de Compra</th>
                                <th class="col-estado">Estatus</th>
                                <th class="col-acciones">Acciones</th>
                            </tr>
                        </thead>
                    </table>
                </div>

                <!-- Acordeones para historial -->
                <div class="accordion mt-5" id="historialAccordion">

                    <!-- Ayer -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingAyer">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseAyer" aria-expanded="false" aria-controls="collapseAyer">
                                Citas de Ayer
                            </button>
                        </h2>
                        <div id="collapseAyer" class="accordion-collapse collapse" aria-labelledby="headingAyer"
                            data-bs-parent="#historialAccordion">
                            <div class="accordion-body">
                                <table id="tablaAyer" class="table table-sm table-striped bg-white w-100">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Proveedor</th>
                                            <th>Orden de Compra</th>
                                            <th>Estatus</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Semana pasada -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingSemana">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseSemana" aria-expanded="false" aria-controls="collapseSemana">
                                Citas de la Semana Pasada
                            </button>
                        </h2>
                        <div id="collapseSemana" class="accordion-collapse collapse" aria-labelledby="headingSemana"
                            data-bs-parent="#historialAccordion">
                            <div class="accordion-body">
                                <table id="tablaSemana" class="table table-sm table-striped bg-white w-100">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Proveedor</th>
                                            <th>Orden de Compra</th>
                                            <th>Estatus</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Mes pasado -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingMes">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseMes" aria-expanded="false" aria-controls="collapseMes">
                                Citas del Mes Pasado
                            </button>
                        </h2>
                        <div id="collapseMes" class="accordion-collapse collapse" aria-labelledby="headingMes"
                            data-bs-parent="#historialAccordion">
                            <div class="accordion-body">
                                <table id="tablaMes" class="table table-sm table-striped bg-white w-100">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Proveedor</th>
                                            <th>Orden de Compra</th>
                                            <th>Estatus</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>

            </div> 
        </div> 
    </div> 


    <!-- Modal Detalle Cita -->
    <div class="modal fade" id="modalDetalleCita" tabindex="-1" aria-labelledby="modalDetalleCitaLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header custom-header">
                    <h5 class="modal-title fw-bold" id="modalDetalleCitaLabel">
                        <i class="fas fa-info-circle me-2"></i> DETALLES DE LA CITA PROGRAMADA
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!-- Columna proveedor -->
                        <div class="col-md-6">
                            <p class="fw-bold mb-1">PROVEEDOR</p>
                            <div class="mb-1" id="detalle_proveedor"></div>
                            <div><strong>RFC:</strong> <span id="detalle_rfc"></span></div>
                            <div><strong>Dirección:</strong> <span id="detalle_direccion"></span></div>
                            <div><strong>Correo:</strong> <span id="detalle_correo"></span></div>
                            <div><strong>Teléfono:</strong> <span id="detalle_telefono"></span></div>
                        </div>

                        <!-- Columna contacto -->
                        <div class="col-md-6">
                            <p class="fw-bold mb-1">CONTACTO</p>
                            <div><strong>Nombre:</strong> <span id="detalle_contacto"></span></div>
                            <div><strong>Teléfono:</strong> <span id="detalle_tel_contacto"></span></div>
                            <div><strong>Celular:</strong> <span id="detalle_cel_contacto"></span></div>
                            <div><strong>Correo:</strong> <span id="detalle_email_contacto"></span></div>
                        </div>
                        <p class="text-center text-muted mt-3 fst-italic">Detalles de la cita.</p>
                        <!-- Orden de compra: fila completa debajo -->
                        <div class="col-md-12 mt-3">

                        </div>
                    </div>
                    <!-- Detalles de ordne de venta -->
                    <div class="row mt-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Orden(es) de Compra:</label>
                            <p id="detalle_orden_compra" type="text" class="form-control" readonly></p>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Fecha:</label>
                            <input type="text" class="form-control" id="detalle_fecha" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Estatus:</label>
                            <input type="text" class="form-control" id="detalle_estado" readonly>
                        </div>
                    </div>
                    <!-- Detalles de vehículos -->
                    <div id="detalle_vehiculos" class="mt-4"></div>
                    <!-- Comentario -->
                    <label class="fw-bold mt-4">Mensaje o comentario que desea mandarle al proveedor:</label>
                    <textarea id="comentario_usuario" class="form-control" rows="2" placeholder="Escribe un mensaje opcional..."></textarea>
                    <!-- Cambiar estatus -->
                    <label class="fw-bold mt-3">Cambiar Estatus:</label>
                    <select id="estado_nuevo" class="form-select">
                        <option value="Confirmada">Confirmar Cita</option>
                        <option value="Cancelada">Cancelar Cita</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-orange" onclick="actualizarEstadoCita()">
                        <i class="fas fa-paper-plane"></i> Actualizar y Enviar Correo
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Tabla de pendientes
            $('#tablaPendientes').DataTable({
                ajax: '<?php echo e(route('almacen.confirmarCitas.pendientes')); ?>',
                columns: [{
                        data: 'fecha',
                        render: data => formatFecha(data)
                    },
                    {
                        data: 'fecha',
                        render: function(fecha) {
                            const hoy = new Date();
                            const f = new Date(fecha);
                            const diffTime = f - hoy;
                            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

                            if (diffDays < 0)
                                return `<span class="text-danger fw-bold">Vencida</span>`;
                            if (diffDays === 0)
                                return `<span class="text-primary fw-semibold">Hoy</span>`;
                            if (diffDays === 1) return `<span class="text-warning">Mañana</span>`;
                            if (diffDays <= 3)
                                return `<span class="text-warning">En ${diffDays} días</span>`;
                            return `<span class="text-success">En ${diffDays} días</span>`;
                        }
                    },
                    {
                        data: 'proveedor_nombre'
                    },
                    {
                        data: 'orden_compra'
                    },
                    {
                        data: 'estado'
                    },
                    {
                        data: 'id',
                        render: data => `
                <button class="btn btn-sm btn-outline-warning" onclick="abrirModal(${data})">
                    <i class="fas fa-pen"></i>
                </button>`
                    }
                ],
                columnDefs: [{
                        targets: 0,
                        className: 'col-fecha'
                    },
                    {
                        targets: 1,
                        className: 'col-dias'
                    },
                    {
                        targets: 2,
                        className: 'col-proveedor'
                    },
                    {
                        targets: 3,
                        className: 'col-orden'
                    },
                    {
                        targets: 4,
                        className: 'col-estado'
                    },
                    {
                        targets: 5,
                        className: 'col-acciones'
                    }
                ],
                language: {
                    search: " Buscar:",
                    lengthMenu: "Mostrar _MENU_ entradas",
                    info: "Mostrando _START_ a _END_ de _TOTAL_ entradas",
                    infoEmpty: "Sin registros",
                    emptyTable: "No hay datos disponibles",
                    paginate: {
                        first: "Primero",
                        previous: "Anterior",
                        next: "Siguiente",
                        last: "Último"
                    }
                }
            });

            $('#collapseayer').on('show.bs.collapse', function() {
                // Tablas en los acordeones
                $('#tablaAyer').DataTable({
                    ajax: '<?php echo e(route('almacen.confirmarCitas.ayer')); ?>',
                    responsive: false, // Desactiva comportamiento "tarjeta"
                    destroy: true,
                    columns: [{
                            data: 'fecha',
                            render: data => formatFecha(data)
                        },
                        {
                            data: 'proveedor_nombre'
                        },
                        {
                            data: 'orden_compra'
                        },
                        {
                            data: 'estado'
                        }
                    ],
                    columnDefs: [{
                            targets: 0,
                            className: 'col-fecha'
                        },
                        {
                            targets: 1,
                            className: 'col-proveedor'
                        },
                        {
                            targets: 2,
                            className: 'col-orden'
                        },
                        {
                            targets: 3,
                            className: 'col-estado'
                        }
                    ],
                    language: {
                        search: " Buscar:",
                        lengthMenu: "Mostrar _MENU_ entradas",
                        info: "Mostrando _START_ a _END_ de _TOTAL_ entradas",
                        //infoEmpty: "Sin registros",
                        emptyTable: "No hay datos disponibles",
                        paginate: {
                            first: "Primero",
                            previous: "Anterior",
                            next: "Siguiente",
                            last: "Último"
                        }
                    }
                });
            });

            $('#collapseSemana').on('show.bs.collapse', function() {
                $('#tablaSemana').DataTable({
                    ajax: '<?php echo e(route('almacen.confirmarCitas.semana')); ?>',
                    responsive: false, // Desactiva comportamiento "tarjeta"
                    destroy: true,
                    columns: [{
                            data: 'fecha',
                            render: data => formatFecha(data)
                        },
                        {
                            data: 'proveedor_nombre'
                        },
                        {
                            data: 'orden_compra'
                        },
                        {
                            data: 'estado'
                        }
                    ],
                    columnDefs: [{
                            targets: 0,
                            className: 'col-fecha'
                        },
                        {
                            targets: 1,
                            className: 'col-proveedor'
                        },
                        {
                            targets: 2,
                            className: 'col-orden'
                        },
                        {
                            targets: 3,
                            className: 'col-estado'
                        }
                    ],
                    language: {
                        search: " Buscar:",
                        lengthMenu: "Mostrar _MENU_ entradas",
                        info: "Mostrando _START_ a _END_ de _TOTAL_ entradas",
                        //infoEmpty: "Sin registros",
                        emptyTable: "No hay datos disponibles",
                        paginate: {
                            first: "Primero",
                            previous: "Anterior",
                            next: "Siguiente",
                            last: "Último"
                        }
                    }
                });
            });

            $('#collapseMes').on('show.bs.collapse', function() {
                $('#tablaMes').DataTable({
                    ajax: '<?php echo e(route('almacen.confirmarCitas.mes')); ?>',
                    responsive: false, // Desactiva comportamiento "tarjeta"
                    destroy: true,
                    columns: [{
                            data: 'fecha',
                            render: data => formatFecha(data)
                        },
                        {
                            data: 'proveedor_nombre'
                        },
                        {
                            data: 'orden_compra'
                        },
                        {
                            data: 'estado'
                        }
                    ],
                    columnDefs: [{
                            targets: 0,
                            className: 'col-fecha'
                        },
                        {
                            targets: 1,
                            className: 'col-proveedor'
                        },
                        {
                            targets: 2,
                            className: 'col-orden'
                        },
                        {
                            targets: 3,
                            className: 'col-estado'
                        }
                    ],
                    language: {
                        search: " Buscar:",
                        lengthMenu: "Mostrar _MENU_ entradas",
                        info: "Mostrando _START_ a _END_ de _TOTAL_ entradas",
                        //infoEmpty: "Sin registros",
                        emptyTable: "No hay datos disponibles",
                        paginate: {
                            first: "Primero",
                            previous: "Anterior",
                            next: "Siguiente",
                            last: "Último"
                        }
                    }
                });
            });
        });

        // Formato dd/mm/yyyy
        function formatFecha(fechaStr) {
            const [year, month, day] = fechaStr.split('-');
            return `${day}/${month}/${year}`;
        }


        //funcion de modal para ver detalles de la cita 
        function abrirModal(id) {
            fetch(`/almacen/confirmarCita/detalle/${id}`)
                .then(res => res.json())
                .then(data => {
                    document.getElementById('detalle_fecha').value = data.info.fecha;
                    document.getElementById('detalle_proveedor').textContent = data.info.proveedor_nombre;
                    document.getElementById('detalle_rfc').textContent = data.info.RFC_proveedor;
                    document.getElementById('detalle_telefono').textContent = data.info.Telefono;
                    document.getElementById('detalle_correo').textContent = data.info.Correo;
                    document.getElementById('detalle_direccion').textContent = data.info.Direccion;
                    document.getElementById('detalle_contacto').textContent = data.info.Contacto_Referencia;
                    document.getElementById('detalle_tel_contacto').textContent = data.info.Telefono_contacto;
                    document.getElementById('detalle_cel_contacto').textContent = data.info.Celular_contacto;
                    document.getElementById('detalle_email_contacto').textContent = data.info.Correo_contacto;
                    document.getElementById('detalle_orden_compra').textContent = data.info.orden_compra;
                    document.getElementById('estado_nuevo').value = data.info.estado;
                    document.getElementById('comentario_usuario').value = '';
                    document.getElementById('estado_nuevo').setAttribute('data-id', data.info.id);
                    document.getElementById('detalle_estado').value = data.info.estado;
                    document.getElementById('detalle_contacto').textContent = data.info.Contacto_Referencia;
                    document.getElementById('detalle_tel_contacto').textContent = data.info.Telefono_contacto;
                    document.getElementById('detalle_cel_contacto').textContent = data.info.Celular_contacto;
                    document.getElementById('detalle_email_contacto').textContent = data.info.Correo_contacto;


                    const detalleVehiculos = document.getElementById('detalle_vehiculos');
                    detalleVehiculos.innerHTML = '';

                    if (data.vehiculos && Array.isArray(data.vehiculos)) {
                        data.vehiculos.forEach((v, i) => {
                            const horaFormateada = new Date('2000-01-01T' + v.hora).toLocaleTimeString(
                                'es-MX', {
                                    hour: '2-digit',
                                    minute: '2-digit',
                                    hour12: true
                                });

                            let extra = '';
                            if (v.lleva_macheteros == 1 && v.descripcion) {
                                extra = `${v.descripcion} macheteros`;
                            } else if (v.monto_maniobra && parseFloat(v.monto_maniobra) > 0) {
                                extra = `El monto total es $${parseFloat(v.monto_maniobra).toFixed(2)}`;
                            }

                            detalleVehiculos.innerHTML += `
                                <div class="detalle-item mb-2">
                                    <div> ${v.anden} a las ${horaFormateada}</div>
                                    ${extra ? `<div> ${extra}</div>` : ''}
                                    <div> Vehículo: ${v.transporte}</div>
                                </div>
                                `;

                        });
                    }

                    const modal = new bootstrap.Modal(document.getElementById('modalDetalleCita'));
                    modal.show();
                });
        }

        // Para actualizar el estado y agrgar el comentario para el envio de correo 
        function actualizarEstadoCita() {
            const id = document.getElementById('estado_nuevo').getAttribute('data-id');
            const nuevoEstado = document.getElementById('estado_nuevo').value;
            const comentario = document.getElementById('comentario_usuario').value;

            fetch(`/almacen/confirmarCita/actualizarEstado`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                    },
                    body: JSON.stringify({
                        id: id,
                        estado: nuevoEstado,
                        comentario: comentario
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const modal = bootstrap.Modal.getInstance(document.getElementById('modalDetalleCita'));
                        modal.hide();
                        $('#tablaPendientes').DataTable().ajax.reload();


                        Swal.fire({
                            icon: 'success',
                            title: '¡Cita actualizada!',
                            text: 'El estado se actualizó correctamente.',
                            confirmButtonColor: '#ee7826'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message || 'No se pudo actualizar la cita.',
                            confirmButtonColor: '#ee7826'
                        });
                    }
                })
                .catch(err => {
                    console.error(err);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error de conexión con el servidor.',
                        confirmButtonColor: '#ee7826'
                    });
                });
        }
    </script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.movil', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\ygonzalez\Synology\Home\Escritorio\Proveedores\resources\views/pages/almacen/confirmarCita.blade.php ENDPATH**/ ?>