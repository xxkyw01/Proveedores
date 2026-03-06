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
                                    <input id="barcodeInput" type="text" class="form-control" placeholder="Escanea o pega aquí el código">
                                    <button id="btnPaste" class="btn btn-outline-secondary">Pegar</button>
                                </div>

                                <div id="barcodePreview" class="p-3 border rounded">
                                    <div class="small text-muted">Código</div>
                                    <div id="barcodeValue" class="barcode-value">-</div>
                                    <div class="small text-muted mt-2">Vista previa</div>
                                    <div id="barcodeGraphic" class="barcode-graphic">||||||||||||</div>
                                </div>
                                
                            </div>

                            <div class="col-12 col-md-7">
                                <label class="form-label">Buscar artículo (por nombre o código)</label>
                                <div class="search-select-wrapper">
                                    <input id="searchItemInput" type="search" class="form-control" placeholder="Escribe para buscar...">
                                    <div id="searchResults" class="mt-2" style="max-height:280px;overflow:auto;"></div>
                                </div>

                                <div id="selectedItemInfo" class="mt-3 p-3" style="min-height:120px;border:1px dashed #e9ecef;border-radius:6px;background:#fbfbfb;">
                                    <div class="text-muted">No hay artículo seleccionado.</div>
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
        (function(){
            const csrfToken = document.querySelector("meta[name='csrf-token']").getAttribute('content');

            const barcodeInput = document.getElementById('barcodeInput');
            const btnPaste = document.getElementById('btnPaste');
            const barcodeValue = document.getElementById('barcodeValue');
            const barcodeGraphic = document.getElementById('barcodeGraphic');
            const searchInput = document.getElementById('searchItemInput');
            const resultsEl = document.getElementById('searchResults');
            const selectedInfo = document.getElementById('selectedItemInfo');
            const btnGuardar = document.getElementById('btnGuardarAltaCodigo');
            const btnLimpiar = document.getElementById('btnLimpiar');

            let selectedItem = null;
            let searchTmr = null;
            let lastResults = [];

            function esc(t){ return String(t||''); }

            function renderBarcode(val){
                const v = esc(val) || '-';
                barcodeValue.textContent = v;
                barcodeGraphic.textContent = v ? ('*' + v + '*') : '||||||||||||';
            }

            function renderSelected(){
                try{
                    if(window.jQuery && $.fn.DataTable){
                        const dt = $('#selectedItemTable');
                        if(dt && dt.length && $.fn.DataTable.isDataTable('#selectedItemTable')){
                            $('#selectedItemTable').DataTable().clear().destroy();
                        }
                    }
                }catch(e){

                }

                if(!selectedItem){
                    selectedInfo.innerHTML = '<div class="text-muted">No hay artículo seleccionado.</div>';
                    return;
                }

                const rows = [
                    ['Código (ItemCode)', esc(selectedItem.ItemCode||selectedItem.Codigo||'')],
                    ['Nombre (ItemName)', esc(selectedItem.ItemName||selectedItem.Nombre||'')],
                    ['Unidad (UoM)', esc(selectedItem.UoM||selectedItem.Unidad||'')],
                    ['Grupo', esc(selectedItem.ItemGroup||selectedItem.Grupo||'')]
                ];
                let tableHtml = '<div class="table-responsive"><table id="selectedItemTable" class="table table-sm table-striped mb-0">';
                tableHtml += '<thead><tr><th style="width:35%">Propiedad</th><th>Valor</th></tr></thead><tbody>';
                rows.forEach(r => {
                    tableHtml += `<tr><td><strong>${r[0]}</strong></td><td>${r[1] || '&nbsp;'}</td></tr>`;
                });
                tableHtml += '</tbody></table></div>';
                selectedInfo.innerHTML = tableHtml;
                try{
                    if(window.jQuery && $.fn.DataTable){
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
                }catch(e){ console.warn('init DataTable selectedItemTable', e); }
            }

            function renderResults(list){
                resultsEl.innerHTML = '';
                if(!list || !list.length){
                    resultsEl.innerHTML = '<div class="text-muted p-2">No se encontraron artículos.</div>';
                    resultsEl.classList.add('show');
                    return;
                }

                list.forEach((it, idx) => {
                    const item = document.createElement('div');
                    item.className = 'search-item';
                    item.setAttribute('data-idx', String(idx));
                    item.innerHTML = `<div style="font-weight:700">${esc(it.ItemName||it.Nombre||'')}</div><small>${esc(it.ItemCode||it.Codigo||'')}</small>`;
                    item.addEventListener('click', ()=>{
                        selectedItem = it;
                        renderSelected();
                        resultsEl.classList.remove('show');
                        resultsEl.innerHTML = '';
                        searchInput.value = it.ItemName || it.Nombre || it.ItemCode || '';
                    });
                    resultsEl.appendChild(item);
                });

                resultsEl.classList.add('show');
            }

            function setResultsLoading(loading){
                if(loading){
                    resultsEl.innerHTML = '<div class="text-center p-3"><div class="spinner-border spinner-border-sm" role="status"></div> Buscando...</div>';
                    resultsEl.classList.add('show');
                } else {
                    if(resultsEl.innerHTML.includes('spinner-border')) resultsEl.innerHTML = '';
                }
            }

            async function doSearch(q){
                if(!q || q.length<2){ renderResults([]); return; }
                setResultsLoading(true);
                try{
                    const res = await fetch(`/almacen/buscar-articulos?q=${encodeURIComponent(q)}`);
                    if(!res.ok){
                        console.warn('buscar articulos HTTP', res.status);
                        renderResults([]);
                        setResultsLoading(false);
                        return;
                    }
                    let data;
                    try{ data = await res.json(); } catch(err){ console.warn('JSON parse error', err); data = null; }

                    let list = [];
                    if(!data){ list = []; }
                    else if(Array.isArray(data)) list = data;
                    else if(Array.isArray(data.items)) list = data.items;
                    else if(Array.isArray(data.data)) list = data.data;
                    else if(Array.isArray(data.results)) list = data.results;
                    else list = [];

                    list = list.map(it => ({
                        ItemCode: it.ItemCode || it.Codigo || it.Code || it.item_code || '',
                        ItemName: it.ItemName || it.Nombre || it.Name || it.descripcion || it.Description || '',
                        UoM: it.UoM || it.Unidad || it.U_Medida || it.Unit || '',
                        ItemGroup: it.ItemGroup || it.Grupo || it.Group || '',
                        ...it
                    }));

                    lastResults = list;
                    renderResults(list);
                }catch(e){ console.warn('buscar articulos',e); resultsEl.innerHTML = '<div class="text-danger p-2">Error buscando artículos</div>'; }
                finally{ setResultsLoading(false); }
            }

            searchInput.addEventListener('input', (e)=>{
                const q = (e.target.value||'').trim();
                clearTimeout(searchTmr);
                searchTmr = setTimeout(()=>doSearch(q), 300);
            });

            searchInput.addEventListener('change', (e) => {
                const v = (e.target.value||'').trim();
                if(!v) return;
                let found = lastResults.find(it => {
                    const label = (it.ItemName || '') + (it.ItemCode ? (' ('+it.ItemCode+')') : '');
                    return label === v || (it.ItemName && it.ItemName === v) || (it.ItemCode && it.ItemCode === v) || (it.ItemCode && v.includes(it.ItemCode));
                });
                if(found){
                    selectedItem = found;
                    renderSelected();
                    resultsEl.classList.remove('show');
                    resultsEl.innerHTML = '';
                    searchInput.value = found.ItemName || found.ItemCode || '';
                }
            });

            document.addEventListener('click', (ev) => {
                const wrapper = document.querySelector('.search-select-wrapper');
                if(!wrapper) return;
                if(!wrapper.contains(ev.target)){
                    resultsEl.classList.remove('show');
                }
            });

            barcodeInput.addEventListener('input', (e)=>{ renderBarcode(e.target.value); });
            btnPaste.addEventListener('click', async ()=>{
                try{
                    const text = (await navigator.clipboard.readText())||'';
                    barcodeInput.value = text;
                    renderBarcode(text);
                }catch(e){
                    barcodeInput.focus();
                }
            });

                btnLimpiar.addEventListener('click', ()=>{
                barcodeInput.value = '';
                renderBarcode('');
                searchInput.value = '';
                resultsEl.innerHTML = '';
                selectedItem = null;
                renderSelected();
            });

            btnGuardar.addEventListener('click', async ()=>{
                const barcode = (barcodeInput.value||'').trim();
                if(!barcode) return Swal.fire('Falta código','Captura o pega el código escaneado.','warning');
                if(!selectedItem) return Swal.fire('Falta artículo','Selecciona un artículo antes de guardar.','warning');

                    const payload = {
                    barcode: barcode,
                    item_code: selectedItem.ItemCode || selectedItem.Codigo || '',
                    item_name: selectedItem.ItemName || selectedItem.Nombre || '',
                    uom: selectedItem.UoM || selectedItem.Unidad || '',
                    item_group: selectedItem.ItemGroup || selectedItem.Grupo || '',
                        num_at_card: ''
                };

                try{
                    btnGuardar.disabled = true;
                    const resp = await fetch('/almacen/guardar-codigo-barra', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify(payload)
                    });
                    const contentType = (resp.headers.get('content-type')||'').toLowerCase();
                    let j = null;
                    if(contentType.includes('application/json')) j = await resp.json();
                    if(!resp.ok) throw new Error((j&&j.msg)?j.msg:`HTTP ${resp.status}`);
                    Swal.fire({ icon:'success', title:'Registrado', text:(j&&j.msg)?j.msg:'Código guardado', timer:1400, showConfirmButton:false });
                    setTimeout(()=>{ btnLimpiar.click(); }, 600);
                }catch(e){ console.error('guardar codigo',e); Swal.fire('Error', String(e.message||e),'error'); }
                finally{ btnGuardar.disabled = false; }
            });

            renderBarcode('');
            renderSelected();
        })();
    </script>
@stop
