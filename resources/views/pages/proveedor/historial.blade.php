@extends('layouts.movil')
@section('title', 'Consultar')
@section('content')
    @include('includes.scripts.Datatables')
    @include('includes.scripts.SweetAlert2@11')
    @include('includes.scripts.bootstrap')

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Enlace a la hoja de estilos -->
    <link rel="stylesheet" href="{{ asset('assets/css/rol/proveedor/historial.css') }}">


    <x-sidebar />

    <div class="container-fluid con-sidebar">
        <div class="row justify-content-center">

            <div class="container">
                <!-- KPIs -->
                <div class="card shadow p-3 mb-4" style="border: 2px solid #ee7826; border-radius: 17px;">
                    <div class="row row-cols-2 row-cols-md-5 g-2 text-center">
                        <div class="col">
                            <div class="card shadow-sm border-0 p-2"
                                style="background-color: #fff; border-left: 4px solid #ee7826; border-radius: 12px;">
                                <i class="fas fa-calendar-alt fa-lg text-warning mb-1"></i>
                                <h6 class="text-uppercase fw-bold text-muted mb-0" style="font-size: 0.8rem;">Total Citas
                                </h6>
                                <h5 class="fw-bold text-dark mb-0">{{ $totalCitas }}</h5>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card shadow-sm border-0 p-2"
                                style="background-color: #fff; border-left: 4px solid #f19a0f; border-radius: 12px;">
                                <i class="fas fa-clock fa-lg text-warning mb-1"></i>
                                <h6 class="text-uppercase fw-bold text-muted mb-0" style="font-size: 0.8rem;">Pendientes
                                </h6>
                                <h5 class="fw-bold text-dark mb-0">{{ $citasPendientes }}</h5>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card shadow-sm border-0 p-2"
                                style="background-color: #fff; border-left: 4px solid #28a745; border-radius: 12px;">
                                <i class="fas fa-check-circle fa-lg text-success mb-1"></i>
                                <h6 class="text-uppercase fw-bold text-muted mb-0" style="font-size: 0.8rem;">Confirmadas
                                </h6>
                                <h5 class="fw-bold text-dark mb-0">{{ $citasConfirmadas }}</h5>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card shadow-sm border-0 p-2"
                                style="background-color: #fff; border-left: 4px solid #e74c3c; border-radius: 12px;">
                                <i class="fas fa-times-circle fa-lg text-danger mb-1"></i>
                                <h6 class="text-uppercase fw-bold text-muted mb-0" style="font-size: 0.8rem;">Canceladas
                                </h6>
                                <h5 class="fw-bold text-dark mb-0">{{ $citasCanceladas }}</h5>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card shadow-sm border-0 p-2"
                                style="background-color: #fff; border-left: 4px solid #0d6efd; border-radius: 12px;">
                                <i class="fas fa-user-check fa-lg text-primary mb-1"></i>
                                <h6 class="text-uppercase fw-bold text-muted mb-0" style="font-size: 0.8rem;">Asistió</h6>
                                <h5 class="fw-bold text-dark mb-0">{{ $citasAsistio ?? 0 }}</h5>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card shadow-sm border-0 p-2"
                                style="background-color: #fff; border-left: 4px solid #6c757d; border-radius: 12px;">
                                <i class="fas fa-user-times fa-lg text-dark mb-1"></i>
                                <h6 class="text-uppercase fw-bold text-muted mb-0" style="font-size: 0.8rem;">No Asistió
                                </h6>
                                <h5 class="fw-bold text-dark mb-0">{{ $citasNoAsistio ?? 0 }}</h5>
                            </div>
                        </div>

                        {{-- Nuevo Estados  --}}
                        <div class="col">
                            <div class="card shadow-sm border-0 p-2"
                                style="background-color: #fff; border-left: 4px solid #ffc107; border-radius: 12px;">
                                <i class="fas fa-spinner fa-lg text-dark mb-1"></i>
                                <h6 class="text-uppercase fw-bold text-muted mb-0" style="font-size: 0.8rem;">En Proceso
                                </h6>
                                <h5 class="fw-bold text-dark mb-0">{{ $citasEnProceso ?? 0 }}</h5>
                            </div>
                        </div>

                        <div class="col">
                            <div class="card shadow-sm border-0 p-2"
                                style="background-color: #fff; border-left: 4px solid #6c757d; border-radius: 12px;">
                                <i class="fas fa-hourglass-start fa-lg text-orange mb-1"></i>
                                <h6 class="text-uppercase fw-bold text-muted mb-0" style="font-size: 0.8rem;">Recepción
                                    tardía</h6>
                                <h5 class="fw-bold text-dark mb-0">{{ $citasRecepcionTardia ?? 0 }}</h5>
                            </div>
                        </div>

                        <div class="col">
                            <div class="card shadow-sm border-0 p-2"
                                style="background-color: #fff; border-left: 4px solid #dc3545; border-radius: 12px;">
                                <i class="fas fa-user-times fa-lg text-danger mb-1"></i>
                                <h6 class="text-uppercase fw-bold text-muted mb-0" style="font-size: 0.8rem;">Cancelada por
                                    proveedor</h6>
                                <h5 class="fw-bold text-dark mb-0">{{ $citasCanceladasPorProveedor ?? 0 }}</h5>
                            </div>
                        </div>

                        <div class="col">
                            <div class="card shadow-sm border-0 p-2"
                                style="background-color: #fff; border-left: 4px solid #17a2b8; border-radius: 12px;">
                                <i class="fas fa-info-circle fa-lg text-info mb-1"></i>
                                <h6 class="text-uppercase fw-bold text-muted mb-0" style="font-size: 0.8rem;">No Programado
                                </h6>
                                <h5 class="fw-bold text-dark mb-0">{{ $citasNoProgramado ?? 0 }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container">
                <!-- Tabla -->
                <div class="card p-3 shadow" style="border: 2px solid #ee7826; border-radius: 20px;">
                    <h5 class="fw-bold mb-3" style="color: #ee7826;">
                        <i class="fas fa-book-open "></i> Historial de Citas Programadas
                    </h5>

                    <div class="table-responsive">
                        <table id="tablaReservas" class="table table-striped table-bordered align-middle">
                            <thead class="text-white" style="background-color: #ee7826;">
                                <tr>
                                    <th>Fecha</th>
                                    <th>Nombre</th>
                                    <th>Orden de Compra</th>
                                    <th>Sucursal</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($reservaciones as $reserva)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($reserva->fecha)->format('d/m/Y') }}
                                            <br>
                                            {{ \Carbon\Carbon::parse($reserva->hora)->format('h:i A') }}
                                        </td>
                                        <td>{{ $reserva->proveedor_nombre }}</td>
                                        <td>{!! nl2br(e($reserva->ordenes_detalle ?? '-')) !!}</td>
                                        <td>{{ $reserva->sucursal_nombre ?? 'N/A' }}</td>
                                        <td>
                                            @php
                                                $estado = strtolower(trim($reserva->estado));
                                                $badgeClass = match ($estado) {
                                                    'pendiente' => 'bg-secondary',
                                                    'confirmada' => 'badge-confirmado',
                                                    'asistió' => 'bg-success',
                                                    'no asistió' => 'bg-dark',
                                                    'cancelado', 'cancelada' => 'bg-danger',
                                                    'en proceso' => 'bg-warning',
                                                    'recepción tardía' => 'bg-dark',
                                                    'cancelada por proveedor' => 'bg-danger',
                                                    'no programado' => 'bg-info',
                                                    default => 'bg-light text-dark',
                                                };
                                            @endphp
                                            <span class="badge {{ $badgeClass }}">{{ ucfirst($reserva->estado) }}</span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary" data-toggle="tooltip"
                                                data-placement="top" title="Ver Detalles"
                                                onclick="mostrarDetalle({{ json_encode($reserva) }})">
                                                <i class="fas fa-eye"></i>
                                            </button>

                                            <button class="btn btn-sm btn-outline-danger" data-toggle="tooltip"
                                                data-placement="top" title="Solicitar Cancelación"
                                                onclick="abrirModalCancelar({{ $reserva->id }})">
                                                <i class="fas fa-xmark"></i>
                                            </button>

                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div> {{-- Cierra row --}}
    </div> {{-- Cierra container-fluid --}}


    </div>
    <!-- Modal Detalles -->
    <div class="modal fade" id="modalDetalles" tabindex="-1" aria-labelledby="modalDetallesLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-5 shadow">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold text-uppercase" style="color: #ee7826;">
                        <i class="fas fa-info-circle me-2"></i> Detalles de la Cita Programada
                    </h5>
                    <button type="button" class="btn-close btn-close-orange position-absolute top-0 end-0 m-3"
                        data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <small class="text-uppercase text-muted fw-bold">Proveedor</small>
                            <div class="fw-semibold" id="detalleProveedorNombre">-</div>
                            <div><strong>RFC:</strong> <span id="detalleProveedorRFC">-</span></div>
                            <div><strong>Dirección:</strong><br><span id="detalleProveedorDireccion">-</span></div>
                            <div><strong>Correo:</strong> <span id="detalleProveedorCorreo">-</span></div>
                            <div><strong>Teléfono:</strong> <span id="detalleProveedorTelefono">-</span></div>
                        </div>

                        <!---Detalles de la deerecho del destinatario-->
                        <div class="col-md-6 text-end">
                            <small class="text-uppercase text-muted fw-bold"></small>
                            <div class="text-muted fst-italic"></div>
                        </div>
                    </div>

                    <p class="text-muted text-center fst-italic">Este documento es solo informativo</p>
                    <hr class="mb-3">

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong class="text-muted">Sucursal:</strong>
                            <div class="border rounded p-2" id="detalleSucursal">-</div>
                        </div>
                        <div class="col-md-4">
                            <strong class="text-muted">Transporte:</strong>
                            <div class="border rounded p-2" id="detalleTransporte">-</div>
                        </div>
                        <div class="col-md-4">
                            <strong class="text-muted">Fecha:</strong>
                            <div class="border rounded p-2" id="detalleFecha">-</div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong class="text-muted">Hora:</strong>
                            <div class="border rounded p-2" id="detalleHora">-</div>
                        </div>
                        <div class="col-md-4">
                            <strong class="text-muted">Estado:</strong>
                            <div class="border rounded p-2">
                                <span id="detalleEstadoModal" class="badge px-3 py-2 fs-6">-</span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <strong class="text-muted">Órdenes de Compra con Folio:</strong>
                        <pre class="border rounded p-3 bg-light" style="white-space: pre-line;" id="detalleOrdenes">-</pre>
                    </div>


                    {{-- Extras: Comentario y Evidencia --}}
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <strong class="text-muted">Comentario de almacén:</strong>
                            <div id="detalleComentario" class="border rounded p-2 bg-light">-</div>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-12" id="detalleEvidenciaWrap" style="display:none;">
                            <strong class="text-muted d-block mb-1">Evidencia adjunta:</strong>
                            <a id="detalleEvidenciaLink" class="btn btn-sm btn-outline-primary" target="_blank">Ver
                                evidencia</a>
                            <small id="detalleEvidenciaMeta" class="text-muted ms-2"></small>
                        </div>
                    </div>



                </div>
            </div>
        </div>
    </div>

    <!-- Modal Descargar PDF -->
    <div class="modal fade" id="modalDescargarPDF" tabindex="-1" aria-labelledby="modalDescargarPDFLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header" style="background:#ee7826; color:#fff;">
                    <h5 class="modal-title fw-bold" id="modalDescargarPDFLabel">
                        <i class="fas fa-file-pdf me-2"></i> Generar Reporte PDF
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <form id="formDescargarPDF">
                        <div class="mb-3">
                            <label for="fechaInicio" class="form-label fw-bold">Fecha Inicio:</label>
                            <input type="date" class="form-control" id="fechaInicio" name="fechaInicio" required>
                        </div>
                        <div class="mb-3">
                            <label for="fechaFin" class="form-label fw-bold">Fecha Fin:</label>
                            <input type="date" class="form-control" id="fechaFin" name="fechaFin" required>
                        </div>
                        <div class="mb-3">
                            <label for="sucursal" class="form-label fw-bold">Sucursal:</label>
                            <select class="form-select" id="sucursal" name="sucursal">
                                <option value="">Todas</option>
                                @foreach ($sucursales as $s)
                                    <option value="{{ $s->id }}">{{ $s->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="fas fa-file-pdf"></i> Descargar PDF
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Solicitud de Cancelación -->
    <div class="modal fade" id="modalCancelarCita" tabindex="-1" aria-labelledby="modalCancelarCitaLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header" style="background:#ee7826; color:#fff;">
                    <h5 class="modal-title fw-bold" id="modalCancelarCitaLabel">
                        <i class="fas fa-xmark me-2"></i> Solicitud de Cancelación
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <form id="formCancelarCita">
                        <input type="hidden" name="cita_id" id="cancelarCitaId">

                        <div class="mb-3">
                            <label for="motivo" class="form-label fw-bold">Motivo de la cancelación:</label>
                            <textarea class="form-control" name="motivo" id="motivo" rows="3" required></textarea>
                        </div>

                        <div class="alert alert-info small">
                            El estado que se enviará será <strong>"Cancelada por Proveedor"</strong>.
                        </div>

                        <button type="submit" class="btn btn-danger w-100">
                            <i class="fas fa-paper-plane"></i> Enviar solicitud
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>



    <script>
        document.getElementById('formDescargarPDF').addEventListener('submit', function(e) {
            e.preventDefault();

            const fechaInicio = document.getElementById('fechaInicio').value;
            const fechaFin = document.getElementById('fechaFin').value;
            const sucursal = document.getElementById('sucursal').value;

            if (!fechaInicio || !fechaFin) {
                Swal.fire('Atención', 'Debes seleccionar ambas fechas', 'warning');
                return;
            }

            // Aquí mandamos a la ruta que genera el PDF
            const url =
                `/proveedor/reporte/pdf?fechaInicio=${fechaInicio}&fechaFin=${fechaFin}&sucursal=${sucursal}`;
            window.open(url, '_blank');

            $('#modalDescargarPDF').modal('hide');
        });


        function mostrarDetalle(reserva) {
            // --- Datos básicos
            document.getElementById('detalleProveedorNombre').innerText = reserva.proveedor_nombre ?? '-';
            document.getElementById('detalleProveedorRFC').innerText = reserva.RFC_proveedor ?? '-';
            document.getElementById('detalleProveedorCorreo').innerText = reserva.Correo ?? '-';
            document.getElementById('detalleProveedorTelefono').innerText = reserva.Telefono ?? '-';
            document.getElementById('detalleProveedorDireccion').innerText = reserva.Direccion ?? '-';
            document.getElementById('detalleSucursal').innerText = reserva.sucursal_nombre ?? '-';
            document.getElementById('detalleTransporte').innerText = reserva.transporte_nombre ?? '-';

            // Fecha y hora
            document.getElementById('detalleFecha').innerText = reserva.fecha ?
                reserva.fecha.split('-').reverse().join('/') :
                '-';

            document.getElementById('detalleHora').innerText = reserva.hora ?
                new Date('1970-01-01T' + reserva.hora).toLocaleTimeString('es-MX', {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: true
                }) :
                '-';

            // Órdenes de compra ya formateadas por el backend en "ordenes_detalle"
            document.getElementById('detalleOrdenes').innerText = reserva.ordenes_detalle ?? '-';

            // Estado (badge)
            const estado = (reserva.estado || '').toLowerCase().trim();
            const badge = document.getElementById('detalleEstadoModal');
            badge.innerText = reserva.estado ?? '-';
            badge.className = 'badge px-3 py-2 fs-6 ' + (
                estado === 'pendiente' ? 'bg-secondary' :
                estado === 'confirmada' ? 'badge-confirmado' :
                estado === 'asistió' ? 'bg-success' :
                estado === 'no asistió' ? 'bg-dark' :
                estado === 'cancelada' || estado === 'cancelado' ?
                'bg-danger' :
                estado === 'en proceso' ? 'bg-warning' :
                estado === 'recepción tardía' ? 'bg-dark' :
                estado === 'cancelada por proveedor' ? 'bg-danger' :
                estado === 'no programado' ? 'bg-info' :
                'bg-light text-dark'
            );

            // ===== Comentario de almacén =====
            const detalleComentario = document.getElementById('detalleComentario');
            const comentarioRow = detalleComentario ? detalleComentario.closest('.row') : null;
            const comentario = (reserva.commit_afterrecep || '').toString().trim();

            if (detalleComentario) detalleComentario.textContent = comentario || '—';
            if (comentarioRow) comentarioRow.style.display = comentario ? 'block' : 'none';

            // ===== Evidencia adjunta =====
            const wrap = document.getElementById('detalleEvidenciaWrap');
            const link = document.getElementById('detalleEvidenciaLink');
            const meta = document.getElementById('detalleEvidenciaMeta');

            const path = (reserva.evidencia_path || '').toString().trim();

            if (wrap && link && meta) {
                if (path) {
                    //const url = `${window.location.origin}/storage/${path}`; // requiere storage:link
                    const url = `/proveedor/evidencia/${reserva.id}`;
                    document.getElementById('detalleEvidenciaLink').href = url;
                    link.href = url;
                    link.textContent = reserva.evidencia_nombre || 'Descargar evidencia';

                    const size = Number(reserva.evidencia_size || 0);
                    const sizeTxt = size ? `${(size / 1024 / 1024).toFixed(2)} MB` : '';
                    const mime = reserva.evidencia_mime || '';

                    meta.textContent = [mime, sizeTxt].filter(Boolean).join(' · ');
                    wrap.style.display = 'block';
                } else {
                    wrap.style.display = 'none';
                    link.removeAttribute('href');
                    link.textContent = 'Ver evidencia';
                    meta.textContent = '';
                }
            }

            // Mostrar modal
            $('#modalDetalles').modal('show');
        }



        $(document).ready(function() {
            $('#tablaReservas').DataTable({
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
                },
                pageLength: 10,
                lengthMenu: [25, 50, 100],
                searching: true,
                paging: true,
                ordering: true,
                order: [
                    [0, 'desc']
                ],
                columnDefs: [{
                    targets: [2, 3, 4],
                    className: 'text-center'
                }],
                responsive: true,
                autoWidth: false,
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                    '<"row"<"col-sm-12"tr>>' +
                    '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                initComplete: function() {
                    this.api().columns().every(function() {
                        var column = this;
                        if (column.index() === 0) {
                            var input = $(
                                    '<input type="text" class="form-control" placeholder="Buscar fecha">'
                                )
                                .appendTo($(column.footer()).empty())
                                .on('keyup change', function() {
                                    if (column.search() !== this.value) {
                                        column.search(this.value).draw();
                                    }
                                });
                        }
                    });
                }
            });
        });


        // Función para abrir el modal de cancelación
        function abrirModalCancelar(id) {
            document.getElementById('cancelarCitaId').value = id;
            $('#modalCancelarCita').modal('show');
        }

        document.getElementById('formCancelarCita').addEventListener('submit', function(e) {
            e.preventDefault();

            const citaId = document.getElementById('cancelarCitaId').value;
            const motivo = document.getElementById('motivo').value;

            fetch('/proveedor/cancelacion/solicitar', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        cita_id: citaId,
                        motivo
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Solicitud enviada', 'Se notificó a recepción.', 'success');
                        $('#modalCancelarCita').modal('hide');
                    } else {
                        Swal.fire('Error', 'No se pudo enviar la solicitud.', 'error');
                    }
                })
                .catch(err => Swal.fire('Error', 'Error de servidor.', 'error'));
        });
    </script>

@endsection
