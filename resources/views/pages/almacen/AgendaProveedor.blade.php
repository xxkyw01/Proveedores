@extends('layouts.movil')
@section('title', 'Arribos programados')
@section('content')
    @include('includes.scripts.SweetAlert2@11')
    @include('includes.scripts.googleapis')
    @include('includes.scripts.bootstrap')
    <x-sidebar />
    <link rel="stylesheet" href="{{ asset('assets/css/rol/almacen/agenda.css') }}">

    @php
        $rolId = session('Usuario.IdRol');
        $sucursalIdUsuario = session('Usuario.id_sucursal');
    @endphp

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="row con-sidebar">
            <div class="d-flex justify-content-center align-items-center nav-agenda-row">
                <button id="btn-prev" class="btn btn-orange btn-nav-agenda d-flex align-items-center justify-content-center"
                    onclick="moverAgenda(-1)">
                    <i class="material-icons">chevron_left</i>
                </button>

                @if (!in_array($rolId, [2]))
                    <form method="GET" action="{{ route('agenda.index') }}" class="m-0 flex-fill">
                        <div class="selector-sucursal px-3 py-2 rounded">
                            <select name="sucursal_id" id="sucursal_id" class="form-select form-select-sm text-center"
                                onchange="this.form.submit()">
                                @foreach ($sucursales as $s)
                                    <option value="{{ $s->id }}"
                                        {{ request('sucursal_id') == $s->id ? 'selected' : '' }}>
                                        {{ $s->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                @else
                    <input type="hidden" id="sucursal_id" value="{{ $sucursalIdUsuario }}">
                @endif

                <button id="btn-next"
                    class="btn btn-orange btn-nav-agenda d-flex align-items-center justify-content-center"
                    onclick="moverAgenda(1)">
                    <i class="material-icons">chevron_right</i>
                </button>
            </div>

            <div class="row">
                <div class="col columna-dia" data-index="0" id="col-ayer">
                    <div class="container mt-4">
                        <div class="row date-header text-center">
                            <div class="date-day col-12" id="day-0"></div>
                            <div class="date-number col-12" id="date-0"></div>
                        </div>
                        <div class="agenda-scroll">
                            <div class="timeline" id="timeline-0"></div>
                        </div>
                    </div>
                </div>
                <div class="col columna-dia" data-index="1" id="col-hoy">
                    <div class="container mt-4">
                        <div class="row date-header text-center">
                            <div class="date-day col-12" id="day-1"></div>
                            <div class="date-number col-12" id="date-1"></div>
                        </div>
                        <div class="agenda-scroll">
                            <div class="timeline" id="timeline-1"></div>
                        </div>
                    </div>
                </div>
                <div class="col columna-dia" data-index="2" id="col-manana">
                    <div class="container mt-4">
                        <div class="row date-header text-center">
                            <div class="date-day col-12" id="day-2"></div>
                            <div class="date-number col-12" id="date-2"></div>
                        </div>
                        <div class="agenda-scroll">
                            <div class="timeline" id="timeline-2"></div>
                        </div>
                    </div>
                </div>
            </div>
    </div>

    <div class="modal fade" id="modalDetalles" tabindex="-1" role="dialog" aria-labelledby="modalDetallesLabel"
        aria-hidden="true" data-bs-focus="false">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modalDetallesLabel">Detalles de la Orden de compra</h4>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="reservacionId" value="" />
                    <input type="hidden" id="reservacionFecha" value="" />
                    <input type="hidden" id="reservacionHora" value="" />
                    <div id="contenidoModal"></div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-orange" onclick="editarEstado()">Cambiar Status</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>

            </div>
        </div>
    </div>

    <script>
        let dates = [];
        let grposCreados = [];
        let currentIndex = 0;
        let fechasDisponibles = [];
        let currentStartIndex = 0;
        let recepcionEnProceso = false;

        window._agendaCache = window._agendaCache || {
            inFlight: new Set(),
            data: new Map(),
            ttl: 30 * 1000 
        };

        window._agendaDebug = window._agendaDebug || {
            renderAgendaCount: 0,
            loadAgendaCount: new Map(),
            events: []
        };

        function _agendaDebugLog(ev, info) {
            try {
                const now = Date.now();
                window._agendaDebug.events.push({
                    t: now,
                    ev,
                    info
                });
                if (window._agendaDebug.events.length > 80) window._agendaDebug.events.shift();
                console.debug('[Agenda][' + ev + ']', info);
            } catch (e) {

            }
        }
        const ROL_ID = {{ (int) $rolId }};
        const ES_ADMIN = [5].includes(ROL_ID);

        function isWeekend(d) {
            const day = d.getDay();
            return day === 0;
        }

        function clone(d) {
            return new Date(d.getFullYear(), d.getMonth(), d.getDate());
        }

        function nextWeekday(d) {
            const x = clone(d);
            while (isWeekend(x)) x.setDate(x.getDate() + 1);
            return x;
        }

        function prevWeekday(d) {
            const x = clone(d);
            while (isWeekend(x)) x.setDate(x.getDate() - 1);
            return x;
        }

        function formatDate(date) {
            const optionsDay = {
                weekday: 'long',
                timeZone: 'America/Mexico_City'
            };
            const optionsNumber = {
                day: 'numeric',
                month: 'long',
                timeZone: 'America/Mexico_City'
            };
            return {
                day: date.toLocaleDateString('es-ES', optionsDay),
                number: date.toLocaleDateString('es-ES', optionsNumber)
            };
        }

        function sameYMD(a, b) {
            return a.getFullYear() === b.getFullYear() &&
                a.getMonth() === b.getMonth() &&
                a.getDate() === b.getDate();
        }

        function loadDates() {
            const hoy = new Date();
            fechasDisponibles = [];

            const start = new Date();
            start.setDate(hoy.getDate() - 7);
            const end = new Date();
            end.setDate(hoy.getDate() + 7);

            for (let d = new Date(start); d <= end; d.setDate(d.getDate() + 1)) {
                const x = new Date(d);
                if (!isWeekend(x)) fechasDisponibles.push(x);
            }

            const hoyHabil = isWeekend(hoy) ? nextWeekday(hoy) : hoy;
            const idx = fechasDisponibles.findIndex(fd => sameYMD(fd, hoyHabil));
            currentStartIndex = Math.max(0, idx);

            renderAgenda();
        }

        function renderAgenda() {
            const ancho = window.innerWidth;
            window._agendaDebug.renderAgendaCount++;
            _agendaDebugLog('renderAgenda', {
                count: window._agendaDebug.renderAgendaCount,
                currentStartIndex,
                ancho
            });
            let cantidadDias = 1;

            if (ancho >= 992) cantidadDias = 3;
            else if (ancho >= 768) cantidadDias = 2;
            else cantidadDias = 1;

            ajustarColumnasPorDispositivo();

            for (let i = 0; i < 3; i++) {
                const index = currentStartIndex + i;
                const el = document.getElementById(`day-${i}`);

                if (index < 0 || index >= fechasDisponibles.length) {
                    if (el) el.parentElement.style.display = 'none';
                    continue;
                }

                const fecha = fechasDisponibles[index];
                const formatted = formatDate(fecha);

                document.getElementById(`day-${i}`).innerText = formatted.day.toUpperCase();
                document.getElementById(`date-${i}`).innerText = formatted.number;
                loadAgendaDataForDate(fecha, i);
            }
            actualizarVisibilidadFlechas();
        }

        function handleAgendaDataResponse(data, timeline, formattedDate, index) {
            _agendaDebugLog('handleAgendaDataResponse', {
                index,
                formattedDate,
                items: Array.isArray(data) ? data.length : 0
            });
            if (!timeline) return;
            timeline.innerHTML = '';

            data.sort((a, b) => {
                const ha = (a?.hora ?? '').slice(0, 5);
                const hb = (b?.hora ?? '').slice(0, 5);
                if (!ha && !hb) return 0;
                if (!ha) return 1;
                if (!hb) return -1;
                return new Date(`1970-01-01T${ha}:00`) - new Date(`1970-01-01T${hb}:00`);
            });

            data.forEach(item => {
                const hasComent = !!(item.tiene_comentario ?? (item.commit_afterrecep && String(item
                    .commit_afterrecep).trim().length));
                const hasEvid = !!(item.tiene_evidencia ?? (item.evidencia_nombre || item.evidencias ||
                    item.evidencia_path));
                const ocBadges = (typeof formatOrdenCompra === 'function') ? formatOrdenCompra(item.orden_compra) :
                    '';

                let ocIcons = '';

                if (hasComent) ocIcons += `
                    <span class="icono-info icono-coment" title="Tiene comentario" onclick="showDetails(${item.id})">
                        <i class="material-icons">mode_comment</i>
                    </span>`;

                if (hasEvid) ocIcons += `
                    <a class="icono-info icono-evid" title="Ver evidencia" href="/almacen/evidencia/${item.id}" target="_blank" rel="noopener">
                        <i class="material-icons">attach_file</i>
                    </a>`;

                const card = `
                        <div class="timeline-item">
                            <div class="timeline-marker"></div>

                            <div class="timeline-card">
                                <!-- BODY -->
                                <div class="timeline-card-body p-2">

                                    <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                        <div class="flex-grow-1">
                                            <div class="d-inline-block me-2">
                                                <span class="d-inline-block p-2 status-badge ${getStatusClass(item?.estado)}">
                                                    ${item?.estado ?? 'cancelada'}
                                                </span>
                                            </div>

                                            <i class="material-icons arrow-icon" style="font-size: 1.5rem; vertical-align: middle;">arrow_forward</i>
  
                                            <div class="d-inline-block">
                                                <span class="d-inline-block p-2 evento-badge ${getEventClass(item?.tipo_evento)}">
                                                    ${item?.tipo_evento ?? 'N/A'}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="orden-compra d-flex justify-content-start align-items-start gap-2 mb-2">
                                        ${ocBadges}
                                        <span id="oc-icons-${item.id}" class="iconos-oc"></span>
                                        ${ocIcons}
                                    </div>

                                    <div class="detalle-proveedor">
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                                <i class="material-icons" style="font-size: 1.5rem;">store</i>
                                                <strong class="text-truncate prov-name">
                                                    ${escapeHTML(item.proveedor_nombre || 'Paqueteria Express / Cita no programada')}
                                                </strong>
                                        </div>

                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <i class="material-icons" style="font-size: 1.5rem;">access_time</i>
                                            <span class="hora">${formatTime12h(item.hora) || 'No especificado'}</span>
                                        </div>

                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <i class="material-icons" style="font-size: 1.5rem;">local_shipping</i>
                                            <span class="text-truncate transport">
                                                ${escapeHTML(item.transporte_nombre || 'No especificado')}
                                            </span>
                                        </div>

                                        <div class="d-flex align-items-center gap-2">
                                            <i class="material-icons" style="font-size: 1.5rem;">place</i>
                                            <span class="text-truncate place">
                                                ${escapeHTML(item.Lugar || 'No especificado')}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- FOOTER -->
                                <div class="timeline-card-footer">
                                    ${ES_ADMIN? `
                                                                        <button class="btn-detalles btn btn-outline-secondary btn-sm w-30"
                                                                            onclick="showDetails(${item.id})">
                                                                            Ver Detalles
                                                                        </button>`:''
                                    }

                                </div>

                            </div>

                        </div>`;
                timeline.insertAdjacentHTML('beforeend', card);
                enqueueIndicator(item.id);
            });
        }

        function loadAgendaDataForDate(date, index) {
            const formattedDate = ymdLocal(date);
            const sucursalId = document.getElementById('sucursal_id')?.value || '{{ $sucursal_id }}';
            const timeline = document.getElementById(`timeline-${index}`);
            const key = `${formattedDate}::${sucursalId}`;

            _agendaDebugLog('loadAgendaDataForDate:start', {
                key,
                index,
                formattedDate
            });

            if (timeline) {
                renderSkeletonTimeline(index, 1);
            }

            const cache = window._agendaCache;
            const now = Date.now();

            if (cache.data.has(key)) {
                const entry = cache.data.get(key);
                if ((now - entry.ts) < cache.ttl) {
                    try {
                        _agendaDebugLog('loadAgendaDataForDate:cacheHit', {
                            key
                        });
                        handleAgendaDataResponse(entry.data, timeline, formattedDate, index);
                        return;
                    } catch (e) {
                        console.warn('[Agenda] fallo render cache, refetch', e);
                    }
                } else {
                    cache.data.delete(key);
                }
            }

            if (cache.inFlight.has(key)) {
                _agendaDebugLog('loadAgendaDataForDate:inFlight', {
                    key
                });
                return;
            }
            cache.inFlight.add(key);

            _agendaDebugLog('loadAgendaDataForDate:fetchStart', {
                key
            });

            fetch(`/almacen/agenda/data?fecha=${formattedDate}&sucursal_id=${sucursalId}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(async r => {
                    const text = await r.text();

                    if (!r.ok) {
                        throw new Error(`HTTP ${r.status}: ${text.slice(0, 200)}`);
                    }

                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        throw new Error(`Respuesta NO JSON (${r.status}): ${text.slice(0, 200)}`);
                    }
                })
                .then(data => {
                    _agendaDebugLog('loadAgendaDataForDate:fetchSuccess', {
                        key,
                        items: Array.isArray(data) ? data.length : 0
                    });

                    try {
                        cache.data.set(key, {
                            data: data,
                            ts: Date.now()
                        });
                    } catch (e) {
                        console.warn('[Agenda] no se pudo cachear respuesta', e);
                    }
                    handleAgendaDataResponse(data, timeline, formattedDate, index);
                })
                .catch(err => {
                    console.error('[Agenda] Error', formattedDate, err);
                    _agendaDebugLog('loadAgendaDataForDate:error', {
                        key,
                        message: String(err && err.message ? err.message : err)
                    });
                    if (timeline) {
                        timeline.innerHTML = `
                    <div class="text-danger" style="padding:8px 12px;">
                        Error cargando ${formattedDate}<br>
                        <small>${escapeHTML(String(err.message||err))}</small>
                    </div>`;
                    }
                })
                .finally(() => {
                    cache.inFlight.delete(key);
                    _agendaDebugLog('loadAgendaDataForDate:finally', {
                        key
                    });
                });
        }

        function renderSkeletonTimeline(index, count = 3) {
            const timeline = document.getElementById(`timeline-${index}`);
            if (!timeline) return;
            let html = '';

            for (let i = 0; i < count; i++) {
                html +=
                    `<div class="timeline-item skeleton-card">

                <div class="timeline-marker"></div>

                <div class="timeline-card">
                    <div class="orden-compra mb-2">
                        <span class="skeleton-bg skeleton-badge"></span>
                    </div>

                    <div class="orden-compra mb-2">
                        <span class="skeleton-bg skeleton-badge"></span>
                        <span class="skeleton-bg skeleton-badge"></span>
                    </div>

                    <p>
                        <span class="skeleton-bg skeleton-line w-60"></span>
                    </p>

                    <p>
                        <span class="skeleton-bg skeleton-line w-40"></span>
                    </p>

                    <p>
                        <span class="skeleton-bg skeleton-line w-50"></span>
                    </p>

                    <p>
                        <span class="skeleton-bg skeleton-line w-80"></span>
                    </p>

                    <div>
                        <span class="skeleton-bg skeleton-pill"></span>
                    </div>
                </div>

                </div>`;
            }

            timeline.innerHTML = html;
        }

        function moverAgenda(direccion) {
            const ancho = window.innerWidth;
            let cantidadDias = 1;

            if (ancho >= 992) cantidadDias = 3;
            else if (ancho >= 768) cantidadDias = 2;
            else cantidadDias = 1;

            const nuevoIndex = currentStartIndex + direccion;
            if (nuevoIndex >= 0 && (nuevoIndex + cantidadDias - 1) < fechasDisponibles.length) {
                currentStartIndex = nuevoIndex;
                renderAgenda();
            }
        }

        function ymdLocal(d) {
            const y = d.getFullYear();
            const m = String(d.getMonth() + 1).padStart(2, '0');
            const day = String(d.getDate()).padStart(2, '0');
            return `${y}-${m}-${day}`;
        }

        window._agendaIndicators = window._agendaIndicators || {
            cache: new Map(), // id -> data
            queue: [],
            inFlight: 0,
            concurrency: 4,
            started: false,
            drainTimer: null
        };

        function _updateIndicatorDom(id, d) {
            try {
                const cont = document.getElementById(`oc-icons-${id}`);
                if (!cont) return;
                const icons = [];
                const tieneComentario = d && d.commit_afterrecep && String(d.commit_afterrecep).trim().length;
                const tieneEvidencia = d && ((d.evidencia_path && d.evidencia_nombre) || d.evidencias);
                if (tieneComentario) {
                    icons.push(
                        `<span class="icono-info icono-coment" title="Tiene comentario" onclick="showDetails(${id})"><i class="material-icons">mode_comment</i></span>`
                        );
                }
                if (tieneEvidencia) {
                    icons.push(
                        `<a class="icono-info icono-evid" title="Ver evidencia" href="/almacen/evidencia/${id}" target="_blank" rel="noopener"><i class="material-icons">attach_file</i></a>`
                        );
                }
                cont.innerHTML = icons.join('');
            } catch (e) {
                console.warn('[_updateIndicatorDom] error', id, e);
            }
        }

        function processIndicatorQueue() {
            const s = window._agendaIndicators;
            if (s.inFlight >= s.concurrency) return;
            if (!s.queue.length) return;

            while (s.inFlight < s.concurrency && s.queue.length) {
                const id = s.queue.shift();
                if (!id) continue;
                if (s.cache.has(id)) {
                    _updateIndicatorDom(id, s.cache.get(id));
                    continue;
                }

                s.inFlight++;
                fetch(`/almacen/agenda/detalles/${id}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(async r => {
                        const text = await r.text();
                        if (!r.ok) throw new Error(`HTTP ${r.status}: ${text.slice(0,200)}`);
                        try {
                            return JSON.parse(text);
                        } catch (e) {
                            throw new Error(`Respuesta NO JSON (${r.status}): ${text.slice(0,200)}`);
                        }
                    })
                    .then(d => {
                        try {
                            s.cache.set(id, d);
                            _updateIndicatorDom(id, d);
                        } catch (e) {
                            console.warn('[processIndicatorQueue] cache/update failed', id, e);
                        }
                    })
                    .catch(err => {
                        console.warn('[marcarIndicadores queue] error', id, err);
                    })
                    .finally(() => {
                        s.inFlight--;
                        setTimeout(processIndicatorQueue, 0);
                    });
            }
        }

        function enqueueIndicator(id) {
            const s = window._agendaIndicators;
            if (!id) return;
            if (s.cache.has(id)) {
                _updateIndicatorDom(id, s.cache.get(id));
                return;
            }
            if (s.queue.indexOf(id) !== -1) return;
            s.queue.push(id);
            if (!s.started) {
                s.started = true;
                s.drainTimer = setTimeout(() => {
                    processIndicatorQueue();
                }, 900);
            } else {
                setTimeout(processIndicatorQueue, 200);
            }
        }

        function changeDate(step) {
            if ((currentIndex + step) >= 0 && (currentIndex + step) < dates.length) {
                currentIndex += step;
                updateDateDisplay();
                loadAgendaData();
            }
        }

        function updateDateDisplay() {
            let formatted = formatDate(dates[currentIndex]);
            document.getElementById('current-day').innerText = formatted.day;
            document.getElementById('current-date').innerText = formatted.number;
        }

        function formatOrdenCompra(data) {
            try {
                if (data == null) return '<span class="badge-orden">Otros</span>';

                let ordenes = data;
                if (typeof data === 'string') {
                    try {
                        ordenes = JSON.parse(data);
                    } catch {
                        ordenes = data.split(',');
                    }
                }
                if (!Array.isArray(ordenes)) ordenes = [ordenes];

                ordenes = ordenes
                    .filter(Boolean)
                    .map(o => String(o).trim())
                    .filter(o => o.length);

                if (!ordenes.length) return '<span class="badge-orden">Otros</span>';
                return ordenes.map(o => `<span class="badge-orden">${escapeHTML(o)}</span>`).join('');
                //return ordenes.map(o => `<span class="badge-orden">${o}</span>`).join('');
            } catch {
                return '<span class="badge-orden">Otros</span>';
            }
        }

        function getStatusClass(status) {
            const s = String(status ?? '')
                .trim()
                .toLowerCase()
                .normalize('NFD').replace(/[\u0300-\u036f]/g, '');

            switch (s) {
                case 'confirmada':
                    return 'status-confirmada';
                case 'pendiente':
                    return 'status-pendiente';
                case 'asistio':
                    return 'status-asistio';
                case 'no asistio':
                    return 'status-noasistio';
                case 'en proceso':
                    return 'status-proceso';
                case 'recepcion tardia':
                    return 'status-tardia';
                case 'cancelada por proveedor':
                    return 'status-cancelada-sp';
                case 'asistio fuera de horario':
                    return 'status-timeout';
                case 'cancelada':
                    return 'status-cancelada';
                default:
                    return 'status-cancelada';
            }
        }

        function getEventClass(evento) {
            const e = String(evento ?? '')
                .trim()
                .toLowerCase()
                .normalize('NFD').replace(/[\u0300-\u036f]/g, '');

            switch (e) {
                case 'programada':
                    return 'evento-programada';
                case 'no programada':
                    return 'evento-no-programada';
                case 'apartado':
                    return 'evento-apartada';
                case 'paqueteria express':
                    return 'evento-paqueteria-express';
                default:
                    return 'evento-programada';
            }
        }

        function verComentario(id, texto) {
            const t = String(texto || '').trim();
            if (!t) return showDetails(id);
            Swal.fire({
                icon: 'info',
                title: 'Comentario de almacén',
                html: '<div style="text-align:left;white-space:pre-wrap;">' + escapeHTML(t) + '</div>',
                confirmButtonText: 'OK'
            });
        }

        function ajustarColumnasPorDispositivo() {
            const ancho = window.innerWidth;
            const cols = document.querySelectorAll('.columna-dia');

            cols.forEach(col => col.style.display = 'none');

            if (ancho >= 992) {
                // PC: mostrar las 3 columnas (ayer, hoy, mañana)
                document.getElementById('col-ayer').style.display = 'block';
                document.getElementById('col-hoy').style.display = 'block';
                document.getElementById('col-manana').style.display = 'block';
            } else if (ancho >= 768) {
                // Tablet: mostrar 2 columnas (hoy, mañana)
                document.getElementById('col-hoy').style.display = 'block';
                document.getElementById('col-manana').style.display = 'block';
            } else {
                // Móvil: mostrar solo la columna central (hoy)
                document.getElementById('col-hoy').style.display = 'block';
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            loadDates();
        });

        window.addEventListener('resize', renderAgenda);

        document.addEventListener('click', (ev) => {
            try {
                if (window.innerWidth > 767) return;
                const card = ev.target.closest('.timeline-card');
                if (!card) return;
                if (ev.target.closest('button, a, input')) return;
                card.classList.toggle('expanded');
            } catch (e) {}
        }, {
            passive: true
        });

        function formatTime12h(hora = '') {
            if (!hora) return '';
            const [hours = '0', minutes = '00'] = hora.split(':');
            let h = parseInt(hours, 10);
            if (Number.isNaN(h)) return '';
            const ampm = h >= 12 ? 'PM' : 'AM';
            h = h % 12 || 12;
            return `${h}:${minutes.padStart(2,'0')} ${ampm}`;
        }

        function actualizarVisibilidadFlechas() {
            const ancho = window.innerWidth;
            let cantidadDias = 1;

            if (ancho >= 992) cantidadDias = 3;
            else if (ancho >= 768) cantidadDias = 2;
            else cantidadDias = 1;

            const btnPrev = document.getElementById('btn-prev');
            const btnNext = document.getElementById('btn-next');

            btnPrev.style.visibility = currentStartIndex > 0 ? 'visible' : 'hidden';
            btnNext.style.visibility = (currentStartIndex + cantidadDias <= fechasDisponibles.length) ? 'visible' :
                'hidden';
        }

        function escapeHTML(text) {
            return text
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        function imprimirModal() {
            const contenido = document.getElementById('contenidoModal').innerHTML;
            const ventana = window.open('', '', 'width=800,height=600');
            ventana.document.write(
                `<html>
                <head>
                    <title>Reporte de Orden de Compra</title>
                    <style>
                        body {
                            font-family: Arial, sans-serif;
                            padding: 20px;
                            color: #333;
                        }
        
                        header {
                            display: flex;
                            align-items: center;
                            border-bottom: 2px solid #ee7826;
                            padding-bottom: 10px;
                            margin-bottom: 20px;
                        }
        
                        header img {
                            height: 70px;
                            margin-right: 20px;
                        }
        
                        header h1 {
                            color: #ee7826;
                            font-size: 22px;
                        }
        
                        .proveedor {
                            font-size: 16px;
                            margin-bottom: 15px;
                        }
        
                        .orden {
                            margin-bottom: 25px;
                        }
        
                        .orden-title {
                            background-color: #ee7826;
                            color: white;
                            padding: 6px 10px;
                            border-radius: 4px;
                            display: inline-block;
                            font-weight: bold;
                            margin-bottom: 10px;
                        }
        
                        table {
                            width: 100%;
                            border-collapse: collapse;
                            margin-top: 10px;
                        }
        
                        th, td {
                            padding: 10px;
                            border: 1px solid #ccc;
                            text-align: left;
                            font-size: 14px;remo
                        }
        
                        th {
                            background-color: #ee7826;
                            color: white;
                        }
        
                        .checklist-cell {
                            text-align: center;
                            font-size: 18px;
                            color: #28a745;
                        }
                    </style>
                </head>
                <body>
                    <header>
                        <img src="${window.location.origin}/assets/img/logo.png" alt="Logo">
                        <h1>Reporte de Orden de Compra</h1>
                    </header>
                    ${contenido}

                <footer style="margin-top: 40px; font-size: 14px; text-align: center; color: #777;">
                Reporte generado automáticamente por el sistema de citas. <strong>${new Date().toLocaleDateString()}</strong>
                </footer>
                </body>
                </html>`);

            ventana.document.close();
            ventana.print();
        }

        function horasHastaCita(fechaStr, horaStr) {
            try {
                const [y, m, d] = fechaStr.split('-').map(Number);
                const [hh, mm] = (horaStr || '00:00').split(':').map(Number);
                const cita = new Date(y, (m - 1), d, hh, mm, 0, 0);
                const ahora = new Date();
                const diffMs = cita.getTime() - ahora.getTime();
                return diffMs / 36e5;
            } catch (e) {
                return 9999;
            }
        }

        function editarEstado() {
            const id = document.getElementById('reservacionId').value;
            if (!id) return Swal.fire('Error', 'No se encontró el ID de la reservación.', 'error');

            const fecha = document.getElementById('reservacionFecha').value;
            const hora = document.getElementById('reservacionHora').value;
            const horas = horasHastaCita(fecha, hora);
            const bloqueoCancel = (horas < 1);

            const options = {
                'Asistió': 'Asistió',
                'Confirmada': 'Confirmada',
                'No asistió': 'No asistió',
                'En proceso': 'En proceso',
                'Recepción tardía': 'Recepción tardía',
                'No Programado': 'No Programado',
                'Asistio Fuera de Horario': 'Asistio Fuera de Horario'
            };
            if (!bloqueoCancel) options['Cancelada por proveedor'] = 'Cancelada por proveedor';

            Swal.fire({
                title: 'Actualizar Estado',
                html: `
                <div class="mb-2 text-start">
                    <h5><label class="form-label">Nuevo estado</label></h5>
                    <select id="swal-estado" class="form-select">
                        <option value="">-- Selecciona --</option>
                        ${Object.keys(options).map(k=>`<option value="${k}">${options[k]}</option>`).join('')}
                    </select>
                </div>
                <div class="mb-2 text-start">
                    <h5><label class="form-label">Comentario (opcional)</label></h5>
                    <textarea id="swal-comentario" class="form-control" rows="3" placeholder="Detalle de recepción, incidencias, etc."></textarea>
                </div>
                <div class="mb-2 text-start">
                    <h5><label class="form-label">Adjuntar evidencia (opcional)</label></h5>
                    <input id="swal-evidencia" type="file" class="form-control" />
                    <div class="form-text">PDF, JPG/PNG, Excel, Word. Máx: 10 MB.</div>
                </div>
                ${bloqueoCancel ? `<div class="alert alert-warning mt-2">No se puede cancelar dentro de 48 horas.</div>`:''}
            `,
                showCancelButton: true,
                confirmButtonText: 'Actualizar',
                cancelButtonText: 'Cancelar',
                preConfirm: () => {
                    const estadoSel = document.getElementById('swal-estado').value;
                    if (!estadoSel) {
                        Swal.showValidationMessage('Debes seleccionar un estado');
                        return false;
                    }
                    if (bloqueoCancel && estadoSel === 'Cancelada por proveedor') {
                        Swal.showValidationMessage('No es posible cancelar dentro de 48 h.');
                        return false;
                    }
                    return {
                        estado: estadoSel,
                        comentario: document.getElementById('swal-comentario').value,
                        evidencia: document.getElementById('swal-evidencia').files[0] || null
                    };
                }
            }).then(res => {
                if (!res.isConfirmed) return;

                const fd = new FormData();
                fd.append('id', id);
                fd.append('estado', res.value.estado);
                if (res.value.comentario) fd.append('comentario', res.value.comentario);
                if (res.value.evidencia) fd.append('evidencia', res.value.evidencia);

                fetch('/almacen/agenda/actualizar-estado', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector("meta[name='csrf-token']").getAttribute(
                                'content')
                        },
                        body: fd
                    })
                    .then(r => r.json())
                    .then(d => {
                        if (d.success) Swal.fire('Éxito', d.message, 'success').then(() => location.reload());
                        else Swal.fire('Error', d.message || 'No se pudo actualizar.', 'error');
                    })
                    .catch(err => Swal.fire('Error', 'Ocurrió un error al actualizar.', 'error'));
            });
        }

        function escapeHTML(text = '') {
            return String(text)
                .replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;").replace(/'/g, "&#039;");
        }

        function formatBytes(bytes) {
            if (!bytes && bytes !== 0) return '';
            const units = ['B', 'KB', 'MB', 'GB', 'TB'];
            const i = bytes === 0 ? 0 : Math.floor(Math.log(bytes) / Math.log(1024));
            return (bytes / Math.pow(1024, i)).toFixed(1) + ' ' + units[i];
        }

        function slugId(v) {
            return 'oc-' + String(v).replace(/[^a-zA-Z0-9_-]/g, '-');
        }

        function formatNumber(n) {
            try {
                return new Intl.NumberFormat('es-MX').format(n);
            } catch {
                return n;
            }
        }

        function showWarn(title, text) {
            if (window.Swal) {
                Swal.fire({
                    icon: 'warning',
                    title,
                    text,
                    confirmButtonColor: '#ee7826'
                });
            } else alert(`${title}\n${text}`);
        }

        function syncCheckboxWithInput(input) {
            const tr = input.closest('tr');
            if (!tr) return;

            const chk = tr.querySelector('input[type="checkbox"]');
            if (!chk) return;

            const val = Number(input.value || 0);

            chk.checked = val > 0;
        }


        function getSeriePorSucursalId() {
            const sid = Number(document.getElementById('sucursal_id')?.value || '0');
            const mapa = {
                1: 156, 
                4: 157,
            };
            return mapa[sid] || 156;
        }

        // Captura/recepción deshabilitada en esta vista (solo consulta visual)
        function setupQtyValidation(rootEl) {
            // función deshabilitada intencionalmente
            return;
        }

        function refreshResumen(scope = document) {
            // No-op: resumen de recepción deshabilitado en esta vista
            return;
        }

        function autollenarPendientes(scope = document, soloChequeados = false) {
            // Deshabilitado: autollenado no aplicable en vista de solo consulta
            showWarn('Función deshabilitada', 'Autollenar pendientes está deshabilitado en esta vista.');
        }

        function limpiarCaptura(scope = document) {
            // No-op: captura deshabilitada
            return;
        }

        function toggleLinea(chk) {
            // No-op: selección de línea deshabilitada
            return;
        }

        function previewGRPO() {
            // Previsualización de GRPO deshabilitada en vista de solo consulta
            showWarn('Función deshabilitada', 'Previsualizar recepciones está deshabilitado en esta vista.');
        }

        if (typeof escapeHTML !== 'function') {
            function escapeHTML(text = '') {
                return String(text)
                    .replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;")
                    .replace(/"/g, "&quot;").replace(/'/g, "&#039;");
            }
        }

        if (typeof slugId !== 'function') {
            function slugId(v) {
                return 'oc-' + String(v).replace(/[^a-zA-Z0-9_-]/g, '-');
            }
        }
        window.escapeHTML = window.escapeHTML || function(text = '') {
            return String(text)
                .replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;").replace(/'/g, "&#039;");
        };

        window.slugId = window.slugId || function(v) {
            return 'oc-' + String(v).replace(/[^a-zA-Z0-9_-]/g, '-');
        };

        function fetchJsonOrThrow(url, opt = {}) {
            opt = opt || {};
            opt.headers = Object.assign({
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }, opt.headers || {});

            return fetch(url, opt).then(async r => {
                const text = await r.text();

                if (!r.ok) {
                    // status 4xx/5xx → lanza error con el HTML recortado
                    throw new Error(`HTTP ${r.status} - ${text.slice(0, 300)}`);
                }

                try {
                    return JSON.parse(text);
                } catch (e) {
                    // status 200 pero no es JSON (por ejemplo, te mandó login en HTML)
                    throw new Error(`Respuesta NO JSON (${r.status}) - ${text.slice(0, 300)}`);
                }
            });
        }




        const usuario = @json(session('Usuario.Nombre') ?? (session('Usuario.UserName') ?? 'Usuario'));

        function imprimirReporteGRPO(grposCreados, seleccion, infoCita) {
            if (!Array.isArray(grposCreados) || !grposCreados.length) {
                Swal?.fire?.('Sin Entrada de mercancía', 'No hay información de Entrada de mercancía para imprimir.',
                    'info');
                return;
            }

            const byOC = new Map();
            (seleccion || []).forEach(it => {
                if (!byOC.has(it.oc)) byOC.set(it.oc, []);
                byOC.get(it.oc).push(it);
            });

            const win = window.open('', '_blank', 'width=900,height=700');
            if (!win) {
                Swal?.fire?.('Pop-up bloqueado', 'Habilita ventanas emergentes para imprimir.', 'warning');
                return;
            }

            const today = new Date();
            const fechaStr = today.toLocaleDateString('es-MX');
            const horaStr = today.toLocaleTimeString('es-MX', {
                hour: '2-digit',
                minute: '2-digit'
            });

            const esc = (t = '') => String(t)
                .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;').replace(/'/g, '&#039;');

            const fmt = (n) => {
                try {
                    return new Intl.NumberFormat('es-MX').format(n);
                } catch {
                    return n;
                }
            };

            const cita = infoCita || {};
            const fCita = cita.fecha || '';
            const hCita = cita.hora || '';
            const lugar = cita.lugar || 'N/A';
            const prov = cita.proveedor || 'N/A';
            const codigoProv = cita.codigoProveedor || 'N/A';
            const trans = cita.transporte || 'N/A';
            const tipoEv = cita.tipoEvento || 'Programada';
            const suc = cita.sucursal || 'N/A';
            const comentAlm = cita.comentarioAlmacen || '';

            let html = `
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Reporte de entrada de mercancía</title>
  <style>
    body{ font-family: Arial, sans-serif; padding:20px; color:#333; }
    header{ display:flex; align-items:center; border-bottom:2px solid #ee7826; padding-bottom:10px; margin-bottom:15px; }
    header img{ height:60px; margin-right:15px; }
    header h1{ color:#ee7826; font-size:22px; margin:0; }
    .subinfo{ font-size:13px; color:#555; }
    .bloque-cita{ border:1px solid #ddd; border-radius:6px; padding:10px 12px; margin-bottom:18px; font-size:13px; background:#fafafa; }
    .bloque-doc{ margin-bottom:30px; page-break-inside: avoid; }
    .meta{ font-size:13px; margin-bottom:10px; }
    table{ width:100%; border-collapse:collapse; margin-top:10px; font-size:13px; }
    th,td{ border:1px solid #ccc; padding:6px 8px; }
    th{ background:#ffc0999e; text-align:left; }
    td.num{ text-align:right; }
    footer{ margin-top:30px; font-size:12px; text-align:center; color:#777; }
  </style>
</head>
<body>
  <header>
    <img src="${window.location.origin}/assets/img/logo.png" alt="Logo">
    <div>
      <h1>Reporte de entrada de mercancía</h1>
      <div class="subinfo">Generado ${esc(fechaStr)} ${esc(horaStr)}</div>
    </div>
  </header>

  <section class="bloque-cita">
    <strong>Datos de la cita</strong><br>
    <table style="width:100%;border-collapse:collapse;margin-top:6px;font-size:13px;">
      <tr>
        <td><strong>Fecha cita:</strong> ${esc(fCita || fechaStr)}</td>
        <td><strong>Hora cita:</strong> ${esc(hCita || '')}</td>
        <td><strong>Sucursal:</strong> ${esc(suc)}</td>
      </tr>
      <tr>
        <td><strong>Lugar:</strong> ${esc(lugar)}</td>
        <td><strong>Código del proveedor:</strong> ${esc(codigoProv)}</td>
        <td><strong>Tipo de evento:</strong> ${esc(tipoEv)}</td>
      </tr>
      <tr>
        <td colspan="2"><strong>Proveedor:</strong> ${esc(prov)}</td>
        <td><strong>Transporte:</strong> ${esc(trans)}</td>
      </tr>
    </table>

    ${comentAlm ? `
                      <div style="margin-top:8px;">
                        <strong>Comentario de almacén:</strong><br>
                        <div style="border:1px solid #ddd;border-radius:4px;padding:6px 8px;background:#fff;">${esc(comentAlm)}</div>
                      </div>` : ''}
  </section>
`;

            grposCreados.forEach(grpo => {
                const items = byOC.get(grpo.oc) || [];
                let subtotal = 0;

                html += `
  <section class="bloque-doc">
    <h2>OC ${esc(grpo.oc)} → Entrada de mercancia: ${esc(grpo.docNum || '')}</h2>
    <div class="meta">
      <div>Fecha Generada: ${esc(fechaStr)}</div>
      <div><strong>No. ref. del acreedor:</strong> ${esc(grpo.numAtCard || '')}</div>
      <div><strong>Comentarios Entrada de mercancia:</strong> ${esc(grpo.comments || '')}</div>
    </div>

    <table>
      <thead>
        <tr>
          <th style="width:15%;">Código</th>
          <th>Descripción</th>
          <th style="width:15%;text-align:right;">Cantidad recibida</th>
        </tr>
      </thead>
      <tbody>
`;

                items.forEach(it => {
                    subtotal += (it.qty || 0);
                    html += `
        <tr>
          <td>${esc(it.code)}</td>
          <td>${esc(it.desc)}</td>
          <td class="num">${fmt(it.qty)}</td>
        </tr>
`;
                });

                html += `
      </tbody>
      <tfoot>
        <tr>
          <td colspan="2" style="text-align:right;font-weight:bold;">Total recibido</td>
          <td class="num" style="font-weight:bold;">${fmt(subtotal)}</td>
        </tr>
      </tfoot>
    </table>
  </section>
`;
            });

            html += `
  <footer>
    Reporte generado automáticamente por Intranet Proveedores<br>
    <strong>${esc(fechaStr)}</strong><br>
    Generado por: ${esc(usuario || '-')}
  </footer>
</body>
</html>`;

            win.document.open();
            win.document.write(html);
            win.document.close();

            win.onload = () => {
                win.focus();
                setTimeout(() => win.print(), 250);
            };
        }



        function normCode(s) {
            return String(s || '')
                .trim()
                .toUpperCase()
        }

        function collectCheckedLines(scope = document) {
            // Recepción deshabilitada: no hay líneas seleccionables en la vista simplificada
            return [];
        }

        function fmt(n) {
            try {
                return new Intl.NumberFormat('es-MX').format(n);
            } catch {
                return n;
            }
        }

        function buildPreviewTableRows(items) {
            const byOC = new Map();
            items.forEach(it => {
                if (!byOC.has(it.oc)) byOC.set(it.oc, []);
                byOC.get(it.oc).push(it);
            });

            let rows = '';
            let grand = 0;

            for (const [oc, list] of byOC.entries()) {
                let subtotal = 0;

                rows += `
                <tr>
                    <td colspan="2" style="background:#fff3e6;border:1px solid #ddd;padding:6px;font-weight:700;">
                    OC ${oc}
                    </td>
                </tr>`;

                for (const it of list) {
                    subtotal += it.qty;
                    rows += `
                    <tr>
                    <td style="border:1px solid #ddd;padding:6px;">
                    <span style="opacity:.6;">(${escapeHTML(it.code)}) - </span>  ${escapeHTML(it.desc)} 
                    </td>
                    <td style="border:1px solid #ddd;padding:6px;text-align:right;font-weight:700;">
                        ${fmt(it.qty)} 
                    </td>
                    </tr>`;
                }

                rows += `
                <tr>
                    <td style="border:1px solid #ddd;padding:6px;text-align:right;font-weight:600;">Subtotal OC</td>
                    <td style="border:1px solid #ddd;padding:6px;text-align:right;font-weight:700;">${fmt(subtotal)}</td>
                </tr>`;
                grand += subtotal;
            }

            return {
                rows,
                grand
            };
        }

        function buildPreviewHeader(seleccion) {
            // Simplificado: solo mostrar OC en el header de previsualización
            const ocs = Array.from(new Set((seleccion || []).map(it => it.oc)));
            if (!ocs.length) return '';
            return ocs.map(oc => `<div style="margin-bottom:10px;padding:8px 10px;border:1px solid #ffd2a6;border-radius:6px;background:#fff7ef;font-size:.85rem;"><div style="font-weight:700;">OC ${escapeHTML(String(oc))}</div></div>`).join('');
        }

        function parseOrdenCompra(raw) {
            if (raw == null) return [];

            if (Array.isArray(raw)) return raw.map(x => String(x).trim()).filter(Boolean);


            if (typeof raw === 'string') {
                const s = raw.trim();
                if (!s) return [];

                if ((s.startsWith('[') && s.endsWith(']')) || (s.startsWith('{') && s.endsWith('}'))) {
                    try {
                        const j = JSON.parse(s);
                        if (Array.isArray(j)) return j.map(x => String(x).trim()).filter(Boolean);
                    } catch (_) {}
                }

                return s.split(',')
                    .map(x => x.replace(/[\[\]"]/g, '').trim())
                    .filter(Boolean);
            }

            return [String(raw).trim()].filter(Boolean);
        }


        window.showDetails = function(id) {
            document.getElementById('reservacionId').value = id;

            fetch(`/almacen/agenda/detalles/${id}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(async r => {
                    const text = await r.text();

                    if (!r.ok) {
                        throw new Error(`HTTP ${r.status}: ${text.slice(0, 200)}`);
                    }

                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        throw new Error(`Respuesta NO JSON (${r.status}): ${text.slice(0, 200)}`);
                    }
                })

                .then(data => {
                    document.getElementById('reservacionFecha').value = data.fecha || '';
                    document.getElementById('reservacionHora').value = (data.hora || '00:00');

                    window.recepcionContext = {
                        fecha: data.fecha || '',
                        hora: data.hora || '',
                        lugar: data.Lugar || data.lugar || '',
                        anden: data.anden_nombre || data.anden || '',
                        proveedor: data.proveedor_nombre || '',
                        transporte: data.transporte_nombre || '',
                        tipoEvento: data.tipo_evento || '',
                        sucursal: data.sucursal_nombre || '',
                        comentarioAlmacen: data.commit_afterrecep || ''
                    };

                    let contenido = `
                    <div class="accordion accordion-flush" id="accordionOrdenes">`;

                    //let ordenes = [];
                    let ordenes = parseOrdenCompra(data.orden_compra);
                    if (Array.isArray(data.orden_compra)) ordenes = data.orden_compra;
                    else if (typeof data.orden_compra === 'string') ordenes = data.orden_compra.split(',').map(o =>
                        o.trim());
                    else if (data.orden_compra != null) ordenes = [data.orden_compra];

                    ordenes.forEach(orden => {
                        const oid = slugId(orden);
                        const label = escapeHTML(String(orden));
                        const numLimpio = String(orden).match(/\d+/)?.[0] || '';

                        contenido +=
                            `<div class="accordion-item">
                            <h2 class="accordion-header" id="heading-${oid}">
                            <button class="accordion-button collapsed" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#collapse-${oid}"
                                    aria-expanded="false" aria-controls="collapse-${oid}">
                                <strong>Orden de compra:&nbsp;</strong> ${label}
                            </button>
                            </h2>
                            <div id="collapse-${oid}" class="accordion-collapse collapse"
                                aria-labelledby="heading-${oid}" data-bs-parent="#accordionOrdenes">
                            <div class="accordion-body p-2" id="tabla-${oid}">Cargando artículos...</div>
                            </div>
                        </div>`;

                        fetchJsonOrThrow(`/almacen/agenda/articulos/${encodeURIComponent(orden)}`)

                            .then(articulos => {
                                const rows = (Array.isArray(articulos) ? articulos : []).map((a, i) =>
                                    ({
                                        idx: i + 1,
                                        code: a.CodigoArticulo || '',
                                        desc: a.DescripcionArticulo || '',
                                        cant: isFinite(parseFloat(a.CantidadPendiente)) ?
                                            parseFloat(a.CantidadPendiente) : 0,
                                        um: (a.UnidadMedida || '').toString()
                                    }));

                                console.log('ARTICULOS:', articulos);
                                console.log('ROWS:', rows.length);

                                let html =
                                    `<div class="oc-table-wrapper">
                                        <table data-oc="${orden}" class="table-sticky-right oc-table" style="width:100%;border-collapse:collapse;margin-top:10px;">
                                            <thead>
                                                <tr style="background:#ffc0999e;">
                                                    <th style="padding:8px;border:1px solid #ddd;text-align:left;">Código</th>
                                                    <th style="padding:8px;border:1px solid #ddd;text-align:left;">Artículo</th>
                                                    <th style="padding:8px;border:1px solid #ddd;width:120px;text-align:right;">Pendiente</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                ${rows.map(r => `
                                                    <tr>
                                                        <td style="padding:6px;border:1px solid #eee;">${escapeHTML(r.code)}</td>
                                                        <td style="padding:6px;border:1px solid #eee;">${escapeHTML(r.desc)}</td>
                                                        <td style="padding:6px;border:1px solid #eee;text-align:right;">${r.cant % 1 === 0 ? r.cant : r.cant.toFixed(2)} ${escapeHTML(r.um)}</td>
                                                    </tr>
                                                `).join('')}
                                            </tbody>
                                        </table>
                                    </div>`;

                                const target = document.getElementById(`tabla-${oid}`);
                                if (target) {
                                    target.innerHTML = html;
                                    if (typeof setupQtyValidation === 'function') setupQtyValidation(
                                        target);
                                    if (typeof refreshResumen === 'function') refreshResumen(target);
                                }

                            })

                        fetchJsonOrThrow(`/almacen/recepcion/po/${encodeURIComponent(numLimpio)}`)
                            .then(po => {
                                if (!po || po.ok === false) {
                                    console.warn('[PO] respuesta no OK para OC', numLimpio, po);
                                    return;
                                }

                                const linesArr = Array.isArray(po?.lines) ?
                                    po.lines :
                                    (Array.isArray(po?.lines?.value) ? po.lines.value : []);

                                /* console.groupCollapsed(`[MAP] OC ${numLimpio}`);
                                console.log('DocEntry:', po?.po?.DocEntry, 'DocNum:', po?.po?.DocNum,
                                    'CardCode:', po?.po?.CardCode);
                                console.table((linesArr || []).map(l => ({
                                    LineNum: l.LineNum,
                                    ItemCode: l.ItemCode,
                                    UoMEntry: l.UoMEntry,
                                    Quantity: l.Quantity,
                                    OpenQty: l.OpenQuantity,
                                    TaxCode: l.TaxCode,
                                    Whs: l.WarehouseCode
                                })));
                                console.groupEnd(); */

                                if (!linesArr || !linesArr.length) {
                                    console.error('[MAP] linesArr vacío para OC', numLimpio, po);
                                    return;
                                }

                                const byItem = new Map(linesArr.map(l => [normCode(l.ItemCode), l]));
                                const table = document.querySelector(`#tabla-${slugId(orden)} table`);
                                if (!table) return;

                                table.querySelectorAll('tbody tr').forEach(tr => {
                                    const codeText = tr.children[2]?.textContent?.trim() || '';
                                    const key = normCode(codeText);
                                    const inp = tr.querySelector('input.inp-recibir');
                                    if (!inp || !key) return;

                                    const l = byItem.get(key);
                                    if (!l) {
                                        inp.dataset.baseline = -1;
                                        tr.classList.add('table-warning');
                                        tr.title = 'No vinculado a SAP (ItemCode no coincide)';
                                    } else {
                                        inp.dataset.baseline = (l.LineNum ?? -1);
                                        inp.dataset.docentry = po?.po?.DocEntry ?? '';
                                        inp.dataset.numAtCard = po?.po?.NumAtCard || '';
                                        inp.dataset.cardcode = po?.po?.CardCode || '';
                                        inp.dataset.itemcode = l.ItemCode ?? '';
                                        inp.dataset.whs = l.WarehouseCode || '';
                                        inp.dataset.uom = l.UoMEntry ?? '';
                                    }
                                });

                            })
                            .catch(err => {
                                const t = document.getElementById(`tabla-${slugId(orden)}`);
                                if (t) {
                                    t.innerHTML =
                                        `<div class="text-danger">
                                    No fue posible cargar los artículos de SAP.<br>
                                    <small>${escapeHTML(String(err.message || err))}</small>
                                    </div>`;
                                }
                                console.error('po_error:', err);
                            });
                    });

                    contenido += `</div>`;

                    let extra = '';
                    if (data.commit_afterrecep) {
                        extra += `
                        <h5 class="fw-bold mb-1">Comentario de almacén</h5>
                        <div class="mt-3 p-3" style="background:#FBDDC6;border-radius:8px;">
                            <div>${escapeHTML(data.commit_afterrecep)}</div>
                        </div>`;
                    }
                    if (data.evidencia_path && data.evidencia_nombre) {
                        const url = `/almacen/evidencia/${id}`;
                        const peso = data.evidencia_size ? ` (${(data.evidencia_size/1024/1024).toFixed(1)} MB)` :
                            '';
                        extra += `
                        <div class="mt-2">
                            <a class="btn btn-outline-orange btn-sm" href="${url}" target="_blank" rel="noopener">
                            Descargar evidencia: ${escapeHTML(data.evidencia_nombre)}${peso}
                            </a>
                        </div>`;
                    }
                    if (extra) contenido += `<hr class="my-3">${extra}`;

                    document.getElementById('contenidoModal').innerHTML = contenido;
                    new bootstrap.Modal(document.getElementById('modalDetalles')).show();
                })

                .catch(err => {
                    document.getElementById('contenidoModal').innerHTML = `
                    <div class="text-danger">
                    No fue posible cargar la reservación.<br>
                    <small>${escapeHTML(String(err.message || err))}</small>
                    </div>`;
                    new bootstrap.Modal(document.getElementById('modalDetalles')).show();
                    console.error('[showDetails] error:', err);
                });

        };
    </script>
@stop
