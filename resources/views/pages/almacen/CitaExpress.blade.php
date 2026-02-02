@extends('layouts.movil')
@section('title', 'Citas Express')
@section('content')
    @include('includes.scripts.SweetAlert2')
    @include('includes.scripts.flatpickr')
    @include('includes.scripts.bootstrap')
    <x-sidebar />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Enlace al CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/rol/almacen/express.css') }}">

    <div class="container-fluid con-sidebar">

        <div class="container">
            <h4 class="titulo-paso">
                <i class="fas fa-shipping-fast text-orange"></i> Citas Express Recepción Inmediata
            </h4>

            <!-- FORMULARIO COMPLETO -->
            <form id="form-cita-express">
                <div class="mb-3">
                    <label for="ciudad" class="form-label">Ciudad</label>
                    <select id="ciudad" name="ciudad" class="form-select custom-select" required>
                        <option value="">Seleccione...</option>
                        @foreach ($ciudades as $c)
                            <option value="{{ $c->id }}">{{ $c->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="sucursal" class="form-label">CDI</label>
                    <select id="sucursal" name="sucursal_id" class="form-select custom-select" required>
                        <option value="">Seleccione...</option>
                    </select>
                </div>

                <h5 class="titulo-paso">
                    <i class="fas fa-box text-orange"></i> Detalles de la Entrega
                </h5>

                <div class="mb-3">
                    <label for="tipo" class="form-label">Tipo de entrega</label>
                    <select id="tipo" name="qbox" class="form-select custom-select" required>
                        <option value="Paquetería">Paquetería</option>
                        <option value="Uber">Uber</option>
                        <option value="Didi">Didi</option>
                        <option value="Otro">Otro</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="proveedor" class="form-label">Proveedor (opcional)</label>
                    <input type="text" id="proveedor" name="proveedor_id" class="form-control custom-input"
                        placeholder="Código del proveedor (si aplica)">
                </div>

                <div class="mb-3">
                    <label for="fecha" class="form-label">Fecha</label>
                    <input type="date" id="fecha" name="fecha" class="form-control custom-input" required>
                </div>

                <div class="mb-3">
                    <label for="hora" class="form-label">Hora (opcional)</label>
                    <input type="time" id="hora" name="hora" class="form-control custom-input">
                    <small class="text-muted">
                        Si no selecciona, se usará 17:00 (5:00 PM)
                    </small>
                </div>

                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción o contenido</label>
                    <textarea id="descripcion" name="descripcion" class="form-control custom-input" minlength="50" required
                        placeholder="Descripción del paquete o contenido (mínimo 50 caracteres) es recomendable tambien mencionar la orden de compra"></textarea>
                </div>

                <div class="mb-3">
                    <label for="archivo" class="form-label">Adjunto de archivo</label>
                    <input type="file" id="archivo" name="evidencias" class="form-control custom-input">
                </div>

                <button type="submit" class="btn btn-primary w-100">Registrar entrega express</button>
            </form>
        </div>

    </div> {{-- Cierra container-fluid --}}

    <script>
        document.getElementById('ciudad').addEventListener('change', function() {
            const ciudadId = this.value;

            const sucursalSelect = document.getElementById('sucursal');
            sucursalSelect.innerHTML = "<option value=''>Seleccione...</option>";

            if (!ciudadId) return;

            fetch(`/almacen/cita-express/sucursales/${ciudadId}`)
                .then(res => {
                    if (!res.ok) throw new Error("No se pudieron cargar las sucursales");
                    return res.json();
                })
                .then(data => {
                    data.forEach(s => {
                        sucursalSelect.innerHTML += `<option value="${s.id}">${s.nombre}</option>`;
                    });
                })
                .catch(err => {
                    console.error("Error cargando sucursales:", err);
                    Swal.fire('Error', 'No se pudieron cargar las sucursales', 'error');
                });
        });

        document.getElementById('form-cita-express').addEventListener('submit', function(e) {
            e.preventDefault();

            const sucursalSelect = document.getElementById('sucursal');
            if (!sucursalSelect.value) {
                Swal.fire('Error', 'Seleccione una sucursal', 'error');
                return;
            }

            const formData = new FormData(this);

            console.log("FormData enviado:");
            for (let [key, value] of formData.entries()) {
                console.log(key, value);
            }

            fetch('/almacen/cita-express/registrar', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                })
                .then(async (res) => {
                    const data = await res.json().catch(() => ({}));
                    if (!res.ok) {
                        const msg = data.message || `Error ${res.status}`;
                        if (res.status === 401) Swal.fire('Sesión', msg ||
                            'No autenticado o sesión expirada', 'warning');
                        else if (res.status === 422) Swal.fire('Validación', (data.errors && JSON.stringify(
                            data.errors)) || msg, 'warning');
                        else Swal.fire('Error', msg, 'error');
                        throw new Error(msg);
                    }
                    return data;
                })
                .then((data) => {
                    if (data.success) {
                        Swal.fire('¡Listo!', data.message, 'success');
                        document.getElementById('form-cita-express').reset();
                        document.getElementById('sucursal').innerHTML =
                            "<option value=''>Seleccione...</option>";
                    } else {
                        Swal.fire('Error', data.message || 'Ocurrió un problema al guardar la cita', 'error');
                    }
                })
                .catch((err) => {
                    console.error('Error en fetch:', err);
                });
        });
    </script>
@endsection
