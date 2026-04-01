@extends('layouts.movil')
@section('title', 'Código de barras')
@section('content')
    @include('includes.scripts.SweetAlert2@11')
    @include('includes.scripts.googleapis')
    @include('includes.scripts.bootstrap')
    @include('includes.scripts.SweetAlert2')
    @include('includes.scripts.Datatables')
    @include('includes.scripts.fontAwesome')
    @include('includes.scripts.flatpickr')

    <x-sidebar />
    <link rel="stylesheet" href="{{ asset('assets/css/rol/almacen/codebar.css') }}">

    @php
        $rolId = session('Usuario.IdRol');
        $sucursalIdUsuario = session('Usuario.SucursalID');
    @endphp

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="container-fluid con-sidebar mt-3">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h4 class="mb-0">Alta de código de barra</h4>
                        <p class="text-muted small mb-0">Escanea, relaciona con un artículo SAP y guarda el registro.</p>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12 col-md-5">
                                <label class="form-label">Código escaneado</label>
                                <div class="mb-2 d-flex gap-2">
                                    <input id="barcodeInput" type="text" inputmode="numeric" pattern="\d*" class="form-control"
                                        placeholder="Escanea o pega aquí el código">
                                    <button id="btnPaste" class="btn btn-outline-secondary">Pegar</button>
                                </div>

                                <div id="barcodePreview" class="p-3 border rounded">
                                    <div class="small text-muted">Código</div>
                                    <div id="barcodeValue" class="barcode-value">-</div>
                                    <div class="small text-muted mt-2">Vista previa</div>
                                    <div id="barcodeGraphic" class="barcode-graphic">
                                        <div id="barcodeSvgWrap" style="min-height:60px;display:flex;align-items:center;">
                                            <svg id="barcodeSvg" xmlns="http://www.w3.org/2000/svg"></svg>
                                        </div>
                                        <div id="barcodeLibError" style="display:none;margin-top:8px;color:#b71c1c;font-size:13px;
                                            background:#fff2f2;padding:8px;border-radius:4px;border:1px solid #f5c6cb;">
                                            No se pudo cargar la librería de generación de códigos. Revisa la consola o coloca
                                            <strong>/assets/js/JsBarcode.all.min.js</strong> en tu servidor.
                                        </div>
                                    </div>
                                    <div id="barcodeProductPreview" class="mt-2" style="display:none;">
                                        <div id="barcodeProductRow" style="display:flex;gap:10px;align-items:flex-start;">
                                            <img id="barcodeImage" src="" alt=""
                                                style="width:80px;height:80px;object-fit:contain;border:1px solid #eee;padding:4px;background:#fff;">
                                            <div style="flex:1;min-width:0;">
                                                <div id="barcodeProductName"
                                                    style="font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                                </div>
                                                <div id="barcodeProductDesc" class="small text-muted mt-1"
                                                    style="max-height:72px;overflow:auto;">&nbsp;</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> 

                            <div class="col-12 col-md-7">
                                <label class="form-label">Buscar artículo (por nombre o código)</label>
                                <div class="search-select-wrapper">
                                    <input id="searchItemInput" type="search" class="form-control"
                                        placeholder="Escribe para buscar...">
                                    <div id="searchResults" class="mt-2" style="max-height:280px;overflow:auto;"></div>
                                </div>

                                <div id="selectedItemInfo" class="mt-3 p-3"
                                    style="min-height:120px;border:1px dashed #e9ecef;border-radius:6px;background:#fbfbfb;">
                                    <div class="text-muted">No hay artículo seleccionado.</div>
                                </div>
                                
                                <div class="mt-2">
                                    <label class="form-label">Unidad de medida</label>
                                    <select id="uomSelect" class="form-select" disabled>
                                        <option value="">-- Selecciona --</option>
                                        <option value="BULTO">BULTO</option>
                                        <option value="PAQUETE">PAQUETE</option>
                                        <option value="KILO">KILO</option>
                                        <option value="CAJA">CAJA</option>
                                        <option value="PIEZA">PIEZA</option>
                                        <option value="CUBETA">CUBETA</option>
                                    </select>
                                </div>
                                <div class="mt-3 d-flex gap-2 justify-content-end">
                                    <button id="btnGuardarAltaCodigo" class="btn btn-primary">Capturar y guardar</button>
                                    <button id="btnLimpiar" class="btn btn-outline-secondary">Limpiar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function() {
            const csrfToken = document.querySelector("meta[name='csrf-token']").getAttribute('content');

            const barcodeInput = document.getElementById('barcodeInput');
            const btnPaste = document.getElementById('btnPaste');
            const barcodeValue = document.getElementById('barcodeValue');
            const barcodeGraphic = document.getElementById('barcodeGraphic');
            const searchInput = document.getElementById('searchItemInput');
            const resultsEl = document.getElementById('searchResults');
            const selectedInfo = document.getElementById('selectedItemInfo');
            const uomSelect = document.getElementById('uomSelect');
            const btnGuardar = document.getElementById('btnGuardarAltaCodigo');
            const btnLimpiar = document.getElementById('btnLimpiar');

            let selectedItem = null;
            let searchTmr = null;
            let lastResults = [];
            let currentSearchController = null;
            let currentQuery = '';
            let currentShown = 0;

            const productCache = new Map();
            let currentProductController = null;
            const searchCache = new Map();
            const RESULTS_LIMIT = 50;

            const barcodeSvg = document.getElementById('barcodeSvg');
            let jsBarcodeReady = false;

            function ensureJsBarcode() {
                return new Promise((resolve) => {
                    if (window.JsBarcode) {
                        jsBarcodeReady = true;
                        return resolve(true);
                    }
                    const urls = [
                        'https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js',
                        'https://cdnjs.cloudflare.com/ajax/libs/jsbarcode/3.11.5/JsBarcode.all.min.js',
                        'https://unpkg.com/jsbarcode@3.11.5/dist/JsBarcode.all.min.js',
                        '/assets/js/JsBarcode.all.min.js'
                    ];
                    let idx = 0;
                    function tryLoad() {
                        if (idx >= urls.length) return resolve(false);
                        const s = document.createElement('script');
                        s.src = urls[idx++];
                        s.onload = () => {
                            jsBarcodeReady = true;
                            resolve(true);
                        };
                        s.onerror = () => {
                            tryLoad();
                        };
                        document.head.appendChild(s); 
                    }
                    tryLoad();
                });
            }

            async function renderSvgBarcode(val) {
                if (!barcodeSvg) return;
                const v = String(val || '');
                if (!v) {
                    barcodeSvg.innerHTML = '';
                    return;
                }
                const type = 'CODE128';
                const ok = await ensureJsBarcode();
                if (!ok || !window.JsBarcode) {
                    barcodeSvg.innerHTML = '';
                    const wrap = document.getElementById('barcodeSvgWrap');
                    if (wrap) wrap.textContent = v;
                    const err = document.getElementById('barcodeLibError');
                    if (err) err.style.display = 'block';
                    console.warn('JsBarcode no disponible. Intenta colocar JsBarcode.all.min.js en /assets/js o permitir carga desde CDN.');
                    return;
                }
                try {
                    const wrap = document.getElementById('barcodeSvgWrap');
                    if (wrap) wrap.textContent = '';
                    const err = document.getElementById('barcodeLibError');
                    if (err) err.style.display = 'none';
                    JsBarcode(barcodeSvg, v, {
                        format: type,
                        displayValue: true,
                        fontSize: 12,
                        height: 60,
                        margin: 10
                    });
                } catch (e) {
                    console.warn('JsBarcode render failed', e);
                    barcodeSvg.innerHTML = '';
                    const wrap2 = document.getElementById('barcodeSvgWrap');
                    if (wrap2) wrap2.textContent = v;
                }
            }

            function esc(t) {
                return String(t || '');
            }

            function renderBarcode(val) {
                const v = esc(val) || '-';
                barcodeValue.textContent = v;
                try {
                    const wrap = document.getElementById('barcodeSvgWrap');
                    if (wrap) {  }
                } catch (_) {}
                renderSvgBarcode(val);
            }

            function showProductPreview(data) {
                const preview = document.getElementById('barcodeProductPreview');
                const img = document.getElementById('barcodeImage');
                const name = document.getElementById('barcodeProductName');
                const desc = document.getElementById('barcodeProductDesc');
                if (!data) {
                    preview.style.display = 'none';
                    img.src = '';
                    name.textContent = '';
                    desc.textContent = '';
                    return;
                }
                preview.style.display = 'block';
                img.src = data.image || '';
                img.alt = data.name || '';
                name.textContent = data.name || data.title || '';
                desc.textContent = data.description || data.notes || (data.brands ? data.brands : '') || '';
            }

            function clearProductPreview() {
                showProductPreview(null);
            }

            async function fetchProductInfo(barcode) {
                if (!barcode) {
                    clearProductPreview();
                    return;
                }
                if (productCache.has(barcode)) {
                    showProductPreview(productCache.get(barcode));
                    return;
                }
                try {
                    if (currentProductController) {
                        try {
                            currentProductController.abort();
                        } catch (_) {}
                        currentProductController = null;
                    }
                    currentProductController = new AbortController();
                    const signal = currentProductController.signal;
                    const url = `/almacen/lookup-product?barcode=${encodeURIComponent(barcode)}`;
                    showProductPreview({
                        name: 'Buscando información...',
                        description: ''
                    });
                    const res = await fetch(url, {
                        signal
                    });
                    currentProductController = null;
                    if (!res.ok) {
                        clearProductPreview();
                        return;
                    }
                    const j = await res.json();
                    if (j && j.name) {
                        const info = {
                            name: j.name || '',
                            description: j.description || '',
                            image: j.image || ''
                        };
                        productCache.set(barcode, info);
                        showProductPreview(info);
                        return;
                    }
                    if (j && j.source === 'openfoodfacts' && j.raw && j.raw.product) {
                        const p = j.raw.product;
                        const info = {
                            name: p.product_name || p.product_name_en || p.generic_name || '',
                            description: p.generic_name || p.categories || p.ingredients_text || '',
                            image: p.image_small_url || p.image_url || ''
                        };
                        productCache.set(barcode, info);
                        showProductPreview(info);
                        return;
                    }

                    if (j && j.source === 'barcodelookup' && j.raw) {
                        const raw = j.raw;
                        const first = Array.isArray(raw.products) && raw.products.length ? raw.products[0] : null;
                        if (first) {
                            const info = {
                                name: first.title || first.product_name || first.name || '',
                                description: first.description || first.details || '',
                                image: first.images && first.images.length ? first.images[0].url : (first
                                    .image || '')
                            };
                            productCache.set(barcode, info);
                            showProductPreview(info);
                            return;
                        }
                    }
                    productCache.set(barcode, null);
                    clearProductPreview();
                } catch (e) {
                    if (e.name === 'AbortError') return;
                    console.warn('fetchProductInfo', e);
                    clearProductPreview();
                }
            }

            function renderSelected() {
                try {
                    if (window.jQuery && $.fn.DataTable) {
                        const dt = $('#selectedItemTable');
                        if (dt && dt.length && $.fn.DataTable.isDataTable('#selectedItemTable')) {
                            $('#selectedItemTable').DataTable().clear().destroy();
                        }
                    }
                } catch (e) {

                }

                if (!selectedItem) {
                    selectedInfo.innerHTML = '<div class="text-muted">No hay artículo seleccionado.</div>';
                    if (uomSelect) {
                        uomSelect.value = '';
                        uomSelect.disabled = true;
                    }
                    return;
                }

                const rows = [
                    ['Código (ItemCode)', esc(selectedItem.ItemCode || selectedItem.Codigo || '')],
                    ['Nombre (ItemName)', esc(selectedItem.ItemName || selectedItem.Nombre || '')],
                    ['Unidad (UoM)', esc(selectedItem.UoM || selectedItem.Unidad || '')],
                    ['Grupo', esc(selectedItem.ItemGroup || selectedItem.Grupo || '')]
                ];
                let tableHtml =
                    '<div class="table-responsive"><table id="selectedItemTable" class="table table-sm table-striped mb-0">';
                tableHtml += '<thead><tr><th style="width:35%">Propiedad</th><th>Valor</th></tr></thead><tbody>';
                rows.forEach(r => {
                    tableHtml += `<tr><td><strong>${r[0]}</strong></td><td>${r[1] || '&nbsp;'}</td></tr>`;
                });
                tableHtml += '</tbody></table></div>';
                selectedInfo.innerHTML = tableHtml;
                try {
                    if (window.jQuery && $.fn.DataTable) {
                        $('#selectedItemTable').DataTable({
                            paging: false,
                            searching: false,
                            info: false,
                            ordering: false,
                            responsive: true,
                            lengthChange: false,
                            autoWidth: false
                        });
                    }
                } catch (e) {
                    console.warn('init DataTable selectedItemTable', e);
                }
                try {
                    if (uomSelect) {
                        const uomVal = selectedItem.UoM || selectedItem.Unidad || '';
                        const opt = Array.from(uomSelect.options).some(o => o.value === uomVal && uomVal !== '');
                        uomSelect.value = opt ? uomVal : '';
                        uomSelect.disabled = false;
                    }
                } catch (e) {
                    console.warn('set uomSelect', e);
                }
            }

            function renderResults(list, totalCount) {
                resultsEl.innerHTML = '';
                if (!list || !list.length) {
                    resultsEl.innerHTML = '<div class="text-muted p-2">No se encontraron artículos.</div>';
                    resultsEl.classList.add('show');
                    return;
                }
                const frag = document.createDocumentFragment();
                list.forEach((it, idx) => {
                    const item = document.createElement('div');
                    item.className = 'search-item';
                    item.setAttribute('data-idx', String(idx));
                    item.innerHTML =
                        `<div style="font-weight:700">${esc(it.ItemName||it.Nombre||'')}</div><small>${esc(it.ItemCode||it.Codigo||'')}</small>`;
                    item.addEventListener('click', () => {
                        selectedItem = it;
                        renderSelected();
                        resultsEl.classList.remove('show');
                        resultsEl.innerHTML = '';
                        searchInput.value = it.ItemName || it.Nombre || it.ItemCode || '';
                    });
                    frag.appendChild(item);
                });

                resultsEl.appendChild(frag);
                const cacheEntry = searchCache.get(currentQuery);
                if (cacheEntry && cacheEntry.list && cacheEntry.list.length > cacheEntry.shown) {
                    const btnWrap = document.createElement('div');
                    btnWrap.className = 'text-center mt-2';
                    const btn = document.createElement('button');
                    btn.className = 'btn btn-sm btn-outline-secondary';
                    btn.textContent = 'Cargar más';
                    btn.addEventListener('click', () => {
                        cacheEntry.shown = Math.min(cacheEntry.list.length, cacheEntry.shown + RESULTS_LIMIT);
                        currentShown = cacheEntry.shown;
                        renderResults(cacheEntry.list.slice(0, cacheEntry.shown), cacheEntry.total);
                    });
                    btnWrap.appendChild(btn);
                    resultsEl.appendChild(btnWrap);
                } else if (totalCount && totalCount > list.length) {
                    const more = document.createElement('div');
                    more.className = 'text-muted small p-2';
                    more.textContent =
                        `Mostrando ${list.length} de ${totalCount} resultados. Refina la búsqueda para ver más.`;
                    resultsEl.appendChild(more);
                }

                resultsEl.classList.add('show');
            }

            function setResultsLoading(loading) {
                if (loading) {
                    resultsEl.innerHTML =
                        '<div class="text-center p-3"><div class="spinner-border spinner-border-sm" role="status"></div> Buscando...</div>';
                    resultsEl.classList.add('show');
                } else {
                    if (resultsEl.innerHTML.includes('spinner-border')) resultsEl.innerHTML = '';
                }
            }

            async function doSearch(q) {
                currentQuery = q;
                if (!q || q.length < 2) {
                    renderResults([]);
                    return;
                }
                const cacheKeys = Array.from(searchCache.keys()).filter(k => q.startsWith(k) && k.length >= 2);
                if (cacheKeys.length) {
                    cacheKeys.sort((a, b) => b.length - a.length);
                    const bestKey = cacheKeys[0];
                    const base = searchCache.get(bestKey);
                    if (base && Array.isArray(base.list) && base.list.length) {
                        const filtered = base.list.filter(it => {
                            const name = (it.ItemName || it.Nombre || '').toLowerCase();
                            const code = (it.ItemCode || it.Codigo || '').toLowerCase();
                            const qq = q.toLowerCase();
                            return name.includes(qq) || code.includes(qq);
                        });
                        if (filtered.length >= RESULTS_LIMIT) {
                            searchCache.set(q, {
                                list: filtered,
                                total: filtered.length,
                                shown: Math.min(filtered.length, RESULTS_LIMIT)
                            });
                            lastResults = filtered;
                            currentShown = Math.min(filtered.length, RESULTS_LIMIT);
                            renderResults(filtered.slice(0, currentShown), filtered.length);
                            return;
                        }
                        if (filtered.length > 0) {
                            lastResults = filtered;
                            currentShown = Math.min(filtered.length, RESULTS_LIMIT);
                            renderResults(filtered.slice(0, currentShown), filtered.length);
                        }
                    }
                }
                if (searchCache.has(q)) {
                    const cached = searchCache.get(q);
                    lastResults = cached.list;
                    cached.shown = cached.shown || Math.min(cached.list.length, RESULTS_LIMIT);
                    currentShown = cached.shown;
                    renderResults(cached.list.slice(0, currentShown), cached.total || cached.list.length);
                    return;
                }

                setResultsLoading(true);
                try {
                    if (currentSearchController) {
                        try {
                            currentSearchController.abort();
                        } catch (e) {}
                        currentSearchController = null;
                    }
                    currentSearchController = new AbortController();
                    const res = await fetch(`/almacen/buscar-articulos?q=${encodeURIComponent(q)}`, {
                        signal: currentSearchController.signal
                    });
                    currentSearchController = null;

                    if (!res.ok) {
                        console.warn('buscar articulos HTTP', res.status);
                        renderResults([]);
                        setResultsLoading(false);
                        return;
                    }
                    let data;
                    try {
                        data = await res.json();
                    } catch (err) {
                        console.warn('JSON parse error', err);
                        data = null;
                    }

                    let list = [];
                    if (!data) {
                        list = [];
                    } else if (Array.isArray(data)) list = data;
                    else if (Array.isArray(data.items)) list = data.items;
                    else if (Array.isArray(data.data)) list = data.data;
                    else if (Array.isArray(data.results)) list = data.results;
                    else list = [];

                    list = list.map(it => ({
                        ItemCode: it.ItemCode || it.Codigo || it.Code || it.item_code || '',
                        ItemName: it.ItemName || it.Nombre || it.Name || it.descripcion || it
                            .Description || '',
                        UoM: it.UoM || it.Unidad || it.U_Medida || it.Unit || '',
                        ItemGroup: it.ItemGroup || it.Grupo || it.Group || '',
                        ...it
                    }));

                    const totalCount = (Array.isArray(data) ? list.length : (data && typeof data.total ===
                        'number' ? data.total : list.length));
                    searchCache.set(q, {
                        list,
                        total: totalCount
                    });

                    lastResults = list;
                    renderResults(list.slice(0, RESULTS_LIMIT), totalCount);
                } catch (e) {
                    if (e.name === 'AbortError') {

                        return;
                    }
                    console.warn('buscar articulos', e);
                    resultsEl.innerHTML = '<div class="text-danger p-2">Error buscando artículos</div>';
                } finally {
                    setResultsLoading(false);
                }
            }

            searchInput.addEventListener('input', (e) => {
                const q = (e.target.value || '').trim();
                clearTimeout(searchTmr);
                searchTmr = setTimeout(() => doSearch(q), 300);
            });

            if (uomSelect) {
                uomSelect.addEventListener('change', () => {
                    try {
                        if (selectedItem) selectedItem.UoM = uomSelect.value;
                    } catch (e) {}
                });
            }

            searchInput.addEventListener('change', (e) => {
                const v = (e.target.value || '').trim();
                if (!v) return;
                let found = lastResults.find(it => {
                    const label = (it.ItemName || '') + (it.ItemCode ? (' (' + it.ItemCode + ')') : '');
                    return label === v || (it.ItemName && it.ItemName === v) || (it.ItemCode && it
                        .ItemCode === v) || (it.ItemCode && v.includes(it.ItemCode));
                });
                if (found) {
                    selectedItem = found;
                    renderSelected();
                    resultsEl.classList.remove('show');
                    resultsEl.innerHTML = '';
                    searchInput.value = found.ItemName || found.ItemCode || '';
                }
            });

            document.addEventListener('click', (ev) => {
                const wrapper = document.querySelector('.search-select-wrapper');
                if (!wrapper) return;
                if (!wrapper.contains(ev.target)) {
                    resultsEl.classList.remove('show');
                }
            });

            barcodeInput.addEventListener('input', (e) => {
                const raw = e.target.value || '';
                const cleaned = raw.replace(/\D/g, '');
                if (raw !== cleaned) {
                    const selStart = e.target.selectionStart || 0;
                    const diff = raw.length - cleaned.length;
                    e.target.value = cleaned;
                    try {
                        e.target.setSelectionRange(Math.max(0, selStart - diff), Math.max(0, selStart - diff));
                    } catch (_) {}
                }
                renderBarcode(cleaned);
                if ((cleaned || '').trim().length >= 6) {
                    fetchProductInfo(cleaned.trim());
                } else {
                    clearProductPreview();
                }
            });

            btnPaste.addEventListener('click', async () => {
                try {
                    const text = (await navigator.clipboard.readText()) || '';
                    const cleaned = String(text).replace(/\D/g, '');
                    barcodeInput.value = cleaned;
                    renderBarcode(cleaned);
                    if ((cleaned || '').trim().length >= 6) fetchProductInfo(cleaned.trim());
                } catch (e) {
                    barcodeInput.focus();
                }
            });

            

            btnLimpiar.addEventListener('click', () => {
                barcodeInput.value = '';
                renderBarcode('');
                searchInput.value = '';
                resultsEl.innerHTML = '';
                selectedItem = null;
                renderSelected();
                if (uomSelect) {
                    uomSelect.value = '';
                    uomSelect.disabled = true;
                }
            });

            btnGuardar.addEventListener('click', async () => {
                const barcode = (barcodeInput.value || '').trim();
                if (!barcode) return Swal.fire('Falta código', 'Captura o pega el código escaneado.',
                    'warning');
                if (!selectedItem) return Swal.fire('Falta artículo',
                    'Selecciona un artículo antes de guardar.', 'warning');

                async function checkBarcodeUnique(code) {
                    if (!code) return {
                        ok: true,
                        exists: false
                    };
                    try {
                        const resp = await fetch(
                            `/almacen/checar-codigo?barcode=${encodeURIComponent(code)}`);
                        if (resp.status === 404) return {
                            ok: true,
                            exists: false
                        };
                        if (!resp.ok) return {
                            ok: false,
                            error: `HTTP ${resp.status}`
                        };
                        const j = await resp.json();
                        if (typeof j.exists !== 'undefined') return {
                            ok: true,
                            exists: !!j.exists,
                            data: j
                        };
                        if (Array.isArray(j) && j.length) return {
                            ok: true,
                            exists: true,
                            data: j[0]
                        };
                        if (j && (j.found || j.exists === true)) return {
                            ok: true,
                            exists: true,
                            data: j
                        };
                        return {
                            ok: true,
                            exists: false
                        };
                    } catch (e) {
                        if (e.name === 'AbortError') return {
                            ok: false,
                            aborted: true
                        };
                        console.warn('checar codigo', e);
                        return {
                            ok: false,
                            error: String(e)
                        };
                    }
                }

                const check = await checkBarcodeUnique(barcode);
                if (!check.ok) {
                    return Swal.fire('Validación fallida',
                        'No se pudo verificar si el código ya existe. Intenta nuevamente.', 'warning');
                }
                if (check.exists) {
                    const itemLabel = (check.data && (check.data.item_code || check.data.ItemCode || check
                            .data.Codigo)) ?
                        ` (${check.data.item_code || check.data.ItemCode || check.data.Codigo})` : '';
                    return Swal.fire('Código duplicado',
                        `El código ya está registrado${itemLabel}. Verifica antes de continuar.`,
                        'warning');
                }

                const payload = {
                    barcode: barcode,
                    item_code: selectedItem.ItemCode || selectedItem.Codigo || '',
                    item_name: selectedItem.ItemName || selectedItem.Nombre || '',
                    uom: (uomSelect && uomSelect.value) ? uomSelect.value : (selectedItem.UoM || selectedItem.Unidad || ''),
                    item_group: selectedItem.ItemGroup || selectedItem.Grupo || '',
                    num_at_card: ''
                };

                try {
                    btnGuardar.disabled = true;
                    const resp = await fetch('/almacen/guardar-codigo-barra', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify(payload)
                    });
                    const contentType = (resp.headers.get('content-type') || '').toLowerCase();
                    let j = null;
                    if (contentType.includes('application/json')) j = await resp.json();
                    if (!resp.ok) throw new Error((j && j.msg) ? j.msg : `HTTP ${resp.status}`);
                    Swal.fire({
                        icon: 'success',
                        title: 'Registrado',
                        text: (j && j.msg) ? j.msg : 'Código guardado',
                        timer: 1400,
                        showConfirmButton: false
                    });
                    setTimeout(() => {
                        btnLimpiar.click();
                    }, 600);
                } catch (e) {
                    console.error('guardar codigo', e);
                    let userMsg = 'Error al guardar. Intenta nuevamente o contacta al administrador.';
                    try {
                        const em = String(e && e.message ? e.message : e);
                        if (/unique key|unique constraint|duplicate key/i.test(em)) {
                            userMsg = 'No se pudo guardar: el código ya existe (código duplicado).';    
                        } else if (/sqlstate|sql server|odbc|violation of/i.test(em)) {
                            userMsg = 'Error de base de datos. Contacta al equipo de soporte.';
                        } else if (/^HTTP \d{3}/.test(em) || /timeout|network/i.test(em)) {
                            userMsg = 'Error de comunicación con el servidor. Intenta de nuevo.';
                        } else if (em.length < 200) {
                            userMsg = em;
                        }
                    } catch (_) {}
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: userMsg
                    });
                } finally {
                    btnGuardar.disabled = false;
                }
            });

            renderBarcode('');
            renderSelected();
        })();
    </script>
@stop
