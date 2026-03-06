@extends('layouts.movil')
@section('title', 'Consultar')
@section('content')
    @include('includes.scripts.Datatables')
    @include('includes.scripts.SweetAlert2@11')
    @include('includes.scripts.bootstrap')

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('assets/css/rol/proveedor/historial.css') }}">

    <x-sidebar />
    <div class="container-fluid con-sidebar">
        <div class="row justify-content-center">

            <div class="container">
                <div class="card shadow p-3 mb-4" style="border: 2px solid #ee7826; border-radius: 17px;">
                    <div class="row row-cols-2 row-cols-md-5 g-2 text-center">
                        <div class="col">
                            <div class="card shadow-sm border-0 p-2 filter-card" data-status="todos" role="button"
                                tabindex="0" style=" border-left: 4px solid #ee7826; border-radius: 12px;">
                                <i class="fas fa-calendar-alt fa-lg text-warning mb-1"></i>
                                <h6 class="text-uppercase fw-bold text-muted mb-0" style="font-size: 0.8rem;">Total Citas
                                </h6>
                                <h5 class="fw-bold text-dark mb-0">{{ $totalCitas }}</h5>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card shadow-sm border-0 p-2 filter-card" data-status="pendientes" role="button"
                                tabindex="0" style="border-left: 4px solid #f19a0f; border-radius: 12px;">
                                <i class="fas fa-clock fa-lg text-warning mb-1"></i>
                                <h6 class="text-uppercase fw-bold text-muted mb-0" style="font-size: 0.8rem;">Pendientes
                                </h6>
                                <h5 class="fw-bold text-dark mb-0">{{ $citasPendientes }}</h5>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card shadow-sm border-0 p-2 filter-card" data-status="confirmadas" role="button"
                                tabindex="0" style="border-left: 4px solid #28a745; border-radius: 12px;">
                                <i class="fas fa-check-circle fa-lg text-success mb-1"></i>
                                <h6 class="text-uppercase fw-bold text-muted mb-0" style="font-size: 0.8rem;">Confirmadas
                                </h6>
                                <h5 class="fw-bold text-dark mb-0">{{ $citasConfirmadas }}</h5>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card shadow-sm border-0 p-2 filter-card" data-status="canceladas" role="button"
                                tabindex="0" style="border-left: 4px solid #e74c3c; border-radius: 12px;">
                                <i class="fas fa-times-circle fa-lg text-danger mb-1"></i>
                                <h6 class="text-uppercase fw-bold text-muted mb-0" style="font-size: 0.8rem;">Canceladas
                                </h6>
                                <h5 class="fw-bold text-dark mb-0">{{ $citasCanceladas }}</h5>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card shadow-sm border-0 p-2 filter-card" data-status="asistio" role="button"
                                tabindex="0" style="border-left: 4px solid #0d6efd; border-radius: 12px;">
                                <i class="fas fa-user-check fa-lg text-primary mb-1"></i>
                                <h6 class="text-uppercase fw-bold text-muted mb-0" style="font-size: 0.8rem;">Asistió</h6>
                                <h5 class="fw-bold text-dark mb-0">{{ $citasAsistio ?? 0 }}</h5>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card shadow-sm border-0 p-2 filter-card" data-status="no-asistio" role="button"
                                tabindex="0" style="border-left: 4px solid #6c757d; border-radius: 12px;">
                                <i class="fas fa-user-times fa-lg text-dark mb-1"></i>
                                <h6 class="text-uppercase fw-bold text-muted mb-0" style="font-size: 0.8rem;">No Asistió
                                </h6>
                                <h5 class="fw-bold text-dark mb-0">{{ $citasNoAsistio ?? 0 }}</h5>
                            </div>
                        </div>

                        <div class="col">
                            <div class="card shadow-sm border-0 p-2 filter-card" data-status="en-proceso" role="button"
                                tabindex="0" style="border-left: 4px solid #ffc107; border-radius: 12px;">
                                <i class="fas fa-spinner fa-lg text-dark mb-1"></i>
                                <h6 class="text-uppercase fw-bold text-muted mb-0" style="font-size: 0.8rem;">En Proceso
                                </h6>
                                <h5 class="fw-bold text-dark mb-0">{{ $citasEnProceso ?? 0 }}</h5>
                            </div>
                        </div>

                        <div class="col">
                            <div class="card shadow-sm border-0 p-2 filter-card" data-status="recepcion-tardia"
                                role="button" tabindex="0" style="border-left: 4px solid #6c757d; border-radius: 12px;">
                                <i class="fas fa-hourglass-start fa-lg text-orange mb-1"></i>
                                <h6 class="text-uppercase fw-bold text-muted mb-0" style="font-size: 0.8rem;">Recepción
                                    tardía</h6>
                                <h5 class="fw-bold text-dark mb-0">{{ $citasRecepcionTardia ?? 0 }}</h5>
                            </div>
                        </div>

                        <div class="col">
                            <div class="card shadow-sm border-0 p-2 filter-card" data-status="cancelada-por-proveedor"
                                role="button" tabindex="0" style="border-left: 4px solid #dc3545; border-radius: 12px;">
                                <i class="fas fa-user-times fa-lg text-danger mb-1"></i>
                                <h6 class="text-uppercase fw-bold text-muted mb-0" style="font-size: 0.8rem;">Cancelada por
                                    proveedor</h6>
                                <h5 class="fw-bold text-dark mb-0">{{ $citasCanceladasPorProveedor ?? 0 }}</h5>
                            </div>
                        </div>

                        <div class="col">
                            <div class="card shadow-sm border-0 p-2 filter-card" data-status="no-programado"
                                role="button" tabindex="0"
                                style="border-left: 4px solid #17a2b8; border-radius: 12px;">
                                <i class="fas fa-info-circle fa-lg text-info mb-1"></i>
                                <h6 class="text-uppercase fw-bold text-muted mb-0" style="font-size: 0.8rem;">No
                                    Programado
                                </h6>
                                <h5 class="fw-bold text-dark mb-0">{{ $citasNoProgramado ?? 0 }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container">
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
                                        @php
                                            try {
                                                $orderDt = \Carbon\Carbon::parse(
                                                    $reserva->fecha . ' ' . ($reserva->hora ?? '00:00:00'),
                                                )->format('Y-m-d H:i:s');
                                            } catch (\Exception $e) {
                                                $orderDt = '';
                                            }
                                            $displayDate = $reserva->fecha
                                                ? \Carbon\Carbon::parse($reserva->fecha)->format('d/m/Y')
                                                : '-';

                                            $displayTime = $reserva->hora
                                                ? \Carbon\Carbon::parse($reserva->hora)->format('h:i A')
                                                : '-';

                                        @endphp
                                        <td data-order="{{ $orderDt }}">{{ $displayDate }}
                                            <br>
                                            {{ $displayTime }}
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
                                            <span
                                                class="badge {{ $badgeClass }}">{{ ucfirst($reserva->estado) }}</span>
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

        </div>
    </div>

    </div>
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

                        <div id="detalleTimeline" class="timeline-wrap" style="display:block;">
                            <small class="text-muted">Historial de estados</small>
                            <div class="timeline" aria-hidden="false"></div>
                        </div>
                        <div id="detalleGraph" class="graph-wrap mt-3" style="display:block;">
                            <small class="text-uppercase text-muted fw-bold">Relación OC → Entradas</small>
                            <div id="graphSvgContainer" style="width:100%; overflow:auto;">
                                <svg id="graphSvg" xmlns="http://www.w3.org/2000/svg" width="100%"
                                    height="220"></svg>
                            </div>
                        </div>
                    </div>



                </div>
            </div>
        </div>
    </div>

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

            const url =
                `/proveedor/reporte/pdf?fechaInicio=${fechaInicio}&fechaFin=${fechaFin}&sucursal=${sucursal}`;
            window.open(url, '_blank');

            $('#modalDescargarPDF').modal('hide');
        });

        function mostrarDetalle(reserva) {
            document.getElementById('detalleProveedorNombre').innerText = reserva.proveedor_nombre ?? '-';
            document.getElementById('detalleProveedorRFC').innerText = reserva.RFC_proveedor ?? '-';
            document.getElementById('detalleProveedorCorreo').innerText = reserva.Correo ?? '-';
            document.getElementById('detalleProveedorTelefono').innerText = reserva.Telefono ?? '-';
            document.getElementById('detalleProveedorDireccion').innerText = reserva.Direccion ?? '-';
            document.getElementById('detalleSucursal').innerText = reserva.sucursal_nombre ?? '-';
            document.getElementById('detalleTransporte').innerText = reserva.transporte_nombre ?? '-';

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

            document.getElementById('detalleOrdenes').innerText = reserva.ordenes_detalle ?? '-';

            (function buildTimeline() {
                var container = document.querySelector('#detalleTimeline .timeline');
                if (!container) return;
                container.innerHTML = '';

                function fmtDate(val) {
                    if (!val) return '';
                    try {
                        var d = new Date(val);
                        if (isNaN(d.getTime())) return '';
                        var dd = String(d.getDate()).padStart(2, '0');
                        var mm = String(d.getMonth() + 1).padStart(2, '0');
                        var yyyy = d.getFullYear();
                        var hh = String(d.getHours()).padStart(2, '0');
                        var min = String(d.getMinutes()).padStart(2, '0');
                        return dd + '/' + mm + '/' + yyyy + ' ' + hh + ':' + min;
                    } catch (e) {
                        return '';
                    }
                }

                var steps = [{
                        key: 'created_at',
                        label: 'Creada'
                    },
                    {
                        key: 'estado_confirmado',
                        label: 'Confirmada'
                    },
                    {
                        key: 'estado_proceso',
                        label: 'En proceso'
                    },
                    {
                        key: 'estado_asistio',
                        label: 'Asistió'
                    },
                    {
                        key: 'estado_no_asistio',
                        label: 'No asistió'
                    },
                    {
                        key: 'estado_cancelado',
                        label: 'Cancelada'
                    },
                    {
                        key: 'estado_cancelada_sp',
                        label: 'Cancelada SP'
                    },
                    {
                        key: 'estado_tardia',
                        label: 'Recepción tardía'
                    },
                    {
                        key: 'estado_timeout',
                        label: 'Timeout'
                    }
                ];

                var visible = steps.map(function(step) {
                    var val = reserva[step.key];
                    var dateTxt = '';
                    var active = false;

                    if (val === null || val === undefined || val === '') {
                        active = false;
                    } else if (typeof val === 'string') {
                        dateTxt = fmtDate(val);
                        if (dateTxt) active = true;
                        else if (val === '1' || val.toLowerCase() === 'true') active = true;
                    } else if (typeof val === 'number' || typeof val === 'boolean') {
                        active = Boolean(val);
                    } else {
                        active = true;
                    }

                    if (active && !dateTxt) dateTxt = fmtDate(reserva.updated_at) || fmtDate(reserva
                        .created_at) || '';

                    return active ? {
                        key: step.key,
                        label: step.label,
                        date: dateTxt
                    } : null;
                }).filter(Boolean);

                visible.forEach(function(step, idx) {
                    var item = document.createElement('div');
                    item.className = 'timeline-item active';

                    var dot = document.createElement('div');
                    dot.className = 'timeline-dot';
                    item.appendChild(dot);

                    var label = document.createElement('div');
                    label.className = 'timeline-label';
                    label.textContent = step.label;
                    item.appendChild(label);

                    var date = document.createElement('div');
                    date.className = 'timeline-date';
                    date.textContent = step.date || '-';
                    item.appendChild(date);

                    container.appendChild(item);

                    if (idx < visible.length - 1) {
                        var conn = document.createElement('div');
                        conn.className = 'timeline-connector active';
                        container.appendChild(conn);
                    }
                });
            })();

            const estado = (reserva.estado || '').toLowerCase().trim();
            const badge = document.getElementById('detalleEstadoModal');
            badge.innerText = reserva.estado ?? '-';
            badge.className = 'badge px-3 py-2 fs-6 ' + (
                estado === 'pendiente' ? 'bg-secondary' :
                estado === 'confirmada' ? 'badge-confirmado' :
                estado === 'asistió' ? 'bg-success' :
                estado === 'no asistió' ? 'bg-dark' :
                estado === 'cancelada' || estado === 'cancelado' ? 'bg-danger' :
                estado === 'en proceso' ? 'bg-warning' :
                estado === 'recepción tardía' ? 'bg-dark' :
                estado === 'cancelada por proveedor' ? 'bg-danger' :
                estado === 'no programado' ? 'bg-info' :
                'bg-light text-dark'
            );

            const detalleComentario = document.getElementById('detalleComentario');
            const comentarioRow = detalleComentario ? detalleComentario.closest('.row') : null;
            const comentario = (reserva.commit_afterrecep || '').toString().trim();

            if (detalleComentario) detalleComentario.textContent = comentario || '—';
            if (comentarioRow) comentarioRow.style.display = comentario ? 'block' : 'none';

            const wrap = document.getElementById('detalleEvidenciaWrap');
            const link = document.getElementById('detalleEvidenciaLink');
            const meta = document.getElementById('detalleEvidenciaMeta');

            const path = (reserva.evidencia_path || '').toString().trim();

            if (wrap && link && meta) {
                if (path) {

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

            // Cargar relaciones OC -> Entradas (gráfico)
            if (reserva && reserva.id) {
                cargarOCRelacionadas(reserva.id);
            }

            $('#modalDetalles').modal('show');
        }



        $(document).ready(function() {
            var table = $('#tablaReservas').DataTable({
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

            // Mapa de status: key = data-status en el card, value = patrón regex a buscar en la columna Estado
            var statusMap = {
                'pendientes': 'pendiente',
                'confirmadas': 'confirmada',
                'canceladas': 'cancelada|cancelado',
                'asistio': 'asistió|asistio',
                'no-asistio': 'no asistió|no asistio',
                'en-proceso': 'en proceso',
                'recepcion-tardia': 'recepción tardía|recepcion tardia',
                'cancelada-por-proveedor': 'cancelada por proveedor',
                'no-programado': 'no programado'
            };

            // Handler para los cards que filtran
            $('.filter-card').on('click keypress', function(e) {
                if (e.type === 'keypress' && e.key !== 'Enter' && e.key !== ' ') return;
                var key = $(this).data('status');

                // 'todos' limpia el filtro
                if (!key || key === 'todos') {
                    $('.filter-card').removeClass('active');
                    table.column(4).search('').draw();
                    return;
                }

                var pattern = statusMap[key] || key;

                if ($(this).hasClass('active')) {
                    $(this).removeClass('active');
                    table.column(4).search('').draw();
                } else {
                    $('.filter-card').removeClass('active');
                    $(this).addClass('active');
                    table.column(4).search('^(' + pattern + ')$', true, false, true).draw();
                }
            });
        });

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

        /* --- Gráfico OC -> Entradas: funciones auxiliares --- */
        function cargarOCRelacionadas(reservaId) {
            const svg = document.getElementById('graphSvg');
            if (svg) svg.innerHTML = '';

            fetch(`/proveedor/reserva/${reservaId}/ocs`)
                .then(res => res.json())
                .then(data => {
                    console.log('ocRelacionadas response:', data);
                    const ocs = data.ocs || [];
                    renderGraph(ocs);
                })
                .catch(err => {
                    console.error('Error cargando OCs relativas:', err);
                });
        }

        function renderGraph(ocs) {
            const svg = document.getElementById('graphSvg');
            if (!svg) return;

            const padding = 20;
            const leftX = 120;
            const rightX = 420;
            const rowH = 68;

            if (!ocs.length) {
                const text = document.createElementNS('http://www.w3.org/2000/svg', 'text');
                text.setAttribute('x', 20);
                text.setAttribute('y', 30);
                text.setAttribute('fill', '#666');
                text.textContent = 'No se encontraron Órdenes de Compra vinculadas';
                svg.appendChild(text);
                return;
            }

            svg.setAttribute('height', Math.max(220, ocs.length * rowH + padding));

            ocs.forEach(function(oc, idx) {
                const y = padding + idx * rowH;

                // Nodo OC (izquierda)
                const gOc = document.createElementNS('http://www.w3.org/2000/svg', 'g');
                gOc.setAttribute('class', 'node oc');
                gOc.setAttribute('data-docnum', oc.docnum || '');
                gOc.setAttribute('cursor', 'pointer');

                const rect = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
                rect.setAttribute('x', leftX - 100);
                rect.setAttribute('y', y);
                rect.setAttribute('rx', 8);
                rect.setAttribute('ry', 8);
                rect.setAttribute('width', 200);
                rect.setAttribute('height', 48);
                rect.setAttribute('fill', '#f8f9fa');
                rect.setAttribute('stroke', '#007bff');
                rect.setAttribute('stroke-width', 1);

                const t1 = document.createElementNS('http://www.w3.org/2000/svg', 'text');
                t1.setAttribute('x', leftX - 90);
                t1.setAttribute('y', y + 18);
                t1.setAttribute('fill', '#000');
                t1.setAttribute('style', 'font-weight:700; font-size:12px');
                t1.textContent = oc.docnum || '-';

                const t2 = document.createElementNS('http://www.w3.org/2000/svg', 'text');
                t2.setAttribute('x', leftX - 90);
                t2.setAttribute('y', y + 36);
                t2.setAttribute('fill', '#555');
                t2.setAttribute('style', 'font-size:11px');
                t2.textContent = oc.fecha ? (oc.fecha + ' • ' + (oc.total || '')) : (oc.total || '');

                gOc.appendChild(rect);
                gOc.appendChild(t1);
                gOc.appendChild(t2);

                gOc.addEventListener('click', function() {
                    openOcModal(oc, 'oc');
                });

                svg.appendChild(gOc);

                // Entradas (si hay) a la derecha
                const entradas = oc.entradas || [];
                if (entradas.length) {
                    entradas.forEach(function(en, j) {
                        const ey = y + j * 40;
                        const gEn = document.createElementNS('http://www.w3.org/2000/svg', 'g');
                        gEn.setAttribute('class', 'node entrada');
                        gEn.setAttribute('cursor', 'pointer');

                        const rect2 = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
                        rect2.setAttribute('x', rightX - 40);
                        rect2.setAttribute('y', ey);
                        rect2.setAttribute('rx', 6);
                        rect2.setAttribute('ry', 6);
                        rect2.setAttribute('width', 220);
                        rect2.setAttribute('height', 40);
                        rect2.setAttribute('fill', '#fff8e1');
                        rect2.setAttribute('stroke', '#ffc107');
                        rect2.setAttribute('stroke-width', 1);

                        const et1 = document.createElementNS('http://www.w3.org/2000/svg', 'text');
                        et1.setAttribute('x', rightX - 30);
                        et1.setAttribute('y', ey + 18);
                        et1.setAttribute('fill', '#000');
                        et1.setAttribute('style', 'font-weight:600; font-size:12px');
                        et1.textContent = en.docnum || '-';

                        const et2 = document.createElementNS('http://www.w3.org/2000/svg', 'text');
                        et2.setAttribute('x', rightX - 30);
                        et2.setAttribute('y', ey + 34);
                        et2.setAttribute('fill', '#444');
                        et2.setAttribute('style', 'font-size:11px');
                        et2.textContent = en.ref_proveedor ? (en.ref_proveedor + ' • ' + (en.total || '')) :
                            (en.total || '');

                        gEn.appendChild(rect2);
                        gEn.appendChild(et1);
                        gEn.appendChild(et2);

                        const line = document.createElementNS('http://www.w3.org/2000/svg', 'line');
                        line.setAttribute('x1', leftX + 10);
                        line.setAttribute('y1', y + 24);
                        line.setAttribute('x2', rightX - 50);
                        line.setAttribute('y2', ey + 20);
                        line.setAttribute('stroke', '#6c757d');
                        line.setAttribute('stroke-width', 1.2);
                        line.setAttribute('marker-end', 'url(#arrow)');

                        svg.appendChild(line);

                        gEn.addEventListener('click', function() {
                            openOcModal(en, 'entrada');
                        });

                        svg.appendChild(gEn);
                    });
                }
            });

            if (!svg.querySelector('defs')) {
                const defs = document.createElementNS('http://www.w3.org/2000/svg', 'defs');
                const marker = document.createElementNS('http://www.w3.org/2000/svg', 'marker');
                marker.setAttribute('id', 'arrow');
                marker.setAttribute('markerWidth', '8');
                marker.setAttribute('markerHeight', '8');
                marker.setAttribute('refX', '6');
                marker.setAttribute('refY', '3');
                marker.setAttribute('orient', 'auto');
                const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                path.setAttribute('d', 'M0,0 L6,3 L0,6 Z');
                path.setAttribute('fill', '#6c757d');
                marker.appendChild(path);
                defs.appendChild(marker);
                svg.insertBefore(defs, svg.firstChild);
            }
        }

        function openOcModal(item, tipo) {
            let title = tipo === 'oc' ? 'Orden de Compra' : 'Entrada de mercancía';
            let cuerpo = '';
            cuerpo += '<div><strong>Documento:</strong> ' + (item.docnum || '-') + '</div>';
            cuerpo += '<div><strong>Fecha:</strong> ' + (item.fecha || '-') + '</div>';
            if (tipo === 'oc') {
                cuerpo += '<div><strong>Almacén:</strong> ' + (item.almacen || '-') + '</div>';
                cuerpo += '<div><strong>Total:</strong> ' + (item.total || '-') + '</div>';
            } else {
                cuerpo += '<div><strong>No. ref. proveedor:</strong> ' + (item.ref_proveedor || '-') + '</div>';
                cuerpo += '<div><strong>Total:</strong> $ ' + (item.total || '-') + '</div>';
            }

            const modalBody = document.querySelector('#modalDetalles .modal-body');
            if (modalBody) {
                const container = document.createElement('div');
                container.className = 'mt-3 p-3 border rounded bg-white';
                container.innerHTML = '<h6 class="fw-bold">' + title + '</h6>' + cuerpo;
                Swal.fire({
                    title: title,
                    html: container.innerHTML,
                    width: 600,
                    icon: 'info'
                });
            }
        }
    </script>

@endsection
