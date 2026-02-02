@extends('layouts.movil')
@section('title', 'Cita Apartado')
@section('content')
    @include('includes.scripts.SweetAlert2')
    @include('includes.scripts.flatpickr')
    @include('includes.scripts.bootstrap')

    <x-sidebar />

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('assets/css/rol/almacen/apartado.css') }}">

    <div class="container-fluid con-sidebar">
        <div class="container">
            <h3 class="mb-3">Apartar horarios (Almacén)</h3>

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Ciudad</label>
                    <select id="ciudad" class="form-select">
                        <option value="">Seleccione...</option>
                        @foreach ($ciudades as $c)
                            <option value="{{ $c->id }}">{{ $c->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">CDI</label>
                    <select id="sucursal" class="form-select" disabled>
                        <option value="">Seleccione...</option>
                    </select>
                </div>


                <div class="col-md-4" id="wrap-anden">
                    <label class="form-label">Andén</label>
                    <select id="anden" class="form-select">
                        <option value="">Seleccione...</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label"> Duracion por defecto</label>
                    <select id="transporte" class="form-select" disabled>
                        @foreach ($transportes as $t)
                            @if ($t->id == 8)
                                <option value="{{ $t->id }}" selected>
                                    {{ $t->tipo ?? $t->nombre }} ({{ $t->duracion ?? 'Default' }})
                                </option>
                            @endif
                        @endforeach
                    </select>
                    <small class="text-muted">Transporte interno fijo para apartados.</small>

                </div>


                <div class="col-md-4">
                    <label class="form-label">Fecha</label>
                    <input type="text" id="fecha" class="form-control" placeholder="YYYY-MM-DD" disabled>
                </div>

                <div class="col-12">
                    <label class="form-label">Horas disponibles</label>
                    <div id="horasContainer" class="d-flex flex-wrap gap-2">
                        <div class="text-muted">Seleccione sucursal/andén, transporte y fecha.</div>
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Tipo de apartado</label>
                    <select id="apartado_tipo" class="form-select">
                        @foreach ($tiposApartado as $tipo)
                            <option value="{{ $tipo }}">{{ $tipo }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-8">
                    <label class="form-label">Motivo / Notas</label>
                    <input type="text" id="motivo" class="form-control" maxlength="500"
                        placeholder="Ej. limpieza general, inventario, etc.">
                </div>

                <div class="col-12 mt-2">
                    <button type="button" id="btnGuardar" class="btn btn-success">
                        Guardar apartados seleccionados
                    </button>
                </div>
            </div>
        </div>

    </div>

    <script>
        const csrf = document.querySelector('meta[name="csrf-token"]').content;

        // Controles
        const $ciudad = document.getElementById('ciudad');
        const $sucursal = document.getElementById('sucursal');
        const $anden = document.getElementById('anden');
        const $transporte = document.getElementById('transporte');
        const $fecha = document.getElementById('fecha');
        const $horas = document.getElementById('horasContainer');
        const $tipo = document.getElementById('apartado_tipo');
        const $motivo = document.getElementById('motivo');
        const $btnGuardar = document.getElementById('btnGuardar');

        const fpFecha = flatpickr("#fecha", {
            altInput: true,
            altFormat: "F j, Y",
            dateFormat: "Y-m-d",
            disable: [d => d.getDay() === 0]
        });

        // helper para alternar habilitado en ambos inputs
        function setFechaDisabled(flag) {
            const real = document.getElementById('fecha');
            real.disabled = flag;
            // el altInput es el que el usuario ve
            if (fpFecha && fpFecha.altInput) {
                fpFecha.altInput.disabled = flag;
            }
        }


        // Ciudades -> Sucursales
        $ciudad.addEventListener('change', async () => {
            $sucursal.innerHTML = "<option value=''>Seleccione...</option>";
            $sucursal.disabled = true;
            $transporte.disabled = false; // siempre activo
            setFechaDisabled(true);
            $anden.innerHTML = "<option value=''>Seleccione...</option>";
            $horas.innerHTML = "<div class='text-muted'>Seleccione sucursal/andén, transporte y fecha.</div>";

            if (!$ciudad.value) return;

            const res = await fetch(`/almacen/obtener-sucursales/${$ciudad.value}`);
            const data = await res.json();
            data.forEach(s => $sucursal.innerHTML += `<option value="${s.id}">${s.nombre}</option>`);
            $sucursal.disabled = false;
        });

        $sucursal.addEventListener('change', async () => {
            $transporte.disabled = !$sucursal.value;
            setFechaDisabled(!$sucursal.value);

            if ($sucursal.value) {
                const res = await fetch(`/almacen/obtener-andenes/${$sucursal.value}`);
                const data = await res.json();
                $anden.innerHTML = "<option value=''>Seleccione...</option>";
                data.forEach(a => $anden.innerHTML += `<option value="${a.id}">${a.nombre}</option>`);
            }

            $horas.innerHTML = "<div class='text-muted'>Seleccione sucursal, andén, transporte y fecha.</div>";
        });



        // Cargar horarios disponibles
        async function cargarDisponibilidad() {
            $horas.innerHTML = "<div class='text-muted'>Consultando disponibilidad...</div>";

            // SIEMPRE por andén
            const payload = {
                sucursal_id: $sucursal.value,
                fecha: $fecha.value,
                transporte_id: $transporte.value,
                anden_id: $anden.value
            };

            if (!payload.sucursal_id || !payload.fecha || !payload.transporte_id || !payload.anden_id) {
                $horas.innerHTML = "<div class='text-muted'>Seleccione sucursal/andén, transporte y fecha.</div>";
                return;
            }

            const res = await fetch('{{ route('almacen.citaApartado.disponibilidad') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf
                },
                body: JSON.stringify(payload)
            });
            const data = await res.json();

            const horarios = (data.ok && Array.isArray(data.horarios)) ? data.horarios : [];
            if (!horarios.length) {
                $horas.innerHTML = "<div class='text-muted'>Sin horarios disponibles (o seleccione un andén).</div>";
                return;
            }

            $horas.innerHTML = '';

            function formatearHora12(hora24) {
                // Quitar milisegundos si existen
                hora24 = hora24.split('.')[0];

                const [hora, minuto] = hora24.split(':').map(Number);
                const ampm = hora >= 12 ? 'PM' : 'AM';
                const hora12 = hora % 12 || 12;
                return `${hora12}:${minuto.toString().padStart(2, '0')} ${ampm}`;
            }

            $horas.innerHTML = '';
            horarios
                .filter(h => h.estado === 'Disponible')
                .forEach(h => {
                    const id = 'h_' + h.horario.replace(':', '_');
                    const hora12 = formatearHora12(h.horario);
                    $horas.insertAdjacentHTML('beforeend', `
      <input type="checkbox" class="btn-check hora-check" id="${id}" autocomplete="off" value="${h.horario}">
      <label class="btn btn-outline-secondary" for="${id}">${hora12}</label>
    `);
                });


            if (!$horas.querySelector('.hora-check')) {
                $horas.innerHTML = "<div class='text-muted'>No hay horarios disponibles para esos filtros.</div>";
            }
        }


        $transporte.addEventListener('change', cargarDisponibilidad);
        $fecha.addEventListener('change', cargarDisponibilidad);
        $anden.addEventListener('change', cargarDisponibilidad);

        // Guardar
        $btnGuardar.addEventListener('click', async () => {
            const horasSel = [...document.querySelectorAll('.hora-check:checked')].map(x => x.value);

            if (!$ciudad.value || !$sucursal.value || !$transporte.value || !$fecha.value) {
                return Swal.fire('Campos faltantes', 'Completa ciudad, sucursal, transporte y fecha.',
                    'warning');
            }
            // SIEMPRE por andén: valida directamente
            if (!$anden.value) {
                return Swal.fire('Falta andén', 'Selecciona un andén.', 'warning');
            }
            if (horasSel.length === 0) {
                return Swal.fire('Selecciona horarios', 'Elige al menos un horario.', 'warning');
            }

            const body = {
                sucursal_id: $sucursal.value,
                fecha: $fecha.value,
                transporte_id: $transporte.value, // fijo 8 en backend por seguridad
                apartado_tipo: $tipo.value,
                motivo: $motivo.value,
                anden_id: $anden.value,
                horas: horasSel
            };

            try {
                const res = await fetch('{{ route('almacen.citaApartado.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf
                    },
                    body: JSON.stringify(body)
                });
                const json = await res.json();

                if (json.success) {
                    Swal.fire('Listo', json.message || 'Apartados creados', 'success');
                    document.querySelectorAll('.hora-check:checked').forEach(c => c.checked = false);
                } else {
                    Swal.fire('Atención', json.message || 'No se pudieron crear apartados', 'warning');
                }
            } catch (e) {
                console.error(e);
                Swal.fire('Error', 'Ocurrió un error al guardar.', 'error');
            }
        });
    </script>
@endsection
