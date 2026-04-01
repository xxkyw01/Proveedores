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
                {{-- Filtros de eventos  --}}
                <div class="card shadow p-3 mb-4" style="border: 2px solid #ee7826; border-radius: 17px;">
                    <div class="row row-cols-2 row-cols-md-5 g-2 text-center">
                        <div class="col">
                            <div class="card shadow-sm border-0 p-2 filter-card" data-status="todos" role="button"
                                tabindex="0" style=" border-left: 4px solid #ee7826; border-radius: 12px;">
                                <i class="fas fa-calendar-alt fa-lg text-warning mb-1"></i>
                                <h6 class="text-uppercase fw-bold text-muted mb-0" style="font-size: 0.8rem;">Total Citas
                                </h6>
                                <h5 id="count-todos" class="fw-bold text-dark mb-0">{{ $totalCitas }}</h5>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card shadow-sm border-0 p-2 filter-card" data-evento="programada" role="button"
                                tabindex="0" style="border-left: 4px solid #28a745; border-radius: 12px;">
                                <i class="fas fa-calendar-check fa-lg text-warning mb-1"></i>
                                <h6 class="text-uppercase fw-bold text-muted mb-0" style="font-size: 0.8rem;">Programada
                                </h6>
                                <h5 class="fw-bold text-dark mb-0">{{ $eventoProgramada }}</h5>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card shadow-sm border-0 p-2 filter-card" data-evento="no-programada" role="button"
                                tabindex="0" style="border-left: 4px solid #e74c3c; border-radius: 12px;">
                                <i class="fas fa-calendar-plus fa-lg text-success mb-1"></i>
                                <h6 class="text-uppercase fw-bold text-muted mb-0" style="font-size: 0.8rem;">No Programada
                                </h6>
                                <h5 class="fw-bold text-dark mb-0">{{ $eventoNoProgramada }}</h5>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card shadow-sm border-0 p-2 filter-card" data-evento="expres" role="button"
                                tabindex="0" style="border-left: 4px solid #f3d321; border-radius: 12px;">
                                <i class="fas fa-shipping-fast fa-lg text-danger mb-1"></i>
                                <h6 class="text-uppercase fw-bold text-muted mb-0" style="font-size: 0.8rem;">Exprés
                                </h6>
                                <h5 class="fw-bold text-dark mb-0">{{ $eventoExpres }}</h5>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card shadow-sm border-0 p-2 filter-card" data-evento="apartado" role="button"
                                tabindex="0" style="border-left: 4px solid #0d6efd; border-radius: 12px;">
                                <i class="fas fa-bookmark fa-lg text-primary mb-1"></i>
                                <h6 class="text-uppercase fw-bold text-muted mb-0" style="font-size: 0.8rem;">Apartado</h6>
                                <h5 class="fw-bold text-dark mb-0">{{ $eventoApartado ?? 0 }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Filtros de estado --}}
                <div class="card shadow p-3 mb-4" style="border: 2px solid #ee7826; border-radius: 17px;">
                    <div class="row row-cols-2 row-cols-md-5 g-2 text-center">
                        <div class="col">
                            <div class="card shadow-sm border-0 p-2 filter-card" data-status="pendientes" role="button"
                                tabindex="0" style="border-left: 4px solid #f19a0f; border-radius: 12px;">
                                <i class="fas fa-clock fa-lg text-warning mb-1"></i>
                                <h6 class="text-uppercase fw-bold text-muted mb-0" style="font-size: 0.8rem;">Pendientes
                                </h6>
                                <h5 id="count-pendientes" class="fw-bold text-dark mb-0">{{ $citasPendientes }}</h5>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card shadow-sm border-0 p-2 filter-card" data-status="confirmadas" role="button"
                                tabindex="0" style="border-left: 4px solid #28a745; border-radius: 12px;">
                                <i class="fas fa-check-circle fa-lg text-success mb-1"></i>
                                <h6 class="text-uppercase fw-bold text-muted mb-0" style="font-size: 0.8rem;">Confirmadas
                                </h6>
                                <h5 id="count-confirmadas" class="fw-bold text-dark mb-0">{{ $citasConfirmadas }}</h5>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card shadow-sm border-0 p-2 filter-card" data-status="canceladas" role="button"
                                tabindex="0" style="border-left: 4px solid #e74c3c; border-radius: 12px;">
                                <i class="fas fa-times-circle fa-lg text-danger mb-1"></i>
                                <h6 class="text-uppercase fw-bold text-muted mb-0" style="font-size: 0.8rem;">Canceladas
                                </h6>
                                <h5 id="count-canceladas" class="fw-bold text-dark mb-0">{{ $citasCanceladas }}</h5>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card shadow-sm border-0 p-2 filter-card" data-status="asistio" role="button"
                                tabindex="0" style="border-left: 4px solid #0d6efd; border-radius: 12px;">
                                <i class="fas fa-user-check fa-lg text-primary mb-1"></i>
                                <h6 class="text-uppercase fw-bold text-muted mb-0" style="font-size: 0.8rem;">Asistió</h6>
                                <h5 id="count-asistio" class="fw-bold text-dark mb-0">{{ $citasAsistio ?? 0 }}</h5>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card shadow-sm border-0 p-2 filter-card" data-status="no-asistio" role="button"
                                tabindex="0" style="border-left: 4px solid #6c757d; border-radius: 12px;">
                                <i class="fas fa-user-times fa-lg text-dark mb-1"></i>
                                <h6 class="text-uppercase fw-bold text-muted mb-0" style="font-size: 0.8rem;">No Asistió
                                </h6>
                                <h5 id="count-no-asistio" class="fw-bold text-dark mb-0">{{ $citasNoAsistio ?? 0 }}</h5>
                            </div>
                        </div>

                        <div class="col">
                            <div class="card shadow-sm border-0 p-2 filter-card" data-status="recepcion-tardia"
                                role="button" tabindex="0"
                                style="border-left: 4px solid #6c757d; border-radius: 12px;">
                                <i class="fas fa-hourglass-start fa-lg text-orange mb-1"></i>
                                <h6 class="text-uppercase fw-bold text-muted mb-0" style="font-size: 0.8rem;">Asistió
                                    Fuera de Horario</h6>
                                <h5 id="count-recepcion-tardia" class="fw-bold text-dark mb-0">
                                    {{ $citasRecepcionTardia ?? 0 }}</h5>
                            </div>
                        </div>

                        <div class="col">
                            <div class="card shadow-sm border-0 p-2 filter-card" data-status="cancelada-por-proveedor"
                                role="button" tabindex="0"
                                style="border-left: 4px solid #dc3545; border-radius: 12px;">
                                <i class="fas fa-user-times fa-lg text-danger mb-1"></i>
                                <h6 class="text-uppercase fw-bold text-muted mb-0" style="font-size: 0.8rem;">Cancelada
                                    por
                                    proveedor</h6>
                                <h5 id="count-cancelada-por-proveedor" class="fw-bold text-dark mb-0">
                                    {{ $citasCanceladasPorProveedor ?? 0 }}</h5>
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
                                    <th>Tipo Evento</th>
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
                                                $evento = strtolower(trim($reserva->tipo_evento ?? ''));
                                                $eventoBadgeClass = match ($evento) {
                                                    'programada' => 'bg-success',
                                                    'no programada', 'no-programada', 'noprogramada' => 'bg-danger',
                                                    'expres', 'paquetería express' => 'bg-warning',
                                                    'apartado' => 'bg-primary',
                                                    default => 'bg-light text-dark',
                                                };
                                            @endphp
                                            <span
                                                class="badge {{ $eventoBadgeClass }}">{{ ucfirst($reserva->tipo_evento ?? '-') }}</span>

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
                                                    'asistio fuera del horario' => 'bg-dark',
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
                estado === 'asistio fuera del horario' ? 'bg-dark' :
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

            if (reserva && reserva.id) {
                cargarOCRelacionadas(reserva.id);
            }

            $('#modalDetalles').modal('show');
        }

        $(document).ready(function() {
            var table;
            if ($.fn.DataTable && $.fn.DataTable.isDataTable('#tablaReservas')) {
                table = $('#tablaReservas').DataTable();
            } else {
                table = $('#tablaReservas').DataTable({
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
                        targets: [2, 3, 4, 5, 6],
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
            }
            
            try {
                window.table = table;
            } catch (e) {
                
            }

            var statusMap = {
                'pendientes': 'pendiente',
                'confirmadas': 'confirmada',
                'canceladas': 'cancelada|cancelado',
                'asistio': 'asistió|asistio',
                'no-asistio': 'no asistió|no asistio',
                'en-proceso': 'en proceso',
                'recepcion-tardia': 'recepción tardía|recepcion tardia|asistió fuera de horario|asistio fuera de horario|asistio fuera del horario|asistio fuera horario',
                'cancelada-por-proveedor': 'cancelada por proveedor',
                'no-programado': 'no programado'
            };

            var eventoMap = {
                'programada': 'programada',
                'no-programada': 'no programada|no-programada|noprogramada',
                'expres': 'expres|exprés|paquetería express|express',
                'apartado': 'apartado'
            };

            $('.filter-card').on('click keypress', function(e) {
                if (e.type === 'keypress' && e.key !== 'Enter' && e.key !== ' ') return;
                var key = $(this).data('status');

                var statusKey = $(this).data('status');
                var eventoKey = $(this).data('evento');

                if ((!statusKey && !eventoKey) || statusKey === 'todos') {
                    $('.filter-card').removeClass('active');
                    table.column(5).search('').draw();
                    table.column(4).search('').draw();
                    return;
                }

                if (eventoKey) {
                    var pattern = eventoMap[eventoKey] || eventoKey;
                    if ($(this).hasClass('active')) {
                        $(this).removeClass('active');
                        table.column(4).search('').draw();
                    } else {
                        $('.filter-card').removeClass('active');
                        $(this).addClass('active');
                        table.column(4).search('(' + pattern + ')', true, false, true).draw();
                        table.column(4).search('(' + pattern + ')', true, false, true).draw();
                    }
                    return;
                }

                var pattern = statusMap[statusKey] || statusKey;
                if ($(this).hasClass('active')) {
                    $(this).removeClass('active');
                    table.column(5).search('').draw();
                } else {
                    $('.filter-card').removeClass('active');
                    $(this).addClass('active');
                    table.column(5).search('(' + pattern + ')', true, false, true).draw();
                    table.column(5).search('(' + pattern + ')', true, false, true).draw();
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

            function updateCounters() {
                if (!table) return;
                var nodes = table.rows({
                    search: 'applied'
                }).nodes();
                var total = nodes.length;
                var pendientes = 0,
                    confirmadas = 0,
                    canceladas = 0,
                    asistio = 0,
                    noAsistio = 0,
                    recepcionTardia = 0,
                    canceladaProveedor = 0;

                $(nodes).each(function() {
                    var estado = $(this).find('td').eq(5).text().toLowerCase().trim();
                    if (!estado) return;

                    if (estado.indexOf('no') !== -1 && estado.indexOf('asist') !== -1) {
                        noAsistio++;
                        return;
                    }

                    if (estado.indexOf('recepcion') !== -1 || estado.indexOf('tardi') !== -1 || estado.indexOf(
                            'fuera') !== -1) {
                        recepcionTardia++;
                        return;
                    }

                    if (estado.indexOf('asist') !== -1) {
                        asistio++;
                    }

                    if (estado.indexOf('proveedor') !== -1) {
                        canceladaProveedor++;
                    }

                    if (estado.indexOf('cancel') !== -1) {
                        canceladas++;
                    }

                    if (estado.indexOf('confirm') !== -1) {
                        confirmadas++;
                    }

                    if (estado.indexOf('pendient') !== -1) {
                        pendientes++;
                    }
                });

                $('#count-todos').text(total);
                $('#count-pendientes').text(pendientes);
                $('#count-confirmadas').text(confirmadas);
                $('#count-canceladas').text(canceladas);
                $('#count-asistio').text(asistio);
                $('#count-no-asistio').text(noAsistio);
                $('#count-recepcion-tardia').text(recepcionTardia);
                $('#count-cancelada-por-proveedor').text(canceladaProveedor);
            }
            table.on('draw', function() {
                updateCounters();
            });

            updateCounters();
        }

        function renderGraph(ocs) {
            const svg = document.getElementById('graphSvg');
            if (!svg) return;
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

                const filter = document.createElementNS('http://www.w3.org/2000/svg', 'filter');
                filter.setAttribute('id', 'shadow');
                filter.setAttribute('x', '-20%');
                filter.setAttribute('y', '-20%');
                filter.setAttribute('width', '140%');
                filter.setAttribute('height', '140%');
                const fe = document.createElementNS('http://www.w3.org/2000/svg', 'feDropShadow');
                fe.setAttribute('dx', '0');
                fe.setAttribute('dy', '3');
                fe.setAttribute('stdDeviation', '4');
                fe.setAttribute('flood-color', '#000');
                fe.setAttribute('flood-opacity', '0.18');
                filter.appendChild(fe);
                defs.appendChild(filter);

                svg.insertBefore(defs, svg.firstChild);
            }

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

            var currentY = padding;
            for (var idx = 0; idx < ocs.length; idx++) {
                var oc = ocs[idx];

                var lines = [];
                lines.push(oc.docnum || '-');

                var po = oc.po || null;
                var fechaRaw = (po && (po.DocDate || po.DocDateString)) || oc.fecha_inicio || oc.fecha || oc.start_date || oc.fechaInicio || '';
                if (fechaRaw) {
                    var fechaTxt = fechaRaw;
                    try {
                        var d = new Date(fechaRaw);
                        if (!isNaN(d.getTime())) {
                            var dd = String(d.getDate()).padStart(2, '0');
                            var mm = String(d.getMonth() + 1).padStart(2, '0');
                            var yyyy = d.getFullYear();
                            fechaTxt = dd + '/' + mm + '/' + yyyy;
                        }
                    } catch (e) {}
                    lines.push('Fecha de inicio : ' + fechaTxt);
                }

                var fechaFin = (po && (po.TaxDate || po.TaxDateString)) || oc.fecha_final || oc.fecha_fin || oc.end_date || oc.fechaFin || '';
                var fechaFinTxt = '-';
                if (fechaFin) {
                    try {
                        var d2 = new Date(fechaFin);
                        if (!isNaN(d2.getTime())) {
                            var dd2 = String(d2.getDate()).padStart(2, '0');
                            var mm2 = String(d2.getMonth() + 1).padStart(2, '0');
                            var yyyy2 = d2.getFullYear();
                            fechaFinTxt = dd2 + '/' + mm2 + '/' + yyyy2;
                        } else {
                            fechaFinTxt = fechaFin;
                        }
                    } catch (e) { fechaFinTxt = fechaFin; }
                }
                lines.push('Fecha de Final : ' + fechaFinTxt);

                if (oc.almacen) lines.push('Almacén : ' + oc.almacen);
                if (oc.docentry) lines.push('DocEntry : ' + oc.docentry);
                if (oc.total !== undefined && oc.total !== null) {
                    try {
                        var totalNum = Number(oc.total);
                        lines.push('Total : ' + (isNaN(totalNum) ? oc.total : totalNum.toLocaleString('es-MX', { style: 'currency', currency: 'MXN' })));
                    } catch (e) {
                        lines.push('Total : ' + oc.total);
                    }
                }
                
                var articleCount = '';
                if (oc.lines && Array.isArray(oc.lines)) {
                    articleCount = oc.lines.length;
                } else {
                    var lc = oc.lineas_articulos || oc.lineas || oc.articulos || oc.lineas_count || oc.total_lineas || '';
                    if (typeof lc === 'number') articleCount = lc;
                    else if (typeof lc === 'string' && lc.trim() !== '') {
                        var parsed = parseInt(lc, 10);
                        if (!isNaN(parsed)) articleCount = parsed;
                    }
                }
                if (articleCount) lines.push(String(articleCount) + ' Articulos');

                var estadoTxt = oc.estado || oc.estado_oc || oc.status || oc.estadoOC || '';
                
                var rawEstado = oc.estado_documento || '';
                if (!estadoTxt && rawEstado) {
                    var dsLower = String(rawEstado).toLowerCase();
                    
                    if (dsLower.indexOf('bost_open') !== -1 || dsLower.indexOf('open') !== -1 || dsLower === 'o') {
                        estadoTxt = 'Abierto';
                    } else if (dsLower.indexOf('bost_closed') !== -1 || dsLower.indexOf('closed') !== -1 || dsLower === 'c') {
                        estadoTxt = 'Cerrado';
                    } else if (dsLower.indexOf('printed') !== -1 || dsLower.indexOf('imprim') !== -1) { 

                        estadoTxt = 'Abierto (Impreso)';
                    } else {

                        estadoTxt = String(rawEstado);
                    }
                }
                if (!estadoTxt && oc.state) estadoTxt = oc.state;
                if (!estadoTxt && oc.state) estadoTxt = oc.state;
                lines.push('Estado : ' + (estadoTxt || '-'));

                try {
                    console.log('renderGraph - OC raw object:', oc);
                } catch (e) {}

                (function addExtraFields() {
                    var shownKeys = {
                        'docnum': true, 'fecha_inicio': true, 'fecha': true, 'start_date': true, 'fechaInicio': true,
                        'fecha_final': true, 'fecha_fin': true, 'end_date': true, 'fechaFin': true,
                        'almacen': true, 'docentry': true, 'total': true, 'entradas': true,
                        'lineas_articulos': true, 'lineas': true, 'lines': true, 'articulos': true,
                        'lineas_count': true, 'total_lineas': true, 'estado': true, 'estado_oc': true,
                        'status': true, 'estadoOC': true, 'estado_documento': true, 'state': true
                    };

                    var extraAdded = 0;
                    var maxExtra = 6;

                    function pushField(key, val) {
                        if (extraAdded >= maxExtra) return false;
                        var label = key.replace(/[_\-]/g, ' ').replace(/([a-z])([A-Z])/g, '$1 $2');
                        lines.push(label.charAt(0).toUpperCase() + label.slice(1) + ' : ' + String(val));
                        extraAdded++;
                        return extraAdded < maxExtra;
                    }

                    for (var k in oc) {
                        if (!oc.hasOwnProperty(k)) continue;
                        if (shownKeys[k]) continue;
                        var v = oc[k];
                        if (v === null || v === undefined) continue;

                        if (typeof v === 'object') {
                            try {

                                var keys = Object.keys(v || {});
                                var primCount = 0;
                                for (var ki = 0; ki < keys.length; ki++) {
                                    var kk = keys[ki];
                                    var vv = v[kk];
                                    if (vv === null || vv === undefined) continue;
                                    if (typeof vv === 'object') continue;
                                    primCount++;
                                }

                                if (primCount > 0 && primCount <= 4) {
                                    for (var ki2 = 0; ki2 < keys.length; ki2++) {
                                        var kk2 = keys[ki2];
                                        var vv2 = v[kk2];
                                        if (vv2 === null || vv2 === undefined) continue;
                                        if (typeof vv2 === 'object') continue;
                                        var compoundKey = k + '.' + kk2;
                                        if (!pushField(compoundKey, vv2)) break;
                                    }
                                    if (extraAdded >= maxExtra) break;
                                    continue;
                                }
                            } catch (e) {

                            }
                            try {
                                var small = JSON.stringify(v);
                                if (small && small.length > 0) {
                                    if (!pushField(k, small.length > 120 ? small.slice(0, 120) + '...' : small)) break;
                                }
                            } catch (e) {}
                            if (extraAdded >= maxExtra) break;
                            continue;
                        }

                        if (!pushField(k, v)) break;
                    }
                })();

                var lineHeight = 20;
                var paddingRect = 14; 
                var rectHeight = Math.max(48, lines.length * lineHeight + paddingRect);

                var gOc = document.createElementNS('http://www.w3.org/2000/svg', 'g');
                gOc.setAttribute('class', 'node oc');
                gOc.setAttribute('data-docnum', oc.docnum || '');
                gOc.setAttribute('cursor', 'pointer');

                var rect = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
                rect.setAttribute('x', leftX - 100);
                rect.setAttribute('y', currentY);
                rect.setAttribute('rx', 8);
                rect.setAttribute('ry', 8);
                rect.setAttribute('width', 200);
                rect.setAttribute('height', rectHeight);
                rect.setAttribute('stroke-width', 2);

                var entradasArr = oc.entradas || [];
                var hasEntradas = (
                    (Array.isArray(entradasArr) && entradasArr.length > 0) ||
                    (oc.entradas_count && Number(oc.entradas_count) > 0) ||
                    (oc.has_entradas === true) ||
                    (oc.cantidad_entradas && Number(oc.cantidad_entradas) > 0) ||
                    (typeof oc.entradas === 'number' && oc.entradas > 0)
                );

                try { console.log('renderGraph - OC', oc.docnum, 'hasEntradas=', hasEntradas, 'entradas=', entradasArr); } catch (e) {}
                if (hasEntradas) {
                    rect.setAttribute('fill', '#fff3cd');
                    rect.setAttribute('stroke', '#ff9800');
                    rect.setAttribute('filter', 'url(#shadow)');
                } else {
                    rect.setAttribute('fill', '#f8f9fa');
                    rect.setAttribute('stroke', '#007bff');
                }

                gOc.appendChild(rect);
                for (var li = 0; li < lines.length; li++) {
                    var text = document.createElementNS('http://www.w3.org/2000/svg', 'text');
                    text.setAttribute('x', leftX - 90);
                    text.setAttribute('y', currentY + 18 + li * lineHeight);
                    if (li === 0) {
                        text.setAttribute('fill', '#000');
                        text.setAttribute('style', 'font-weight:700; font-size:12px');
                    } else {
                        text.setAttribute('fill', '#555');
                        text.setAttribute('style', 'font-size:11px');
                    }
                    text.textContent = lines[li];
                    gOc.appendChild(text);
                }

                if (hasEntradas) {
                    var badge = document.createElementNS('http://www.w3.org/2000/svg', 'g');
                    var bRect = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
                    bRect.setAttribute('x', leftX + 40);
                    bRect.setAttribute('y', currentY + 6);
                    bRect.setAttribute('rx', 6);
                    bRect.setAttribute('ry', 6);
                    bRect.setAttribute('width', 40);
                    bRect.setAttribute('height', 20);
                    bRect.setAttribute('fill', '#ffc107');
                    bRect.setAttribute('stroke', 'none');
                    var bText = document.createElementNS('http://www.w3.org/2000/svg', 'text');
                    bText.setAttribute('x', leftX + 60);
                    bText.setAttribute('y', currentY + 20);
                    bText.setAttribute('fill', '#fff');
                    bText.setAttribute('style', 'font-weight:700; font-size:10px');
                    bText.setAttribute('text-anchor', 'middle');
                    bText.textContent = 'OC';
                    badge.appendChild(bRect);
                    badge.appendChild(bText);
                    gOc.appendChild(badge);
                }

                gOc.addEventListener('click', (function(o) { return function() {
                    try {
                        if (o && (o.docentry || o.docEntry)) {
                            var entry = o.docentry || o.docEntry;
                            var url = '/proveedor/oc/' + encodeURIComponent(entry) + '/print';
                            window.open(url, '_blank');
                            return;
                        }
                    } catch (e) {}
                    openOcModal(o, 'oc');
                }; })(oc));
                svg.appendChild(gOc);

                var entradas = entradasArr || [];
                if (entradas && entradas.length) {
                    for (var j = 0; j < entradas.length; j++) {
                        var en = entradas[j];
                        var ey = currentY + j * 40;
                        var gEn = document.createElementNS('http://www.w3.org/2000/svg', 'g');
                        gEn.setAttribute('class', 'node entrada');
                        gEn.setAttribute('cursor', 'pointer');
                        gEn.setAttribute('data-docentry', enDocEntry || '');

                        var rect2 = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
                        rect2.setAttribute('x', rightX - 40);
                        rect2.setAttribute('y', ey);
                        rect2.setAttribute('rx', 8);
                        rect2.setAttribute('ry', 8);
                        rect2.setAttribute('width', 240);
                        rect2.setAttribute('fill', '#ffffff');
                        rect2.setAttribute('stroke', '#198754');
                        rect2.setAttribute('stroke-width', 1.2);

                        // Preferencias SAP OPDN: DocDate = fecha inicial, TaxDate = fecha final
                        var startRaw = en.DocDate || en.DocDateString || en.docDate || en.docdate || en.Date || en.fecha || en.Fecha || '';
                        var endRaw = en.TaxDate || en.TaxDateString || en.taxDate || en.taxdate || en.DocDueDate || en.fecha || en.Fecha || '';

                        function _fmtDate(raw) {
                            if (!raw && raw !== 0) return '-';
                            try {
                                var ddObj = new Date(raw);
                                if (!isNaN(ddObj.getTime())) {
                                    var ddd = String(ddObj.getDate()).padStart(2, '0');
                                    var mmm = String(ddObj.getMonth() + 1).padStart(2, '0');
                                    var yyyy2 = ddObj.getFullYear();
                                    return ddd + '/' + mmm + '/' + yyyy2;
                                }
                            } catch (e) {}
                            return String(raw || '-');
                        }

                        var enFechaInicioTxt = _fmtDate(startRaw);
                        var enFechaFinalTxt = _fmtDate(endRaw);

                        var enDocNum = en.DocNum || en.docnum || en.Numero || en.num || '-';
                        var enDocEntry = en.DocEntry || en.docentry || en.DocEntry || null;
                        var enRef = en.NumAtCard || en.ref_proveedor || en.CarCode || en.CardCode || '';
                        // Heurística: buscar campo numérico de total en todo el objeto (varios nombres posibles)
                        function findNumericTotal(obj) {
                            var candidates = ['DocTotal', 'DocTotalFC', 'DocTotalSys', 'Total', 'total', 'DocumentTotal', 'docTotal', 'DocSum', 'DocTotalFc'];
                            for (var i = 0; i < candidates.length; i++) {
                                var k = candidates[i];
                                if (typeof obj[k] !== 'undefined' && obj[k] !== null) return obj[k];
                            }
                            // buscar cualquier clave que contenga 'total' (case-insensitive)
                            for (var kk in obj) {
                                if (!obj.hasOwnProperty(kk)) continue;
                                try {
                                    if (/total/i.test(kk) && obj[kk] !== null && obj[kk] !== undefined) return obj[kk];
                                } catch (e) {}
                            }
                            return '';
                        }

                        var enTotalRaw = findNumericTotal(en);
                        var enTotalTxt = '';
                        try {
                            var tN = Number(enTotalRaw);
                            enTotalTxt = isNaN(tN) ? (enTotalRaw || '') : tN.toLocaleString('es-MX', { style: 'currency', currency: 'MXN' });
                        } catch (e) { enTotalTxt = enTotalRaw || ''; }

                        var enAlmacen = en.WarehouseCode || en.Warehouse || en.WhsCode || en.Destination || en.ShipTo || '';
                        // Heurística: buscar arrays de líneas en todo el objeto
                        function findLinesCount(obj) {
                            var arrNames = ['PDN1','Pdn1','pdn1','DocumentLines','lines','LineItems','LineArray','lines_'];
                            for (var i2 = 0; i2 < arrNames.length; i2++) {
                                var key = arrNames[i2];
                                if (Array.isArray(obj[key])) return obj[key].length;
                            }
                            // buscar cualquier propiedad que parezca un array de líneas
                            for (var k2 in obj) {
                                if (!obj.hasOwnProperty(k2)) continue;
                                try {
                                    if (/pdn1|documentlines|lines|lineas|lineitems/i.test(k2) && Array.isArray(obj[k2])) return obj[k2].length;
                                } catch (e) {}
                            }
                            if (typeof obj.LineCount !== 'undefined') return Number(obj.LineCount) || 0;
                            if (typeof obj.LineNum !== 'undefined') return Number(obj.LineNum) || 0;
                            return 0;
                        }

                        var enLinesCount = findLinesCount(en);

                        var enEstadoRaw = en.DocumentStatus || en.status || en.estado || '';
                        var enEstadoTxt = '';
                        if (enEstadoRaw) {
                            var rs = String(enEstadoRaw).toLowerCase();
                            if (rs.indexOf('bost_open') !== -1 || rs.indexOf('open') !== -1 || rs === 'o') enEstadoTxt = 'Abierto';
                            else if (rs.indexOf('bost_closed') !== -1 || rs.indexOf('closed') !== -1 || rs === 'c') enEstadoTxt = 'Cerrado';
                            else enEstadoTxt = String(enEstadoRaw);
                        }

                        // Construir líneas separadas para cada campo
                        var fieldLines = [];
                        fieldLines.push({text: enDocNum || '-', bold: true});
                        fieldLines.push({text: 'Doc: ' + enFechaInicioTxt, bold: false});
                        fieldLines.push({text: 'Tax: ' + enFechaFinalTxt, bold: false});
                        fieldLines.push({text: 'Ref: ' + (enRef || '-'), bold: false});
                        if (enLinesCount) fieldLines.push({text: String(enLinesCount) + ' líneas', bold: false});
                        if (enEstadoTxt) fieldLines.push({text: 'Estado: ' + enEstadoTxt, bold: false});

                        var lineHeightEn = 16;
                        var paddingEn = 12;
                        var rect2Height = Math.max(40, fieldLines.length * lineHeightEn + paddingEn);
                        rect2.setAttribute('height', rect2Height);

                        gEn.appendChild(rect2);

                        for (var lf = 0; lf < fieldLines.length; lf++) {
                            var tx = document.createElementNS('http://www.w3.org/2000/svg', 'text');
                            tx.setAttribute('x', rightX - 30);
                            tx.setAttribute('y', ey + 12 + lf * lineHeightEn);
                            tx.setAttribute('fill', lf === 0 ? '#000' : '#444');
                            tx.setAttribute('style', (fieldLines[lf].bold ? 'font-weight:700; ' : '') + 'font-size:11px');
                            tx.textContent = fieldLines[lf].text;
                            gEn.appendChild(tx);
                        }

                        var et3 = document.createElementNS('http://www.w3.org/2000/svg', 'text');
                        et3.setAttribute('x', rightX + 160);
                        et3.setAttribute('y', ey + 12 + Math.floor(rect2Height / lineHeightEn) * lineHeightEn - 2);
                        et3.setAttribute('fill', '#000');
                        et3.setAttribute('style', 'font-weight:700; font-size:11px');
                        et3.setAttribute('text-anchor', 'end');
                        et3.textContent = enTotalTxt || '';
                        gEn.appendChild(et3);

                        // Si faltan total o líneas, intentar cargar detalles vía AJAX y actualizar el nodo
                        if ((!enLinesCount || enLinesCount === 0) || !enTotalTxt) {
                            (function(gEl, docEntry, etTotal, baseEy, baseFieldCount, lHeight) {
                                if (!docEntry) return;
                                fetch('/proveedor/pdn/' + encodeURIComponent(docEntry) + '/json')
                                    .then(function(res) { return res.json(); })
                                    .then(function(json) {
                                        if (!json) return;
                                        var newLines = (json.summary && json.summary.lines) ? json.summary.lines : (Array.isArray(json.lines) ? json.lines.length : 0);
                                        var pdn = json.pdn || {};
                                        var newTotal = pdn.DocTotal ?? pdn.DocTotalFC ?? pdn.Total ?? pdn.total ?? '';

                                        // actualizar / agregar texto de líneas
                                        try {
                                            var texts = gEl.querySelectorAll('text');
                                            var found = false;
                                            texts.forEach(function(t) {
                                                if (t && t.textContent && /líneas|lineas/i.test(t.textContent)) {
                                                    t.textContent = String(newLines) + ' líneas';
                                                    found = true;
                                                }
                                            });
                                            if (!found && newLines) {
                                                var tnew = document.createElementNS('http://www.w3.org/2000/svg', 'text');
                                                tnew.setAttribute('x', rightX - 30);
                                                tnew.setAttribute('y', baseEy + 12 + baseFieldCount * lHeight);
                                                tnew.setAttribute('fill', '#444');
                                                tnew.setAttribute('style', 'font-size:11px');
                                                tnew.textContent = String(newLines) + ' líneas';
                                                gEl.insertBefore(tnew, etTotal);
                                            }
                                        } catch (e) {}

                                        // actualizar total
                                        try {
                                            if (newTotal !== '' && newTotal !== null && typeof newTotal !== 'undefined') {
                                                var tnum = Number(newTotal);
                                                etTotal.textContent = isNaN(tnum) ? String(newTotal) : tnum.toLocaleString('es-MX', { style: 'currency', currency: 'MXN' });
                                            }
                                        } catch (e) {}
                                    })
                                    .catch(function() {});
                            })(gEn, enDocEntry, et3, ey, fieldLines.length, lineHeightEn);
                        }

                        var line = document.createElementNS('http://www.w3.org/2000/svg', 'line');
                        var rectRightX = (leftX - 100) + 200; 
                        line.setAttribute('x1', rectRightX + 6);
                        line.setAttribute('y1', currentY + rectHeight / 2);
                        line.setAttribute('x2', rightX + 20);
                        line.setAttribute('y2', ey + 28);
                        line.setAttribute('stroke', '#6c757d');
                        line.setAttribute('stroke-width', 1.2);
                        line.setAttribute('marker-end', 'url(#arrow)');

                        svg.appendChild(line);

                        gEn.addEventListener('click', (function(item) { return function() {
                            try {
                                if (item && (item.DocEntry || item.docentry || item.DocEntry)) {
                                    var entry = item.DocEntry || item.docentry || item.DocEntry;
                                    var url = '/proveedor/pdn/' + encodeURIComponent(entry) + '/print';
                                    window.open(url, '_blank');
                                    return;
                                }
                            } catch (e) {}
                            openOcModal(item, 'entrada');
                        }; })(en));

                        svg.appendChild(gEn);
                    }
                }

                var entradasHeight = (entradas && entradas.length) ? entradas.length * 40 : 0;
                var usedHeight = Math.max(rectHeight, entradasHeight);
                var ocRowH = usedHeight + 20;
                currentY += ocRowH;
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
                var printBtn = '';
                if (item.docentry) {
                    printBtn = '<a class="btn btn-sm btn-danger ms-2" target="_blank" href="/proveedor/oc/' + item.docentry + '/print">\u00A0\u00A0<i class="fas fa-print"></i> Imprimir OC\u00A0\u00A0</a>';
                }
                container.innerHTML = '<div class="d-flex justify-content-between align-items-center"><h6 class="fw-bold">' + title + '</h6>' + printBtn + '</div>' + cuerpo;
                Swal.fire({
                    title: title,
                    html: container.innerHTML,
                    width: 700,
                    icon: 'info'
                });
            }
        }
    </script>

@endsection
