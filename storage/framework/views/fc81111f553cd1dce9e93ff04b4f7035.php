<?php $__env->startSection('title', 'Arribos programados'); ?>
<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('includes.scripts.SweetAlert2@11', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('includes.scripts.googleapis', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('includes.scripts.bootstrap', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
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
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/rol/almacen/agenda.css')); ?>">

    <?php
        $rolId = session('Usuario.IdRol');
        $sucursalIdUsuario = session('Usuario.id_sucursal');
    ?>

    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <div class="container-fluid con-sidebar">
        <div class="row justify-content-center">

            <div class="d-flex justify-content-center align-items-center gap-2 nav-agenda-row">
                <button id="btn-prev" class="btn btn-orange btn-nav-agenda d-flex align-items-center justify-content-center"
                    onclick="moverAgenda(-1)">
                    <i class="material-icons">chevron_left</i>
                </button>

                <?php if(!in_array($rolId, [2])): ?>
                    <form method="GET" action="<?php echo e(route('agenda.index')); ?>" class="m-0 flex-fill">
                        <div class="selector-sucursal px-3 py-2 rounded">
                            <select name="sucursal_id" id="sucursal_id" class="form-select form-select-sm text-center"
                                onchange="this.form.submit()">
                                <?php $__currentLoopData = $sucursales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($s->id); ?>"
                                        <?php echo e(request('sucursal_id') == $s->id ? 'selected' : ''); ?>>
                                        <?php echo e($s->nombre); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </form>
                <?php else: ?>
                    <input type="hidden" id="sucursal_id" value="<?php echo e($sucursalIdUsuario); ?>">
                <?php endif; ?>

                <button id="btn-next"
                    class="btn btn-orange btn-nav-agenda d-flex align-items-center justify-content-center"
                    onclick="moverAgenda(1)">
                    <i class="material-icons">chevron_right</i>
                </button>
            </div>


            <div class="row">
                <!-- Ayer -->
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

                <!-- Hoy -->
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

                <!-- Mañana -->
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
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modalDetalles" tabindex="-1" role="dialog" aria-labelledby="modalDetallesLabel"
        aria-hidden="true" data-bs-focus="false">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modalDetallesLabel">Detalles de la Orden de compra</h4>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="reservacionId">
                    <input type="hidden" id="reservacionFecha">
                    <input type="hidden" id="reservacionHora">
                    <div id="contenidoModal"></div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-orange" onclick="editarEstado()">Cambiar Status</button>
                    <button type="button" class="btn btn-success" onclick="confirmarRecepcion()">Confirmar</button>
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

        const ROL_ID = <?php echo e((int) $rolId); ?>;
        const ES_ADMIN = [5].includes(ROL_ID);
        //const ES_ALMACEN = [2, 5].includes(ROL_ID);


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

        function loadAgendaDataForDate(date, index) {
            const formattedDate = ymdLocal(date);
            const sucursalId = document.getElementById('sucursal_id')?.value || '<?php echo e($sucursal_id); ?>';
            const timeline = document.getElementById(`timeline-${index}`);

            if (timeline) {
                renderSkeletonTimeline(index, 1);
            }

            fetch(`/almacen/agenda/data?fecha=${formattedDate}&sucursal_id=${sucursalId}`)
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
                        //const ocBadges = formatOrdenCompra(item.orden_compra);
                        const ocBadges = (typeof formatOrdenCompra === 'function') ?
                            formatOrdenCompra(item.orden_compra) :
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
                            <div class="timeline-marker">
                                <div class="linea-con-circulo">
                                    <div class="circulo"></div>
                                    <div class="linea"></div>
                                </div>
                            </div>

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

                                            <i class="material-icons" style="font-size: 1.5rem; vertical-align: middle;">arrow_forward</i>
  
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
                                            <strong class="text-truncate">
                                                ${escapeHTML(item.proveedor_nombre || 'Paqueteria Express / Cita no programada')}
                                            </strong>
                                        </div>

                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <i class="material-icons" style="font-size: 1.5rem;">access_time</i>
                                            <span>${formatTime12h(item.hora) || 'No especificado'}</span>
                                        </div>

                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <i class="material-icons" style="font-size: 1.5rem;">local_shipping</i>
                                            <span class="text-truncate">
                                                ${escapeHTML(item.transporte_nombre || 'No especificado')}
                                            </span>
                                        </div>

                                        <div class="d-flex align-items-center gap-2">
                                            <i class="material-icons" style="font-size: 1.5rem;">place</i>
                                            <span class="text-truncate">
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
                        marcarIndicadores(item.id);
                    });
                })

                .catch(err => {
                    console.error('[Agenda] Error', formattedDate, err);
                    if (timeline) {
                        timeline.innerHTML = `
                    <div class="text-danger" style="padding:8px 12px;">
                        Error cargando ${formattedDate}<br>
                        <small>${escapeHTML(String(err.message||err))}</small>
                    </div>`;
                    }
                });
        }

        function renderSkeletonTimeline(index, count = 3) {
            const timeline = document.getElementById(`timeline-${index}`);
            if (!timeline) return;
            let html = '';

            for (let i = 0; i < count; i++) {
                html +=
                    `<div class="timeline-item skeleton-card">

                <div class="timeline-marker">
                    <div class="linea-con-circulo">
                        <div class="circulo skeleton-bg"></div>
                        <div class="linea skeleton-bg"></div>
                    </div>
                </div>

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

        function marcarIndicadores(id) {
            fetch(`/almacen/agenda/detalles/${id}`)
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
                    const cont = document.getElementById(`oc-icons-${id}`);
                    if (!cont) return;

                    const icons = [];

                    const tieneComentario = d.commit_afterrecep && String(d.commit_afterrecep).trim().length;
                    const tieneEvidencia = (d.evidencia_path && d.evidencia_nombre) || d.evidencias;

                    if (tieneComentario) {
                        icons.push(`
                        <span class="icono-info icono-coment" title="Tiene comentario" onclick="showDetails(${id})">
                            <i class="material-icons">mode_comment</i>
                        </span>
                        `);
                    }
                    if (tieneEvidencia) {
                        icons.push(`
                        <a class="icono-info icono-evid" title="Ver evidencia" href="/almacen/evidencia/${id}" target="_blank" rel="noopener">
                            <i class="material-icons">attach_file</i>
                        </a>
                        `);
                    }

                    cont.innerHTML = icons.join('');
                })
                .catch(err => {
                    console.warn('[marcarIndicadores] error', id, err);
                });
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
                1: 156, // Matriz
                4: 157, // Guadalajara
            };
            return mapa[sid] || 156;
        }

        function setupQtyValidation(rootEl) {
            const inputs = rootEl.querySelectorAll('input.inp-recibir');
            inputs.forEach(inp => {
                inp.addEventListener('wheel', (e) => e.target.blur(), {
                    passive: true
                });
                inp.addEventListener('keydown', (ev) => {
                    const allowed = ['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'Tab', 'Home',
                        'End'
                    ];
                    if (allowed.includes(ev.key)) return;
                    if (!/^\d$/.test(ev.key)) ev.preventDefault();
                });
                inp.addEventListener('input', (ev) => {
                    ev.target.value = ev.target.value.replace(/[^\d]/g, '');
                    syncCheckboxWithInput(ev.target);
                    validarYRefrescar(ev.target);
                });
                inp.addEventListener('blur', (ev) => validarYRefrescar(ev.target, true));
            });

            function validarYRefrescar(el, showAlert = false) {
                const pendiente = Number(el.dataset.pendiente || 0);
                let val = el.value === '' ? '' : Number(el.value);
                const td = el.closest('td');

                if (el.value === '') {
                    td?.classList.remove('cell-excede');
                    refreshResumen(rootEl);
                    return;
                }
                if (!Number.isFinite(val) || val < 0) val = 0;
                if (!Number.isInteger(val)) val = Math.floor(val);
                if (val > pendiente) {
                    td?.classList.add('cell-excede');
                    if (showAlert) showWarn('Cantidad excedida',
                        `No puedes recibir más de ${new Intl.NumberFormat('es-MX').format(pendiente)} (pendiente).`);
                    val = pendiente;
                } else td?.classList.remove('cell-excede');

                el.value = String(val);
                refreshResumen(rootEl);
            }
        }

        function refreshResumen(scope = document) {
            const rows = [...scope.querySelectorAll('input.inp-recibir')];
            let lineasSel = 0,
                totalUds = 0;
            rows.forEach(inp => {
                const qty = (inp.value || '').trim() === '' ? 0 : Number(inp.value);
                if (qty > 0) {
                    lineasSel++;
                    totalUds += qty;
                }
            });

            const lblLineas = document.getElementById('lblLineasSeleccionadas');
            const lblUnids = document.getElementById('lblTotalUnidades');
            if (lblLineas) lblLineas.textContent = new Intl.NumberFormat('es-MX').format(lineasSel);
            if (lblUnids) lblUnids.textContent = new Intl.NumberFormat('es-MX').format(totalUds);

            scope.querySelectorAll('table[data-oc]').forEach(tbl => {
                let sum = 0;
                tbl.querySelectorAll('input.inp-recibir').forEach(i => {
                    if ((i.value || '').trim()) sum += Number(i.value);
                });
                const foot = tbl.querySelector('tfoot .sum-oc');
                if (foot) foot.textContent = new Intl.NumberFormat('es-MX').format(sum);
            });
        }

        function autollenarPendientes(scope = document, soloChequeados = false) {
            const tablas = scope.querySelectorAll('table[data-oc]');
            let marcados = 0;

            tablas.forEach(tbl => {
                tbl.querySelectorAll('tbody tr').forEach(tr => {
                    const chk = tr.querySelector('input[type="checkbox"]');
                    const inp = tr.querySelector('input.inp-recibir');
                    if (!chk || !inp) return;

                    // si soloChequeados = true, sólo toca las filas que ya están marcadas
                    if (soloChequeados && !chk.checked) return;

                    const pendiente = Number(inp.dataset.pendiente || 0) || 0;

                    // si es autollenado general, marca el check
                    if (!soloChequeados) chk.checked = true;

                    inp.value = pendiente ? String(Math.floor(pendiente)) : '';
                    inp.dispatchEvent(new Event('input')); // para refrescar totales
                    marcados++;
                });
            });

            if (soloChequeados && marcados === 0) {
                showWarn('Nada seleccionado', 'Marca al menos una línea para autollenar.');
            }
        }

        function limpiarCaptura(scope = document) {
            scope.querySelectorAll('table[data-oc]').forEach(tbl => {
                tbl.querySelectorAll('tbody tr').forEach(tr => {
                    const chk = tr.querySelector('input[type="checkbox"]');
                    const inp = tr.querySelector('input.inp-recibir');

                    if (chk) chk.checked = false; // desmarcar check
                    if (inp) {
                        inp.value = ''; // limpiar input
                        inp.dispatchEvent(new Event('input')); // refrescar totales
                    }
                });
            });
        }

        function toggleLinea(chk) {
            const tr = chk.closest('tr');
            if (!tr) return;

            const inp = tr.querySelector('input.inp-recibir');
            if (!inp) return;

            const pendiente = Number(inp.dataset.pendiente || 0) || 0;

            if (chk.checked) {
                // poner el pendiente
                inp.value = pendiente ? String(Math.floor(pendiente)) : '';
            } else {
                // limpiar
                inp.value = '';
            }

            inp.dispatchEvent(new Event('input')); // vuelve a calcular sumas/resumen
        }

        function previewGRPO() {
            const scope = document.getElementById('contenidoModal');
            scope.querySelectorAll('input.inp-recibir').forEach(inp => inp.dispatchEvent(new Event('blur')));

            const entradas = [...scope.querySelectorAll('table[data-oc]')].flatMap(tbl => {
                const ocNum = Number(tbl.dataset.oc);
                return [...tbl.querySelectorAll('input.inp-recibir')].map(inp => {
                    const qty = (inp.value || '').trim() === '' ? 0 : Number(inp.value);
                    if (!qty) return null;
                    return {
                        oc: ocNum,
                        docEntry: Number(inp.dataset.docentry || 0),
                        BaseLine: Number(inp.dataset.baseline || -1),
                        Quantity: qty,
                        //TaxCode: (inp && inp.dataset ? inp.dataset.taxcode : undefined),
                        WarehouseCode: (inp && inp.dataset ? inp.dataset.whs : undefined),
                        ItemCode: (inp && inp.dataset ? inp.dataset.itemcode : undefined)
                    };

                }).filter(Boolean);
            });

            if (!entradas.length) {
                return showWarn('Sin captura', 'No hay cantidades para previsualizar.');
            }

            const byOC = {};
            for (const e of entradas) {
                byOC[e.oc] = byOC[e.oc] || [];
                byOC[e.oc].push({
                    BaseLine: e.BaseLine,
                    ItemCode: e.ItemCode,
                    //TaxCode: e.TaxCode,
                    Quantity: e.Quantity,
                    WarehouseCode: e.WarehouseCode,
                });
            }
            const payloads = Object.entries(byOC).map(([oc, lines]) => ({
                docNum: Number(oc),
                docEntry: entradas.find(e => e.oc === Number(oc))?.docEntry || 0,
                comments: 'Recepción desde Intranet Proveedores 📦 ',
                lines
            }));

            Swal.fire({
                width: 500,
                title: 'Payload(s) GRPO',
                html: `<pre style="text-align:left;white-space:pre-wrap;background:#f7f7f8;padding:12px;border-radius:8px;max-height:60vh;overflow:auto;">${escapeHTML(JSON.stringify(payloads,null,2))}</pre>`,
                confirmButtonText: 'OK'
            });
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

        function fetchJsonOrThrow(url, opt) {
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

        async function confirmarRecepcion() {
            if (recepcionEnProceso) {
                return Swal.fire({
                    icon: 'info',
                    title: 'Recepción en proceso',
                    text: 'Ya se está creando la recepción en SAP, no es necesario volver a presionar el botón.'
                });
            }

            document.querySelectorAll('#contenidoModal input.inp-recibir')
                .forEach(inp => inp.dispatchEvent(new Event('blur')));

            const csrf = document.querySelector("meta[name='csrf-token']").getAttribute('content');
            const sucursalId = Number(document.getElementById('sucursal_id')?.value || 0);
            const seleccion = collectCheckedLines(document.getElementById('contenidoModal'));
            if (!seleccion.length) {
                return Swal.fire({
                    icon: 'info',
                    title: 'Sin selección',
                    text: 'Marca líneas y captura cantidades para continuar.'
                });
            }

            const {
                rows,
                grand
            } = buildPreviewTableRows(seleccion);

            const headerHtml = buildPreviewHeader(seleccion);

            const ok = await Swal.fire({
                icon: 'question',
                title: '¿Confirmar recepción?',
                html: `
                <div style="text-align:left;max-height:50vh;overflow:auto;">

                    ${headerHtml} 

                    <table style="width:100%;border-collapse:collapse;">
                        <thead>
                        <tr>
                            <th style="border:1px solid #ccc;padding:6px;background:#f0f0f0;text-align:left;">Descripción</th>
                            <th style="border:1px solid #ccc;padding:6px;background:#f0f0f0;text-align:right;">Cantidad a recibir</th>
                        </tr>
                        </thead>
                        <tbody>
                        ${rows}
                        </tbody>
                        <tfoot>
                        <tr>
                            <td style="border:1px solid #ddd;padding:6px;text-align:right;font-weight:700;">Total</td>
                            <td style="border:1px solid #ddd;padding:6px;text-align:right;font-weight:800;">${fmt(grand)}</td>
                        </tr>
                        </tfoot>
                    </table>
                    <div style="margin-top:10px;color:#666;font-size:.9rem;">
                        Se crearán las recepciones en SAP según lo mostrado.
                    </div>
                </div>`,
                showCancelButton: true,
                confirmButtonText: 'Sí, continuar',
                cancelButtonText: 'Revisar'
            }).then(r => r.isConfirmed);

            if (!ok) return;

            recepcionEnProceso = true;

            const porOC = {};
            document.querySelectorAll('#contenidoModal table[data-oc]').forEach(tbl => {
                const oc = Number(tbl.dataset.oc);

                const numAtCard = tbl.closest('.accordion-item')
                    ?.querySelector('.inp-numatcard')
                    ?.value?.trim() || '';

                let cardCode = '';
                const sampleInp = tbl.querySelector('tbody input.inp-recibir');
                if (sampleInp) {
                    cardCode = sampleInp.dataset.cardcode || '';
                }

                const comentarioSap = tbl.closest('.accordion-item')
                    ?.querySelector('.inp-comentario-grpo')
                    ?.value?.trim() || '';

                const lines = [];
                tbl.querySelectorAll('tbody tr').forEach(tr => {
                    const chk = tr.querySelector('input[type="checkbox"]');
                    const inp = tr.querySelector('input.inp-recibir');
                    if (!chk || !inp || !chk.checked) return;

                    const raw = (inp.value || '').trim();
                    const qty = raw === '' ? 0 : Number(raw);
                    if (!(qty > 0)) return;

                    const baseLine = parseInt(inp.dataset.baseline || '-1', 10);
                    const pendiente = parseFloat(inp.dataset.pendiente || '0') || 0;

                    if (baseLine < 0) {
                        showWarn('Línea sin BaseLine',
                            `El artículo ${inp.dataset.itemcode || '(sin código)'} no quedó ligado a SAP.`
                        );
                        throw new Error('baseline_missing');
                    }
                    if (qty > pendiente) {
                        showWarn('Cantidad excedida', 'La cantidad a recibir supera el pendiente.');
                        throw new Error('exceso');
                    }

                    lines.push({
                        BaseLine: baseLine,
                        ItemCode: inp.dataset.itemcode || undefined,
                        //TaxCode: inp.dataset.taxcode || undefined,
                        Quantity: qty,
                        WarehouseCode: inp.dataset.whs || undefined
                    });
                });

                if (lines.length) {
                    if (!numAtCard) {
                        showWarn('Falta referencia', 'Captura el No. ref. del acreedor para esta OC.');
                        throw new Error('numAtCard_missing');
                    }
                    if (!cardCode) {
                        showWarn('Falta proveedor', 'No tengo el CardCode de la OC en SAP.');
                        throw new Error('cardcode_missing');
                    }

                    porOC[oc] = {
                        numAtCard,
                        cardCode,
                        lines,
                        comentarioSap
                    };
                }
            });

            const ocs = Object.keys(porOC);
            if (!ocs.length) {
                return Swal.fire({
                    icon: 'info',
                    title: 'Sin cambios',
                    text: 'No ingresaste cantidades a recibir.'
                });
            }
            const btn = document.querySelector('.modal-footer .btn.btn-success');
            const oldText = btn ? btn.textContent : '';
            if (btn) {
                btn.disabled = true;
                btn.textContent = 'Creando recepción…';
            }

            try {
                const mensajesOK = [];
                const errores = [];

                for (const oc of ocs) {
                    const {
                        numAtCard,
                        cardCode,
                        lines,
                        comentarioSap
                    } = porOC[oc];

                    const commentSap = comentarioSap ?
                        `${comentarioSap} | Recepción desde Intranet Proveedores ${Number(oc)}` :
                        `Recepción desde Intranet Proveedores ${Number(oc)}`;

                    const valid = await fetch('/almacen/recepcion/grpo/validar', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            docNum: Number(oc),
                            sucursal_id: sucursalId,
                            cardCode,
                            numAtCard,
                            lines
                        })
                    }).then(async r => {
                        const text = await r.text();
                        try {
                            return JSON.parse(text);
                        } catch (e) {
                            throw new Error(`Respuesta NO JSON (${r.status}): ${text.substring(0,200)}`);
                        }
                    });


                    if (!valid.ok) {
                        errores.push(`OC ${oc}: ${(valid.errors || [valid.msg]).join('\n')}`);
                        continue;
                    }

                    const createRes = await fetch('/almacen/recepcion/grpo', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrf
                        },
                        body: JSON.stringify({
                            docNum: Number(oc),
                            cardCode: cardCode,
                            comments: commentSap,
                            numAtCard,
                            sucursal_id: sucursalId,
                            lines
                        })
                    }).then(async r => {
                        const text = await r.text();
                        try {
                            return JSON.parse(text);
                        } catch (e) {
                            throw new Error(`Respuesta NO JSON (${r.status}): ${text.substring(0,200)}`);
                        }
                    });

                    if (createRes.ok) {
                        const docNumGrpo = createRes?.data?.DocNum ?? null;

                        mensajesOK.push(`GRPO <b>${docNumGrpo || '(sin DocNum)'}</b> creado (OC ${oc})`);

                        grposCreados.push({
                            oc: Number(oc),
                            docNum: docNumGrpo,
                            numAtCard,
                            cardCode,
                            comments: commentSap
                        });
                    } else {
                        errores.push(`OC ${oc}: ${createRes.msg || 'Error al crear Entrada de mercancia'}`);
                        console.error('Error creando Entrada de mercancia:', createRes);
                    }
                }

                if (mensajesOK.length && !errores.length) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Recepción creada',
                        html: mensajesOK.join('<br>'),
                        showCancelButton: true,
                        confirmButtonText: 'Imprimir reporte',
                        cancelButtonText: 'Cerrar'
                    }).then(res => {
                        if (res.isConfirmed) {
                            imprimirReporteGRPO(grposCreados, seleccion, window.recepcionContext || null);
                        } else {
                            location.reload();
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        html: errores.join('<br>') || 'No se creó ninguna recepción.'
                    });
                }
            } catch (err) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: String(err.message || err)
                });
            } finally {
                if (btn) {
                    btn.disabled = false;
                    btn.textContent = oldText;
                }
                recepcionEnProceso = false;
            }
        }


        const usuario = <?php echo json_encode(session('Usuario.Nombre') ?? (session('Usuario.UserName') ?? 'Usuario'), 15, 512) ?>;

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

            // escritura estable + esperar carga
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
            const res = [];
            scope.querySelectorAll('table[data-oc]').forEach(tbl => {
                const oc = Number(tbl.dataset.oc);
                tbl.querySelectorAll('tbody tr').forEach(tr => {
                    const chk = tr.querySelector('input[type="checkbox"]');
                    const inp = tr.querySelector('input.inp-recibir');
                    if (!chk || !inp) return;
                    const raw = (inp.value || '').trim();
                    const qty = raw === '' ? 0 : Number(raw);
                    if (!chk.checked || !(qty > 0)) return;

                    const code = (tr.children[2]?.textContent || '').trim();
                    const desc = (tr.children[3]?.textContent || '').trim();
                    res.push({
                        oc,
                        code,
                        desc,
                        qty,
                        tr,
                        tbl
                    });
                });
            });
            return res;
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
            const scope = document.getElementById('contenidoModal');
            const ocs = Array.from(new Set(seleccion.map(it => it.oc)));
            let html = '';
            ocs.forEach(oc => {
                const tbl = scope.querySelector(`table[data-oc="${oc}"]`);
                if (!tbl) return;

                const numAtCard = tbl.closest('.accordion-item')
                    ?.querySelector('.inp-numatcard')
                    ?.value?.trim() || '';

                const comentarioSap = tbl.closest('.accordion-item')
                    ?.querySelector('.inp-comentario-grpo')
                    ?.value?.trim() || '';

                html +=
                    `<div style="margin-bottom:10px;
                            padding:8px 10px;
                            border:1px solid #ffd2a6;
                            border-radius:6px;
                            background:#fff7ef;
                            font-size:.85rem;">
                    <div style="font-weight:700;margin-bottom:4px;">OC ${oc}</div>
                    <div><strong>No. ref. del acreedor:</strong> ${escapeHTML(numAtCard || 'Sin captura')}</div>
                    <div><strong>Comentario recepción:</strong> ${escapeHTML(comentarioSap || 'Sin comentario')}</div>
                </div>`;
            });

            return html;
        }

        function parseOrdenCompra(raw) {
            if (raw == null) return [];

            // si ya viene array
            if (Array.isArray(raw)) return raw.map(x => String(x).trim()).filter(Boolean);

            // si viene string
            if (typeof raw === 'string') {
                const s = raw.trim();
                if (!s) return [];

                // intenta JSON primero
                if ((s.startsWith('[') && s.endsWith(']')) || (s.startsWith('{') && s.endsWith('}'))) {
                    try {
                        const j = JSON.parse(s);
                        if (Array.isArray(j)) return j.map(x => String(x).trim()).filter(Boolean);
                    } catch (_) {
                        /* cae al split */
                    }
                }

                // fallback split
                return s.split(',')
                    .map(x => x.replace(/[\[\]"]/g, '').trim())
                    .filter(Boolean);
            }

            // cualquier otra cosa
            return [String(raw).trim()].filter(Boolean);
        }


        window.showDetails = function(id) {
            document.getElementById('reservacionId').value = id;

            fetch(`/almacen/agenda/detalles/${id}`)
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
                                    `<div class="row g-2 mb-2">
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <strong>
                                                <span class="input-group-text" style="background-color: #ffc0999e;" id="basic-addon1">
                                                    # Folio del Proveedor
                                                </span>
                                            </strong>
                                        </div>
                                        <input class="form-control form-control-sm inp-numatcard"
                                            placeholder="Remisión || Folio proveedor" />
                                    </div>
                                </div>

                                <div class="row g-2 mb-2">
                                    <div class="col-12">
                                        <label class="form-label mb-1" style="font-size:.85rem;">
                                            Comentario de recepción (se enviará a SAP)
                                        </label>
                                        <textarea class="form-control form-control-sm inp-comentario-grpo"
                                                rows="2"
                                                placeholder="Comentario para SAP (opcional)"></textarea>
                                    </div>
                                </div>

                                <div class="d-flex gap-2 mb-2">
                                    <button type="button" class="btn btn-outline-orange btn-sm"
                                    onclick="autollenarPendientes(document.getElementById('contenidoModal'), false)">
                                    Autollenar
                                    </button>

                                    <button type="button" class="btn btn-light btn-sm"
                                    onclick="limpiarCaptura(document.getElementById('contenidoModal'))">
                                    Limpiar
                                    </button>
                                    ${ES_ADMIN? `
                                                                        <button type="button" class="btn btn-success btn-sm" onclick="previewGRPO()">
                                                                        Previsualizar
                                                                        </button>
                                                                        <button type="button" class="btn btn-secondary btn-sm" onclick="imprimirModal()">
                                                                        Imprimir
                                                                        </button>`:''
                                    }
                                </div>

                                <div class="oc-table-wrapper">
                                    <table data-oc="${orden}" class="table-sticky-right oc-table" style="width:100%;border-collapse:collapse;margin-top:10px;">
                                        <thead>
                                            <tr style="background:#ffc0999e;">
                                                <th style="padding:8px;border:1px solid #ddd;width:40px;text-align:center;">#</th>
                                                <th style="padding:8px;border:1px solid #ddd;width:50px;text-align:center;">✓</th>
                                                <th style="padding:8px;border:1px solid #ddd;text-align:left;">Código</th>
                                                <th style="padding:8px;border:1px solid #ddd;text-align:left;">Artículo</th>
                                                <th style="padding:8px;border:1px solid #ddd;width:80px;text-align:right;">Pendiente</th>
                                                <th style="padding:8px;border:1px solid #ddd;width:100px;text-align:center;">Recepción</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            ${rows.map(r => {
                                            const inputId = `rec-${oid}-${r.idx}`;
                                            return `<tr>
                                                                        <td style="padding:6px;border:1px solid #eee;text-align:center;">${r.idx}</td>
                                                                        <td style="padding:6px;border:1px solid #eee;text-align:center;"><input type="checkbox"class="chk-linea" style="transform:scale(1.2);accent-color:#ee7826;" onchange="toggleLinea(this)"></td>
                                                                        <td style="padding:6px;border:1px solid #eee;">${escapeHTML(r.code)}</td>
                                                                        <td style="padding:6px;border:1px solid #eee;">${escapeHTML(r.desc)}</td>
                                                                        <td style="padding:6px;border:1px solid #eee;text-align:right;">${r.cant % 1 === 0 ? r.cant : r.cant.toFixed(2)} ${escapeHTML(r.um)}</td>
                                                                        <td class="sticky-right" style="padding:6px;border:1px solid #eee;text-align:right;"> <input id="${inputId}"class="form-control form-control-sm inp-recibir" type="number" inputmode="numeric" step="1" min="0" aria-label="Cantidad a recibir" placeholder="0" style="text-align:right;" data-pendiente="${r.cant}" data-desc="${escapeHTML(r.desc).replace(/"/g,'&quot;')}" data-um="${escapeHTML(r.um).replace(/"/g,'&quot;')}" /></td>
                                                                    </tr>`;}).join('')}
                                        </tbody>

                                        <tfoot>
                                            <tr>
                                                <td colspan="5" style="padding:8px;border-top:2px solid #ddd;text-align:right;font-weight:600;"> Total capturado: </td>
                                                <td class="sticky-right" style="padding:8px;border-top:2px solid #ddd;text-align:right;font-weight:700;"> <span class="sum-oc">0</span> </td>
                                            </tr>
                                        </tfoot>
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

                                console.groupCollapsed(`[MAP] OC ${numLimpio}`);
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
                                console.groupEnd();

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
                                        //inp.dataset.taxcode = l.TaxCode || '';
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.movil', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\ygonzalez\Synology\Home\Escritorio\Proveedores\resources\views/pages/almacen/AgendaProveedor.blade.php ENDPATH**/ ?>