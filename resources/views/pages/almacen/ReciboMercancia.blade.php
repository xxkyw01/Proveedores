@extends('layouts.movil')
@section('title', 'Recibo de mercancía')
@section('content')
    @include('includes.scripts.SweetAlert2@11')
    @include('includes.scripts.googleapis')
    @include('includes.scripts.bootstrap')
    @include('includes.scripts.SweetAlert2')
    @include('includes.scripts.Datatables')
    @include('includes.scripts.fontAwesome')
    @include('includes.scripts.flatpickr')
    @include('includes.scripts.bootstrap')

    <x-sidebar />
    <link rel="stylesheet" href="{{ asset('assets/css/rol/almacen/grpo.css') }}">

    @php
        $rolId = session('Usuario.IdRol');
        $sucursalIdUsuario = session('Usuario.SucursalID');
    @endphp

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="container-fluid con-sidebar">
        <div class="row ">
            <div class="detallesCompras col-lg-4 col-md-5 left-panel"
                style="max-height: 100vh; overflow-y: auto; border-right: 1px solid #ddd; padding: 20px;">
                <div class="mb-4 d-flex align-items-center justify-content-between">
                    <h4 class="fw-bold mb-0">Órdenes de Compra</h4>

                    @if (!in_array($rolId, [2]))
                        <form method="GET" action="{{ route('almacen.reciboMercancia') }}" class="mb-0">
                            <select name="sucursal_id" id="sucursal_id" class="form-select form-select-sm"
                                onchange="this.form.submit()">
                                @foreach ($sucursales as $s)
                                    <option value="{{ $s->id }}"
                                        {{ request('sucursal_id') == $s->id ? 'selected' : '' }}>{{ $s->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    @else
                        <input type="hidden" id="sucursal_id" value="{{ $sucursalIdUsuario }}">
                    @endif
                </div>

                <div class="mb-3">
                    <input type="text" id="buscadorOrdenes" class="form-control form-control-sm"
                        placeholder="Buscar por OC o proveedor..." onkeyup="filtrarOrdenes(this.value)">
                </div>

                <div id="ordenesContainer" class="d-flex flex-column gap-2">
                    <div class="text-muted text-center">Cargando...</div>
                </div>
            </div>

            <div class="col-lg-8 col-md-7 right-panel" style="max-height: 100vh; overflow-y: auto; ">
                <div id="detallesDesktop" class="detallesContainer">
                    <div class="text-center text-muted mt-5">
                        <p>Selecciona una orden de compra para ver detalles</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="detallesModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalles</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body" id="modalBodyDetallesRecepcion">
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalAgregarOC" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Agregar Ordenes de Compra</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div id="listaOCsDisponibles">
                        <div class="text-muted">Cargando...</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>


    <script>
        const SERIES_BY_SUC = {
            "1": 156,
            "4": 157
        };
        window.SERIES_BY_SUC = SERIES_BY_SUC;

        const SESSION_USER = @json([
            'id' => session('Usuario.IdUsuario') ?? (session('Usuario.id') ?? null),
            'username' => session('Usuario.Usuario') ?? (session('Usuario.user') ?? (session('Usuario.nombre') ?? 'N/A')),
        ]);

        let ordenesData = [];
        let currentOrder = null;
        let PO_HEADER = {
            DocNum: 0,
            CardCode: '',
            DocEntry: 0
        };
        let PO_LINE_BY_ITEM = new Map();

        const csrfToken = document.querySelector("meta[name='csrf-token']").getAttribute('content');
        const ROL_ID = {{ (int) $rolId }};
        const ES_ADMIN = [5].includes(ROL_ID);
        const ES_ALMACEN = [2].includes(ROL_ID);

        function ymdLocal(d) {
            d = new Date(d);
            const y = d.getFullYear();
            const m = String(d.getMonth() + 1).padStart(2, '0');
            const day = String(d.getDate()).padStart(2, '0');
            return `${y}-${m}-${day}`;
        }

        function sumarDias(fecha, dias) {
            const result = new Date(fecha);
            result.setDate(result.getDate() + dias);
            return ymdLocal(result);
        }

        function escapeHTML(t = '') {
            return String(t)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function fmt(n) {
            try {
                return new Intl.NumberFormat('es-MX').format(n);
            } catch {
                return n;
            }
        }

        function parseOCList(ocRaw) {
            return String(ocRaw || '')
                .split(',')
                .map(s => s.trim())
                .filter(Boolean)
                .map(s => (s.match(/\d+/)?.[0] || ''))
                .filter(Boolean);
        }

        document.addEventListener('DOMContentLoaded', () => {
            loadOrdenes();
            bindTablaEvents();
        });

        function loadOrdenes() {
            const sucursalId = document.getElementById('sucursal_id')?.value || '{{ $sucursal_id ?? 0 }}';
            const hoy = ymdLocal(new Date());
            const mañana = sumarDias(new Date(), 1);
            const pasadoMañana = sumarDias(new Date(), 2);

            const fechas = [hoy, mañana, pasadoMañana];
            const promises = fechas.map(fecha =>
                fetch(
                    `/almacen/agenda/data?fecha=${encodeURIComponent(fecha)}&sucursal_id=${encodeURIComponent(sucursalId)}`
                )
                .then(r => r.ok ? r.json() : Promise.reject(new Error('HTTP ' + r.status)))
                .catch(err => {
                    console.error(`Error cargando ${fecha}:`, err);
                    return [];
                })
            );

            Promise.all(promises)
                .then(results => {
                    ordenesData = results.flat().filter(o => o.orden_compra || o.id);
                    renderOrdenes();
                })
                .catch(err => {
                    document.getElementById('ordenesContainer').innerHTML =
                        `<div class="text-danger">Error cargando órdenes: ${escapeHTML(String(err.message||err))}</div>`;
                    console.error(err);
                });
        }

        function isMobileView() {
            return window.matchMedia('(max-width: 668px)').matches;
        }

        function openDetallesModal(html) {
            const body = document.getElementById('modalBodyDetallesRecepcion');
            const modalEl = document.getElementById('detallesModal');

            if (!body || !modalEl) {
                console.error('Falta modal de detalles: #detallesModal o #modalBodyDetallesRecepcion');
                const desk = document.getElementById('detallesDesktop');
                if (desk) desk.innerHTML = html;
                return;
            }

            body.innerHTML = html;
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
        }

        function formatHoraAMPM(hora = '') {
            if (!hora) return '';
            const [h = '0', m = '00'] = hora.split(':');
            let hours = parseInt(h, 10);
            if (isNaN(hours)) return '';
            const ampm = hours >= 12 ? 'PM' : 'AM';
            hours = hours % 12 || 12;
            return `${hours}:${m.padStart(2, '0')} ${ampm}`;
        }

        function renderOrdenes() {
            const c = document.getElementById('ordenesContainer');
            const ordenesValidas = ordenesData.filter(o =>
                (o.orden_compra || o.id) && o.proveedor_nombre
            );

            if (!ordenesValidas.length) {
                c.innerHTML = '<div class="text-muted text-center">No hay órdenes válidas</div>';
                return;
            }
            c.innerHTML = ordenesValidas.map(o => {
                const titulo = escapeHTML(String(o.orden_compra || o.id));
                const prov = escapeHTML(o.proveedor_nombre || 'N/A');
                const fecha = escapeHTML(o.fecha || 'S/F');
                const hora = formatHoraAMPM(o.hora) || 'S/H';

                return `<button type="button" class="orden-card" onclick="selectOrden(${o.id}, this)" 
                        data-oc="${titulo}" data-proveedor="${prov.toLowerCase()}" style="width:100%;text-align:left;">
                        <div style="display:flex;align-items:center;gap:12px;width:100%;">
                            <div style="flex:0 0 60%;min-width:0;overflow:hidden;text-overflow:ellipsis;">
                                <div class="titulo" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">OC ${titulo}</div>
                                <div class="prov" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;color:#666;">${prov}</div>
                            </div>
                            <div style="flex:0 0 30%;text-align:right;min-width:0;padding-left:8px;padding-right:8px;">
                                <div class="text-muted small" style="font-weight:700;font-size:0.95rem;">${hora}</div>
                                <div class="text-muted small" style="font-size:0.8rem;">${fecha}</div>
                            </div>
                            <div style="flex:0 0 10%;display:flex;padding-left:6px;padding-right:6px;height:100%;">
                                <span role="button" aria-label="Editar cita" class="btn-edit-cita" onclick="event.stopPropagation(); abrirEditarCita(${o.id}, this)" title="Editar cita" style="display:flex;align-items:center;justify-content:center;width:36px;height:36px;border-radius:50%;background:#fff3e6;border:1px solid #ffd6b0;color:#ee7826;cursor:pointer;padding:0;">
                                    <i class="fa fa-pen" aria-hidden="true" style="font-size:14px;line-height:1;margin:0;"></i>
                                </span>
                            </div>
                        </div>
                        </button>`;
            }).join('');
        }

        async function abrirEditarCita(reservacionId, btnEl) {
            try {
                const orden = ordenesData.find(x => Number(x.id) === Number(reservacionId));
                if (!orden) return Swal.fire('Error', 'Orden no encontrada', 'error');

                const currentEstado = orden.estado || '';
                const currentComentario = orden.comentario || orden.commit_afterrecep || '';
                const options = {
                    'Asistió': 'Asistió',
                    'Confirmada': 'Confirmada',
                    'No asistió': 'No asistió',
                    'En proceso': 'En proceso',
                    'Recepción tardía': 'Recepción tardía',
                    'No Programado': 'No Programado',
                    'Asistio Fuera de Horario': 'Asistio Fuera de Horario'
                };

                const html = `
                    <div style="text-align:left;">
                        <div style="margin-bottom:8px;">
                            <label class="form-label">Estado</label>
                            <select id="swal-estado" class="form-select">
                        ${Object.keys(options).map(k=>`<option value="${k}">${options[k]}</option>`).join('')}
                    </select>
                        </div>

                        <div style="margin-bottom:8px;">
                            <label class="form-label">Comentario</label>
                            <textarea id="swal_comentario" class="form-control" rows="3" placeholder="Comentario...">${escapeHTML(currentComentario)}</textarea>
                        </div>

                        <div style="margin-bottom:8px;">
                            <label class="form-label">Adjuntar archivo (evidencia)</label>
                            <input type="file" id="swal_evidencia" class="form-control" />
                        </div>
                    </div>`;

                const result = await Swal.fire({
                    title: `Editar cita #${orden.id}`,
                    html,
                    showCancelButton: true,
                    confirmButtonText: 'Actualizar',
                    didOpen: () => {
                        const sel = document.getElementById('swal_estado');
                        if (sel) sel.value = currentEstado || '';
                    },
                    preConfirm: async () => {
                        const nuevoEstado = document.getElementById('swal_estado')?.value || '';
                        const comentario = document.getElementById('swal_comentario')?.value || '';
                        const fileInput = document.getElementById('swal_evidencia');

                        Swal.showLoading();

                        const payload = {
                            estado: nuevoEstado,
                            comentario: comentario
                        };

                        const resp = await fetch(`/almacen/reservacion/${reservacionId}/actualizar`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify(payload)
                        });

                        const text = await resp.text();
                        let data;
                        try {
                            data = JSON.parse(text);
                        } catch {
                            data = {
                                ok: resp.ok,
                                msg: text
                            };
                        }

                        if (!resp.ok || !data.ok) {
                            throw new Error(data.msg || `HTTP ${resp.status}`);
                        }

                        if (fileInput && fileInput.files && fileInput.files.length) {
                            const fd = new FormData();
                            fd.append('evidencia', fileInput.files[0]);
                            fd.append('_token', csrfToken);

                            const up = await fetch(`/almacen/reservacion/${reservacionId}/evidencia`, {
                                method: 'POST',
                                body: fd
                            });

                            const t2 = await up.text();
                            let d2;
                            try {
                                d2 = JSON.parse(t2);
                            } catch {
                                d2 = {
                                    ok: up.ok,
                                    msg: t2
                                };
                            }
                            if (!up.ok || !d2.ok) {
                                throw new Error(d2.msg || `HTTP ${up.status}`);
                            }
                        }
                        return {
                            estado: nuevoEstado,
                            comentario
                        };
                    }
                });

                if (result && result.isConfirmed && result.value) {
                    const idx = ordenesData.findIndex(x => Number(x.id) === Number(reservacionId));
                    if (idx >= 0) {
                        ordenesData[idx].estado = result.value.estado;
                        ordenesData[idx].tiene_comentario = !!String(result.value.comentario).trim();
                        ordenesData[idx].commit_afterrecep = result.value.comentario;
                    }
                    renderOrdenes();
                    Swal.fire({
                        icon: 'success',
                        title: 'Actualizado',
                        timer: 900,
                        showConfirmButton: false
                    });
                }

            } catch (e) {
                console.error('abrirEditarCita', e);
                Swal.fire('Error', String(e.message || e), 'error');
            }
        }

        function filtrarOrdenes(valor) {
            const filtro = valor.toLowerCase();
            const botones = document.querySelectorAll('.orden-card');
            botones.forEach(btn => {
                const oc = btn.dataset.oc?.toLowerCase() || '';
                const proveedor = btn.dataset.proveedor?.toLowerCase() || '';
                const visible = oc.includes(filtro) || proveedor.includes(filtro);
                btn.style.display = visible ? '' : 'none';
            });
        }

        function selectOrden(id, btnEl) {
            document.querySelectorAll('.orden-card').forEach(b => b.classList.remove('active'));
            if (btnEl) btnEl.classList.add('active');

            currentOrder = ordenesData.find(x => Number(x.id) === Number(id)) || null;
            if (!currentOrder) {
                const msg = `<div class="text-danger">Orden no encontrada</div>`;
                if (isMobileView()) openDetallesModal(msg);
                else document.getElementById('detallesDesktop').innerHTML = msg;
                return;
            }
            renderDetalles(currentOrder);
        }

        function renderDetalles(orden) {
            const ocNumDisplay = escapeHTML(String(orden.orden_compra || orden.id));
            const ocLookup = String(orden.orden_compra || orden.id);
            const ocList = parseOCList(ocLookup);

            const html = `
            ${ES_ADMIN || ES_ALMACEN ? `
                                <div style="display:flex;gap:8px;margin-top:10px;flex-wrap:wrap;">
                                <button type="button" class="btn btn-outline-orange btn-sm" onclick="abrirModalAgregarOC()">
                                + Agregar OC
                                </button>
                                <small class="text-muted" style="align-self:center;">Adjunta otra OC a esta cita</small>
                                </div>` 
            : ''}

            <div class="detalle-header">
            <h5 style="margin:0;">ORDEN DE COMPRA ${ocNumDisplay}</h5>
            <div style="margin-top:8px;">
                <strong>Proveedor:</strong> ${escapeHTML(orden.proveedor_nombre || 'N/A')}<br>
                <strong>Fecha cita:</strong> ${escapeHTML(orden.fecha || 'N/A')}<br>
                <strong>Hora:</strong> ${escapeHTML(formatHoraAMPM(orden.hora) || 'N/A')}<br>
                <strong>Transporte:</strong> ${escapeHTML(orden.transporte_nombre || 'N/A')}<br>
            </div>
            </div>


            <div id="articulosList">
            <div class="text-muted">Cargando artículos</div>
            </div>
            <div class="comentario-area">
            <label for="numeroReferencia">Folio de la Factura del Proveedor</label>
            <input type="text" id="numeroReferencia" style="width:100%;padding:8px;border:1px solid #ddd;border-radius:6px;margin-bottom:12px;">
            </div>

            <div class="comentario-area">
            <label for="comentarios">Comentarios</label>
            <textarea id="comentarios" rows="3"
                style="width:100%;padding:8px;border:1px solid #ddd;border-radius:6px;"
                placeholder="Observaciones..."></textarea>
            </div>

            <div class="botones-action">
                ${ES_ADMIN ? `
                                    <button class="btn-cancel" type="button" onclick="previsualizarGRPO()">Previsualizar</button>
                                    ` : ''                
                }
            <button class="btn-confirm" type="button" onclick="confirmarSeleccion()">Confirmar</button>
            <button class="btn-cancel" type="button" data-bs-dismiss="modal" onclick="cerrarDetalles()">Cerrar</button>
            </div>
        `;
            if (isMobileView()) {
                openDetallesModal(html);
            } else {
                document.getElementById('detallesDesktop').innerHTML = html;
            }
            cargarArticulosPorOCs(ocList);
        }

        function ensureScanInput() {
            let inp = document.getElementById('scanInputHidden');
            if (!inp) {
                inp = document.createElement('input');
                inp.id = 'scanInputHidden';
                inp.type = 'text';
                inp.autocomplete = 'off';
                inp.style.position = 'fixed';
                inp.style.left = '-9999px';
                inp.style.top = '0';
                document.body.appendChild(inp);

                inp.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        const raw = (inp.value || '').trim();
                        inp.value = '';
                        if (raw) onScanResult(raw);
                    }
                });
            }
            return inp;
        }

        function parseScannedCode(raw) {
            const s = String(raw || '').trim();
            try {
                if (s.startsWith('http')) {
                    const u = new URL(s);
                    const item = u.searchParams.get('item') || u.searchParams.get('ItemCode');
                    if (item) return item.trim();
                }
            } catch {}

            try {
                if (s.startsWith('{') && s.endsWith('}')) {
                    const o = JSON.parse(s);
                    const item = o.ItemCode || o.item || o.codigo || o.code;
                    if (item) return String(item).trim();
                }
            } catch {}

            const m = s.match(/(ItemCode|ITEM|COD|CODIGO)\s*[:=]\s*([A-Za-z0-9\-_\.]+)/i);
            if (m?.[2]) return m[2].trim();

            if (s.includes('|')) return s.split('|')[0].trim();

            return s;
        }

        function findRowByScannedCode(code) {
            const wanted = normCode(code);

            const rows = getSelectedRowsFromTable();
            for (const tr of rows) {
                const inp = tr.querySelector('input.inp-recibir');
                if (!inp) continue;

                const itemcode = normCode(inp.dataset.itemcode || '');
                if (itemcode && itemcode === wanted) return tr;

                const visibleCode = normCode(tr.children[1]?.querySelector('div:first-child')?.textContent?.trim() || '');
                if (visibleCode && visibleCode === wanted) return tr;
            }
            return null;
        }

        function applyScanToRow(tr, mode = 'add1') {
            const chk = tr.querySelector('input.chk-art');
            const inp = tr.querySelector('input.inp-recibir');
            if (!chk || !inp) return;
            if (!chk.checked) {
                chk.checked = true;
                onChkToggle(chk);
            }

            const pendiente = Number(inp.dataset.pendiente || 0);
            const current = Number(inp.value || 0);

            if (mode === 'fillPending') {
                inp.value = pendiente ? String(Math.floor(pendiente)) : '1';
            } else {
                const next = Math.min(pendiente || 999999, current + 1);
                inp.value = String(next);
            }

            tr.style.outline = '2px solid #22c55e';
            tr.style.outlineOffset = '-2px';
            setTimeout(() => {
                tr.style.outline = '';
                tr.style.outlineOffset = '';
            }, 900);

            try {
                tr.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            } catch {}
        }

        function onScanResult(raw) {
            const code = parseScannedCode(raw);
            const tr = findRowByScannedCode(code);

            if (!tr) {
                Swal.fire('No encontrado', `No pude localizar el artículo: ${escapeHTML(code)}`, 'warning');
                return;
            }

            applyScanToRow(tr, 'add1');
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: `OK: ${code}`,
                showConfirmButton: false,
                timer: 900
            });
        }

        function openScanner(manual = false) {
            const table = document.querySelector('#articulosList table#tablaArticulos, #articulosList table[data-oc]');
            if (!table) {
                return Swal.fire('Primero carga artículos', 'Selecciona una OC y espera a que cargue la tabla.', 'info');
            }

            if (manual) {
                return Swal.fire({
                    title: 'Captura código',
                    input: 'text',
                    inputPlaceholder: 'Ej: ABC123 (o pega QR)',
                    showCancelButton: true,
                    confirmButtonText: 'Procesar',
                    preConfirm: (val) => {
                        const raw = (val || '').trim();
                        if (!raw) Swal.showValidationMessage('Escribe un código');
                        return raw;
                    }
                }).then(r => {
                    if (r.isConfirmed) onScanResult(r.value);
                });
            }

            const inp = ensureScanInput();
            inp.focus();

            Swal.fire({
                title: 'Escanear',
                html: `
                <div style="text-align:left;">
                    <p style="margin:0 0 8px;">Listo. Escanea con tu lector o pega el código y presiona Enter.</p>
                    <div class="text-muted" style="font-size:12px;">
                    Tip: si tu lector USB actúa como teclado, esto funciona sin cámara 
                    </div>
                </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Cambiar a captura manual',
                cancelButtonText: 'Cerrar',
                didOpen: () => inp.focus()
            }).then(r => {
                if (r.isConfirmed) openScanner(true);
                try {
                    inp.blur();
                } catch {}
            });
        }

        function getSelectedRowsFromTable() {
            if (window.jQuery && $.fn.DataTable && $.fn.DataTable.isDataTable('#tablaArticulos')) {
                const dt = $('#tablaArticulos').DataTable();
                return dt.rows({
                    page: 'all'
                }).nodes().toArray();
            }

            const table = document.querySelector('#articulosList table#tablaArticulos, #articulosList table[data-oc]');
            if (!table) return [];
            return Array.from(table.querySelectorAll('tbody tr'));
        }

        function buildGrpoPayloadFromUI() {
            const table = document.querySelector('#articulosList table#tablaArticulos, #articulosList table[data-oc]');
            if (!table) throw new Error('No hay artículos cargados.');

            const filas = getSelectedRowsFromTable();
            const sucursalId = Number(document.getElementById('sucursal_id')?.value || 0);

            const serieGRPO = Number(SERIES_BY_SUC[String(sucursalId)] || 157); // default ZC

            /* const serieGRPO = Number(SERIES_BY_SUC[String(sucursalId)] || 0);
            if (!serieGRPO) throw new Error(`Sucursal sin serie GRPO configurada: ${sucursalId}`); */

            const seleccion = [];
            filas.forEach(tr => {
                const chk = tr.querySelector('input.chk-art');
                const inp = tr.querySelector('input.inp-recibir');
                if (!chk || !inp) return;

                const qty = inp.value === '' ? 0 : Number(inp.value);
                if (chk.checked && qty > 0) seleccion.push({
                    tr,
                    inp,
                    qty
                });
            });

            if (!seleccion.length) throw new Error('No hay selección. Marca líneas y captura cantidades.');

            const numeroReferencia = (document.getElementById('numeroReferencia')?.value || '').trim();
            const comment = document.getElementById('comentarios')?.value || '';
            const username = SESSION_USER?.username || 'N/A';
            const grupos = new Map();
            const printableByDoc = new Map();

            for (const s of seleccion) {
                const inp = s.inp;
                const docNumL = Number(inp.dataset.docnum || 0);
                const docEntryL = Number(inp.dataset.docentry || 0);
                const cardCodeL = String(inp.dataset.cardcode || '');
                const baseLine = Number(inp.dataset.baseline || -1);
                const itemCode = String(inp.dataset.itemcode || '');
                const whs = String(inp.dataset.whs || '');
                const ocL = String(inp.dataset.oc || docNumL || '');
                const serie = Number(SERIES_BY_SUC[String(sucursalId)] || 157); // default ZC
                if (!serie) throw new Error(`Sucursal sin serie GRPO configurada: ${sucursalId}`);

                if (!docEntryL || !docNumL || !cardCodeL) {
                    throw new Error('Hay líneas sin DocEntry/DocNum/CardCode (no mapeadas a SAP).');
                }
                if (baseLine < 0 || !itemCode || !whs) {
                    throw new Error('Hay líneas sin BaseLine/ItemCode/WarehouseCode.');
                }

                if (!grupos.has(docEntryL)) {
                    grupos.set(docEntryL, {
                        docNum: docNumL,
                        docEntry: docEntryL,
                        cardCode: cardCodeL,
                        numAtCard: numeroReferencia,
                        sucursal_id: sucursalId,
                        //Series: serieGRPO,
                        Series: serie,
                        user_comment: `${comment} [OC ${docNumL}]`,
                        oc_num: String(ocL),
                        captured_by: username,
                        lines: []
                    });
                }

                grupos.get(docEntryL).lines.push({
                    BaseLine: baseLine,
                    ItemCode: itemCode,
                    Quantity: s.qty,
                    WarehouseCode: whs
                });

                try {
                    const tr = s.tr;
                    const code = tr.children[1]?.querySelector('div:first-child')?.textContent?.trim() || '';
                    const desc = tr.children[1]?.querySelector('div:nth-child(2)')?.textContent?.trim() || '';
                    const umVal = s.inp?.dataset.um || '';
                    if (!printableByDoc.has(docEntryL)) printableByDoc.set(docEntryL, []);
                    printableByDoc.get(docEntryL).push({
                        code,
                        desc,
                        qty: s.qty,
                        um: umVal
                    });
                } catch (e) {
                    console.warn('No pude construir línea imprimible', e);
                }
            }

            return Array.from(grupos.values());
        }

        async function previsualizarGRPO() {
            try {
                const payloads = buildGrpoPayloadFromUI();
                const jsonPretty = JSON.stringify(payloads, null, 2);
                const html = `
                <div style="text-align:left;">
                    <div style="display:flex;gap:8px;justify-content:space-between;align-items:center;margin-bottom:8px;">
                    <div><b>Recepciones a generar:</b> ${payloads.length}</div>
                    <button type="button" id="btnCopyPayload" class="swal2-confirm swal2-styled"
                        style="margin:0;padding:6px 10px;font-size:12px;">
                        Copiar JSON
                    </button>
                    </div>
                    <pre style="max-height:55vh;overflow:auto;background:#0b0f14;color:#e6edf3;padding:12px;border-radius:10px;border:1px solid #1f2937;">
                        ${escapeHTML(jsonPretty)}
                            </pre>
                        </div>
                    `;

                await Swal.fire({
                    title: 'Payload(s) GRPO',
                    theme: 'auto',
                    html,
                    width: 900,
                    confirmButtonText: 'OK',
                    didOpen: () => {
                        const btn = document.getElementById('btnCopyPayload');
                        if (btn) {
                            btn.addEventListener('click', async () => {
                                try {
                                    await navigator.clipboard.writeText(jsonPretty);
                                    Swal.showValidationMessage('Copiado al portapapeles');
                                    setTimeout(() => Swal.resetValidationMessage(), 800);
                                } catch {
                                    Swal.showValidationMessage(
                                        'No pude copiar (bloqueo del navegador).');
                                }
                            });
                        }
                    }
                });

                console.groupCollapsed('[PREVIEW PAYLOADS GRPO] Multi-OC');
                console.log(payloads);
                payloads.forEach(p => {
                    console.log('OC:', p.docNum, 'DocEntry:', p.docEntry);
                    console.table(p.lines);
                });
                console.groupEnd();

            } catch (e) {
                Swal.fire('No se puede previsualizar', String(e.message || e), 'warning');
            }
        }

        async function guardarOCEnReservacion(ocNum) {
            const reservacionId = Number(currentOrder?.id || 0);
            if (!reservacionId) throw new Error('No hay reservación seleccionada');
            const resp = await fetchJsonOrThrow(`/almacen/reservacion/${reservacionId}/agregar-oc`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    orden_compra: String(ocNum),
                    folio_factura: ''
                })
            });

            if (!resp.ok) throw new Error(resp.msg || 'No se pudo guardar la OC');
            return resp;
        }

        async function cargarArticulosPorOCs(ocList) {
            window.PO_HEADER = {
                DocNum: 0,
                CardCode: '',
                DocEntry: 0
            };
            window.PO_LINE_BY_ITEM = new Map();

            const cont = document.getElementById('articulosList');
            if (!cont) return;

            if (!Array.isArray(ocList) || !ocList.length) {
                cont.innerHTML = `
                    <div style="display:flex;gap:12px;align-items:center;padding:14px;border-radius:10px;background:#fff7ed;border:1px solid #ffedde;color:#92400e;box-shadow:0 6px 18px rgba(16,24,40,0.03);">
                        <div style="width:44px;height:44px;display:flex;align-items:center;justify-content:center;background:#fff3e0;border-radius:8px;border:1px solid #ffd8a8;">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path d="M11 7h2v6h-2zM11 15h2v2h-2z" fill="#92400e"/>
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2z" stroke="#ffd6a5" stroke-width="1.2"/>
                            </svg>
                        </div>
                        <div style="flex:1;min-width:0;">
                            <div style="font-weight:700;color:#92400e;">OC inválida</div>
                            <div style="font-size:0.95rem;color:#7c7c7c;margin-top:4px;">No se proporcionó ninguna orden de compra válida. Verifica la OC y vuelve a intentarlo. || O agrega una Orden de compra existente</div>
                        </div>
                    </div>
                `;
                return;
            }

            cont.innerHTML = `
                <div style="display:flex;align-items:center;gap:12px;padding:12px;border-radius:10px;background:#fff;border:1px solid #eee;box-shadow:0 6px 18px rgba(16,24,40,0.04);">
                    <div style="width:44px;height:44px;display:flex;align-items:center;justify-content:center;">
                        <svg width="34" height="34" viewBox="0 0 50 50" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <circle cx="25" cy="25" r="20" fill="none" stroke="#f0f0f0" stroke-width="4"></circle>
                            <path fill="#ee7826" d="M25 5a20 20 0 0 1 0 40" transform="rotate(0 25 25)">
                                <animateTransform attributeType="xml" attributeName="transform" type="rotate" from="0 25 25" to="360 25 25" dur="1s" repeatCount="indefinite" />
                            </path>
                        </svg>
                    </div>
                    <div style="flex:1;min-width:0;">
                        <div style="font-weight:700;color:#111;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">Cargando artículos</div>
                        <div style="font-size:0.9rem;color:#6b7280;margin-top:4px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">Orden(es) de Compra(s): ${escapeHTML(ocList.join(', '))}</div>
                    </div>
                    <div style="font-size:0.85rem;color:#6b7280;white-space:nowrap;">Por favor espere...</div>
                </div>
            `;

            const _cargarStart = Date.now();

            function setResult(html, cb) {
                const elapsed = Date.now() - _cargarStart;
                const additionalAfterFetch = 3000;
                const wait = Math.max(0, 5000 - elapsed) + additionalAfterFetch;
                setTimeout(() => {
                    cont.innerHTML = html;
                    try {
                        if (typeof cb === 'function') cb();
                    } catch (e) {
                        console.error('callback error', e);
                    }
                }, wait);
            }

            try {
                const artPromises = ocList.map(oc =>
                    fetchJsonOrThrow(`/almacen/agenda/articulos/${encodeURIComponent(oc)}`)
                    .then(rows => Array.isArray(rows) ? rows.map(r => ({
                        ...r,
                        __oc: oc
                    })) : [])
                    .catch(err => {
                        console.error('articulos_error_oc', oc, err);
                        return [];
                    })
                );

                const results = await Promise.all(artPromises);
                const rowsAll = results.flat();

                if (!rowsAll.length) {
                    setResult(`
                            <div style="display:flex;gap:12px;align-items:center;padding:14px;border-radius:10px;background:#f0f9ff;border:1px solid #cfeefe;color:#0f172a;box-shadow:0 6px 18px rgba(2,6,23,0.04);">
                                <div style="width:44px;height:44px;display:flex;align-items:center;justify-content:center;background:#e6f8ff;border-radius:8px;border:1px solid #bfefff;">
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path d="M12 2a10 10 0 100 20 10 10 0 000-20zm0 6a1.5 1.5 0 110 3 1.5 1.5 0 010-3zM11 11h2v5h-2v-5z" fill="#0369a1"/>
                                    </svg>
                                </div>
                                <div style="flex:1;min-width:0;">
                                    <div style="font-weight:700;color:#0369a1;">No se encontraron artículos</div>
                                    <div style="font-size:0.95rem;color:#475569;margin-top:6px;">No se localizaron líneas asociadas a las OC proporcionadas. Verifica las OCs o intenta con otra OC.</div>
                                </div>
                            </div>
                        `);
                    return;
                }

                let table = `
                <div style="display:flex;gap:8px;margin-bottom:12px;align-items:center;">
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="seleccionarTodoArt()">Seleccionar todo</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="limpiarSeleccionArt()">Limpiar</button>
                </div>
                <table id="tablaArticulos" class="tabla-articulos display d-none" style="width:100%;table-layout:auto;">
                    <thead class="d-none d-md-table-header-group">
                    <tr>
                        <th style="width:48px;text-align:center;">✓</th>
                        <th style="text-align:left;">Código - Artículo</th>
                        <th style="width:1%;white-space:nowrap;text-align:center;">Cantidad + UM</th>
                        <th style="width:1%;white-space:nowrap;text-align:center;">Recepción</th>
                        <th style="width:90px;text-align:center;">OC</th>
                    </tr>
                    </thead>
                    <tbody>
                `;

                rowsAll.forEach((a, i) => {
                    const cant = isFinite(parseFloat(a.CantidadPendiente)) ? Number(a.CantidadPendiente) : 0;
                    const code = escapeHTML(a.CodigoArticulo || '');
                    const desc = escapeHTML(a.DescripcionArticulo || '');
                    const um = escapeHTML(a.UnidadMedida || a.Unidad || a.UM || a.U_Medida || a.Unidad_Medida ||
                        '');
                    const oc = escapeHTML(String(a.__oc || ''));
                    const cantDisplay = cant % 1 === 0 ? cant : cant.toFixed(2);

                    table += `
                    <tr>
                    <td style="text-align:center;padding:12px;width:48px;">
                        <input type="checkbox" class="chk-art" aria-label="Seleccionar artículo ${code}" style="width:24px;height:24px;cursor:pointer;">
                    </td>
                    <td style="padding:8px;">
                        <div style="font-weight:700;">${code}</div>
                        <div style="font-size:0.9em;color:#666;">${desc}</div>
                    </td>
                    <td style="text-align:center;padding:8px;white-space:nowrap;">
                        <div style="font-weight:700;">${cantDisplay}</div>
                        <div class="text-muted small" style="font-size:0.8rem;">${um || '-'}</div>
                    </td>
                    <td style="text-align:center;white-space:nowrap;">
                        <input type="number" min="0" step="1"
                        class="inp-recibir"
                        data-pendiente="${cant}"
                        data-oc="${oc}"
                        data-um="${um}"
                        placeholder="0" style="width:90px;padding:6px;" />
                    </td>
                    <td style="text-align:center;font-size:0.85em;color:#999;">${oc}</td>
                    </tr>
                `;
                });
                table += `</tbody></table>`;
                setResult(table, async () => {

                    cont.querySelectorAll('input.inp-recibir').forEach(inp => {
                        inp.addEventListener('input', e => {
                            let v = String(e.target.value || '').replace(/[^\d]/g, '');
                            const max = Number(e.target.dataset.pendiente || 0);
                            if (v !== '' && Number(v) > max) v = String(Math.floor(max));
                            e.target.value = v;
                            try {
                                const tr = e.target.closest('tr');
                                const chk = tr?.querySelector('input.chk-art');
                                if (chk) {
                                    if (v !== '' && Number(v) > 0) {
                                        if (!chk.checked) {
                                            chk.checked = true;
                                            onChkToggle(chk);
                                        }
                                    } else {
                                        if (chk.checked) {
                                            chk.checked = false;
                                            onChkToggle(chk);
                                        }
                                    }
                                }
                            } catch (err) {
                                console.warn('toggle checkbox from input failed', err);
                            }
                        });
                    });

                    for (const oc of ocList) {
                        try {
                            const po = await fetchJsonOrThrow(
                                `/almacen/recepcion/po/${encodeURIComponent(oc)}`);
                            const linesArr = Array.isArray(po?.lines) ? po.lines : (Array.isArray(po?.lines
                                    ?.value) ? po
                                .lines.value : []);

                            console.log(`SAP PO ${oc}:`, po);

                            if (!window.PO_HEADER.DocNum && po?.po?.DocNum) {
                                window.PO_HEADER = {
                                    DocNum: Number(po?.po?.DocNum || 0),
                                    CardCode: String(po?.po?.CardCode || ''),
                                    DocEntry: Number(po?.po?.DocEntry || 0),
                                };
                            }

                            linesArr.forEach(l => {
                                const key = `${oc}::${normCode(l.ItemCode)}`;
                                if (!window.PO_LINE_BY_ITEM.has(key)) {
                                    window.PO_LINE_BY_ITEM.set(key, {
                                        Oc: oc,
                                        LineNum: Number(l.LineNum ?? -1),
                                        ItemCode: String(l.ItemCode ?? ''),
                                        WarehouseCode: String(l.WarehouseCode ?? ''),
                                        DocEntry: Number(po?.po?.DocEntry || 0),
                                        DocNum: Number(po?.po?.DocNum || 0),
                                        CardCode: String(po?.po?.CardCode || ''),
                                    });
                                }
                            });
                            console.log(`Mapeadas ${linesArr.length} líneas para OC ${oc}`);

                        } catch (e) {
                            console.error('sap_po_error_oc', oc, e);
                        }
                    }

                    cont.querySelectorAll('tbody tr').forEach(tr => {
                        const codeEl = tr.children[1]?.querySelector('div:first-child')?.textContent
                            ?.trim() || '';
                        const inp = tr.querySelector('input.inp-recibir');
                        const oc = inp?.dataset.oc || '';
                        const key = `${oc}::${normCode(codeEl)}`;

                        const sapLine = window.PO_LINE_BY_ITEM.get(key);
                        if (!sapLine) {
                            tr.classList.add('table-warning');
                            tr.title = `No vinculado a SAP (OC ${oc})`;
                            inp.dataset.baseline = -1;
                            console.warn('No encontrado en SAP:', key);
                            return;
                        }

                        inp.dataset.baseline = sapLine.LineNum;
                        inp.dataset.itemcode = sapLine.ItemCode;
                        inp.dataset.whs = sapLine.WarehouseCode;
                        inp.dataset.docnum = sapLine.DocNum;
                        inp.dataset.cardcode = sapLine.CardCode;
                        inp.dataset.docentry = sapLine.DocEntry;
                        console.log('Mapeado a SAP:', key, sapLine);
                    });

                    initArticulosDataTable();
                });

            } catch (e) {
                setResult(`
                <div class="text-danger">No fue posible cargar los artículos.<br>
                    <small>${escapeHTML(String(e.message || e))}</small>
                </div>
                <div class="text-danger mt-2">Verifica que la OC sea correcta.</div>
                `);
                console.error(e);
            }
        }

        function bindTablaEvents() {
            const cont = document.getElementById('articulosList');
            if (!cont) return;

            cont.addEventListener('change', (e) => {
                if (e.target && e.target.classList.contains('chk-art')) {
                    onChkToggle(e.target);
                }
            });
        }

        function initArticulosDataTable() {
            if (!window.jQuery || !$.fn.DataTable) return;
            const isMobile = window.matchMedia('(max-width: 768px)').matches;

            if ($.fn.DataTable.isDataTable('#tablaArticulos')) {
                $('#tablaArticulos').DataTable().destroy();
            }

            $('#tablaArticulos').DataTable({
                dom: '<"dt-top d-flex justify-content-between align-items-center"<"dt-left"B><"dt-right"f>>rtip',
                buttons: [{
                        text: 'Seleccionar todo',
                        className: 'btn btn-sm btn-outline-primary',
                        action: function seleccionarTodo() {
                            const container = document.getElementById('articulosList');
                            container.querySelectorAll('input.chk-art').forEach(chk => {
                                chk.checked = true;
                                onChkToggle(chk);
                            });
                        }
                    },
                    {
                        text: 'Limpiar selección',
                        className: 'btn btn-sm btn-outline-secondary',
                        action: function limpiarSeleccion() {
                            try {
                                limpiarSeleccionArt();
                            } catch (e) {
                                console.warn('limpiarSeleccionArt failed', e);
                            }
                        }
                    }
                ],
                rowGroup: {
                    dataSrc: [4]
                },
                columnDefs: [{
                    targets: [4],
                    visible: false
                }],
                searching: true,
                ordering: true,
                autoWidth: false,
                paging: false,
                info: false,
                lengthChange: false,
                scrollY: isMobile ? '55vh' : '45vh',
                scrollCollapse: false,
                responsive: false,
                scrollX: false,
                language: {
                    search: "Buscar:",
                    zeroRecords: "Sin resultados"
                }
            });
            $('#tablaArticulos').removeClass('d-none');

            try {
                if (window.jQuery && $.fn.DataTable) {
                    $('#tablaArticulos').off('change', 'input.chk-art');
                    $('#tablaArticulos').on('change', 'input.chk-art', function() {
                        try {
                            onChkToggle(this);
                        } catch (e) {
                            console.warn('onChkToggle delegated failed', e);
                        }
                    });
                }
            } catch (e) {
                console.warn('Error instalando delegado en tablaArticulos', e);
            }
        }

        async function abrirModalAgregarOC() {
            const id = Number(currentOrder?.id || 0);
            if (!id) return Swal.fire('Error', 'No hay reservación seleccionada', 'error');
            const cont = document.getElementById('listaOCsDisponibles');
            if (!cont) return Swal.fire('Error', 'No existe el contenedor del modal (listaOCsDisponibles)', 'error');
            cont.innerHTML = 'Cargando...';

            try {
                const data = await fetchJsonOrThrow(`/almacen/reservacion/${id}/ocs-disponibles`);
                if (!data.ok) throw new Error(data.msg || 'No se pudo cargar');

                const ordenes = Array.isArray(data.ordenes) ? data.ordenes : [];
                cont.innerHTML = ordenes.length ?
                    ordenes.map(o => {
                        const num = o.NumeroOrdenCompra ?? o.DocNum ?? '';
                        return `
                        <div class="d-flex justify-content-between align-items-center border rounded p-2 mb-2">
                        <div>
                            <div class="fw-bold">OC ${escapeHTML(String(num))}</div>
                            <div class="text-muted" style="font-size:.85rem;">Serie: ${escapeHTML(data.serie)}</div>
                        </div>
                        <button class="btn btn-success btn-sm" onclick="seleccionarOCParaCita('${num}')">Agregar</button>
                        </div>`;
                    }).join('') :
                    `<div class="text-muted">No hay OCs abiertas disponibles para agregar.</div>`;
                bootstrap.Modal.getOrCreateInstance(document.getElementById('modalAgregarOC')).show();
            } catch (e) {
                cont.innerHTML = `<div class="text-danger">${escapeHTML(String(e.message || e))}</div>`;
                bootstrap.Modal.getOrCreateInstance(document.getElementById('modalAgregarOC')).show();
            }
        }

        window.seleccionarOCParaCita = async function(ocNum) {
            try {
                await guardarOCEnReservacion(ocNum);

                bootstrap.Modal.getInstance(document.getElementById('modalAgregarOC'))?.hide();
                Swal.fire({
                    icon: 'success',
                    title: 'Orden de Compra agregada',
                    timer: 1000,
                    showConfirmButton: false
                });

                refreshPageKeepQuery();

            } catch (e) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No se pudo agregar',
                    text: String(e.message || e)
                });
            }
        }

        function refreshPageKeepQuery() {
            window.location.href = window.location.pathname + window.location.search;
        }

        function onChkToggle(chk) {
            const tr = chk.closest('tr');
            if (!tr) return;
            const inp = tr.querySelector('input.inp-recibir');
            const pendiente = Number(inp?.dataset.pendiente || 0);
            if (chk.checked) {
                inp.value = pendiente ? String(Math.floor(pendiente)) : '0';
            } else {
                inp.value = '';
            }
        }

        function autollenarSeleccion() {
            const container = document.getElementById('articulosList');
            container.querySelectorAll('tbody tr').forEach(tr => {
                const chk = tr.querySelector('input.chk-art');
                const inp = tr.querySelector('input.inp-recibir');
                if (chk && chk.checked && inp) {
                    const p = Number(inp.dataset.pendiente || 0);
                    inp.value = p ? String(Math.floor(p)) : '0';
                }
            });
        }

        function renderArticulosConBusqueda(rows) {
            const cont = document.getElementById('articulosList');
            let table = `<div style="margin-bottom:12px;">
            <input type="text" id="filtroArticulos" placeholder="Buscar por código o nombre..." 
                style="width:100%;padding:8px;border:1px solid #ddd;border-radius:6px;font-size:14px;" 
                onkeyup="filtrarArticulos(this.value)">
                </div>
                <table class="tabla-articulos" data-oc="${escapeHTML(String(rows[0]?.oc||''))}" style="width:100%;table-layout:auto;">
                    <thead>
                        <tr>
                            <th style="width:48px;text-align:center;">✓</th>
                            <th style="text-align:left;">Código - Artículo</th>
                            <th style="width:1%;white-space:nowrap;text-align:center;">Cantidad + UM</th>
                            <th style="width:1%;white-space:nowrap;text-align:center;">Recepción</th>
                        </tr>
                    </thead>
            <tbody>`;

            rows.forEach((a, i) => {
                const cant = isFinite(parseFloat(a.CantidadPendiente)) ? Number(a.CantidadPendiente) : 0;
                const code = escapeHTML(a.CodigoArticulo || '');
                const desc = escapeHTML(a.DescripcionArticulo || '');
                const um = escapeHTML(a.UnidadMedida || a.Unidad || a.UM || a.U_Medida || a.Unidad_Medida || '');
                const cantDisplay = cant % 1 === 0 ? cant : cant.toFixed(2);
                const idx = i + 1;
                table += `<tr data-idx="${idx}" data-codigo="${code.toLowerCase()}" data-nombre="${desc.toLowerCase()}">
                <td style="text-align:center;padding:12px;width:48px;">
                    <input type="checkbox" class="chk-art" data-idx="${idx}" onchange="onChkToggle(this)" style="width:24px;height:24px;cursor:pointer;">
                </td>
                <td style="padding:8px;">
                    <div style="font-weight:700;">${code}</div>
                    <div style="font-size:0.9em;color:#666;">${desc}</div>
                </td>
                <td style="text-align:center;padding:8px;white-space:nowrap;">
                    <div style="font-weight:700;">${cantDisplay}</div>
                    <div style="font-size:0.85em;color:#999;">${um || '-'}</div>
                </td>
                <td style="text-align:center;white-space:nowrap;">
                    <input type="number" min="0" step="1" class="inp-recibir" data-pendiente="${cant}" data-um="${um}" placeholder="0" style="width:90px;padding:6px;" />
                </td>
            </tr>`;
            });

            table += `</tbody></table>`;
            cont.innerHTML = table;

            document.querySelectorAll('#articulosList input.inp-recibir').forEach(inp => {
                inp.addEventListener('input', e => {
                    let v = String(e.target.value || '').replace(/[^\d]/g, '');
                    const max = Number(e.target.dataset.pendiente || 0);
                    if (v !== '' && Number(v) > max) v = String(Math.floor(max));
                    e.target.value = v;

                    try {
                        const tr = e.target.closest('tr');
                        const chk = tr?.querySelector('input.chk-art');
                        if (chk) {
                            if (v !== '' && Number(v) > 0) {
                                if (!chk.checked) {
                                    chk.checked = true;
                                    onChkToggle(chk);
                                }
                            } else {
                                if (chk.checked) {
                                    chk.checked = false;
                                    onChkToggle(chk);
                                }
                            }
                        }
                    } catch (err) {
                        console.warn('toggle checkbox from input failed', err);
                    }
                });
            });
            document.querySelectorAll('#articulosList input.chk-art').forEach(chk => {
                chk.addEventListener('change', e => {
                    try {
                        onChkToggle(e.target);
                    } catch (err) {
                        console.warn('onChkToggle failed', err);
                    }
                });
            });
        }

        function filtrarArticulos(valor) {
            const filtro = valor.toLowerCase();
            const filas = document.querySelectorAll('#articulosList tbody tr');
            filas.forEach(fila => {
                const codigo = fila.dataset.codigo || '';
                const nombre = fila.dataset.nombre || '';
                const visible = codigo.includes(filtro) || nombre.includes(filtro);
                fila.style.display = visible ? '' : 'none';
            });
        }

        function cerrarDetalles() {
            const desktop = document.getElementById('detallesDesktop');
            if (desktop) {
                desktop.innerHTML =
                    `<div class="text-center text-muted mt-5"><p>Selecciona una orden de compra para ver detalles</p></div>`;
            }

            document.querySelectorAll('.orden-card').forEach(b => b.classList.remove('active'));
            currentOrder = null;
        }

        function normCode(s) {
            return String(s || '').trim().toUpperCase();
        }

        function seleccionarTodoArt() {
            const container = document.getElementById('articulosList');
            if (!container) return;
            container.querySelectorAll('input.chk-art').forEach(chk => {
                const tr = chk.closest('tr');
                const inp = tr?.querySelector('input.inp-recibir');
                chk.checked = true;
                if (inp) {
                    const pendiente = Number(inp.dataset.pendiente || 0);
                    inp.value = pendiente ? String(Math.floor(pendiente)) : '1';
                    try {
                        onChkToggle(chk);
                    } catch (e) {}
                } else {
                    try {
                        onChkToggle(chk);
                    } catch (e) {}
                }
            });
        }

        function limpiarSeleccionArt() {
            const container = document.getElementById('articulosList');
            if (!container) return;
            container.querySelectorAll('input.inp-recibir').forEach(i => i.value = '');
            container.querySelectorAll('input.chk-art').forEach(c => {
                if (c.checked) {
                    c.checked = false;
                    try {
                        onChkToggle(c);
                    } catch (e) {}
                } else {
                    const tr = c.closest('tr');
                    const inp = tr?.querySelector('input.inp-recibir');
                    if (inp) inp.value = '';
                }
            });
        }

        function filtrarTablaArticulos(valor) {
            const filtro = valor.toLowerCase();
            const filas = document.querySelectorAll('#tablaArticulos tbody tr');
            filas.forEach(fila => {
                const ocText = fila.children[4]?.textContent?.toLowerCase() || '';
                const visible = ocText.includes(filtro);
                fila.style.display = visible ? '' : 'none';
            });
        }

        function fetchJsonOrThrow(url, opt) {
            return fetch(url, opt).then(async r => {
                const text = await r.text();
                if (!r.ok) throw new Error(`HTTP ${r.status}: ${text.slice(0, 300)}`);
                try {
                    return JSON.parse(text);
                } catch (e) {
                    throw new Error(`Respuesta NO JSON (${r.status}) - ${text.slice(0, 300)}`);
                }
            });
        }

        function buildSapComments({
            userComment,
            ocNum,
            grpoDocNum,
            username
        }) {
            const base = (userComment || '').trim();
            const extra =
                `Creado desde Intranet Proveedores ` +
                `| OC #${ocNum} ` +
                `| Entrada mercancía #${grpoDocNum ?? 'PENDIENTE'} ` +
                `| Capturado por: @${username}`;
            if (!base) return extra;
            return `${base} | ${extra}`;
        }

        async function confirmarSeleccion() {
            const table = document.querySelector('#articulosList table#tablaArticulos, #articulosList table[data-oc]');
            const username = SESSION_USER?.username || 'N/A';

            if (!table) {
                return Swal.fire('Sin artículos', 'No hay artículos cargados', 'info');
            }

            const filas = getSelectedRowsFromTable();
            const seleccion = [];

            filas.forEach(tr => {
                const chk = tr.querySelector('input.chk-art');
                const inp = tr.querySelector('input.inp-recibir');
                if (!chk || !inp) return;

                const qty = inp.value === '' ? 0 : Number(inp.value);
                if (chk.checked && qty > 0) {
                    seleccion.push({
                        tr,
                        inp,
                        qty
                    });
                }
            });

            if (!seleccion.length) {
                return Swal.fire('Sin selección', 'Marca líneas y captura cantidades para continuar.', 'info');
            }

            // 2) validar numAtCard
            const numeroReferencia = (document.getElementById('numeroReferencia')?.value || '').trim();
            if (!numeroReferencia) {
                return Swal.fire('Falta referencia', 'Captura el número de referencia del acreedor.', 'warning');
            }

            const comment = document.getElementById('comentarios')?.value || '';
            const sucursalId = Number(document.getElementById('sucursal_id')?.value || 0);

            // 3) armar resumen visual (opcional)
            let total = 0;
            let rowsHtml = '';
            for (const s of seleccion) {
                const tr = s.tr;
                const qty = s.qty;
                const um = s.inp?.dataset.um || '';

                const codeDescText = tr.children[1]?.textContent?.trim() || '';
                const ocCol = tr.children[4]?.textContent?.trim() || (s.inp?.dataset.oc || '');

                rowsHtml += `
                <tr>
                    <td style="border:1px solid #ddd;padding:6px;">
                    <b>OC ${escapeHTML(ocCol)}</b><br/>
                    ${escapeHTML(codeDescText)}
                    </td>
                    <td style="border:1px solid #ddd;padding:6px;text-align:right;">
                    ${fmt(qty)} ${escapeHTML(um || '')}
                    </td>
                </tr>`;
                total += qty;
            }

            const confirmHtml = `
                <div style="text-align:left;">
                <div style="margin-bottom:6px;">
                    <strong>No. ref. del acreedor:</strong>
                    <div style="border:1px solid #eee;padding:6px;border-radius:6px;background:#fff;">
                    ${escapeHTML(numeroReferencia)}
                    </div>
                </div>

                <div style="max-height:40vh;overflow:auto;margin-bottom:8px;">
                    <table style="width:100%;border-collapse:collapse;">
                    <thead>
                        <tr>
                        <th style="text-align:left;border:1px solid #ddd;padding:6px;">Artículo</th>
                        <th style="text-align:right;border:1px solid #ddd;padding:6px;width:140px;">Cantidad</th>
                        </tr>
                    </thead>
                    <tbody>${rowsHtml}</tbody>
                    <tfoot>
                        <tr>
                        <td style="text-align:right;border:1px solid #ddd;padding:6px;font-weight:700;">Total</td>
                        <td style="text-align:right;border:1px solid #ddd;padding:6px;font-weight:800;">${fmt(total)}</td>
                        </tr>
                    </tfoot>
                    </table>
                </div>

                <div style="margin-top:8px;">
                    <strong>Comentario:</strong>
                    <div style="border:1px solid #eee;padding:6px;border-radius:6px;background:#fff;">
                    ${escapeHTML(comment)}
                    </div>
                </div>
                </div>
            `;

            const ok = await Swal.fire({
                title: '¿Confirmar recepción?',
                html: confirmHtml,
                showCancelButton: true,
                confirmButtonText: 'Sí, confirmar'
            }).then(r => r.isConfirmed);

            if (!ok) return;

            // 4) AGRUPAR POR DOCENTRY (1 payload por OC)
            const grupos = new Map();
            // Colección de líneas human-readable para impresión, por docEntry
            const printableByDoc = new Map();
            for (const s of seleccion) {
                const inp = s.inp;
                const docNumL = Number(inp.dataset.docnum || 0);
                const docEntryL = Number(inp.dataset.docentry || 0);
                const cardCodeL = String(inp.dataset.cardcode || '');
                const baseLine = Number(inp.dataset.baseline || -1);
                const itemCode = String(inp.dataset.itemcode || '');
                const whs = String(inp.dataset.whs || '');
                const ocL = String(inp.dataset.oc || docNumL || '');
                const serie = Number(SERIES_BY_SUC[String(sucursalId)] || 157); // default ZC
                if (!serie) throw new Error(`Sucursal sin serie GRPO configurada: ${sucursalId}`);

                if (!docEntryL || !docNumL || !cardCodeL) {
                    return Swal.fire('Error', 'Hay líneas sin DocEntry/DocNum/CardCode (no mapeadas a SAP).', 'error');
                }
                if (baseLine < 0 || !itemCode || !whs) {
                    return Swal.fire('Error', 'Hay líneas sin BaseLine/ItemCode/WarehouseCode.', 'error');
                }

                if (!grupos.has(docEntryL)) {
                    grupos.set(docEntryL, {
                        docNum: docNumL,
                        docEntry: docEntryL,
                        cardCode: cardCodeL,
                        numAtCard: numeroReferencia,
                        sucursal_id: sucursalId,
                        Series: serie,
                        user_comment: `${comment} [OC ${docNumL}]`,
                        oc_num: String(ocL),
                        captured_by: username,
                        lines: []
                    });
                }

                grupos.get(docEntryL).lines.push({
                    BaseLine: baseLine,
                    ItemCode: itemCode,
                    Quantity: s.qty,
                    WarehouseCode: whs
                });

                try {
                    const tr = s.tr;
                    const code = tr.children[1]?.querySelector('div:first-child')?.textContent?.trim() || '';
                    const desc = tr.children[1]?.querySelector('div:nth-child(2)')?.textContent?.trim() || '';
                    const umVal = s.inp?.dataset.um || '';
                    if (!printableByDoc.has(docEntryL)) printableByDoc.set(docEntryL, []);
                    printableByDoc.get(docEntryL).push({
                        code,
                        desc,
                        qty: s.qty,
                        um: umVal
                    });
                } catch (e) {
                    console.warn('No pude construir línea imprimible', e);
                }
            }

            const payloads = Array.from(grupos.values());

            console.groupCollapsed('[PAYLOADS GRPO] Multi-OC');
            console.log('Total payloads:', payloads.length);
            console.log(payloads);
            payloads.forEach(p => console.table(p.lines));
            console.groupEnd();

            // 5) enviar uno por uno (si uno falla, se detiene)
            try {
                Swal.fire({
                    title: 'Procesando...',
                    text: `Generando ${payloads.length} recepción(es) en SAP`,
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                const resultados = [];

                for (const p of payloads) {
                    const resp = await fetch('/almacen/recepcion/grpo', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify(p)
                    });

                    const text = await resp.text();
                    let data;
                    try {
                        data = JSON.parse(text);
                    } catch {
                        data = {
                            ok: false,
                            msg: text.slice(0, 200)
                        };
                    }

                    if (!(resp.ok && data.ok)) {
                        Swal.close();
                        return Swal.fire(
                            'Error',
                            data.msg || `Error al crear GRPO para OC ${p.docNum}`,
                            'error'
                        );
                    }

                    const grpoDocNum = data?.data?.DocNum || data?.DocNum || null;
                    resultados.push({
                        oc: p.docNum,
                        grpo: grpoDocNum,
                        docEntry: p.docEntry
                    });
                }

                Swal.close();

                // 6) success + resumen
                const resumenHtml = `
                <div style="text-align:left;">
                    <b>Recepciones generadas:</b>
                    <ul style="margin-top:8px;">
                    ${resultados.map(r => `<li>Orden de Compra <b>${escapeHTML(String(r.oc))}</b> → Entrega de Recepcion <b>${escapeHTML(String(r.grpo || 'OK'))}</b></li>`).join('')}
                    </ul>
                </div>
                `;

                // Ofrecer imprimir recibos ahora
                const printChoice = await Swal.fire({
                    icon: 'success',
                    title: 'Recepción(es) creada(s)',
                    html: resumenHtml,
                    showCancelButton: true,
                    confirmButtonText: 'Imprimir recibos',
                    cancelButtonText: 'Finalizar'
                });

                if (printChoice.isConfirmed) {
                    // Intentar imprimir cada recibo; usamos las líneas capturadas en printableByDoc
                    try {
                        for (const r of resultados) {
                            const printable = printableByDoc.get(Number(r.docEntry)) || [];
                            const payloadForDoc = payloads.find(x => Number(x.docEntry) === Number(r.docEntry)) || {};
                            // Llamada a la función de impresión
                            try {
                                imprimirReciboGRPO({
                                    grpoDocNum: r.grpo,
                                    ocDocNum: payloadForDoc.docNum || r.oc,
                                    proveedorNombre: currentOrder?.proveedor_nombre || '',
                                    proveedorCodigo: payloadForDoc.cardCode || window.PO_HEADER?.CardCode || '',
                                    sucursalNombre: '',
                                    fechaCita: currentOrder?.fecha || '',
                                    horaCita: currentOrder?.hora || '',
                                    transporte: currentOrder?.transporte_nombre || '',
                                    lugar: '',
                                    numAtCard: numeroReferencia,
                                    comentarios: comment,
                                    usuario: username,
                                    lineas: printable
                                });
                            } catch (e) {
                                console.warn('Error imprimiendo recibo', e);
                            }
                            // pequeña pausa para evitar bloqueos por pop-ups simultáneos
                            await new Promise(res => setTimeout(res, 550));
                        }
                    } catch (e) {
                        console.warn('Error en proceso de impresión masiva', e);
                    }
                    // Después de imprimir, refrescar
                    refreshPageKeepQuery();
                } else {
                    refreshPageKeepQuery();
                }

            } catch (e) {
                Swal.close();
                Swal.fire('Error', String(e.message || e), 'error');
            }
        }

        function imprimirReciboGRPO({
            grpoDocNum,
            ocDocNum,
            proveedorNombre,
            proveedorCodigo,
            sucursalNombre,
            fechaCita,
            horaCita,
            transporte,
            lugar,
            numAtCard,
            comentarios,
            usuario,
            lineas
        }) {
            const win = window.open('', '_blank', 'width=900,height=700');
            if (!win) {
                Swal?.fire?.('Pop-up bloqueado', 'Habilita ventanas emergentes para imprimir.', 'warning');
                return;
            }

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

            const hoy = new Date();
            const fechaImp = hoy.toLocaleDateString('es-MX');
            const horaImp = hoy.toLocaleTimeString('es-MX', {
                hour: '2-digit',
                minute: '2-digit'
            });

            let total = 0;
            const rows = (lineas || []).map(l => {
                total += Number(l.qty || 0);
                return `
                <tr>
                    <td class="c-code">${esc(l.code || '')}</td>
                    <td class="c-desc">${esc(l.desc || '')}</td>
                    <td class="c-qty">${fmt(l.qty || 0)}</td>
                    <td class="c-um">${esc(l.um || '')}</td>
                </tr>
                `;
            }).join('');

            const html = `<!doctype html>
            <html>
            <head>
            <meta charset="utf-8" />
            <title>Recibo de mercancía</title>
            <style>
            :root{ --brand:#ee7826; --muted:#666; --line:#e5e7eb; }
            *{ box-sizing:border-box; }
            body{ font-family: Arial, sans-serif; color:#111; margin:0; padding:24px; background:#f7f7f7; }
            .paper{ background:#fff; border:1px solid #eee; border-radius:12px; padding:18px 18px 12px; max-width:900px; margin:0 auto; }
            header{ display:flex; gap:12px; align-items:center; border-bottom:2px solid var(--brand); padding-bottom:10px; margin-bottom:12px; }
            header img{ height:56px; }
            header .title{ flex:1; }
            header h1{ margin:0; font-size:20px; color:var(--brand); }
            header .meta{ font-size:12px; color:var(--muted); margin-top:4px; }
            .grid{ display:grid; grid-template-columns:1fr 1fr; gap:10px; margin:12px 0 14px; }
            .box{ border:1px solid var(--line); border-radius:10px; padding:10px; }
            .box h3{ margin:0 0 8px; font-size:13px; color:#111; }
            .kv{ font-size:13px; line-height:1.45; }
            .kv b{ color:#111; }
            .pill{ display:inline-block; padding:4px 8px; border-radius:999px; background:#fff3e6; border:1px solid #ffd6b0; font-size:12px; }
            table{ width:100%; border-collapse:collapse; font-size:13px; }
            thead th{ text-align:left; padding:10px 8px; background:#fff3e6; border:1px solid #ffd6b0; }
            tbody td{ padding:8px; border:1px solid var(--line); vertical-align:top; }
            .c-qty{ text-align:right; font-weight:700; width:110px; }
            .c-um{ width:90px; text-align:center; color:var(--muted); }
            .c-code{ width:120px; font-weight:700; }
            tfoot td{ padding:10px 8px; border:1px solid var(--line); }
            .totalLbl{ text-align:right; font-weight:700; }
            .totalVal{ text-align:right; font-weight:900; font-size:15px; }
            .notes{ margin-top:10px; border:1px dashed #ffd6b0; background:#fffaf5; padding:10px; border-radius:10px; font-size:12.5px; color:#333; }
            .sign{ display:grid; grid-template-columns:1fr 1fr; gap:18px; margin-top:18px; }
            .line{ border-top:1px solid #bbb; padding-top:6px; font-size:12px; color:#333; text-align:center; }
            footer{ margin-top:14px; text-align:center; font-size:12px; color:var(--muted); }
            @media print{
                body{ background:#fff; padding:0; }
                .paper{ border:none; border-radius:0; padding:0; }
                header{ page-break-after:avoid; }
                .box, .notes{ page-break-inside:avoid; }
                table{ page-break-inside:auto; }
                tr{ page-break-inside:avoid; page-break-after:auto; }
            }
            </style>
            </head>
            <body>
            <div class="paper">
                <header>
                <img src="${window.location.origin}/assets/img/logo.png" alt="Logo">
                <div class="title">
                    <h1>Recibo de Mercancía</h1>
                    <div class="meta">Generado ${esc(fechaImp)} ${esc(horaImp)} · Capturó: <b>${esc(usuario || '')}</b></div>
                </div>
                <div style="text-align:right">
                    <div class="pill">Entrega de Mercancia: <b>${esc(grpoDocNum || 'PENDIENTE')}</b></div><br/>
                    <div class="pill">Orden de Compra: <b>${esc(ocDocNum || '')}</b></div>
                </div>
                </header>

                <div class="grid">
                <div class="box">
                    <h3>Proveedor</h3>
                    <div class="kv">
                    <b>${esc(proveedorNombre || 'N/A')}</b><br/>
                    <span style="color:var(--muted)">Código:</span> ${esc(proveedorCodigo || 'N/A')}<br/>
                    <span style="color:var(--muted)">Folio proveedor:</span> <b>${esc(numAtCard || '')}</b>
                    </div>
                </div>

                <div class="box">
                    <h3>Datos de cita / recepción</h3>
                    <div class="kv">
                    <span style="color:var(--muted)">Sucursal:</span> <b>${esc(sucursalNombre || 'N/A')}</b><br/>
                    <span style="color:var(--muted)">Fecha cita:</span> ${esc(fechaCita || '')}<br/>
                    <span style="color:var(--muted)">Hora:</span> ${esc(horaCita || '')}<br/>
                    <span style="color:var(--muted)">Transporte:</span> ${esc(transporte || 'N/A')}<br/>
                    <span style="color:var(--muted)">Lugar:</span> ${esc(lugar || 'N/A')}
                    </div>
                </div>
                </div>

                <table>
                <thead>
                    <tr>
                    <th>Código</th>
                    <th>Artículo</th>
                    <th style="text-align:right">Cantidad</th>
                    <th style="text-align:center">UM</th>
                    </tr>
                </thead>
                <tbody>
                    ${rows || `<tr><td colspan="4">Sin líneas.</td></tr>`}
                </tbody>
                <tfoot>
                    <tr>
                    <td colspan="2" class="totalLbl">Total recibido</td>
                    <td class="totalVal">${fmt(total)}</td>
                    <td></td>
                    </tr>
                </tfoot>
                </table>

                ${comentarios ? `
                                        <div class="notes"><b>Comentarios:</b><br/>${esc(comentarios)}</div>
                                        ` : ''}

                        <div class="sign">
                        <div class="line">Entrega (Proveedor)</div>
                        <div class="line">Recibe (Almacén)</div>
                        </div>

                        <footer>
                        Reporte generado automáticamente por Intranet Proveedores · ${esc(fechaImp)}
                        </footer>
                    </div>

                    <script>
                    window.onload = () => { window.focus(); setTimeout(() => window.print(), 250); };
                <\/script>

                </body>

                </html>`;
            win.document.open();
            win.document.write(html);
            win.document.close();
        }
    </script>
@stop
