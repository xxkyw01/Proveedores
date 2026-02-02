<?php $__env->startSection('title', 'Arribos Programados'); ?>
<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('includes.scripts.SweetAlert2@11', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('includes.scripts.googleapis', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('includes.scripts.bootstrap', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/rol/almacen/monitorAgenda.css')); ?>">

    <?php
        $usuario = session('Usuario', []);
        $rolId = $usuario['IdRol'] ?? null;
        $sucursalIdUsuario = $usuario['SucursalID'] ?? null;
    ?>

    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <div class="container-fluid c">
        <div class="row justify-content-center">
            <div class="d-flex flex-wrap justify-content-center align-items-center gap-3 flex-column flex-md-row">
                <?php if(!in_array($rolId, [8])): ?>
                    <form method="GET" action="<?php echo e(route('agenda.index')); ?>" class="m-0">
                        <div class="selector-sucursal px-3 py-2 rounded">
                            <select name="sucursal_id" id="sucursal_id" class="form-select" onchange="this.form.submit()">
                                <?php $__currentLoopData = $sucursales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($s->id); ?>" <?php echo e(request('sucursal_id') == $s->id ? 'selected' : ''); ?>>
                                        <?php echo e($s->nombre); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </form>
                <?php else: ?>
                    <input type="hidden" id="sucursal_id" value="<?php echo e($sucursalIdUsuario); ?>">
                <?php endif; ?>
            </div>

            <div class="row">
                <!-- HOY -->
                <div class="col columna-dia" data-index="0" id="col-presente">
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

                <!-- MAÑANA -->
                <div class="col columna-dia" data-index="1" id="col-manana">
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

                <!-- PASADO MAÑANA -->
                <div class="col columna-dia" data-index="2" id="col-pasadomanana">
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

    <script>
        // CONFIG
        const AUTO_REFRESH_TIME = 5 * 60 * 1000; // 5 min
        const HOY_REFRESH_MS     = 30 * 1000;     // 30s (solo HOY)
        const DAY_CHANGE_CHECK_MS= 60 * 1000;     // 1 min

        let fechasVisibles = [];
        let lastDateKey = null;

        // Helpers fecha (LOCAL)
        function isWeekend(d) {
            const day = d.getDay();
            return day === 0 || day === 6;
        }

        function clone(d) {
            return new Date(d.getFullYear(), d.getMonth(), d.getDate());
        }

        function toKeyLocal(d = new Date()) {
            const y = d.getFullYear();
            const m = String(d.getMonth() + 1).padStart(2, '0');
            const day = String(d.getDate()).padStart(2, '0');
            return `${y}-${m}-${day}`;
        }

        function toKey(d) {
            return toKeyLocal(d);
        }

        function formatDate(date) {
            const optionsDay = { weekday: 'long' };
            const optionsNumber = { day: 'numeric', month: 'long' };
            return {
                day: date.toLocaleDateString('es-ES', optionsDay),
                number: date.toLocaleDateString('es-ES', optionsNumber)
            };
        }

        function getNextBusinessDays(baseDate, count) {
            const days = [];
            let d = clone(baseDate);

            while (isWeekend(d)) d.setDate(d.getDate() + 1);

            while (days.length < count) {
                if (!isWeekend(d)) days.push(clone(d));
                d.setDate(d.getDate() + 1);
            }
            return days;
        }

        // UI responsive (monitor)
        function ajustarColumnasPorDispositivo() {
            const ancho = window.innerWidth;

            // ocultar todo
            document.querySelectorAll('.columna-dia').forEach(col => col.style.display = 'none');

            if (ancho >= 992) {
                document.getElementById('col-presente').style.display = 'block';
                document.getElementById('col-manana').style.display = 'block';
                document.getElementById('col-pasadomanana').style.display = 'block';
            } else if (ancho >= 768) {
                document.getElementById('col-presente').style.display = 'block';
                document.getElementById('col-manana').style.display = 'block';
            } else {
                document.getElementById('col-presente').style.display = 'block';
            }
        }

        window.addEventListener('resize', ajustarColumnasPorDispositivo);

        // Tiempo -> minutos (robusto)
        function timeToMinutes(raw = '') {
            const s = String(raw ?? '').trim();
            if (!s) return null;
 
            const m = s.match(/(\d{1,2}):(\d{2})(?:[:.]\d+)?\s*(am|pm)?/i);
            if (!m) return null;

            let hh = parseInt(m[1], 10);
            const mm = parseInt(m[2], 10);
            const ap = (m[3] || '').toLowerCase();

            if (Number.isNaN(hh) || Number.isNaN(mm)) return null;

            if (ap === 'pm' && hh !== 12) hh += 12;
            if (ap === 'am' && hh === 12) hh = 0;

            if (hh < 0 || hh > 23 || mm < 0 || mm > 59) return null;
            return hh * 60 + mm;
        }

        // Render encabezados + carga datos
        function renderHeadersAndLoad() {
            ajustarColumnasPorDispositivo();

            for (let i = 0; i < fechasVisibles.length; i++) {
                const fecha = fechasVisibles[i];
                const formatted = formatDate(fecha);

                const dayEl = document.getElementById(`day-${i}`);
                const numEl = document.getElementById(`date-${i}`);


                if (dayEl) dayEl.innerText = ` ${formatted.day.toUpperCase()}`;
                if (numEl) numEl.innerText = formatted.number;

                loadAgendaDataForDate(fecha, i);
            }
        }

        // FILTRO HOY: solo futuras de prensente + dejar 30 minutos de margen 

        function filtrarCitasPasadasHoy(data, fechaKey) {
            const hoyKey = toKeyLocal(new Date());
            if (fechaKey !== hoyKey) return data;

            const ahora = new Date();
            const ahoraMin = ahora.getHours() * 60 + ahora.getMinutes();
            const margenMin = 30; 

            return data.filter(item => {
                const itemMin = timeToMinutes(item?.hora);
                if (itemMin == null) return true; 
                return itemMin >= (ahoraMin - margenMin);
            });
        }

        // Cargar data por fecha
        function loadAgendaDataForDate(date, index) {
            const formattedDate = toKey(date);

            const sucEl = document.getElementById('sucursal_id');
            let sucursalId = sucEl ? sucEl.value : null;
            if (!sucursalId) sucursalId = <?php echo json_encode($sucursalIdUsuario, 15, 512) ?>;

            if (!sucursalId) {
                console.warn('No hay sucursal_id para la consulta');
                return;
            }

            fetch(`/almacen/agenda/data?fecha=${formattedDate}&sucursal_id=${encodeURIComponent(sucursalId)}`)
                .then(r => r.json())
                .then(data => {
                    const timeline = document.getElementById(`timeline-${index}`);
                    if (!timeline) return;
                    timeline.innerHTML = '';

                    // ordenar por hora
                    data.sort((a, b) => {
                        const ma = timeToMinutes(a?.hora);
                        const mb = timeToMinutes(b?.hora);
                        if (ma == null && mb == null) return 0;
                        if (ma == null) return 1;
                        if (mb == null) return -1;
                        return ma - mb;
                    });

                    // HOY: filtrar pasadas
                    if (index === 0) {
                        const originales = [...data];
                        const filtradas = filtrarCitasPasadasHoy(data, formattedDate);

                        // debug opcional (puedes quitarlo luego)
                        console.log('HOY col:', formattedDate, 'HOY real:', toKeyLocal(new Date()));
                        console.log('TOTAL hoy:', originales.length, 'FUTURAS hoy:', filtradas.length);

                        if (originales.length === 0) {
                            timeline.innerHTML = `<div class="text-center opacity-75 mt-4">Sin citas hoy</div>`;
                            return;
                        }

                        if (filtradas.length === 0) {
                            timeline.innerHTML = `
                                <div class="text-center opacity-75 mt-4">
                                    <div style="font-size:1.2rem;font-weight:900;">Hoy ya no hay citas pendientes</div>
                                    <div style="font-size:0.95rem;">(todas las de hoy ya pasaron)</div>
                                </div>
                            `;
                            return;
                        }

                        filtradas.forEach(item => {
                            timeline.innerHTML += buildCard(item);
                        });
                        return;
                    }

                    // Otros días normal
                    if (!data.length) {
                        timeline.innerHTML = `<div class="text-center opacity-75 mt-4">Sin citas</div>`;
                        return;
                    }

                    data.forEach(item => {
                        timeline.innerHTML += buildCard(item);
                    });
                })
                .catch(err => console.error('Error agenda:', err));
        }

        // UI Card
        function buildCard(item) {
            return `
                <div class="timeline-item">
                    <div class="timeline-marker">
                        <div class="linea-con-circulo">
                            <div class="circulo"></div>
                            <div class="linea"></div>
                        </div>
                    </div>
                    <div class="timeline-card">
                        <p>
                            <i class="orden-compra">${formatOrdenCompra(item.orden_compra)}</i>
                            <i class="material-icons">arrow_right</i>
                            <span class="status-badge ${getStatusClass(item?.estado)}">${item?.estado ?? 'cancelada'}</span>
                            <i class="material-icons">arrow_right</i>
                            <span class="evento-badge ${getEventClass(item?.tipo_evento)}">${item?.tipo_evento ?? 'Programada'}</span>
                        </p>

                        <p><i class="material-icons">store</i><strong>${escapeHTML(item.proveedor_nombre || 'Paqueteria Express / Cita no programada')}</strong></p>

                        <p>
                            <i class="material-icons">local_shipping</i> ${escapeHTML(item.transporte_nombre || 'No especificado')}
                            &nbsp;&nbsp;
                            <i class="material-icons">place</i> ${escapeHTML(item.Lugar || 'Paqueteria Express')}
                            &nbsp;&nbsp;
                            <i class="material-icons">access_time</i> ${formatTime12h(item.hora) || 'No especificado'}
                        </p>
                    </div>
                </div>
            `;
        }

        // Refresh / recarga por cambio de día
        function refrescarAgendaActual() {
            for (let i = 0; i < fechasVisibles.length; i++) {
                loadAgendaDataForDate(fechasVisibles[i], i);
            }
        }

        function checkDayChangeAndReload() {
            const nowKey = toKeyLocal(new Date());
            if (nowKey !== lastDateKey) location.reload();
            lastDateKey = nowKey;
        }

        // Utilidades existentes
        function formatOrdenCompra(data) {
            try {
                if (data == null) return '<span class="badge-orden">Otros</span>';
                let ordenes = data;
                if (typeof data === 'string') {
                    try { ordenes = JSON.parse(data); }
                    catch { ordenes = data.split(','); }
                }
                if (!Array.isArray(ordenes)) ordenes = [ordenes];

                ordenes = ordenes.filter(Boolean).map(o => String(o).trim()).filter(o => o.length);
                if (!ordenes.length) return '<span class="badge-orden">Otros</span>';
                return ordenes.map(o => `<span class="badge-orden">${escapeHTML(o)}</span>`).join('');
            } catch {
                return '<span class="badge-orden">Otros</span>';
            }
        }

        function getStatusClass(status) {
            const s = String(status ?? '').trim().toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
            switch (s) {
                case 'confirmada': return 'status-confirmada';
                case 'pendiente': return 'status-pendiente';
                case 'asistio': return 'status-asistio';
                case 'no asistio': return 'status-noasistio';
                case 'en proceso': return 'status-proceso';
                case 'recepcion tardia': return 'status-tardia';
                case 'cancelada por proveedor': return 'status-cancelada-sp';
                case 'no programado': return 'status-noprogramado';
                case 'asistio fuera de horario': return 'status-timeout';
                case 'cancelada': return 'status-cancelada';
                default: return 'status-cancelada';
            }
        }

        function getEventClass(evento) {
            const e = String(evento ?? '').trim().toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
            switch (e) {
                case 'programada': return 'evento-programada';
                case 'no programada': return 'evento-no-programada';
                case 'apartada': return 'evento-apartada';
                case 'paquetería express':
                case 'paqueteria express': return 'evento-express';
                default: return 'evento-programada';
            }
        }

        function formatTime12h(hora = '') {
            if (!hora) return '';
            const m = String(hora).match(/(\d{1,2}):(\d{2})/);
            if (!m) return '';
            let h = parseInt(m[1], 10);
            const minutes = m[2];
            if (Number.isNaN(h)) return '';
            const ampm = h >= 12 ? 'PM' : 'AM';
            h = h % 12 || 12;
            return `${h}:${String(minutes).padStart(2,'0')} ${ampm}`;
        }

        function escapeHTML(text = '') {
            return String(text)
                .replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;").replace(/'/g, "&#039;");
        }

        // Init
        document.addEventListener('DOMContentLoaded', () => {
            lastDateKey = toKeyLocal(new Date());
            fechasVisibles = getNextBusinessDays(new Date(), 3);

            renderHeadersAndLoad();

            // refresh general
            setInterval(refrescarAgendaActual, AUTO_REFRESH_TIME);

            // refresh HOY para ir descartando pasadas
            setInterval(() => {
                if (!fechasVisibles?.length) return;
                loadAgendaDataForDate(fechasVisibles[0], 0);
            }, HOY_REFRESH_MS);

            // cambio de día
            setInterval(checkDayChangeAndReload, DAY_CHANGE_CHECK_MS);
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.monitor', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\Proveedores\resources\views/index_monitor.blade.php ENDPATH**/ ?>